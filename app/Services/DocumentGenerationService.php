<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentGenerationService
{
    protected $templatePath;
    protected $outputPath;
    protected $apiKey;
    protected $apiUrl;
    protected $delimiters;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/document_templates');
        $this->outputPath = storage_path('app/generated_documents');
        
        // Ensure directories exist
        if (!file_exists($this->templatePath)) {
            \File::makeDirectory($this->templatePath, 0755, true);
        }
        if (!file_exists($this->outputPath)) {
            \File::makeDirectory($this->outputPath, 0755, true);
        }

        // Get DocuGenerate API configuration
        $config = config('services.docugenerate');
        $this->apiKey = $config['api_key'] ?? null;
        $this->apiUrl = rtrim($config['api_url'] ?? 'https://api.docugenerate.com/v1', '/');
        $this->delimiters = $config['delimiters'] ?? ['left' => '[', 'right' => ']'];

        if (!$this->apiKey) {
            Log::warning('DocuGenerate API key not configured. Document generation will fail.');
        }
    }

    /**
     * Generate document from template using DocuGenerate API
     * 
     * @param DocumentRequest $documentRequest
     * @return array ['docx_path', 'pdf_path', 'docx_url', 'pdf_url', 'docx_filename', 'pdf_filename']
     */
    public function generateDocument(DocumentRequest $documentRequest): array
    {
        set_time_limit(180); // 3 minutes for document generation
        
        try {
            // Load document type and relationships (needed for Barangay ID photo and user data)
            $documentType = $documentRequest->documentType;
            if (!$documentType) {
                throw new \Exception('Document type not found for this request');
            }

            // Load attachments if not already loaded (needed for photo in Barangay ID)
            if (!$documentRequest->relationLoaded('attachments')) {
                $documentRequest->load('attachments');
            }
            
            // Load user relationship if not already loaded (needed for place_of_birth)
            if (!$documentRequest->relationLoaded('user')) {
                $documentRequest->load('user');
            }

            $documentTypeName = $documentType->document_name;
            
            // Get or upload template to DocuGenerate
            // This is REQUIRED - we only use DocuGenerate, never create documents from scratch
            $templateId = $this->ensureTemplateUploaded($documentType);
            
            if (!$templateId) {
                $errorMessage = "Template not available for '{$documentTypeName}'. Please ensure a DOCX template file exists in storage/app/templates/document_templates/ and is uploaded to DocuGenerate.";
                Log::error('Document generation failed - template not available', [
                    'doc_request_id' => $documentRequest->doc_request_id,
                    'document_type' => $documentTypeName,
                    'template_path' => $this->templatePath,
                ]);
                throw new \Exception($errorMessage);
            }

            // Prepare data for template replacement
            $data = $this->prepareTemplateData($documentRequest);

            // Generate DOCX format first (this works reliably)
            $docxResult = $this->generateDocumentViaAPI($templateId, $data, 'docx', $documentRequest);

            // Generate file names
            $docxFileName = $this->generateFileName($documentRequest, 'docx');
            $pdfFileName = $this->generateFileName($documentRequest, 'pdf');

            // Download and save DOCX document
            $docxPath = null;
            $pdfPath = null;

            if ($docxResult && isset($docxResult['document_uri'])) {
                $docxPath = $this->downloadDocument($docxResult['document_uri'], $docxFileName);
            }

            // Try to generate PDF, but if it fails or returns wrong format, use DOCX for PDF too
            $pdfResult = $this->generateDocumentViaAPI($templateId, $data, 'pdf', $documentRequest);
            
            if ($pdfResult && isset($pdfResult['document_uri'])) {
                // Verify the response format matches what we requested
                $responseFormat = $pdfResult['format'] ?? '';
                $isValidPdfFormat = $responseFormat && str_contains(strtolower($responseFormat), 'pdf');
                
                if (!$isValidPdfFormat) {
                    Log::warning('PDF generation returned wrong format, will use DOCX instead', [
                        'requested' => 'pdf',
                        'received' => $responseFormat,
                        'document_uri' => $pdfResult['document_uri'],
                    ]);
                    // Don't download if format is wrong - we'll use DOCX for PDF
                    $pdfPath = null;
                } else {
                    $pdfPath = $this->downloadDocument($pdfResult['document_uri'], $pdfFileName);
                    
                    // Double-check the downloaded file is actually a PDF
                    if ($pdfPath && file_exists($pdfPath)) {
                        $firstBytes = file_get_contents($pdfPath, false, null, 0, 4);
                        $isPdf = ($firstBytes === '%PDF');
                        if (!$isPdf) {
                            Log::warning('Downloaded PDF file is not actually a PDF, using DOCX instead', [
                                'file' => $pdfFileName,
                            ]);
                            // Delete the incorrect file
                            @unlink($pdfPath);
                            $pdfPath = null;
                        }
                    }
                }
            }
            
            // If PDF generation failed or returned wrong format, use DOCX for PDF URL
            if (!$pdfPath && $docxPath) {
                Log::info('Using DOCX file for PDF download since PDF generation failed', [
                    'doc_request_id' => $documentRequest->doc_request_id,
                ]);
                // Don't create a separate PDF file, just use DOCX for both
                $pdfPath = null;
            }

            // Generate URLs
            $docxUrl = $docxPath ? route('document_requests.download', [
                'id' => $documentRequest->doc_request_id,
                'format' => 'docx'
            ]) : null;

            // If PDF doesn't exist, use DOCX URL for PDF (users can save as PDF from Word)
            if ($pdfPath) {
                $pdfUrl = route('document_requests.download', [
                    'id' => $documentRequest->doc_request_id,
                    'format' => 'pdf'
                ]);
            } elseif ($docxPath) {
                // Use DOCX for PDF if PDF generation failed
                $pdfUrl = $docxUrl;
                Log::info('PDF not available, using DOCX URL for PDF download', [
                    'doc_request_id' => $documentRequest->doc_request_id,
                ]);
            } else {
                $pdfUrl = null;
            }

            return [
                'docx_path' => $docxPath,
                'pdf_path' => $pdfPath,
                'docx_url' => $docxUrl,
                'pdf_url' => $pdfUrl,
                'docx_filename' => $docxFileName,
                'pdf_filename' => $pdfFileName,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate document via DocuGenerate API', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Ensure template is uploaded to DocuGenerate and return template_id
     * 
     * @param DocumentType $documentType
     * @return string|null Template ID
     */
    protected function ensureTemplateUploaded(DocumentType $documentType): ?string
    {
        $documentTypeName = $documentType->document_name;
        
        // For Building Permit, use Business Permit's template_id if available
        // This allows both permits to share the same template
        if (stripos($documentTypeName, 'Building Permit') !== false) {
            $businessPermitType = DocumentType::where('document_name', 'Business Permit')->first();
            if ($businessPermitType && $businessPermitType->template_id) {
                Log::info("Building Permit using Business Permit's template_id for unified template", [
                    'building_permit_id' => $documentType->document_type_id,
                    'business_permit_template_id' => $businessPermitType->template_id,
                ]);
                return $businessPermitType->template_id;
            }
        }
        
        Log::info("Ensuring template is uploaded for document type", [
            'document_type' => $documentTypeName,
            'existing_template_id' => $documentType->template_id,
        ]);

        // Find local template file first (we need it to check if it changed)
        $templatePath = $this->findLocalTemplate($documentTypeName);
        
        if (!$templatePath) {
            Log::error("Local template not found for document type - DocuGenerate cannot be used", [
                'document_type' => $documentTypeName,
                'template_path' => $this->templatePath,
            ]);
            return null;
        }

        // If template_id already exists, verify it still exists in DocuGenerate
        // Only re-upload if the template was deleted from DocuGenerate
        // To update a template, manually clear the template_id in the database first
        if ($documentType->template_id) {
            Log::info("Template ID exists, verifying with DocuGenerate", [
                'template_id' => $documentType->template_id,
                'document_type' => $documentTypeName,
            ]);
            
            if ($this->verifyTemplateExists($documentType->template_id)) {
                Log::info("Template verified in DocuGenerate, reusing existing template", [
                    'template_id' => $documentType->template_id,
                ]);
                return $documentType->template_id;
            } else {
                // Template was deleted from DocuGenerate, clear the ID so we can search for existing or re-upload
                Log::warning("Template ID exists in DB but not in DocuGenerate, clearing it to search for existing template", [
                    'template_id' => $documentType->template_id,
                ]);
                $documentType->template_id = null;
                $documentType->save();
            }
        }

        // Before uploading, check if a template with this name already exists in DocuGenerate
        // This prevents creating duplicate templates when template_id is null but template already exists
        $existingTemplateId = $this->findExistingTemplateByName($documentTypeName);
        if ($existingTemplateId) {
            Log::info("Found existing template in DocuGenerate with same name, reusing it", [
                'document_type' => $documentTypeName,
                'template_id' => $existingTemplateId,
            ]);
            
            // Save the found template_id to database
            $documentType->template_id = $existingTemplateId;
            $documentType->save();
            
            return $existingTemplateId;
        }

        // Only upload if no existing template was found
        // IMPORTANT: This will fail if you've reached your template limit
        // In that case, manually set the template_id in the database using the existing template ID
        Log::warning("No existing template found in DocuGenerate. Attempting to upload new template.", [
            'template_path' => $templatePath,
            'document_type' => $documentTypeName,
            'warning' => 'If you have reached your DocuGenerate template limit, this will fail. Please manually set template_id in database instead.',
        ]);
        
        $templateId = $this->uploadTemplate($templatePath, $documentTypeName);
        
        if ($templateId) {
            // Save template_id to database
            $documentType->template_id = $templateId;
            $documentType->save();
            
            Log::info("Template successfully uploaded and saved to DocuGenerate", [
                'document_type' => $documentTypeName,
                'template_id' => $templateId,
            ]);
        } else {
            Log::error("Failed to upload template to DocuGenerate", [
                'document_type' => $documentTypeName,
                'template_path' => $templatePath,
                'note' => 'If you reached your template limit, manually set template_id in database using existing template ID',
                'instruction' => 'Run: php artisan cedula:set-template-id YOUR_TEMPLATE_ID',
            ]);
        }

        return $templateId;
    }

    /**
     * Find local template file
     * 
     * @param string $documentTypeName
     * @return string|null Template file path
     */
    protected function findLocalTemplate(string $documentTypeName): ?string
    {
        // Map Building Permit to use Business Permit template (unified template)
        $templateName = $documentTypeName;
        if (stripos($documentTypeName, 'Building Permit') !== false) {
            $templateName = 'Business Permit';
            Log::info("Mapping Building Permit to Business Permit template for unified template", [
                'original_document_type' => $documentTypeName,
                'template_name' => $templateName,
            ]);
        }
        
        // Try multiple variations of the document name
        $possibleNames = [
            // Exact match
            $templateName . '.docx',
            // Sanitized version
            $this->sanitizeFileName($templateName) . '.docx',
            // Lowercase version
            strtolower($templateName) . '.docx',
            // Uppercase first letter
            ucfirst(strtolower($templateName)) . '.docx',
            // With spaces replaced by underscores
            str_replace(' ', '_', $templateName) . '.docx',
            str_replace(' ', '_', strtolower($templateName)) . '.docx',
            // With spaces replaced by hyphens
            str_replace(' ', '-', $templateName) . '.docx',
            str_replace(' ', '-', strtolower($templateName)) . '.docx',
        ];

        // Remove duplicates while preserving order
        $possibleNames = array_unique($possibleNames);

        foreach ($possibleNames as $possibleName) {
            // Use DIRECTORY_SEPARATOR for cross-platform compatibility
            $possiblePath = $this->templatePath . DIRECTORY_SEPARATOR . $possibleName;
            // Also try with forward slash (works on Windows too)
            $possiblePathAlt = $this->templatePath . '/' . $possibleName;
            
            $foundPath = null;
            if (file_exists($possiblePath)) {
                $foundPath = $possiblePath;
            } elseif (file_exists($possiblePathAlt)) {
                $foundPath = $possiblePathAlt;
            }
            
            if ($foundPath) {
                // DocuGenerate only supports DOCX templates
                if (str_ends_with(strtolower($possibleName), '.docx')) {
                    Log::info("Found template file: {$possibleName}", [
                        'document_type' => $documentTypeName,
                        'template_name' => $templateName,
                        'template_path' => $foundPath,
                    ]);
                    return $foundPath;
                } else {
                    Log::warning("PDF template found but DocuGenerate requires DOCX: {$possibleName}");
                }
            }
        }

        Log::warning("Template not found for document type", [
            'document_type' => $documentTypeName,
            'template_name' => $templateName,
            'template_path' => $this->templatePath,
            'searched_names' => $possibleNames,
        ]);

        return null;
    }

    /**
     * Upload template to DocuGenerate
     * 
     * @param string $templatePath
     * @param string $templateName
     * @return string|null Template ID
     */
    public function uploadTemplate(string $templatePath, string $templateName): ?string
    {
        if (!$this->apiKey) {
            throw new \Exception('DocuGenerate API key not configured');
        }

        if (!file_exists($templatePath)) {
            throw new \Exception("Template file not found: {$templatePath}");
        }

        try {
            // Read file contents
            $fileContents = file_get_contents($templatePath);
            $fileName = basename($templatePath);
            
            // Build multipart form data array
            $multipartData = [
                [
                    'name' => 'file',
                    'contents' => $fileContents,
                    'filename' => $fileName,
                ],
                [
                    'name' => 'name',
                    'contents' => $templateName,
                ],
                [
                    'name' => 'delimiters',
                    'contents' => json_encode([
                        'left' => $this->delimiters['left'],
                        'right' => $this->delimiters['right'],
                    ]),
                ],
            ];
            
            // Build the request with multipart form data
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->asMultipart()->post("{$this->apiUrl}/template", $multipartData);

            if ($response->successful()) {
                $data = $response->json();
                return $data['id'] ?? null;
            } else {
                Log::error('Failed to upload template to DocuGenerate', [
                    'template' => $templatePath,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while uploading template to DocuGenerate', [
                'template' => $templatePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify template exists in DocuGenerate
     * 
     * @param string $templateId
     * @return bool
     */
    protected function verifyTemplateExists(string $templateId): bool
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->apiUrl}/template/{$templateId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Failed to verify template existence', [
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Find existing template in DocuGenerate by name
     * This prevents creating duplicate templates when template_id is null
     * 
     * @param string $templateName
     * @return string|null Template ID if found
     */
    protected function findExistingTemplateByName(string $templateName): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            // Try to list templates and find one with matching name
            // DocuGenerate API endpoint for listing templates
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->apiUrl}/template");

            if ($response->successful()) {
                $templates = $response->json();
                
                // Handle different response formats
                $templateList = [];
                if (isset($templates['data']) && is_array($templates['data'])) {
                    $templateList = $templates['data'];
                } elseif (is_array($templates)) {
                    $templateList = $templates;
                }
                
                // Search for template with matching name (case-insensitive)
                foreach ($templateList as $template) {
                    $name = $template['name'] ?? $template['template_name'] ?? '';
                    if (strcasecmp(trim($name), trim($templateName)) === 0) {
                        $templateId = $template['id'] ?? $template['template_id'] ?? null;
                        if ($templateId) {
                            Log::info("Found existing template in DocuGenerate by name", [
                                'template_name' => $templateName,
                                'template_id' => $templateId,
                            ]);
                            return (string) $templateId;
                        }
                    }
                }
            } else {
                // If listing fails (e.g., endpoint doesn't exist), log and continue
                Log::debug("Could not list templates from DocuGenerate (may not be supported)", [
                    'status' => $response->status(),
                    'response' => substr($response->body(), 0, 200),
                ]);
            }
        } catch (\Exception $e) {
            // If listing templates fails, that's okay - we'll just try to upload
            // This prevents errors if the API endpoint doesn't support listing
            Log::debug("Could not search for existing template by name (API may not support listing)", [
                'template_name' => $templateName,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Generate document via DocuGenerate API
     * 
     * @param string $templateId
     * @param array $data
     * @param string $format 'docx' or 'pdf'
     * @param DocumentRequest $documentRequest
     * @return array|null API response data
     */
    protected function generateDocumentViaAPI(string $templateId, array $data, string $format, DocumentRequest $documentRequest): ?array
    {
        if (!$this->apiKey) {
            throw new \Exception('DocuGenerate API key not configured');
        }

        // DocuGenerate expects data as an array of objects
        // We'll wrap our data in an array
        $dataArray = [$data];

        // Generate document name
        $documentName = $this->generateFileName($documentRequest, $format);
        $documentName = pathinfo($documentName, PATHINFO_FILENAME); // Remove extension

        // Log data being sent (excluding large base64 strings for readability)
        $logData = $data;
        if (isset($logData['photo'])) {
            $logData['photo'] = '[BASE64_IMAGE_DATA: ' . strlen($logData['photo']) . ' chars]';
        }
        if (isset($logData['image'])) {
            $logData['image'] = '[BASE64_IMAGE_DATA: ' . strlen($logData['image']) . ' chars]';
        }
        if (isset($logData['picture'])) {
            $logData['picture'] = '[BASE64_IMAGE_DATA: ' . strlen($logData['picture']) . ' chars]';
        }
        
        Log::info('Sending data to DocuGenerate API', [
            'template_id' => $templateId,
            'format' => $format,
            'data_keys' => array_keys($data),
            'has_photo' => isset($data['photo']),
            'data_sample' => $logData,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post("{$this->apiUrl}/document", [
                'template_id' => $templateId,
                'data' => $dataArray,
                'format' => $format === 'pdf' ? '.pdf' : '.docx', // Ensure correct format format
                'name' => $documentName,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('DocuGenerate API response', [
                    'format_requested' => $format,
                    'response_format' => $result['format'] ?? 'unknown',
                    'document_uri' => $result['document_uri'] ?? 'missing',
                    'filename' => $result['filename'] ?? 'unknown',
                ]);
                return $result;
            } else {
                Log::error('Failed to generate document via DocuGenerate API', [
                    'template_id' => $templateId,
                    'format' => $format,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while generating document via DocuGenerate API', [
                'template_id' => $templateId,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download document from DocuGenerate URI and save locally
     * 
     * @param string $documentUri
     * @param string $fileName
     * @return string|null Local file path
     */
    protected function downloadDocument(string $documentUri, string $fileName): ?string
    {
        try {
            $response = Http::timeout(60)
                ->withOptions([
                    'sink' => null, // Get response as string
                ])
                ->get($documentUri);

            if ($response->successful()) {
                $filePath = $this->outputPath . '/' . $fileName;
                
                // Get content type from response
                $contentType = $response->header('Content-Type') ?? '';
                Log::info('Downloading document from DocuGenerate', [
                    'uri' => $documentUri,
                    'expected_file' => $fileName,
                    'content_type' => $contentType,
                ]);
                
                // Ensure we write binary data correctly
                $fileContents = $response->body();
                file_put_contents($filePath, $fileContents, LOCK_EX);
                
                // Verify file was written correctly
                if (!file_exists($filePath) || filesize($filePath) === 0) {
                    Log::error('Downloaded file is empty or not written', [
                        'file' => $fileName,
                        'path' => $filePath,
                    ]);
                    return null;
                }
                
                // Verify file type by checking first bytes
                $firstBytes = substr($fileContents, 0, 4);
                $isPdf = ($firstBytes === '%PDF');
                $isDocx = ($firstBytes[0] === 'P' && $firstBytes[1] === 'K' && ord($firstBytes[2]) === 0x03 && ord($firstBytes[3]) === 0x04);
                
                Log::info('Document downloaded from DocuGenerate', [
                    'file' => $fileName,
                    'path' => $filePath,
                    'size' => filesize($filePath),
                    'content_type' => $contentType,
                    'detected_pdf' => $isPdf,
                    'detected_docx' => $isDocx,
                    'first_bytes' => bin2hex($firstBytes),
                ]);
                
                // Warn if file type doesn't match expected format
                if (str_ends_with(strtolower($fileName), '.pdf') && !$isPdf) {
                    Log::warning('PDF file appears to be wrong format!', [
                        'file' => $fileName,
                        'expected' => 'PDF',
                        'detected' => $isDocx ? 'DOCX' : 'Unknown',
                    ]);
                }
                
                return $filePath;
            } else {
                Log::error('Failed to download document from DocuGenerate', [
                    'uri' => $documentUri,
                    'status' => $response->status(),
                    'response' => substr($response->body(), 0, 200), // First 200 chars for debugging
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while downloading document from DocuGenerate', [
                'uri' => $documentUri,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Prepare data array for template replacement
     * This method is kept from the original implementation
     */
    protected function prepareTemplateData(DocumentRequest $documentRequest): array
    {
        $reviewedAt = $documentRequest->reviewed_at ?? Carbon::now();
        if (is_string($reviewedAt)) {
            $reviewedAt = Carbon::parse($reviewedAt);
        }

        // Extract day, month, year from reviewed_at
        $dayApproved = $reviewedAt->format('d');
        $monthApproved = $reviewedAt->format('F'); // Full month name
        $monthApprovedNumeric = $reviewedAt->format('m');
        $yearApproved = $reviewedAt->format('Y');

        // Get raw name values (with fallback to user table)
        $user = $documentRequest->user;
        $rawFirst  = trim((string) ($documentRequest->first_name ?? $user?->first_name ?? ''));
        $rawMiddle = trim((string) ($documentRequest->middle_name ?? $user?->middle_name ?? ''));
        $rawLast   = trim((string) ($documentRequest->last_name ?? $user?->last_name ?? ''));
        $rawSuffix = trim((string) ($documentRequest->suffix ?? $user?->suffix ?? ''));

        // Normalize names to remove duplicates (same logic as DocumentRequestController)
        // Check if last name is duplicated in first name
        if ($rawFirst !== '') {
            $parts = preg_split('/\s+/', $rawFirst, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) > 1 && empty($rawLast)) {
                // If first name has multiple parts and no last name, split it
                $firstCandidate = array_shift($parts);
                $lastCandidate  = array_pop($parts);
                $middleCandidate = count($parts) ? implode(' ', $parts) : $rawMiddle;

                $rawFirst  = $firstCandidate;
                $rawLast   = $lastCandidate;
                if (empty($rawMiddle)) {
                    $rawMiddle = $middleCandidate;
                }
            } elseif (count($parts) > 1 && $rawLast !== '') {
                // If first name has multiple parts and last name exists, check for duplicate
                if (strcasecmp(end($parts), $rawLast) === 0) {
                    // Last part of first name matches last name - remove it
                    $rawFirst = array_shift($parts);
                    if (empty($rawMiddle) && count($parts) > 0) {
                        array_pop($parts); // Remove the duplicate last name
                        $rawMiddle = count($parts) ? implode(' ', $parts) : $rawMiddle;
                    }
                }
            }
        }

        // Check if last name is duplicated in middle name
        if ($rawMiddle !== '' && $rawLast !== '') {
            $pattern = '/\b' . preg_quote($rawLast, '/') . '$/i';
            if (preg_match($pattern, $rawMiddle)) {
                $rawMiddle = trim(preg_replace($pattern, '', $rawMiddle));
            }
        }

        // Normalize case (Title Case)
        $normalize = function ($s) {
            $s = trim((string) $s);
            if ($s === '') return '';
            $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
            $words = array_map(function ($w) {
                $w = mb_strtolower($w);
                $first = mb_strtoupper(mb_substr($w, 0, 1));
                $rest  = mb_substr($w, 1);
                return $first . $rest;
            }, $words);
            return implode(' ', $words);
        };

        $firstName = $normalize($rawFirst);
        $middleName = $normalize($rawMiddle);
        $lastName = $normalize($rawLast);
        $suffix = $normalize($rawSuffix);
        
        // Build full name from normalized parts
        $nameParts = array_filter([$firstName, $middleName, $lastName, $suffix], function ($p) {
            return $p !== '' && $p !== null;
        });
        $fullName = implode(' ', $nameParts);

        // Format birthdate
        $birthdate = $documentRequest->birthdate;
        $birthdateFormatted = $birthdate ? Carbon::parse($birthdate)->format('F d, Y') : '';
        $birthdateShort = $birthdate ? Carbon::parse($birthdate)->format('m/d/Y') : '';
        
        // Get place of birth from user (for Barangay ID template)
        $user = $documentRequest->user;
        $placeOfBirth = $user->place_of_birth ?? '';

        // Get address components
        $houseNumber = $documentRequest->house_number ?? '';
        $phase = $documentRequest->phase ?? '';
        $package = $documentRequest->package ?? '';
        $fullAddress = trim("{$houseNumber} {$phase} {$package}");
        
        // Get ID number (for Barangay ID template)
        $idNumber = $documentRequest->valid_id_number ?? '';

        // Get approver name
        $approverName = 'N/A';
        if ($documentRequest->approver) {
            $approver = $documentRequest->approver;
            $approverName = trim("{$approver->first_name} {$approver->middle_name} {$approver->last_name} {$approver->suffix}");
        }

        // Get extra fields from document request
        $extraFields = $documentRequest->extra_fields ?? [];
        if (is_string($extraFields)) {
            $extraFields = json_decode($extraFields, true) ?? [];
        }
        
        // Log extra_fields for debugging
        Log::info('Extra fields extracted from document request', [
            'doc_request_id' => $documentRequest->doc_request_id,
            'extra_fields_type' => gettype($documentRequest->extra_fields),
            'extra_fields_raw' => $documentRequest->extra_fields,
            'extra_fields_parsed' => $extraFields,
            'extra_fields_keys' => is_array($extraFields) ? array_keys($extraFields) : [],
        ]);

        // Check if this is a Barangay ID request and get photo
        $photoBase64 = null;
        $documentTypeName = $documentRequest->documentType->document_name ?? '';
        $isBarangayId = stripos($documentTypeName, 'Barangay ID') !== false;
        
        if ($isBarangayId) {
            // Load attachments relationship if not already loaded
            if (!$documentRequest->relationLoaded('attachments')) {
                $documentRequest->load('attachments');
            }
            
            // Look for photo attachment - check common field names
            $photoAttachment = null;
            $photoFieldNames = ['photo', '2x2_photo', '2x2 photo', 'profile_photo', 'resident_photo', 'id_photo'];
            
            foreach ($photoFieldNames as $fieldName) {
                $photoAttachment = $documentRequest->attachments
                    ->firstWhere('field_name', $fieldName);
                if ($photoAttachment) {
                    break;
                }
            }
            
            // If no specific photo field found, try to find any image attachment
            if (!$photoAttachment) {
                $photoAttachment = $documentRequest->attachments
                    ->filter(function ($attachment) {
                        $mimeType = $attachment->file_type ?? '';
                        return str_starts_with($mimeType, 'image/');
                    })
                    ->first();
            }
            
            // Convert photo to base64 if found
            if ($photoAttachment && $photoAttachment->file_path) {
                try {
                    $photoPath = $photoAttachment->file_path;
                    
                    // Normalize the path for Storage facade
                    // Remove 'public/' prefix if present, as Storage::disk('public') handles it
                    $storagePath = $photoPath;
                    if (str_starts_with($storagePath, 'public/')) {
                        $storagePath = substr($storagePath, 7); // Remove 'public/' prefix
                    } elseif (str_starts_with($storagePath, '/storage/')) {
                        $storagePath = substr($storagePath, 9); // Remove '/storage/' prefix
                    }
                    
                    // Try to read using Storage facade first (more reliable)
                    if (Storage::disk('public')->exists($storagePath)) {
                        $photoContent = Storage::disk('public')->get($storagePath);
                        $mimeType = $photoAttachment->file_type ?? 'image/jpeg';
                        
                        // DocuGenerate expects images in data URI format: data:image/type;base64,base64string
                        // This is the standard format for embedding images in documents
                        $photoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($photoContent);
                        
                        Log::info('Photo found and converted to base64 for Barangay ID (via Storage)', [
                            'doc_request_id' => $documentRequest->doc_request_id,
                            'field_name' => $photoAttachment->field_name,
                            'storage_path' => $storagePath,
                            'original_path' => $photoAttachment->file_path,
                            'mime_type' => $mimeType,
                            'file_size' => strlen($photoContent),
                            'base64_length' => strlen($photoBase64),
                            'data_uri_preview' => substr($photoBase64, 0, 80) . '...',
                        ]);
                    } else {
                        // Fallback to direct file access
                        $directPath = storage_path('app/public/' . $storagePath);
                        if (file_exists($directPath)) {
                            $photoContent = file_get_contents($directPath);
                            $mimeType = $photoAttachment->file_type ?? mime_content_type($directPath) ?? 'image/jpeg';
                            $photoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($photoContent);
                            
                            Log::info('Photo found and converted to base64 for Barangay ID (via direct file access)', [
                                'doc_request_id' => $documentRequest->doc_request_id,
                                'field_name' => $photoAttachment->field_name,
                                'direct_path' => $directPath,
                                'mime_type' => $mimeType,
                                'file_size' => strlen($photoContent),
                            ]);
                        } else {
                            Log::warning('Photo file not found for Barangay ID', [
                                'doc_request_id' => $documentRequest->doc_request_id,
                                'storage_path' => $storagePath,
                                'direct_path' => $directPath,
                                'original_path' => $photoAttachment->file_path,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to convert photo to base64 for Barangay ID', [
                        'doc_request_id' => $documentRequest->doc_request_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::info('No photo attachment found for Barangay ID request', [
                    'doc_request_id' => $documentRequest->doc_request_id,
                    'attachment_count' => $documentRequest->attachments->count(),
                ]);
            }
        }

        // Base data array with standard fields
        $data = [
            // Personal Information
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
            'full_name' => $fullName,
            // Alternative with spaces for templates that use {full name}
            'full name' => $fullName,
            
            // Date Information
            'birthdate' => $birthdateFormatted,
            'birthdate_short' => $birthdateShort,
            'birth_date' => $birthdateFormatted, // For Barangay ID template (matches {birth_date})
            'day_approved' => $dayApproved,
            'month_approved' => $monthApproved,
            // Alternative with spaces for templates that use {day approved}, {month approved}, {year approved}
            'day approved' => $dayApproved,
            'month approved' => $monthApproved,
            'year approved' => $yearApproved,
            'month_approved_numeric' => $monthApprovedNumeric,
            'year_approved' => $yearApproved,
            'date_approved' => $reviewedAt->format('F d, Y'),
            'date_approved_short' => $reviewedAt->format('m/d/Y'),
            
            // Address Information
            'house_number' => $houseNumber,
            'phase' => $phase,
            'package' => $package,
            'full_address' => $fullAddress,
            // Alternative with spaces for templates that use {full address}
            'full address' => $fullAddress,
            
            // ID Information (for Barangay ID template)
            'id_number' => $idNumber,
            
            // Place of Birth (for Barangay ID template)
            'birth_place' => $placeOfBirth,
            
            // Request Information
            'doc_request_ticket' => $documentRequest->doc_request_ticket ?? '',
            'purpose' => $documentRequest->purpose ?? '',
            'reason_type' => $documentRequest->reason_type ?? '',
            
            // Contact Information
            'contact_number' => $documentRequest->contact_number ?? '',
            'email' => $documentRequest->email ?? '',
            
            // Personal Details
            'sex' => $documentRequest->sex ?? '',
            'civil_status' => $documentRequest->civil_status ?? '',
            
            // Approval Information
            'approver_name' => $approverName,
            
            // Pickup Information
            'pickup_location' => $documentRequest->pickup_location ?? '',
            'pickup_item' => $documentRequest->pickup_item ?? '',
            'person_to_look' => $documentRequest->person_to_look ?? '',
            'pickup_start' => $documentRequest->pickup_start ? Carbon::parse($documentRequest->pickup_start)->format('F d, Y h:i A') : '',
            'pickup_end' => $documentRequest->pickup_end ? Carbon::parse($documentRequest->pickup_end)->format('F d, Y h:i A') : '',
        ];
        
        // Add photo for Barangay ID documents
        // DocuGenerate image placeholders can use various names: {photo}, {image}, {picture}, etc.
        // We'll add it with multiple keys to ensure it matches the template placeholder
        if ($photoBase64) {
            $data['photo'] = $photoBase64;
            $data['image'] = $photoBase64; // Alternative placeholder name
            $data['picture'] = $photoBase64; // Another alternative
            Log::info('Photo added to template data for Barangay ID', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'photo_data_uri_length' => strlen($photoBase64),
                'photo_data_uri_preview' => substr($photoBase64, 0, 80) . '...',
                'keys_added' => ['photo', 'image', 'picture'],
            ]);
        } else {
            Log::warning('No photo base64 data available for Barangay ID', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'is_barangay_id' => $isBarangayId,
                'photo_attachment_found' => isset($photoAttachment),
            ]);
        }

        // Add extra_fields dynamically
        foreach ($extraFields as $key => $value) {
            // Convert array values to string (for checkboxes, etc.)
            if (is_array($value)) {
                $value = implode(', ', array_filter($value));
            }
            // Only add non-empty values
            if (!empty($value) || $value === '0' || $value === 0) {
                $data[$key] = (string) $value;
                // Also add with spaces for templates that use {key with spaces}
                $keyWithSpaces = str_replace('_', ' ', $key);
                if ($keyWithSpaces !== $key) {
                    $data[$keyWithSpaces] = (string) $value;
                }
            }
        }
        
        // Add permit-specific fields for unified Business/Building Permit template
        $documentTypeName = $documentRequest->documentType->document_name ?? '';
        $isPermit = stripos($documentTypeName, 'Permit') !== false;
        
        if ($isPermit) {
            // Determine permit type
            $isBuildingPermit = stripos($documentTypeName, 'Building') !== false;
            $permitType = $isBuildingPermit ? 'Building Permit' : 'Business Permit';
            $permitHeader = $isBuildingPermit ? 'BUILDING' : 'BUSINESS';
            
            // Business Permit fields
            $businessName = $extraFields['business_name'] ?? '';
            $businessType = $extraFields['business_type'] ?? '';
            $dtiSecNumber = $extraFields['dtI_sec_number'] ?? $extraFields['dti_sec_number'] ?? '';
            
            // Building Permit fields
            // Extract building_type and building_reg_number from extra_fields
            $buildingType = isset($extraFields['building_type']) ? (string) $extraFields['building_type'] : '';
            $buildingRegNumber = isset($extraFields['building_reg_number']) ? (string) $extraFields['building_reg_number'] : '';
            
            // Debug: log what we found
            Log::info('Building Permit field extraction', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'building_type_found' => $buildingType,
                'building_reg_number_found' => $buildingRegNumber,
                'building_type_raw' => $extraFields['building_type'] ?? 'NOT SET',
                'building_reg_number_raw' => $extraFields['building_reg_number'] ?? 'NOT SET',
                'all_extra_fields' => $extraFields,
                'extra_fields_keys' => array_keys($extraFields),
            ]);
            
            // Add permit header placeholder (for "BARANGAY {permit} PERMIT")
            $data['permit'] = $permitHeader;
            
            // Add permit type (for "{permit_type} Type: {businessorbuilding_type}")
            $data['permit_type'] = $permitType;
            $data['permit type'] = $permitType;
            
            // Add unified type field: {businessorbuilding_type}
            // Uses business_type for Business Permits, building_type for Building Permits
            $businessOrBuildingType = $isBuildingPermit ? $buildingType : $businessType;
            // Always set the type field (convert to string to ensure it's not null)
            $data['businessorbuilding_type'] = (string) ($businessOrBuildingType ?? '');
            $data['businessorbuilding type'] = (string) ($businessOrBuildingType ?? '');
            $data['business_or_building_type'] = (string) ($businessOrBuildingType ?? '');
            
            // Add registration number: {reg_number}
            // For Business Permits: use DTI/SEC number
            // For Building Permits: use building registration number
            $regNumber = $isBuildingPermit ? $buildingRegNumber : $dtiSecNumber;
            // Always set reg_number (convert to string to ensure it's not null)
            $data['reg_number'] = (string) ($regNumber ?? '');
            $data['reg number'] = (string) ($regNumber ?? '');
            $data['registration_number'] = (string) ($regNumber ?? '');
            
            // Also add building_type and building_reg_number directly for template compatibility
            $data['building_type'] = (string) ($buildingType ?? '');
            $data['building type'] = (string) ($buildingType ?? '');
            $data['building_reg_number'] = (string) ($buildingRegNumber ?? '');
            $data['building reg number'] = (string) ($buildingRegNumber ?? '');
            
            // Add business permit fields (for backward compatibility)
            if ($businessName) {
                $data['business_name'] = $businessName;
                $data['business name'] = $businessName;
            }
            if ($businessType) {
                $data['business_type'] = $businessType;
                $data['business type'] = $businessType;
            }
            if ($dtiSecNumber) {
                $data['dti_sec_number'] = $dtiSecNumber;
                $data['dti sec number'] = $dtiSecNumber;
                $data['dti_sec_registration'] = $dtiSecNumber; // Alternative key
            }
            
            // Add building permit fields (for backward compatibility)
            if ($buildingType) {
                $data['building_type'] = $buildingType;
                $data['building type'] = $buildingType;
            }
            
            Log::info('Added permit-specific fields to template data', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'permit_type' => $permitType,
                'permit_header' => $permitHeader,
                'businessorbuilding_type' => $businessOrBuildingType,
                'building_type' => $buildingType,
                'business_type' => $businessType,
                'reg_number' => $regNumber,
                'building_reg_number' => $buildingRegNumber,
                'dti_sec_number' => $dtiSecNumber,
                'has_business_fields' => !empty($businessName) || !empty($businessType),
                'has_building_fields' => !empty($buildingType),
                'extra_fields_keys' => array_keys($extraFields),
            ]);
        }

        // Cedula-specific fields
        $isCedula = stripos($documentTypeName, 'Cedula') !== false;
        if ($isCedula) {
            // Year (current year) - support both {year} and {YEAR}
            $currentYear = $reviewedAt->format('Y');
            $data['year'] = $currentYear;
            $data['Year'] = $currentYear;
            $data['YEAR'] = $currentYear; // Template uses {YEAR}
            
            // Certificate Control Number (CCI format: CCI{YEAR}{cert_number})
            // Generate a certificate number from the document request ticket
            $certNumber = str_replace('DOC-', '', $documentRequest->doc_request_ticket ?? '000');
            $data['cert_number'] = $certNumber;
            $data['cert number'] = $certNumber;
            $data['certificate_number'] = $certNumber;
            $data['CCI'] = "CCI{$currentYear}{$certNumber}";
            
            // Place of Issue (City/Municipality/Province)
            $city = $user->city ?? '';
            $province = $user->province ?? '';
            $placeOfIssue = trim("{$city}, {$province}");
            if (empty($placeOfIssue) || $placeOfIssue === ',') {
                $placeOfIssue = 'City/Municipality, Province'; // Default placeholder
            }
            $data['place_of_issue'] = $placeOfIssue;
            $data['place of issue'] = $placeOfIssue;
            $data['Place of Issue'] = $placeOfIssue;
            $data['place_issued'] = $placeOfIssue; // Template uses {place_issued}
            
            // Income source / Profession/Occupation/Business
            $incomeSource = $extraFields['income_source'] ?? '';
            if (isset($extraFields['occupation'])) {
                $occupation = $extraFields['occupation'];
            } elseif (isset($user->occupation)) {
                $occupation = $user->occupation;
            } else {
                $occupation = $incomeSource; // Fallback to income source
            }
            $data['income_source'] = $incomeSource;
            $data['income source'] = $incomeSource;
            $data['profession_occupation_business'] = $occupation;
            $data['profession occupation business'] = $occupation;
            $data['Profession/Occupation/Business'] = $occupation;
            
            // Date issued (same as date approved)
            $dateIssued = $reviewedAt->format('F d, Y');
            $dateIssuedShort = $reviewedAt->format('m/d/Y');
            $data['date_issued'] = $dateIssued;
            $data['date issued'] = $dateIssued;
            $data['Date Issued'] = $dateIssued;
            $data['approval_date'] = $dateIssued;
            $data['approval date'] = $dateIssued;
            $data['dop'] = $dateIssuedShort; // Date of Payment/Processing
            
            // Citizenship/Nationality
            $nationality = $extraFields['citizenship'] ?? $user->nationality ?? 'Filipino';
            $data['citizenship'] = $nationality;
            $data['Citizenship'] = $nationality;
            $data['nationality'] = $nationality;
            $data['Nationality'] = $nationality;
            
            // Tax Identification Number (TIN) - if available
            $tin = $extraFields['tin'] ?? '';
            $data['tin'] = $tin;
            $data['TIN'] = $tin;
            $data['tax_identification_number'] = $tin;
            
            // Height and Weight (optional - from extra_fields if provided)
            $height = $extraFields['height'] ?? '';
            $weight = $extraFields['weight'] ?? '';
            $data['height'] = $height;
            $data['Height'] = $height;
            $data['weight'] = $weight;
            $data['Weight'] = $weight;
            
            // ===== COMMUNITY TAX COMPUTATION =====
            // Based on BIR Form 0016 structure
            
            // A. BASIC COMMUNITY TAX (P5.00) or VOLUNTARY/EXEMPTED (P1.00)
            $isVoluntaryOrExempted = isset($extraFields['voluntary_exempted']) && $extraFields['voluntary_exempted'] === 'yes';
            $basicTax = $isVoluntaryOrExempted ? 1.00 : 5.00;
            $data['comm_tax_due1'] = number_format($basicTax, 2, '.', '');
            $data['comm tax due1'] = $data['comm_tax_due1'];
            
            // B. ADDITIONAL COMMUNITY TAX
            // 1. Gross receipts/earnings from BUSINESS during preceding year
            $businessGrossReceipts = isset($extraFields['business_gross_receipts']) && is_numeric($extraFields['business_gross_receipts']) 
                ? (float) $extraFields['business_gross_receipts'] 
                : 0.00;
            $taxable1 = $businessGrossReceipts;
            $commTaxDue2 = floor($taxable1 / 1000) * 1.00; // P1.00 for every P1,000
            $data['taxable_1'] = number_format($taxable1, 2, '.', '');
            $data['taxable 1'] = $data['taxable_1'];
            $data['comm_tax_due2'] = number_format($commTaxDue2, 2, '.', '');
            $data['comm tax due2'] = $data['comm_tax_due2'];
            
            // 2. Salaries/gross receipts from PROFESSION/OCCUPATION
            // Use annual_income field from the form
            $salaryOrProfessionEarnings = isset($extraFields['annual_income']) && is_numeric($extraFields['annual_income'])
                ? (float) $extraFields['annual_income']
                : 0.00;
            $taxable2 = $salaryOrProfessionEarnings;
            $commTaxDue3 = floor($taxable2 / 1000) * 1.00; // P1.00 for every P1,000
            $data['taxable_2'] = number_format($taxable2, 2, '.', '');
            $data['taxable 2'] = $data['taxable_2'];
            $data['comm_tax_due3'] = number_format($commTaxDue3, 2, '.', '');
            $data['comm tax due3'] = $data['comm_tax_due3'];
            
            // 3. Income from REAL PROPERTY
            $realPropertyIncome = isset($extraFields['real_property_income']) && is_numeric($extraFields['real_property_income'])
                ? (float) $extraFields['real_property_income']
                : 0.00;
            $taxable3 = $realPropertyIncome;
            $commTaxDue4 = floor($taxable3 / 1000) * 1.00; // P1.00 for every P1,000
            $data['taxable_3'] = number_format($taxable3, 2, '.', '');
            $data['taxable 3'] = $data['taxable_3'];
            $data['comm_tax_due4'] = number_format($commTaxDue4, 2, '.', '');
            $data['comm tax due4'] = $data['comm_tax_due4'];
            
            // TOTAL COMMUNITY TAX DUE
            $totalTaxDue = $basicTax + $commTaxDue2 + $commTaxDue3 + $commTaxDue4;
            // Cap at P5,000 maximum as per BIR regulations
            if ($totalTaxDue > 5000.00) {
                $totalTaxDue = 5000.00;
            }
            $data['total'] = number_format($totalTaxDue, 2, '.', '');
            $data['Total'] = $data['total'];
            $data['total_community_tax_due'] = $data['total'];
            $data['total community tax due'] = $data['total'];
            
            // INTEREST (if late payment - typically 25% per year, but can be customized)
            $interest = isset($extraFields['interest']) && is_numeric($extraFields['interest'])
                ? (float) $extraFields['interest']
                : 0.00;
            $data['interest'] = number_format($interest, 2, '.', '');
            $data['Interest'] = $data['interest'];
            
            // TOTAL AMOUNT PAID
            $totalAmountPaid = $totalTaxDue + $interest;
            $data['amount_paid'] = number_format($totalAmountPaid, 2, '.', '');
            $data['amount paid'] = $data['amount_paid'];
            $data['Total Amount Paid'] = $data['amount_paid'];
            
            // Amount in words (for "(IN WORDS)" field)
            $amountInWords = $this->numberToWords($totalAmountPaid);
            $data['amount_in_words'] = $amountInWords;
            $data['amount in words'] = $amountInWords;
            $data['Amount in Words'] = $amountInWords;
            
            Log::info('Added Cedula-specific fields and tax computation to template data', [
                'doc_request_id' => $documentRequest->doc_request_id,
                'year' => $currentYear,
                'cert_number' => $certNumber,
                'basic_tax' => $basicTax,
                'business_gross_receipts' => $businessGrossReceipts,
                'salary_profession_earnings' => $salaryOrProfessionEarnings,
                'real_property_income' => $realPropertyIncome,
                'total_tax_due' => $totalTaxDue,
                'interest' => $interest,
                'total_amount_paid' => $totalAmountPaid,
            ]);
        }

        // Common extra field mappings (based on your document fields)
        // Duration of residency - handle both underscore and space formats
        if (isset($extraFields['duration_of_residency'])) {
            $durationValue = $extraFields['duration_of_residency'];
            $data['duration_of_residency'] = $durationValue;
            // Also add with spaces for templates that use [Duration of Residency]
            $data['Duration of Residency'] = $durationValue;
        }
        
        // Clearance for (Barangay Clearance)
        if (isset($extraFields['clearance_for'])) {
            $data['clearance_for'] = $extraFields['clearance_for'];
        }

        // Business Permit fields
        if (isset($extraFields['business_type'])) {
            $data['business_type'] = $extraFields['business_type'];
            // Also add with spaces for templates that use [Business Type]
            $data['Business Type'] = $extraFields['business_type'];
        }
        
        if (isset($extraFields['dtI_sec_number'])) {
            $data['dtI_sec_number'] = $extraFields['dtI_sec_number'];
            // Also add cleaner versions
            $data['dti_sec_number'] = $extraFields['dtI_sec_number'];
            $data['DTI_SEC_Number'] = $extraFields['dtI_sec_number'];
            $data['DTI/SEC Registration Number'] = $extraFields['dtI_sec_number'];
        }
        
        if (isset($extraFields['business_name'])) {
            $data['business_name'] = $extraFields['business_name'];
            $data['Business Name'] = $extraFields['business_name'];
        }

        return $data;
    }

    /**
     * Convert number to words (for amount in words field)
     * 
     * @param float $number
     * @return string
     */
    protected function numberToWords(float $number): string
    {
        $whole = (int) floor($number);
        $fraction = (int) round(($number - $whole) * 100);
        
        $words = $this->convertNumberToWords($whole);
        
        if ($fraction > 0) {
            $words .= ' and ' . $this->convertNumberToWords($fraction) . ' centavos';
        }
        
        return ucfirst($words) . ' pesos' . ($fraction > 0 ? '' : ' only');
    }

    /**
     * Convert integer to words
     * 
     * @param int $number
     * @return string
     */
    protected function convertNumberToWords(int $number): string
    {
        if ($number == 0) {
            return 'zero';
        }
        
        $ones = [
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        ];
        
        $tens = [
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        ];
        
        if ($number < 20) {
            return $ones[$number];
        }
        
        if ($number < 100) {
            $ten = (int) floor($number / 10);
            $one = $number % 10;
            return $tens[$ten] . ($one > 0 ? '-' . $ones[$one] : '');
        }
        
        if ($number < 1000) {
            $hundred = (int) floor($number / 100);
            $remainder = $number % 100;
            $result = $ones[$hundred] . ' hundred';
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberToWords($remainder);
            }
            return $result;
        }
        
        if ($number < 1000000) {
            $thousand = (int) floor($number / 1000);
            $remainder = $number % 1000;
            $result = $this->convertNumberToWords($thousand) . ' thousand';
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberToWords($remainder);
            }
            return $result;
        }
        
        if ($number < 1000000000) {
            $million = (int) floor($number / 1000000);
            $remainder = $number % 1000000;
            $result = $this->convertNumberToWords($million) . ' million';
            if ($remainder > 0) {
                $result .= ' ' . $this->convertNumberToWords($remainder);
            }
            return $result;
        }
        
        return 'number too large';
    }

    /**
     * Generate file name based on ticket number and document type
     */
    public function generateFileName(DocumentRequest $documentRequest, string $extension): string
    {
        $ticketNumber = $documentRequest->doc_request_ticket ?? 'UNKNOWN';
        $documentType = $documentRequest->documentType->document_name ?? 'Document';
        $documentTypeSanitized = $this->sanitizeFileName($documentType);
        
        return "{$ticketNumber}_{$documentTypeSanitized}.{$extension}";
    }

    /**
     * Sanitize file name
     */
    protected function sanitizeFileName(string $fileName): string
    {
        // Remove or replace invalid characters
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
        $fileName = preg_replace('/_+/', '_', $fileName); // Replace multiple underscores with single
        $fileName = trim($fileName, '_');
        
        return $fileName;
    }

    /**
     * Get generated document path
     */
    public function getDocumentPath(DocumentRequest $documentRequest, string $format = 'pdf'): ?string
    {
        $fileName = $this->generateFileName($documentRequest, $format);
        $filePath = $this->outputPath . '/' . $fileName;
        
        return file_exists($filePath) ? $filePath : null;
    }

    /**
     * List all templates from DocuGenerate
     * 
     * @return array|null
     */
    public function listTemplates(): ?array
    {
        if (!$this->apiKey) {
            throw new \Exception('DocuGenerate API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->apiUrl}/template");

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to list templates from DocuGenerate', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while listing templates from DocuGenerate', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete a template from DocuGenerate
     * 
     * @param string $templateId
     * @return bool
     */
    public function deleteTemplate(string $templateId): bool
    {
        if (!$this->apiKey) {
            throw new \Exception('DocuGenerate API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->delete("{$this->apiUrl}/template/{$templateId}");

            if ($response->successful()) {
                Log::info('Template deleted from DocuGenerate', [
                    'template_id' => $templateId,
                ]);
                return true;
            } else {
                Log::error('Failed to delete template from DocuGenerate', [
                    'template_id' => $templateId,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while deleting template from DocuGenerate', [
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
