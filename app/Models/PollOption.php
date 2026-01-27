<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    protected $table = 'poll_options';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'poll_id',
        'option_text',
        'vote_count',
    ];

    protected $casts = [
        'vote_count' => 'integer',
    ];

    /**
     * Get the poll that owns this option
     */
    public function poll()
    {
        return $this->belongsTo(PostPoll::class, 'poll_id', 'id');
    }

    /**
     * Get all votes for this option
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'option_id', 'id');
    }

    /**
     * Calculate percentage of votes for this option
     */
    public function getPercentageAttribute($totalVotes)
    {
        if ($totalVotes == 0) {
            return 0;
        }
        return round(($this->vote_count / $totalVotes) * 100, 1);
    }
}

