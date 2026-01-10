<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'fk_post_author_id',
        'header',
        'section',
        'content',
        'image_content',
        'video_content',
        'is_poll',
        'is_reported',
    ];

    protected $casts = [
        'is_poll' => 'boolean',
        'is_reported' => 'boolean',
    ];

    // relationships
    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_post_author_id', 'user_id');
    }

    /**
     * Tags relation (pivot table post_tag).
     * Pivot columns: post_id (this model) <-> tag_id (TagsModel)
     */
    public function tags()
    {
        return $this->belongsToMany(
            \App\Models\TagsModel::class, // related
            'post_tags',                  // pivot table
            'fk_post_id',                 // foreign pivot key on pivot for this model
            'fk_tag_id'                   // related pivot key on pivot for TagsModel
        )->using(\App\Models\PostTag::class);
    }

    /**
     * Reports relation
     */
    public function reports()
    {
        return $this->hasMany(\App\Models\PostReport::class, 'fk_post_id', 'post_id');
    }
}
