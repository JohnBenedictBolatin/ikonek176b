<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use App\Models\DocumentTypeRestriction;

class DocumentTypeRestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Sets restrictions for document types that require clean records.
     * Barangay Certificate and Business/Building Permit require no Medium/High offenses.
     */
    public function run(): void
    {
        // Document types that require no Medium/High offenses
        $restrictedDocuments = [
            'Barangay Certificate',
            'Business Permit',
            'Building Permit',
        ];

        foreach ($restrictedDocuments as $docName) {
            $docType = DocumentType::where('document_name', $docName)->first();
            
            if ($docType) {
                // Set restriction to 'Medium' - meaning no Medium or High offenses allowed
                DocumentTypeRestriction::updateOrCreate(
                    ['fk_document_type_id' => $docType->document_type_id],
                    ['required_offense_severity' => 'Medium'] // Requires no Medium/High offenses
                );
            } else {
                $this->command->warn("Document type '{$docName}' not found. Skipping restriction.");
            }
        }

        $this->command->info('Document type restrictions seeded successfully.');
    }
}
