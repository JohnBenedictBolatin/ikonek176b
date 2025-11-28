<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class users extends Model
{
    use HasFactory;

    // <-- disable automatic created_at / updated_at handling
    public $timestamps = false;
    protected $fillable = [
    'role_id', 'last_name', 'first_name', 'middle_name', 'suffix',
    'birthdate', 'sex', 'civil_status', 'contact_number', 'secondary_number',
    'password', 'home_address', 'barangay', //'phase', 'package',
    'proof_of_residency', 'profile_description', 'date_joined',
    ];

    public function getFullNameAttribute()
    {
        return trim(($this->fName ?? '') . ' ' . ($this->lName ?? ''));
    }
}
