<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCredential extends Model
{
    protected $table = 'admin_credentials';

    // primary key is user_cred_id
    protected $primaryKey = 'admin_cred_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false; // if created_at/updated_at exist; set to false if they don't


    protected $fillable = [
    'fk_user_id',
    'username',
    'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'fk_user_id', 'user_id');
        // ->belongsTo(User::class) would also work if you follow Laravel conventions:
        // (fk_user_id -> id)
    }
}
