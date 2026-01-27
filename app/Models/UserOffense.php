<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOffense extends Model
{
    protected $table = 'user_offenses';
    protected $primaryKey = 'user_offense_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fk_user_id',
        'fk_resident_offense_id',
        'offense_type',
        'severity_level',
        'offense_date',
        'status',
    ];

    protected $casts = [
        'offense_date' => 'date',
    ];

    public $timestamps = true;

    /**
     * Get the user who has this offense
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'user_id');
    }

    /**
     * Get the original resident offense this was copied from
     */
    public function residentOffense(): BelongsTo
    {
        return $this->belongsTo(ResidentOffense::class, 'fk_resident_offense_id', 'offense_id');
    }
}
