<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    protected $table = 'document_requirement_types';
    protected $primaryKey = 'doc_requirement_type_id';

    public $incrementing = true;
    protected $keyType = 'int';

    // Table does NOT contain created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'doc_requirement_type_name',
    ];
}
