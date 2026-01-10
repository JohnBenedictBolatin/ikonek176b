<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceItem extends Model
{
    protected $table = 'event_assistance_items';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = ['item_name', 'category'];
}
