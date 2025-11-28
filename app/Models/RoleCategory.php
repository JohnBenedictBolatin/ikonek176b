<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleCategory extends Model
{
    protected $table = 'role_categories';
    public $timestamps = false;

    // If your primary key column is role_id, tell Eloquent:
    protected $primaryKey = 'role_id';

    // if primary key is non-incrementing or a string, adjust these:
    // protected $keyType = 'int';
    // public $incrementing = true;

    protected $fillable = ['name'];
}
