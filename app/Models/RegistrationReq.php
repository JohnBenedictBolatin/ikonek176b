<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationReq extends Model
{
    protected $table = 'registration_requests';

    protected $primaryKey = 'registration_request_id';

    // Table has created_at column but not necessarily updated_at — controller will set created_at explicitly
    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'contact_number',
        'secondary_contact_number',
        'email',
        'password',
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
        'created_at',
        'proof',
        'proof_of_residency',
        'registration_status',
        'offense_type',
        'admin_feedback',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'birthdate' => 'date',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relationship to the User model (created when request is approved)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
