<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentOffense extends Model
{
    protected $table = 'resident_offenses';
    protected $primaryKey = 'offense_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fk_resident_id',
        'offense_type',
        'severity_level',
        'offense_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'offense_date' => 'date',
    ];

    public $timestamps = true;

    /**
     * Get the resident who committed this offense
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'fk_resident_id', 'resident_id');
    }
}
