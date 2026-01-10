<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentGenerationService;

class ListDocuGenerateTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docugenerate:list-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all templates in DocuGenerate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching templates from DocuGenerate...');

        $service = new DocumentGenerationService();
        
        // Check if API key is configured
        $config = config('services.docugenerate');
        if (empty($config['api_key'])) {
            $this->error('DocuGenerate API key not configured. Please set DOCUGENERATE_API_KEY in your .env file.');
            return 1;
        }

        try {
            $templates = $service->listTemplates();
            
            if ($templates === null) {
                $this->error('Failed to fetch templates. Check logs for details.');
                return 1;
            }

            if (empty($templates)) {
                $this->info('No templates found in DocuGenerate.');
                return 0;
            }

            $this->newLine();
            $this->info('Templates in DocuGenerate:');
            $this->newLine();

            $tableData = [];
            foreach ($templates as $template) {
                $tableData[] = [
                    'ID' => $template['id'] ?? 'N/A',
                    'Name' => $template['name'] ?? 'N/A',
                    'Format' => $template['format'] ?? 'N/A',
                    'Pages' => $template['page_count'] ?? 'N/A',
                    'Created' => isset($template['created']) ? date('Y-m-d H:i:s', $template['created'] / 1000) : 'N/A',
                ];
            }

            $this->table(['ID', 'Name', 'Format', 'Pages', 'Created'], $tableData);
            $this->newLine();
            $this->info('Total templates: ' . count($templates));

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
