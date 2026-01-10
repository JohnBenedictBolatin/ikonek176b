<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceRequest extends Model
{
    protected $table = 'event_assistance_requests';
    protected $primaryKey = 'event_assist_request_id';
    public $timestamps = false; // your table manages created_at manually

    protected $fillable = [
        'event_assist_request_ticket',
        'fk_user_id',
        'fk_approver_id',
        'event_date',
        'event_start',
        'event_end',
        'expected_attendees',
        'purpose',
        'other_purpose',
        'event_location',
        'fk_valid_id_type',
        'valid_id_content',
        'status',
        'admin_feedback',
        'reviewed_at',
        'created_at',
        'extra_fields',
    ];

    protected $casts = [
        'event_assist_request_id' => 'integer',
        'fk_user_id' => 'integer',
        'fk_approver_id' => 'integer',
        'event_date' => 'date',
        'event_start' => 'datetime', // or 'time' if you store time only
        'event_end' => 'datetime',   // or 'time'
        'expected_attendees' => 'integer',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'extra_fields' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(
            EventAssistanceRequestItem::class, // new model class below
            'fk_event_assist_request_id',      // foreign key on items table
            'event_assist_request_id'          // local primary key on this table
        );
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_user_id', 'user_id');
    }

    /**
     * Approver relationship (missing earlier)
     * Assumes approver is also a user record referenced by fk_approver_id
     */
    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_approver_id', 'user_id');
    }

    public function requestItems()
    {
        return $this->hasMany(EventAssistanceAvailableItem::class, 'event_assist_item_id');
    }

    /**
     * Relationship: attachments for this event assistance request
     */
    public function attachments()
    {
        return $this->hasMany(EventAssistanceAttachment::class, 'fk_event_assist_request_id', 'event_assist_request_id');
    }
}
