<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    protected $table = 'document_request_uploads';
    protected $primaryKey = 'doc_request_upload_id';

    public $incrementing = true;
    protected $keyType = 'int';

    // Table has a single timestamp column `uploaded_at`
    public $timestamps = false;

    protected $fillable = [
        'fk_doc_request_id',
        'fk_requirement_type_id',
        'file_content',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Relationship: the document request this upload belongs to
     */
    public function documentRequest()
    {
        return $this->belongsTo(\App\Models\DocumentRequest::class, 'fk_doc_request_id', 'doc_request_id');
    }

    /**
     * Relationship: the requirement type this upload fulfills
     */
    public function requirementType()
    {
        return $this->belongsTo(\App\Models\DocumentRequirement::class, 'fk_requirement_type_id', 'doc_requirement_type_id');
    }
}
