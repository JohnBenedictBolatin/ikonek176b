<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    protected $table = 'payment_receipts';
    protected $primaryKey = 'receipt_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'fk_payment_id',
        'receipt_number',
        'issued_by',
        'issued_at',
        'path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class, 'fk_payment_id', 'payment_id');
    }
}
