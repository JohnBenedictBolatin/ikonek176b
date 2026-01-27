<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'house_number',
        'street',
        'phase',
        'package',
        'barangay',
        'city',
        'province',
        'sex',
        'birthdate',
        'place_of_birth',
        'religion',
        'nationality',
        'occupation',
        'civil_status',
        'fk_role_id',
        'profile_description',
        'profile_pic',
        'created_at',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'created_at' => 'datetime',
        'phase' => 'string',
        'package' => 'string',
    ];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        $fullName = trim(
            ($this->first_name ?? '') . ' ' .
            ($this->last_name ?? '')
        );

        return preg_replace('/\s+/', ' ', $fullName);
    }

    // role relationship
    public function roleCategory()
    {
        return $this->belongsTo(\App\Models\RoleCategory::class, 'fk_role_id', 'role_id');
    }

    // link to credentials table
    public function credential()
    {
        return $this->hasOne(\App\Models\UserCredential::class, 'fk_user_id', 'user_id');
    }

    // Mutators to ensure phase and package are always strings for MySQL ENUM
    public function setPhaseAttribute($value)
    {
        $this->attributes['phase'] = $value !== null ? (string)$value : null;
    }

    public function setPackageAttribute($value)
    {
        $this->attributes['package'] = $value !== null ? (string)$value : null;
    }
}
