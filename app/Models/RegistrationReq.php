<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationReq extends Model
{
    // Table name if it doesn't follow convention (not 'registration_reqs')
    protected $table = 'registration_req';

    // Primary key as in your table
    protected $primaryKey = 'registration_req_id';

    // Eloquent won't maintain updated_at automatically if you set timestamps manually,
    // but we included updated_at in migration so keep timestamps true:
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'contact_number',
        'secondary_contact_number',
        'phase',
        'package',
        'password',
        'sex',
        'birthdate',
        'civil_status',
        'fk_role_id',
        'address',
        // 'barangay',        
        // 'proof_of_residency',
        'registration_status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Hide hashed password when serializing
    protected $hidden = [
        'password',
    ];
}
