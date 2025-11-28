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
    public $incrementing = true;
    protected $primaryKey = 'doc_request_id';
    protected $keyType = 'int';

    // Prevent Eloquent from expecting or maintaining created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'doc_request_ticket',
        'fk_user_id',
        'fk_document_type_id',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'birthdate',
        'sex',
        'civil_status',
        'address',
        'contact_number',
        'valid_id_path',
        'applied_processing_fee',
        'purpose',
        'pickup_item',
        'pickup_location',
        'pickup_start',
        'pickup_end',
        'person_to_look',
        'status',
        'fk_approver_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'applied_processing_fee' => 'float',
    ];

    // expose processing_fee attribute automatically
    protected $appends = ['processing_fee', 'title'];


    /**
     * Relationship to the user who created the request
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_user_id');
    }

    /**
     * Relationship to the document type
     */
    public function documentType()
    {
        return $this->belongsTo(\App\Models\DocumentType::class, 'fk_document_type_id');
    }

    public function userCredential()
    {
        return $this->hasOne(\App\Models\UserCredential::class, 'fk_user_id', 'fk_user_id');
    }

    /**
     * Boot hook to copy user attributes into the model if they are not set.
     * This ensures that if the controller accidentally leaves them blank,
     * the model will still get values from the authenticated user.
     */
     protected static function booted()
    {
        static::creating(function (DocumentRequest $model) {
            // ensure fk_user_id exists
            if (! $model->fk_user_id && Auth::check()) {
                $model->fk_user_id = Auth::id();
            }

            // Load related user and credential if available
            $user = null;
            $credential = null;

            if ($model->fk_user_id) {
                $user = \App\Models\User::find($model->fk_user_id);
                $credential = \App\Models\UserCredential::where('fk_user_id', $model->fk_user_id)->first();
            } elseif (Auth::check()) {
                $user = Auth::user();
                $credential = \App\Models\UserCredential::where('fk_user_id', Auth::id())->first();
            }

            // copy a set of user fields if they are empty on the model
            if ($user) {
                $copyFields = [
                    'last_name',
                    'first_name',
                    'middle_name',
                    'suffix',
                    'birthdate',
                    'sex',
                    'civil_status',
                    'address',
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
            // 3) else use user.contact_number (if you keep it on users)
            if (empty($model->contact_number)) {
                if ($credential && !empty($credential->contact_number)) {
                    $model->contact_number = $credential->contact_number;
                } elseif ($user && !empty($user->contact_number)) {
                    $model->contact_number = $user->contact_number;
                } else {
                    // last resort: empty string to avoid null constraint error — adjust as needed
                    $model->contact_number = '';
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
        if (!is_null($this->applied_processing_fee) && $this->applied_processing_fee !== '') {
            return (float) $this->applied_processing_fee;
        }

        // Try relation (may be null if not eager loaded)
        if ($this->relationLoaded('documentType') && $this->documentType) {
            return (float) $this->documentType->processing_fee;
        }

        // Fallback: try lazy load (cheap)
        if ($this->fk_document_type_id && $this->documentType()->exists()) {
            $dt = $this->documentType()->first();
            if ($dt) return (float) $dt->processing_fee;
        }

        return null;
    }

    public function getTitleAttribute($value)
    {
        // if explicit column exists use it first
        if (!is_null($value) && $value !== '') {
            return $value;
        }

        // prefer eager-loaded relation
        if ($this->relationLoaded('documentType') && $this->documentType && !empty($this->documentType->document_name)) {
            return $this->documentType->document_name;
        }

        // fallback to lazy load the relation if needed
        if ($this->fk_document_type_id && $this->documentType()->exists()) {
            $dt = $this->documentType()->first();
            if ($dt && !empty($dt->document_name)) return $dt->document_name;
        }

        // fallback to any document_name column on the request or purpose
        if (!empty($this->document_name)) {
            return $this->document_name;
        }

        if (!empty($this->purpose)) {
            return $this->purpose;
        }

        return 'Request';
    }

    //
    // Accessors that fall back to the user relation if the DB column is null.
    // This is handy if you prefer to keep document_requests sparse and always
    // read user values when not present on the request record.
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

    public function getAddressAttribute($value)
    {
        return $value ?? $this->user?->address;
    }

    public function getContactNumberAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        // try related credential
        $cred = $this->userCredential ?? (\App\Models\UserCredential::where('fk_user_id', $this->fk_user_id)->first());
        return $cred?->contact_number ?? $this->user?->contact_number ?? null;
    }


    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_approver_id', 'id');
    }
}
