<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false; // set false if you don't have created_at/updated_at columns

    protected $fillable = [
        'fk_doc_request_id',
        'fk_user_id',
        'reference_ticket',
        'amount',
        'fk_pay_method_id',
        'transaction_ref',
        'receipt_path',
        'status',
        'gateway_response',
        'fk_treasurer_id',
        'paid_at',
        'handled_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'handled_at' => 'datetime',
        'gateway_response' => 'array', // if stored as JSON
    ];

    // Append helpful accessors so they are included when the model is serialized (to JSON / Inertia)
    protected $appends = [
        'treasurer_contact',
        'treasurer_contact_string',
    ];

    // relationships
    public function documentRequest()
    {
        return $this->belongsTo(\App\Models\DocumentRequest::class, 'fk_doc_request_id', 'doc_request_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_user_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class, 'fk_pay_method_id', 'pay_method_id');
    }

    public function getTreasurerContactAttribute()
    {
        // Try to get the treasurer user (prefer relationLoaded then lazy load)
        $treasurer = null;
        if ($this->relationLoaded('treasurer')) {
            $treasurer = $this->treasurer;
        } elseif ($this->fk_treasurer_id) {
            $treasurer = \App\Models\User::find($this->fk_treasurer_id);
        }

        if ($treasurer) {
            // Try multiple fields for contact/position because schemas vary
            $first = $treasurer->first_name ?? $treasurer->name ?? null;
            $last = $treasurer->last_name ?? null;
            $fullName = trim(($first ? $first : '') . ' ' . ($last ? $last : ''));

            // Try to read position from a field (if you store roles/position on users), else fallback label
            $position = $treasurer->position ?? $treasurer->role_name ?? 'Barangay Treasurer';

            // Try to read phone-number from possible fields
            $number = $treasurer->contact_number ?? $treasurer->primary_contact ?? $treasurer->phone ?? null;

            return [
                'name' => $fullName ?: 'Barangay Treasurer',
                'position' => $position,
                'number' => $number ?: '+63 912 345 6789', // fallback number if treasurer has no phone
            ];
        }

        // FALLBACK example (edit these with your real barangay treasurer info)
        return [
            'name' => 'Ms. Mercy Alpaño',
            'position' => 'Barangay Treasurer',
            'number' => '+63 912 345 6789',
        ];
    }

    /**
     * Accessor: treasurer_contact_string
     *
     * Returns formatted string like: "Ms. Mercy Alpaño — Barangay Treasurer — +63 912 345 6789"
     */
    public function getTreasurerContactStringAttribute()
    {
        $info = $this->treasurer_contact;

        $parts = [];
        if (!empty($info['name'])) {
            $parts[] = $info['name'];
        }
        if (!empty($info['position'])) {
            $parts[] = $info['position'];
        }
        if (!empty($info['number'])) {
            $parts[] = $info['number'];
        }

        return implode(' — ', $parts);
    }

    public function treasurer()
{
    return $this->belongsTo(\App\Models\User::class, 'fk_treasurer_id', 'id');
}
}
