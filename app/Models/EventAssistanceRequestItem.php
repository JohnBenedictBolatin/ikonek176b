<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceRequestItem extends Model
{
    protected $table = 'event_assistance_request_items';
    protected $primaryKey = 'event_assist_request_item_id';

    // You have `created_at` only — disable automatic `updated_at`.
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';
    public $timestamps = true; // will only maintain created_at; updated_at is null via UPDATED_AT

    protected $fillable = [
        'fk_event_assist_request_id',
        'fk_event_assist_item_id',
        'quantity',
        'created_at',
    ];

    protected $casts = [
        'event_assist_request_item_id' => 'integer',
        'fk_event_assist_request_id' => 'integer',
        'fk_event_assist_item_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Belongs to the parent request
     */
    public function request()
    {
        return $this->belongsTo(EventAssistanceRequest::class, 'fk_event_assist_request_id', 'event_assist_request_id');
    }

    /**
     * (Optional) If you have a separate items master table referenced by fk_event_assist_item_id,
     * create relation here. Example:
     *
     * public function item()
     * {
     *     return $this->belongsTo(EventAssistanceItem::class, 'fk_event_assist_item_id', 'id');
     * }
     */
}
