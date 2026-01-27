<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRestriction extends Model
{
    protected $table = 'user_restrictions';
    
    protected $fillable = [
        'user_id',
        'restrict_posting',
        'restrict_commenting',
        'restrict_document_request',
        'restrict_event_assistance_request',
        'restricted_document_types',
        'allowed_document_types',
        'restricted_event_types',
        'allowed_event_types',
        'restriction_reason',
        'restricted_by',
    ];

    protected $casts = [
        'restrict_posting' => 'boolean',
        'restrict_commenting' => 'boolean',
        'restrict_document_request' => 'boolean',
        'restrict_event_assistance_request' => 'boolean',
        'restricted_document_types' => 'array',
        'allowed_document_types' => 'array',
        'restricted_event_types' => 'array',
        'allowed_event_types' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function restrictedBy()
    {
        return $this->belongsTo(User::class, 'restricted_by', 'user_id');
    }
}
