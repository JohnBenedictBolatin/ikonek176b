<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserCredential extends Model
{
    //  use HasFactory;

    protected $table = 'user_credentials';

    // primary key is user_cred_id
    protected $primaryKey = 'user_cred_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false; // if created_at/updated_at exist; set to false if they don't


    protected $fillable = [
    'fk_user_id',
    'contact_number',
    'secondary_contact_number',
    'email',
    'password',
    ];

    // hide password if you return models in JSON
    // protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'user_id');
    }
}