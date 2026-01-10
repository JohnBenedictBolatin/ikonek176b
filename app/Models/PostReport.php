<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    protected $table = 'post_reports';
    protected $primaryKey = 'post_report_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'fk_post_id',
        'fk_reporter_id',
        'reason',
        'details',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function post()
    {
        return $this->belongsTo(Post::class, 'fk_post_id', 'post_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'fk_reporter_id', 'user_id');
    }

    public function reasons()
    {
        return $this->hasMany(PostReportReason::class, 'fk_report_id', 'post_report_id');
    }
}

