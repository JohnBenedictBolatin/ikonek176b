<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentType;
use App\Services\DocumentGenerationService;

class UploadTemplatesToDocuGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docugenerate:upload-templates 
                            {--force : Force upload even if template_id already exists}
                            {--type= : Upload template for specific document type only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload document templates to DocuGenerate API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting template upload to DocuGenerate...');

        $service = new DocumentGenerationService();
        
        // Check if API key is configured
        $config = config('services.docugenerate');
        if (empty($config['api_key'])) {
            $this->error('DocuGenerate API key not configured. Please set DOCUGENERATE_API_KEY in your .env file.');
            return 1;
        }

        $documentTypeName = $this->option('type');
        $force = $this->option('force');

        // Get document types to process
        if ($documentTypeName) {
            $documentTypes = DocumentType::where('document_name', $documentTypeName)->get();
            if ($documentTypes->isEmpty()) {
                $this->error("Document type '{$documentTypeName}' not found.");
                return 1;
            }
        } else {
            $documentTypes = DocumentType::all();
        }

        if ($documentTypes->isEmpty()) {
            $this->warn('No document types found.');
            return 0;
        }

        $this->info("Found {$documentTypes->count()} document type(s) to process.");
        $this->newLine();

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($documentTypes as $documentType) {
            $this->line("Processing: {$documentType->document_name}");

            // Skip if template_id already exists and not forcing
            if ($documentType->template_id && !$force) {
                $this->warn("  → Template already uploaded (ID: {$documentType->template_id}). Use --force to re-upload.");
                $skipCount++;
                continue;
            }

            // Find local template file
            $templatePath = $this->findLocalTemplate($documentType->document_name);
            
            if (!$templatePath) {
                $this->error("  → Local template file not found for '{$documentType->document_name}'");
                $errorCount++;
                continue;
            }

            $this->line("  → Found template: " . basename($templatePath));

            // Upload template
            try {
                $templateId = $service->uploadTemplate($templatePath, $documentType->document_name);
                
                if ($templateId) {
                    $documentType->template_id = $templateId;
                    $documentType->save();
                    
                    $this->info("  ✓ Uploaded successfully! Template ID: {$templateId}");
                    $successCount++;
                } else {
                    $this->error("  → Upload failed. Check logs for details.");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("  → Error: " . $e->getMessage());
                $errorCount++;
            }

            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('Upload Summary:');
        $this->line("  Success: {$successCount}");
        $this->line("  Skipped: {$skipCount}");
        $this->line("  Errors:  {$errorCount}");

        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Find local template file
     */
    protected function findLocalTemplate(string $documentTypeName): ?string
    {
        $templatePath = storage_path('app/templates/document_templates');
        
        $possibleNames = [
            $documentTypeName . '.docx',
            $this->sanitizeFileName($documentTypeName) . '.docx',
        ];

        foreach ($possibleNames as $possibleName) {
            $possiblePath = $templatePath . '/' . $possibleName;
            if (file_exists($possiblePath)) {
                return $possiblePath;
            }
        }

        return null;
    }

    /**
     * Sanitize file name
     */
    protected function sanitizeFileName(string $fileName): string
    {
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
        $fileName = preg_replace('/_+/', '_', $fileName);
        $fileName = trim($fileName, '_');
        
        return $fileName;
    }
}
