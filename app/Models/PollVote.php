<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $table = 'poll_votes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'poll_id',
        'option_id',
    ];

    /**
     * Get the user who voted
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the poll that was voted on
     */
    public function poll()
    {
        return $this->belongsTo(PostPoll::class, 'poll_id', 'id');
    }

    /**
     * Get the option that was voted for
     */
    public function option()
    {
        return $this->belongsTo(PollOption::class, 'option_id', 'id');
    }
}

