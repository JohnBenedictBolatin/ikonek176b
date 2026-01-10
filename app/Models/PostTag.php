<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostTag extends Pivot
{
    // Set the pivot table name
    protected $table = 'post_tags';

    // If pivot has no timestamps, keep false. Change to true if you have created_at/updated_at on pivot.
    public $timestamps = false;

    // Allow mass assignment of the foreign keys (optional)
    protected $fillable = [
        'fk_post_id',
        'fk_tag_id',
    ];
}
