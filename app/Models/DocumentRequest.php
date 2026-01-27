<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $table = 'document_requests';

    // Primary key
    protected $primaryKey = 'doc_request_id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Table uses created_at but not updated_at.
     * We want Eloquent to manage created_at automatically if desired,
     * but not updated_at. If you prefer to fully manage timestamps manually
     * set $timestamps = false.
     */
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'doc_request_ticket',
        'fk_user_id',
        'fk_document_type_id',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'house_number',
        'phase',
        'package',
        'birthdate',
        'is_requestor_minor',
        'sex',
        'civil_status',
        'contact_number',
        'email',
        'applied_processing_fee',
        'purpose',
        'reason_type',
        'fk_valid_id_type_id',
        'valid_id_content',
        'valid_id_number',
        'pickup_item',
        'pickup_location',
        'pickup_start',
        'pickup_end',
        'person_to_look',
        'status',
        'fk_approver_id',
        // 'created_at' removed - let Laravel manage it automatically via timestamps
        'reviewed_at',
        'admin_feedback',
        'incorrect_fields',
        'extra_fields',
    ];

    protected $casts = [
        'birthdate'              => 'date',
        'pickup_start'           => 'datetime',
        'pickup_end'             => 'datetime',
        'created_at'             => 'datetime',
        'reviewed_at'            => 'datetime',
        'applied_processing_fee' => 'float',
        'is_requestor_minor'     => 'boolean',
        'incorrect_fields'       => 'array',
        'extra_fields'           => 'array',
    ];

    protected $appends = ['processing_fee', 'title'];

    /**
     * Relationship: the user who created the request.
     * Assumes users table primary key is `user_id`. Adjust owner key if different.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_user_id', 'user_id');
    }

    /**
     * Relationship: document type lookup
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DocumentType::class, 'fk_document_type_id', 'document_type_id');
    }

    /**
     * Relationship: approver (a user)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_approver_id', 'user_id');
    }

    /**
     * Relationship: user credential (optional)
     */
    public function userCredential()
    {
        return $this->hasOne(\App\Models\UserCredential::class, 'fk_user_id', 'fk_user_id');
    }

    /**
     * Relationship: valid id type lookup (if you have that table)
     */
    public function validIdType()
    {
        return $this->belongsTo(\App\Models\ValidIdType::class, 'fk_valid_id_type_id', 'valid_id_type_id');
    }

    /**
     * Relationship: attachments for this document request
     */
    public function attachments()
    {
        return $this->hasMany(\App\Models\DocumentRequestAttachment::class, 'fk_doc_request_id', 'doc_request_id');
    }

    /**
     * Booted: copy sensible defaults from authenticated user (or the referenced user)
     * into blank fields at creation time so controller doesn't have to fill everything.
     */
    protected static function booted()
    {
        static::creating(function (DocumentRequest $model) {
            // ensure fk_user_id exists
            if (! $model->fk_user_id && Auth::check()) {
                $model->fk_user_id = Auth::id();
            }

            // load the user and credential for copying fallback values
            $user = null;
            $credential = null;

            if ($model->fk_user_id) {
                $user = \App\Models\User::find($model->fk_user_id);
                $credential = \App\Models\UserCredential::where('fk_user_id', $model->fk_user_id)->first();
            } elseif (Auth::check()) {
                $user = Auth::user();
                $credential = \App\Models\UserCredential::where('fk_user_id', Auth::id())->first();
            }

            if ($user) {
                $copyFields = [
                    'last_name',
                    'first_name',
                    'middle_name',
                    'suffix',
                    'birthdate',
                    'sex',
                    'civil_status',
                ];

                foreach ($copyFields as $f) {
                    if (empty($model->{$f}) && isset($user->{$f})) {
                        $model->{$f} = $user->{$f};
                    }
                }
            }

            // contact_number preference:
            // 1) use model->contact_number if provided
            // 2) else use user_credential.contact_number
            // 3) else use user.contact_number
            if (empty($model->contact_number)) {
                if ($credential && ! empty($credential->contact_number)) {
                    $model->contact_number = $credential->contact_number;
                } elseif ($user && ! empty($user->contact_number)) {
                    $model->contact_number = $user->contact_number;
                } else {
                    $model->contact_number = null;
                }
            }
        });
    }

    /**
     * Effective processing fee for this request.
     * Priority:
     *  - applied_processing_fee (explicit on the request)
     *  - documentType.processing_fee (from document_types table)
     *  - null if none
     */
    public function getProcessingFeeAttribute()
    {
        if (! is_null($this->applied_processing_fee) && $this->applied_processing_fee !== '') {
            return (float) $this->applied_processing_fee;
        }

        if ($this->relationLoaded('documentType') && $this->documentType) {
            return (float) $this->documentType->processing_fee;
        }

        if ($this->fk_document_type_id && $this->documentType()->exists()) {
            $dt = $this->documentType()->first();
            if ($dt) return (float) $dt->processing_fee;
        }

        return null;
    }

    /**
     * Title accessor: prefer explicit value, then document type name, then purpose, etc.
     */
    public function getTitleAttribute($value)
    {
        if (! is_null($value) && $value !== '') {
            return $value;
        }

        if ($this->relationLoaded('documentType') && $this->documentType && ! empty($this->documentType->document_name)) {
            return $this->documentType->document_name;
        }

        if ($this->fk_document_type_id && $this->documentType()->exists()) {
            $dt = $this->documentType()->first();
            if ($dt && ! empty($dt->document_name)) return $dt->document_name;
        }

        if (! empty($this->purpose)) {
            return $this->purpose;
        }

        return 'Request';
    }

    //
    // Accessors that fall back to the user relation if the DB column is null.
    //
    public function getFirstNameAttribute($value)
    {
        return $value ?? $this->user?->first_name;
    }

    public function getLastNameAttribute($value)
    {
        return $value ?? $this->user?->last_name;
    }

    public function getMiddleNameAttribute($value)
    {
        return $value ?? $this->user?->middle_name;
    }

    public function getSuffixAttribute($value)
    {
        return $value ?? $this->user?->suffix;
    }

    public function getBirthdateAttribute($value)
    {
        return $value ?? $this->user?->birthdate;
    }

    public function getSexAttribute($value)
    {
        return $value ?? $this->user?->sex;
    }

    public function getCivilStatusAttribute($value)
    {
        return $value ?? $this->user?->civil_status;
    }

    public function getContactNumberAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        $cred = $this->userCredential ?? (\App\Models\UserCredential::where('fk_user_id', $this->fk_user_id)->first());
        return $cred?->contact_number ?? $this->user?->contact_number ?? null;
    }
}
