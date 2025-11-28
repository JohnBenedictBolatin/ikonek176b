<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceRequest extends Model
{
    protected $table = 'event_assistance_requests';
    protected $primaryKey = 'eventassist_request_id';
    public $timestamps = false; // if your table manages timestamps manually

    protected $fillable = [
        'eventassist_request_ticket',
        'fk_user_id',
        'purpose',
        'others',
        'location',
        'status',
        'fk_approver_id',
        'start_datetime',
        'end_datetime',
        'created_at',
        'reviewed_at',
    ];

    public function details()
    {
        return $this->hasMany(EventAssistanceDetail::class, 'fk_eventassist_request_id', 'eventassist_request_id');
    }
}
