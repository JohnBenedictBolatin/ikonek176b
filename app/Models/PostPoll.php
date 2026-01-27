<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPoll extends Model
{
    protected $table = 'post_polls';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'post_id',
    ];

    /**
     * Get the post that owns this poll
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    /**
     * Get all options for this poll
     */
    public function options()
    {
        return $this->hasMany(PollOption::class, 'poll_id', 'id');
    }

    /**
     * Get all votes for this poll
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_id', 'id');
    }

    /**
     * Get total vote count
     */
    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }
}

