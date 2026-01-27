<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTypeRestriction extends Model
{
    protected $table = 'document_type_restrictions';
    protected $primaryKey = 'restriction_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fk_document_type_id',
        'required_offense_severity',
    ];

    public $timestamps = true;

    /**
     * Get the document type this restriction applies to
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'fk_document_type_id', 'document_type_id');
    }
}
