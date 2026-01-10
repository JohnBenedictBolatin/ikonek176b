<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentAddPost extends Model
{
    use HasFactory;

    protected $table = 'posts'; // optional if your table name follows convention

    // Add the fields you want to be mass assignable
    protected $fillable = [
        'fk_post_author_id',
        'section',
        'content',
        'image_path',
        'video_path',
        'is_poll',
        'is_reported',
        'status'
    ];
    public $timestamps = true;
}
