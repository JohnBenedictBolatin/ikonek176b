<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\EventAssistanceRequest;
use App\Models\Payment;
use Inertia\Inertia;
use Illuminate\Support\Str;

class NotificationRequestController extends Controller
{
    public function index()
    {
        // Temporarily increase memory limit for this request
        $originalLimit = ini_get('memory_limit');
        ini_set('memory_limit', '256M');
        
        $userId = auth()->id();
        if (! $userId) {
            abort(403, 'Unauthorized');
        }

        // Limit records to prevent memory issues (latest 100 of each type)
        $limit = 100;

        // Document requests - select only needed columns and limit
        $documentRequests = DocumentRequest::with(['documentType'])
            ->select([
                'doc_request_id',
                'doc_request_ticket',
                'fk_user_id',
                'fk_document_type_id',
                'status',
                'purpose',
                'pickup_item',
                'pickup_location',
                'pickup_start',
                'pickup_end',
                'person_to_look',
                'applied_processing_fee',
                'created_at',
                'reviewed_at',
                'admin_feedback',
            ])
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Event assistance requests - select only needed columns and limit
        $eventAssistanceRequests = EventAssistanceRequest::select([
                'event_assist_request_id',
                'event_assist_request_ticket',
                'fk_user_id',
                'purpose',
                'event_location',
                'event_date',
                'event_start',
                'event_end',
                'status',
                'extra_fields',
                'created_at',
                'reviewed_at',
                'admin_feedback',
            ])
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Get document request IDs for payment lookup
        $docRequestIds = $documentRequests->pluck('doc_request_id')->filter()->values()->all();

        // Payments - select only needed columns and limit
        $payments = Payment::select([
                'payment_id',
                'fk_doc_request_id',
                'fk_user_id',
                'status',
                'paid_amount',
                'transaction_ref',
                'receipt_content',
                'paid_at',
                'updated_at',
            ])
            ->where(function ($query) use ($userId, $docRequestIds) {
                $query->where('fk_user_id', $userId);
                if (!empty($docRequestIds)) {
                    $query->orWhereIn('fk_doc_request_id', $docRequestIds);
                }
            })
            ->orderByDesc('payment_id')
            ->limit($limit)
            ->get();

        // Helper: sanitize string to UTF-8 (simplified, non-recursive for scalars)
        $sanitizeString = function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            if (mb_check_encoding($value, 'UTF-8')) {
                return $value;
            }
            // Try common encodings
            $tryEncodings = ['Windows-1252', 'ISO-8859-1', 'CP1252'];
            foreach ($tryEncodings as $enc) {
                $converted = @mb_convert_encoding($value, 'UTF-8', $enc);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
            // Fallback: strip invalid bytes
            $stripped = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            return $stripped !== false ? $stripped : mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        };

        // Convert to arrays efficiently - only include what's needed
        $documentRequestsArray = $documentRequests->map(function ($m) use ($sanitizeString) {
            $docType = $m->documentType;
            return [
                'doc_request_id' => $m->doc_request_id,
                'doc_request_ticket' => $sanitizeString($m->doc_request_ticket ?? ''),
                'fk_user_id' => $m->fk_user_id,
                'fk_document_type_id' => $m->fk_document_type_id,
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'purpose' => $sanitizeString($m->purpose ?? ''),
                'pickup_item' => $sanitizeString($m->pickup_item ?? ''),
                'pickup_location' => $sanitizeString($m->pickup_location ?? ''),
                'pickup_start' => $m->pickup_start ? $m->pickup_start->toIso8601String() : null,
                'pickup_end' => $m->pickup_end ? $m->pickup_end->toIso8601String() : null,
                'person_to_look' => $sanitizeString($m->person_to_look ?? ''),
                'applied_processing_fee' => $m->applied_processing_fee,
                'processing_fee' => $docType ? ($docType->processing_fee ?? null) : null,
                'document_name' => $docType ? $sanitizeString($docType->document_name ?? '') : '',
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                'reviewed_at' => $m->reviewed_at ? $m->reviewed_at->toIso8601String() : null,
                'admin_feedback' => $sanitizeString($m->admin_feedback ?? ''),
            ];
        })->all();

        $eventAssistanceRequestsArray = $eventAssistanceRequests->map(function ($m) use ($sanitizeString) {
            // Extract event_type from extra_fields for the title
            $extraFields = $m->extra_fields ?? [];
            $eventType = is_array($extraFields) ? ($extraFields['event_type'] ?? null) : null;
            $title = $eventType ?? $sanitizeString($m->purpose ?? 'Event Assistance Request');
            
            // For event assistance, we need to combine event_date + event_start to create pickup_start
            // and event_date + event_end to create pickup_end (for compatibility with getPickupSchedule)
            $pickupStart = null;
            $pickupEnd = null;
            
            if ($m->event_date && $m->event_start) {
                try {
                    // event_start might be time (H:i:s) or datetime - try both
                    $eventStartStr = is_object($m->event_start) ? $m->event_start->format('H:i:s') : (string)$m->event_start;
                    $eventDateStr = is_object($m->event_date) ? $m->event_date->format('Y-m-d') : (string)$m->event_date;
                    
                    // If event_start is just time (H:i:s), combine with date
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventStartStr)) {
                        $pickupStart = \Carbon\Carbon::parse($eventDateStr . ' ' . $eventStartStr)->toIso8601String();
                    } else {
                        // It's already a datetime, use as-is
                        $pickupStart = \Carbon\Carbon::parse($m->event_start)->toIso8601String();
                    }
                } catch (\Throwable $e) {
                    $pickupStart = null;
                }
            } elseif ($m->event_start) {
                try {
                    $pickupStart = \Carbon\Carbon::parse($m->event_start)->toIso8601String();
                } catch (\Throwable $e) {
                    $pickupStart = null;
                }
            }
            
            if ($m->event_date && $m->event_end) {
                try {
                    // event_end might be time (H:i:s) or datetime - try both
                    $eventEndStr = is_object($m->event_end) ? $m->event_end->format('H:i:s') : (string)$m->event_end;
                    $eventDateStr = is_object($m->event_date) ? $m->event_date->format('Y-m-d') : (string)$m->event_date;
                    
                    // If event_end is just time (H:i:s), combine with date
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventEndStr)) {
                        $pickupEnd = \Carbon\Carbon::parse($eventDateStr . ' ' . $eventEndStr)->toIso8601String();
                    } else {
                        // It's already a datetime, use as-is
                        $pickupEnd = \Carbon\Carbon::parse($m->event_end)->toIso8601String();
                    }
                } catch (\Throwable $e) {
                    $pickupEnd = null;
                }
            } elseif ($m->event_end) {
                try {
                    $pickupEnd = \Carbon\Carbon::parse($m->event_end)->toIso8601String();
                } catch (\Throwable $e) {
                    $pickupEnd = null;
                }
            }
            
            return [
                'event_assist_request_id' => $m->event_assist_request_id,
                'eventassist_request_ticket' => $sanitizeString($m->event_assist_request_ticket ?? ''),
                'fk_user_id' => $m->fk_user_id,
                'purpose' => $sanitizeString($m->purpose ?? ''),
                'title' => $title,
                'event_type' => $eventType,
                'event_location' => $sanitizeString($m->event_location ?? ''),
                'event_date' => $m->event_date ? $m->event_date->toIso8601String() : null,
                'event_start' => $m->event_start ? (\Carbon\Carbon::parse($m->event_start)->toIso8601String() ?? null) : null,
                'event_end' => $m->event_end ? (\Carbon\Carbon::parse($m->event_end)->toIso8601String() ?? null) : null,
                'pickup_start' => $pickupStart, // For compatibility with getPickupSchedule
                'pickup_end' => $pickupEnd, // For compatibility with getPickupSchedule
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                'reviewed_at' => $m->reviewed_at ? $m->reviewed_at->toIso8601String() : null,
                'admin_feedback' => $sanitizeString($m->admin_feedback ?? ''),
            ];
        })->all();

        $paymentsArray = $payments->map(function ($m) use ($sanitizeString) {
            // Format receipt image URL similar to PaymentController
            $receiptImage = null;
            if (!empty($m->receipt_content)) {
                // if it looks like a full URL use as-is
                if (Str::startsWith($m->receipt_content, ['http://', 'https://', '/'])) {
                    $receiptImage = $m->receipt_content;
                } else {
                    // assume it's a storage path (e.g. payments/xyz.jpg)
                    $receiptImage = asset('storage/' . ltrim($m->receipt_content, '/'));
                }
            }
            
            return [
                'payment_id' => $m->payment_id,
                'fk_doc_request_id' => $m->fk_doc_request_id,
                'fk_user_id' => $m->fk_user_id,
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'amount' => $m->paid_amount, // Map paid_amount to amount for frontend
                'paid_amount' => $m->paid_amount, // Also include original name
                'transaction_ref' => $sanitizeString($m->transaction_ref ?? ''),
                'receipt_path' => $receiptImage, // Formatted receipt image URL
                'receipt_content' => $sanitizeString($m->receipt_content ?? ''), // Also include original name
                'receipt_image' => $receiptImage, // Add receipt_image for consistency
                'paid_at' => $m->paid_at ? $m->paid_at->toIso8601String() : null,
                'updated_at' => $m->updated_at ? $m->updated_at->toIso8601String() : null,
            ];
        })->all();

        // Get user data (simplified)
        $user = auth()->user();
        $userData = $user ? [
            'id' => $user->user_id,
            'name' => $sanitizeString($user->name ?? ''),
            'email' => $sanitizeString($user->email ?? ''),
            'fk_role_id' => $user->fk_role_id ?? null,
            'profile_pic' => $sanitizeString($user->profile_pic ?? ''),
        ] : null;

        $result = Inertia::render('User/Resident/R_Notification_Request', [
            // Send both naming conventions for backward compatibility
            'documentRequests' => $documentRequestsArray,
            'document_requests' => $documentRequestsArray,
            'eventAssistanceRequests' => $eventAssistanceRequestsArray,
            'event_assistance_requests' => $eventAssistanceRequestsArray,
            'payments' => $paymentsArray,
            'auth' => ['user' => $userData],
        ]);
        
        // Restore original memory limit
        ini_set('memory_limit', $originalLimit);
        
        return $result;
    }

    public function OfficialReq()
    {
        // Temporarily increase memory limit for this request
        $originalLimit = ini_get('memory_limit');
        ini_set('memory_limit', '256M');
        
        $user = auth()->user();
        if (! $user) {
            abort(403, 'Unauthorized');
        }

        // Resolve user ID - handle both user_id and id fields
        $userId = $user->user_id ?? $user->id ?? auth()->id();
        if (! $userId) {
            abort(403, 'Unauthorized - Unable to resolve user ID');
        }

        // Helper: sanitize string to UTF-8 (same as resident version)
        $sanitizeString = function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            if (mb_check_encoding($value, 'UTF-8')) {
                return $value;
            }
            // Try common encodings
            $tryEncodings = ['Windows-1252', 'ISO-8859-1', 'CP1252'];
            foreach ($tryEncodings as $enc) {
                $converted = @mb_convert_encoding($value, 'UTF-8', $enc);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
            // Fallback: strip invalid bytes
            $stripped = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            return $stripped !== false ? $stripped : mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        };

        // Document requests for the authenticated user - sanitize data before returning
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $documentRequestsRaw = DocumentRequest::with(['documentType', 'user', 'userCredential'])
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert to arrays and sanitize string fields
        $documentRequests = $documentRequestsRaw->map(function ($m) use ($sanitizeString) {
            $docType = $m->documentType;
            return [
                'doc_request_id' => $m->doc_request_id,
                'doc_request_ticket' => $sanitizeString($m->doc_request_ticket ?? ''),
                'fk_user_id' => $m->fk_user_id,
                'fk_document_type_id' => $m->fk_document_type_id,
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'purpose' => $sanitizeString($m->purpose ?? ''),
                'pickup_item' => $sanitizeString($m->pickup_item ?? ''),
                'pickup_location' => $sanitizeString($m->pickup_location ?? ''),
                'pickup_start' => $m->pickup_start ? $m->pickup_start->toIso8601String() : null,
                'pickup_end' => $m->pickup_end ? $m->pickup_end->toIso8601String() : null,
                'person_to_look' => $sanitizeString($m->person_to_look ?? ''),
                'applied_processing_fee' => $m->applied_processing_fee,
                'processing_fee' => $docType ? ($docType->processing_fee ?? null) : null,
                'document_name' => $docType ? $sanitizeString($docType->document_name ?? '') : '',
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                'reviewed_at' => $m->reviewed_at ? $m->reviewed_at->toIso8601String() : null,
                'admin_feedback' => $sanitizeString($m->admin_feedback ?? ''),
                'documentType' => $docType ? [
                    'document_type_id' => $docType->document_type_id,
                    'document_name' => $sanitizeString($docType->document_name ?? ''),
                    'processing_fee' => $docType->processing_fee ?? null,
                ] : null,
                'user' => $m->user ? [
                    'user_id' => $m->user->user_id,
                    'name' => $sanitizeString($m->user->name ?? ''),
                    'email' => $sanitizeString($m->user->email ?? ''),
                ] : null,
                'userCredential' => $m->userCredential ? [
                    'contact_number' => $sanitizeString($m->userCredential->contact_number ?? ''),
                    'secondary_contact_number' => $sanitizeString($m->userCredential->secondary_contact_number ?? ''),
                ] : null,
            ];
        })->values();

        // Event assistance requests for the authenticated user - sanitize data
        $eventAssistanceRequestsRaw = EventAssistanceRequest::with(['user', 'approver', 'details'])
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert to arrays and sanitize string fields
        $eventAssistanceRequests = $eventAssistanceRequestsRaw->map(function ($m) use ($sanitizeString) {
            // Extract event_type from extra_fields for the title
            $extraFields = $m->extra_fields ?? [];
            $eventType = is_array($extraFields) ? ($extraFields['event_type'] ?? null) : null;
            $title = $eventType ?? $sanitizeString($m->purpose ?? 'Event Assistance Request');
            
            // For event assistance, we need to combine event_date + event_start to create pickup_start
            // and event_date + event_end to create pickup_end (for compatibility with getPickupSchedule)
            // Note: event_start and event_end are stored as time (H:i:s) or datetime, need to handle both
            $pickupStart = null;
            $pickupEnd = null;
            
            if ($m->event_date && $m->event_start) {
                try {
                    // event_start might be time (H:i:s) or datetime - try both
                    $eventStartStr = is_object($m->event_start) ? $m->event_start->format('H:i:s') : (string)$m->event_start;
                    $eventDateStr = is_object($m->event_date) ? $m->event_date->format('Y-m-d') : (string)$m->event_date;
                    
                    // If event_start is just time (H:i:s), combine with date
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventStartStr)) {
                        $pickupStart = \Carbon\Carbon::parse($eventDateStr . ' ' . $eventStartStr)->toIso8601String();
                    } else {
                        // It's already a datetime, use as-is
                        $pickupStart = \Carbon\Carbon::parse($m->event_start)->toIso8601String();
                    }
                } catch (\Throwable $e) {
                    $pickupStart = null;
                }
            } elseif ($m->event_start) {
                try {
                    $pickupStart = \Carbon\Carbon::parse($m->event_start)->toIso8601String();
                } catch (\Throwable $e) {
                    $pickupStart = null;
                }
            }
            
            if ($m->event_date && $m->event_end) {
                try {
                    // event_end might be time (H:i:s) or datetime - try both
                    $eventEndStr = is_object($m->event_end) ? $m->event_end->format('H:i:s') : (string)$m->event_end;
                    $eventDateStr = is_object($m->event_date) ? $m->event_date->format('Y-m-d') : (string)$m->event_date;
                    
                    // If event_end is just time (H:i:s), combine with date
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $eventEndStr)) {
                        $pickupEnd = \Carbon\Carbon::parse($eventDateStr . ' ' . $eventEndStr)->toIso8601String();
                    } else {
                        // It's already a datetime, use as-is
                        $pickupEnd = \Carbon\Carbon::parse($m->event_end)->toIso8601String();
                    }
                } catch (\Throwable $e) {
                    $pickupEnd = null;
                }
            } elseif ($m->event_end) {
                try {
                    $pickupEnd = \Carbon\Carbon::parse($m->event_end)->toIso8601String();
                } catch (\Throwable $e) {
                    $pickupEnd = null;
                }
            }
            
            return [
                'event_assist_request_id' => $m->event_assist_request_id,
                'eventassist_request_ticket' => $sanitizeString($m->event_assist_request_ticket ?? ''),
                'fk_user_id' => $m->fk_user_id,
                'purpose' => $sanitizeString($m->purpose ?? ''),
                'title' => $title,
                'event_type' => $eventType,
                'event_location' => $sanitizeString($m->event_location ?? ''),
                'event_date' => $m->event_date ? $m->event_date->toIso8601String() : null,
                'event_start' => $m->event_start ? (\Carbon\Carbon::parse($m->event_start)->toIso8601String() ?? null) : null,
                'event_end' => $m->event_end ? (\Carbon\Carbon::parse($m->event_end)->toIso8601String() ?? null) : null,
                'pickup_start' => $pickupStart, // For compatibility with getPickupSchedule
                'pickup_end' => $pickupEnd, // For compatibility with getPickupSchedule
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                'reviewed_at' => $m->reviewed_at ? $m->reviewed_at->toIso8601String() : null,
                'admin_feedback' => $sanitizeString($m->admin_feedback ?? ''),
                'user' => $m->user ? [
                    'user_id' => $m->user->user_id,
                    'name' => $sanitizeString($m->user->name ?? ''),
                    'email' => $sanitizeString($m->user->email ?? ''),
                ] : null,
                'approver' => $m->approver ? [
                    'user_id' => $m->approver->user_id,
                    'name' => $sanitizeString($m->approver->name ?? ''),
                ] : null,
            ];
        })->values();

        // Sanitize user data
        $userData = [
            'user_id' => $user->user_id,
            'id' => $user->user_id,
            'name' => $sanitizeString($user->name ?? ''),
            'email' => $sanitizeString($user->email ?? ''),
            'fk_role_id' => $user->fk_role_id ?? null,
            'profile_pic' => $sanitizeString($user->profile_pic ?? ''),
        ];

        $result = Inertia::render('User/Employee/E_Notification_Request', [
            'document_requests' => $documentRequests,
            'documentRequests'  => $documentRequests,
            'event_assistance_requests' => $eventAssistanceRequests,
            'eventAssistanceRequests' => $eventAssistanceRequests,
            'auth' => ['user' => $userData],
        ]);
        
        // Restore original memory limit
        ini_set('memory_limit', $originalLimit);
        
        return $result;
    }
}
