<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    protected $table = 'residents';
    protected $primaryKey = 'resident_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthdate',
        'gender',
        'fk_role_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public $timestamps = true;

    /**
     * Get all offenses for this resident
     */
    public function offenses(): HasMany
    {
        return $this->hasMany(ResidentOffense::class, 'fk_resident_id', 'resident_id');
    }

    /**
     * Get active offenses only
     */
    public function activeOffenses(): HasMany
    {
        return $this->hasMany(ResidentOffense::class, 'fk_resident_id', 'resident_id')
                    ->where('status', 'Active');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]);
        return implode(' ', $parts);
    }
}
