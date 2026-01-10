<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagsModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'tag_id';
    public $timestamps = true; // if tags have timestamps
    protected $fillable = ['tag_name'];

    public function posts()
    {
        return $this->belongsToMany(
            \App\Models\Post::class, // related
            'post_tags',             // pivot table
            'fk_tag_id',             // foreign pivot key on pivot for this model (tags)
            'fk_post_id'             // related pivot key on pivot for posts
        )->using(\App\Models\PostTag::class);
    }
}
