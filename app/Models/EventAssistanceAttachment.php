<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAssistanceAttachment extends Model
{
    use HasFactory;

    protected $table = 'event_assistance_attachments';
    protected $primaryKey = 'attachment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fk_event_assist_request_id',
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
     * Accessor: Get the full URL for the file
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        // If it already has /storage/, return as is
        if (str_starts_with($this->file_path, '/storage/')) {
            return $this->file_path;
        }

        // Remove 'public/' prefix if present
        $cleanPath = str_starts_with($this->file_path, 'public/') 
            ? substr($this->file_path, 7) 
            : $this->file_path;

        return '/storage/' . ltrim($cleanPath, '/');
    }

    /**
     * Relationship: the event assistance request this attachment belongs to
     */
    public function eventAssistanceRequest(): BelongsTo
    {
        return $this->belongsTo(EventAssistanceRequest::class, 'fk_event_assist_request_id', 'event_assist_request_id');
    }
}
