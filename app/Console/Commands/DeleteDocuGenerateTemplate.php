<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentGenerationService;
use App\Models\DocumentType;

class DeleteDocuGenerateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docugenerate:delete-template 
                            {template_id : The template ID to delete}
                            {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a template from DocuGenerate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $templateId = $this->argument('template_id');
        $force = $this->option('force');

        $service = new DocumentGenerationService();
        
        // Check if API key is configured
        $config = config('services.docugenerate');
        if (empty($config['api_key'])) {
            $this->error('DocuGenerate API key not configured. Please set DOCUGENERATE_API_KEY in your .env file.');
            return 1;
        }

        if (!$force) {
            if (!$this->confirm("Are you sure you want to delete template '{$templateId}'?")) {
                $this->info('Deletion cancelled.');
                return 0;
            }
        }

        try {
            $this->info("Deleting template '{$templateId}'...");
            
            $success = $service->deleteTemplate($templateId);
            
            if ($success) {
                $this->info("Template '{$templateId}' deleted successfully.");
                
                // Also clear template_id from database if it exists
                $documentType = DocumentType::where('template_id', $templateId)->first();
                if ($documentType) {
                    $documentType->template_id = null;
                    $documentType->save();
                    $this->info("Cleared template_id from document type: {$documentType->document_name}");
                }
                
                return 0;
            } else {
                $this->error("Failed to delete template '{$templateId}'. Check logs for details.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
