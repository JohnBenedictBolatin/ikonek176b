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
            // Load document type
            $documentType = $documentRequest->documentType;
            if (!$documentType) {
                throw new \Exception('Document type not found for this request');
            }

            $documentTypeName = $documentType->document_name;
            
            // Get or upload template to DocuGenerate
            $templateId = $this->ensureTemplateUploaded($documentType);
            
            if (!$templateId) {
                throw new \Exception("Template not available for '{$documentTypeName}'. Please ensure template is uploaded to DocuGenerate.");
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
        // If template_id already exists, return it
        if ($documentType->template_id) {
            // Verify template still exists in DocuGenerate
            if ($this->verifyTemplateExists($documentType->template_id)) {
                return $documentType->template_id;
            } else {
                // Template was deleted from DocuGenerate, clear the ID
                $documentType->template_id = null;
                $documentType->save();
            }
        }

        // Find local template file
        $templatePath = $this->findLocalTemplate($documentType->document_name);
        
        if (!$templatePath) {
            Log::warning("Local template not found for document type: {$documentType->document_name}");
            return null;
        }

        // Upload template to DocuGenerate
        $templateId = $this->uploadTemplate($templatePath, $documentType->document_name);
        
        if ($templateId) {
            // Save template_id to database
            $documentType->template_id = $templateId;
            $documentType->save();
            
            Log::info("Template uploaded to DocuGenerate", [
                'document_type' => $documentType->document_name,
                'template_id' => $templateId,
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
        $possibleNames = [
            $documentTypeName . '.docx',
            $documentTypeName . '.pdf',
            $this->sanitizeFileName($documentTypeName) . '.docx',
            $this->sanitizeFileName($documentTypeName) . '.pdf',
        ];

        foreach ($possibleNames as $possibleName) {
            $possiblePath = $this->templatePath . '/' . $possibleName;
            if (file_exists($possiblePath)) {
                // DocuGenerate only supports DOCX templates
                if (str_ends_with(strtolower($possibleName), '.docx')) {
                    return $possiblePath;
                } else {
                    Log::warning("PDF template found but DocuGenerate requires DOCX: {$possibleName}");
                }
            }
        }

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

        // Build full name
        $firstName = $documentRequest->first_name ?? '';
        $middleName = $documentRequest->middle_name ?? '';
        $lastName = $documentRequest->last_name ?? '';
        $suffix = $documentRequest->suffix ?? '';
        
        $fullName = trim("{$firstName} {$middleName} {$lastName} {$suffix}");

        // Format birthdate
        $birthdate = $documentRequest->birthdate;
        $birthdateFormatted = $birthdate ? Carbon::parse($birthdate)->format('F d, Y') : '';
        $birthdateShort = $birthdate ? Carbon::parse($birthdate)->format('m/d/Y') : '';

        // Get address components
        $houseNumber = $documentRequest->house_number ?? '';
        $phase = $documentRequest->phase ?? '';
        $package = $documentRequest->package ?? '';
        $fullAddress = trim("{$houseNumber} {$phase} {$package}");

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

        // Base data array with standard fields
        $data = [
            // Personal Information
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
            'full_name' => $fullName,
            
            // Date Information
            'birthdate' => $birthdateFormatted,
            'birthdate_short' => $birthdateShort,
            'day_approved' => $dayApproved,
            'month_approved' => $monthApproved,
            'month_approved_numeric' => $monthApprovedNumeric,
            'year_approved' => $yearApproved,
            'date_approved' => $reviewedAt->format('F d, Y'),
            'date_approved_short' => $reviewedAt->format('m/d/Y'),
            
            // Address Information
            'house_number' => $houseNumber,
            'phase' => $phase,
            'package' => $package,
            'full_address' => $fullAddress,
            
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

        // Add extra_fields dynamically
        foreach ($extraFields as $key => $value) {
            // Convert array values to string (for checkboxes, etc.)
            if (is_array($value)) {
                $value = implode(', ', array_filter($value));
            }
            // Only add non-empty values
            if (!empty($value) || $value === '0' || $value === 0) {
                $data[$key] = (string) $value;
            }
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
