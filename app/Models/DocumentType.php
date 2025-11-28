<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    // Explicitly specify the table name
    protected $table = 'document_types';

    // Set the primary key column
    protected $primaryKey = 'document_type_id';

    // Allow mass assignment for these columns
    protected $fillable = [
        'document_type_id',
        'document_name',
        'processing_fee',
    ];

    // If your primary key is not auto-incrementing (e.g. UUID), set this to false
    public $incrementing = true;

    // Set the primary key type
    protected $keyType = 'int';

    // Disable timestamps if your table doesn’t have created_at / updated_at
    public $timestamps = false;

    // Optionally: relationships (useful for your existing ERD)
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'fk_document_type_id', 'document_type_id');
    }

    public function requests()
    {
        return $this->hasMany(DocumentRequest::class, 'fk_document_type_id', 'document_type_id');
    }
}
