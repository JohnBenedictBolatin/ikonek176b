<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentItem extends Model
{
    protected $table = 'document_required_items';
    protected $primaryKey = 'required_item_id';

    public $incrementing = true;
    protected $keyType = 'int';

    // No timestamps in this table
    public $timestamps = false;

    protected $fillable = [
        'fk_document_type_id',
        'fk_requirement_type_id',
        'applies_to',
    ];

    /**
     * Relationship: the document type this required item belongs to
     */
    public function documentType()
    {
        return $this->belongsTo(\App\Models\DocumentType::class, 'fk_document_type_id', 'document_type_id');
    }

    /**
     * Relationship: the requirement type this item belongs to
     */
    public function requirementType()
    {
        return $this->belongsTo(\App\Models\DocumentRequirement::class, 'fk_requirement_type_id', 'doc_requirement_type_id');
    }
}
