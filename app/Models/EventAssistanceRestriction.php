<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAssistanceRestriction extends Model
{
    protected $table = 'event_assistance_restrictions';
    protected $primaryKey = 'restriction_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'event_type',
        'offense_type',
        'severity_level',
    ];

    public $timestamps = true;
}
