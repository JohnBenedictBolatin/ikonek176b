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
        'sex',
        'birthdate',
        'civil_status',
        'address',
        'profile_description',
        'fk_role_id',
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

    // make sure belongsTo uses fk_role_id (local) -> role_id (owner on RoleCategory)
    public function roleCategory()
    {
        return $this->belongsTo(\App\Models\RoleCategory::class, 'fk_role_id', 'role_id');
    }
}
