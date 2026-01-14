<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReportReason extends Model
{
    protected $table = 'post_report_reasons';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'fk_report_id',
        'reason',
    ];

    public function report()
    {
        return $this->belongsTo(PostReport::class, 'fk_report_id', 'report_id');
    }
}








