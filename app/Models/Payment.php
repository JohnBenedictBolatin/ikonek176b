<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Table uses updated_at but no created_at
    public $timestamps = false;

    protected $fillable = [
        'fk_doc_request_id',
        'fk_user_id',
        'request_reference_ticket',
        'paid_amount',
        'fk_pay_method_id',
        'transaction_ref',
        'receipt_content',
        'status',
        'fk_treasurer_id',
        'paid_at',
        'handled_at',
        'updated_at',
    ];

    protected $casts = [
        'paid_amount' => 'float',
        'paid_at'     => 'datetime',
        'handled_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    protected $appends = [
        'treasurer_contact',
        'treasurer_contact_string',
    ];

    /**
     * Relationships
     */
    public function documentRequest()
    {
        return $this->belongsTo(\App\Models\DocumentRequest::class, 'fk_doc_request_id', 'doc_request_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_user_id', 'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'fk_pay_method_id', 'pay_method_id');
    }

    public function treasurer()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_treasurer_id', 'user_id');
    }

    /**
     * Accessor: treasurer_contact (object form)
     */
    public function getTreasurerContactAttribute()
    {
        $treasurer = $this->relationLoaded('treasurer')
            ? $this->treasurer
            : ($this->fk_treasurer_id ? \App\Models\User::find($this->fk_treasurer_id) : null);

        if ($treasurer) {
            $first = $treasurer->first_name ?? $treasurer->name ?? null;
            $last  = $treasurer->last_name ?? null;

            $fullName = trim(($first ?: '') . ' ' . ($last ?: ''));

            return [
                'name'     => $fullName ?: 'Barangay Treasurer',
                'position' => $treasurer->position ?? $treasurer->role_name ?? 'Barangay Treasurer',
                'number'   => $treasurer->contact_number
                              ?? $treasurer->primary_contact
                              ?? $treasurer->phone
                              ?? '+63 912 345 6789',
            ];
        }

        return [
            'name'     => 'Barangay Treasurer',
            'position' => 'Treasurer',
            'number'   => '+63 912 345 6789',
        ];
    }

    /**
     * Accessor: treasurer_contact_string
     */
    public function getTreasurerContactStringAttribute()
    {
        $info = $this->treasurer_contact;
        return implode(' — ', array_filter([
            $info['name'] ?? null,
            $info['position'] ?? null,
            $info['number'] ?? null,
        ]));
    }
}
