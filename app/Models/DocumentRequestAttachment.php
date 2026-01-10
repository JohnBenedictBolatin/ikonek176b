<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequestAttachment extends Model
{
    use HasFactory;

    protected $table = 'document_request_attachments';
    protected $primaryKey = 'attachment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fk_doc_request_id',
        'field_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url'];

    /**
     * Relationship: the document request this attachment belongs to
     */
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class, 'fk_doc_request_id', 'doc_request_id');
    }

    /**
     * Accessor: Get the full URL for the attachment file
     * Works consistently like profile pictures
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        $path = $this->file_path;
        
        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // If it already has /storage/, return as is
        if (str_starts_with($path, '/storage/')) {
            return $path;
        }
        
        // Remove 'public/' prefix if present (Laravel storage sometimes includes it)
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        
        // Prepend /storage/ to make it accessible
        return '/storage/' . ltrim($path, '/');
    }
}
