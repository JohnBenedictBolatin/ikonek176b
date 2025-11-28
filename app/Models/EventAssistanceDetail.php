<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceDetail extends Model
{
    protected $table = 'event_assistance_details';
    protected $primaryKey = 'eventassist_detail_id';
    public $timestamps = false;

    protected $fillable = [
        'fk_eventassist_request_id',
        'fk_item_id',
        'quantity'
    ];

    public function item()
    {
        return $this->belongsTo(EventAssistanceItem::class, 'fk_item_id', 'item_id');
    }
}
