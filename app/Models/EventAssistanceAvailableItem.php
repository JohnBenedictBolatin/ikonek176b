<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceAvailableItem extends Model
{
    protected $table = 'event_assistance_available_items';
    protected $primaryKey = 'event_assist_item_id';

    // Table only has created_at (no updated_at)
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'item_name',
        'available_quantity',
        'created_at',
    ];

    protected $casts = [
        'available_quantity' => 'integer',
        'created_at'         => 'datetime',
    ];

    /**
     * Relationship:
     * This available item corresponds to the items requested in EventAssistanceDetail
     * (if you want this).
     */
    public function requestedDetails()
    {
        return $this->hasMany(EventAssistanceDetail::class, 'fk_item_id', 'event_assist_item_id');
    }
}
