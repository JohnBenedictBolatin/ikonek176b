<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\TagsModel;

class DiscussionModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'fk_post_author_id',
        'section',
        'content',
        'image_path',
        'video_path',
        'is_poll',
        'is_reported',
        'status',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'fk_post_author_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'fk_post_id', 'post_id');
    }

    // CORRECTED: Using 'tag_id' and 'post_id' columns from post_tag table
    public function tags()
    {
        return $this->belongsToMany(TagsModel::class, 'post_tag', 'post_id', 'tag_id');
    }
}