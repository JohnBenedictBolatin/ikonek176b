<?php

namespace App\Console\Commands;

use App\Models\DocumentType;
use Illuminate\Console\Command;

class SetCedulaTemplateId extends Command
{
    protected $signature = 'cedula:set-template-id {template_id}';
    protected $description = 'Manually set the template_id for Cedula document type';

    public function handle()
    {
        $templateId = $this->argument('template_id');
        
        $cedula = DocumentType::where('document_name', 'Cedula')->first();
        
        if (!$cedula) {
            $this->error('Cedula document type not found in database.');
            return 1;
        }
        
        $cedula->template_id = $templateId;
        $cedula->save();
        
        $this->info("Successfully set template_id for Cedula: {$templateId}");
        $this->info("Cedula document generation should now work.");
        
        return 0;
    }
}

