<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\UserCredential;
use App\Models\User;
use App\Models\DocumentRequestAttachment;
use App\Services\DocumentGenerationService;
use App\Services\OtpService;
use Symfony\Component\HttpFoundation\Response;

class DocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $authUser = $request->user();

        if (! $authUser) {
            \Log::info('DocumentRequestController@index - no authenticated user');
            return redirect()->route('login');
        }

        $userId = null;

        try {
            if (method_exists($authUser, 'getKey')) {
                $userId = $authUser->getKey();
            }

            if (! $userId) {
                if (isset($authUser->id)) $userId = $authUser->id;
                elseif (isset($authUser->user_id)) $userId = $authUser->user_id;
                elseif (isset($authUser->fk_user_id)) $userId = $authUser->fk_user_id;
            }

            if (! $userId && method_exists($authUser, 'user')) {
                $related = $authUser->user;
                if ($related) {
                    $userId = $related->getKey() ?? ($related->user_id ?? $related->id ?? null);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('DocumentRequestController@index - error resolving user id: ' . $e->getMessage());
        }

        \Log::info('DocumentRequestController@index - resolved user id: ' . json_encode($userId));

        if (! $userId) {
            \Log::warning('DocumentRequestController@index - cannot resolve user id for authenticated user; aborting.');
            return Inertia::render('User/Resident/R_Notification_Request', [
                'documentRequests' => [],
                'document_requests' => [],
                'eventRequests' => [],
                'event_requests' => [],
            ]);
        }

        // Eager-load related tables: documentType, user, userCredential
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $rows = DocumentRequest::with(['documentType', 'user', 'userCredential'])
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('DocumentRequestController@index - fetched rows count: ' . $rows->count());

        // helper to map a raw row -> shared presentation array
        $mapRow = function ($r) {
            // processing_fee accessor already handles applied_processing_fee / documentType lookup
            $amountNumeric = $r->processing_fee ?? $r->applied_processing_fee ?? ($r->documentType?->processing_fee ?? null);

            // Name fallback: prefer the column on document_requests, otherwise fall back to users table via relation
            $firstName = $r->first_name ?? $r->user?->first_name ?? null;
            $middleName = $r->middle_name ?? $r->user?->middle_name ?? null;
            $lastName = $r->last_name ?? $r->user?->last_name ?? null;
            $suffix = $r->suffix ?? $r->user?->suffix ?? null;

            // Build display name (Title case)
            $normalize = function ($s) {
                $s = trim((string) $s);
                if ($s === '') return null;
                $parts = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                $parts = array_map(function ($w) {
                    $w = mb_strtolower($w);
                    $first = mb_strtoupper(mb_substr($w, 0, 1));
                    $rest  = mb_substr($w, 1);
                    return $first . $rest;
                }, $parts);
                return implode(' ', $parts);
            };

            $parts = array_filter([
                $normalize($firstName),
                $normalize($middleName),
                $normalize($lastName),
                $normalize($suffix),
            ]);

            $displayName = implode(' ', $parts);

            // Contact number fallback: document_request -> userCredential -> users table
            $contactNumber = $r->contact_number
                ?? $r->userCredential?->contact_number
                ?? $r->user?->contact_number
                ?? null;

            // Determine title: for documents use document_name, for events use event_type from extra_fields
            $title = null;
            if ($r->documentType?->document_name) {
                $title = $r->documentType->document_name;
            } elseif (!$r->fk_document_type_id) {
                // This is an event assistance request - check extra_fields for event_type
                $extraFields = $r->extra_fields ?? [];
                $eventType = is_array($extraFields) ? ($extraFields['event_type'] ?? null) : null;
                $title = $eventType ?? $r->purpose ?? 'Request';
            } else {
                $title = $r->purpose ?? 'Request';
            }

            return [
                'id' => $r->doc_request_id,
                'requestNumber' => $r->doc_request_ticket,
                'doc_request_ticket' => $r->doc_request_ticket,
                'title' => $title,
                'date' => $r->created_at?->format('M d, Y'),
                'time' => $r->created_at?->format('g:i A'),
                'status' => $r->status,
                'type' => $r->fk_document_type_id ? 'document' : 'event',
                'amount' => $amountNumeric !== null ? number_format((float)$amountNumeric, 2, '.', '') : null,
                'processing_fee' => $amountNumeric !== null ? (float)$amountNumeric : null,
                'document_type' => $r->documentType ? [
                    'document_type_id' => $r->documentType->document_type_id,
                    'document_name' => $r->documentType->document_name,
                    'processing_fee' => $r->documentType->processing_fee,
                ] : null,
                // include user-facing info
                'name' => $displayName,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'suffix' => $suffix,
                'contact_number' => $contactNumber,
                // Add fields to identify resubmitted requests
                'admin_feedback' => $r->admin_feedback ?? null,
                'incorrect_fields' => $r->incorrect_fields ?? null,
            ];
        };

        // split rows into documents and events
        $documents = $rows->filter(fn($r) => (bool) $r->fk_document_type_id)
            ->map($mapRow)
            ->values();

        $events = $rows->filter(fn($r) => ! (bool) $r->fk_document_type_id)
            ->map($mapRow)
            ->values();

        $target = $request->query('target', $request->route('target') ?: 'document');
        $component = ($target === 'event' || $target === 'events')
            ? 'User/Resident/E_Notification_Request'
            : 'User/Resident/R_Notification_Request';

        return Inertia::render($component, [
            'documentRequests' => $documents,
            'document_requests' => $documents,
            'eventRequests' => $events,
            'event_requests' => $events,
        ]);
    }

    public function docuReq(Request $request)
    {
        $authUser = $request->user();

        // Quick role check (adjust allowed roles to your app)
        $allowedRoles = [3, 9]; // approver role id (3) and system admin role id (9)
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;

        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized.');
        }

        $requestedStatus = $request->query('status', 'Pending');
        
        // If this is an API request (JSON), return JSON response
        if ($request->wantsJson() || $request->expectsJson()) {
            return $this->docuReqJson($request);
        }

        // Eager-load documentType, user, userCredential, and attachments relationships
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $query = DocumentRequest::with(['documentType', 'user', 'userCredential', 'attachments'])
            ->orderBy('created_at', 'desc');

        if (is_string($requestedStatus) && strtolower($requestedStatus) !== 'all') {
            // Normalize status: capitalize first letter, rest lowercase (Pending, Approved, Rejected)
            $statusFilter = ucfirst(strtolower($requestedStatus));
            $query->where('status', $statusFilter);
        }

        $rows = $query->get();

        // Also fetch all document types once (for mapping / selection in UI)
        $allDocumentTypes = DocumentType::orderBy('document_name')->get();

        // Build a map id => name (useful on frontend)
        $documentTypesMap = $allDocumentTypes->pluck('document_name', 'document_type_id')->toArray();

        $documentRequests = $rows->map(function ($r) {
            // relationship shortcuts
            $docType = $r->documentType;
            $user = $r->user;
            $credential = $r->userCredential;

            // build a small object for document_type
            $documentTypeObj = $docType ? [
                'id' => $docType->document_type_id,
                'name' => $docType->document_name,
                'processing_fee' => isset($docType->processing_fee) ? (float)$docType->processing_fee : null,
            ] : [
                'id' => $r->fk_document_type_id,
                'name' => null,
                'processing_fee' => null,
            ];

            // --- Name normalization/fix with fallback to user table ---
            $rawFirst  = trim((string) ($r->first_name ?? $user?->first_name ?? ''));
            $rawMiddle = trim((string) ($r->middle_name ?? $user?->middle_name ?? ''));
            $rawLast   = trim((string) ($r->last_name ?? $user?->last_name ?? ''));
            $rawSuffix = trim((string) ($r->suffix ?? $user?->suffix ?? ''));

            if ($rawFirst !== '') {
                $parts = preg_split('/\s+/', $rawFirst, -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) > 1 && empty($rawLast)) {
                    $firstCandidate = array_shift($parts);
                    $lastCandidate  = array_pop($parts);
                    $middleCandidate = count($parts) ? implode(' ', $parts) : $rawMiddle;

                    $rawFirst  = $firstCandidate;
                    $rawLast   = $lastCandidate;
                    if (empty($rawMiddle)) {
                        $rawMiddle = $middleCandidate;
                    }
                } elseif (count($parts) > 1 && $rawLast !== '' ) {
                    if (strcasecmp(end($parts), $rawLast) === 0) {
                        $rawFirst = array_shift($parts);
                        if (empty($rawMiddle) && count($parts) > 0) {
                            array_pop($parts);
                            $rawMiddle = count($parts) ? implode(' ', $parts) : $rawMiddle;
                        }
                    }
                }
            }

            if ($rawMiddle !== '' && $rawLast !== '') {
                $pattern = '/\b' . preg_quote($rawLast, '/') . '$/i';
                if (preg_match($pattern, $rawMiddle)) {
                    $rawMiddle = trim(preg_replace($pattern, '', $rawMiddle));
                }
            }

            $normalize = function ($s) {
                $s = trim((string) $s);
                if ($s === '') return '';
                $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                $words = array_map(function ($w) {
                    $w = mb_strtolower($w);
                    $first = mb_strtoupper(mb_substr($w, 0, 1));
                    $rest  = mb_substr($w, 1);
                    return $first . $rest;
                }, $words);
                return implode(' ', $words);
            };

            $first  = $normalize($rawFirst);
            $middle = $normalize($rawMiddle);
            $last   = $normalize($rawLast);
            $suffix = $normalize($rawSuffix);

            $nameParts = array_filter([$first, $middle, $last, $suffix], function ($p) {
                return $p !== '' && $p !== null;
            });
            $displayName = implode(' ', $nameParts);
            // --- Name normalization/fix ends here ---

            // Contact number fallback: explicit -> credential -> user
            $contactNumber = $r->contact_number ?? $credential?->contact_number ?? $user?->contact_number ?? null;

            // Get user registration information
            $userInfo = [
                'user_id' => $user?->user_id,
                'email' => $user?->email,
                'house_number' => $user?->house_number,
                'street' => $user?->street,
                'phase' => $user?->phase,
                'package' => $user?->package,
                'barangay' => $user?->barangay,
                'city' => $user?->city,
                'province' => $user?->province,
                'zip_code' => $user?->zip_code,
                'profile_pic' => $user?->profile_pic,
            ];

            // Get credential information
            $credentialInfo = $credential ? [
                'contact_number' => $credential->contact_number,
                'secondary_contact_number' => $credential->secondary_contact_number,
            ] : null;

            // Process attachments - use the file_url accessor for consistent URLs
            // Clean file names to ensure valid UTF-8
            $attachments = [];
            if ($r->attachments && $r->attachments->count() > 0) {
                $attachments = $r->attachments->map(function ($attachment) {
                    $fileName = $attachment->file_name ?? '';
                    // Clean file name to ensure valid UTF-8
                    if (!mb_check_encoding($fileName, 'UTF-8')) {
                        $fileName = mb_convert_encoding($fileName, 'UTF-8', 'UTF-8');
                    }
                    if (function_exists('iconv')) {
                        $fileName = @iconv('UTF-8', 'UTF-8//IGNORE', $fileName);
                        if ($fileName === false) {
                            $fileName = '';
                        }
                    }
                    
                    return [
                        'attachment_id' => $attachment->attachment_id,
                        'field_name' => $attachment->field_name,
                        'file_name' => $fileName,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'file_url' => $attachment->file_url, // Use the accessor for consistent URL generation
                        'created_at' => $attachment->created_at?->toDateTimeString(),
                    ];
                })->toArray();
            }
            
            // Log attachments for debugging
            \Log::info('Document Request Attachments', [
                'doc_request_id' => $r->doc_request_id,
                'attachments_count' => count($attachments),
                'attachments' => $attachments,
            ]);

            // Get extra_fields (dynamic fields) - clean for JSON encoding
            $extraFields = $r->extra_fields ?? [];
            if (is_string($extraFields)) {
                // If it's a JSON string, decode and clean it
                try {
                    $decoded = json_decode($extraFields, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $extraFields = $this->cleanArrayForJson($decoded);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to decode extra_fields: ' . $e->getMessage());
                    $extraFields = [];
                }
            } elseif (is_array($extraFields)) {
                $extraFields = $this->cleanArrayForJson($extraFields);
            }

            // Clean string fields to ensure valid UTF-8 for JSON encoding
            $cleanString = function($value) {
                if (!is_string($value) || empty($value)) {
                    return $value;
                }
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
                if (function_exists('iconv')) {
                    $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
                    if ($value === false) {
                        $value = '';
                    }
                }
                return $value;
            };
            
            return [
                'doc_request_id' => $r->doc_request_id,
                'doc_request_ticket' => $cleanString($r->doc_request_ticket ?? ''),
                'fk_user_id' => $r->fk_user_id,
                'last_name' => $cleanString($last),
                'first_name' => $cleanString($first),
                'middle_name' => $cleanString($middle),
                'suffix' => $cleanString($suffix),
                // Send the normalized display name (First Middle Last [Suffix])
                'name' => $cleanString($displayName),
                'birthdate' => $r->birthdate ?? $user?->birthdate,
                'sex' => $cleanString($r->sex ?? $user?->sex ?? ''),
                'civil_status' => $cleanString($r->civil_status ?? $user?->civil_status ?? ''),
                'address' => $cleanString($r->address ?? ($user?->house_number ? ($user->house_number . ' ' . ($user->street ?? '')) : null) ?? ''),
                'contact_number' => $cleanString($contactNumber),
                // model column is valid_id_content (not valid_id_path)
                // 'valid_id_content' => $r->valid_id_content,
                'has_valid_id' => !empty($r->valid_id_content),
                'valid_id_url' => url("/document-requests/{$r->doc_request_id}/valid-id"),
                'valid_id_type' => $r->fk_valid_id_type_id,
                'valid_id_number' => $cleanString($r->valid_id_number ?? ''),

                'applied_processing_fee' => $r->applied_processing_fee,
                'purpose' => $cleanString($r->purpose ?? ''),
                'reason_type' => $cleanString($r->reason_type ?? null ?? ''),
                'pickup_item' => $cleanString($r->pickup_item ?? ''),
                'pickup_location' => $cleanString($r->pickup_location ?? ''),
                'pickup_start' => $r->pickup_start,
                'pickup_end' => $r->pickup_end,
                'person_to_look' => $cleanString($r->person_to_look ?? ''),
                'status' => $cleanString($r->status ?? ''),
                'fk_approver_id' => $r->fk_approver_id,
                'reviewed_at' => $r->reviewed_at,
                'admin_feedback' => $cleanString($r->admin_feedback ?? ''),
                'created_at' => $r->created_at?->toDateTimeString(),
                // Attach the document type object derived from DocumentType model
                'document_type' => $documentTypeObj,
                // Include extra_fields (dynamic fields)
                'extra_fields' => $extraFields,
                // Include attachments
                'attachments' => $attachments,
                // Include full user registration information
                'user_info' => $userInfo,
                'credential_info' => $credentialInfo,
            ];
        });

        return Inertia::render('Admin/Approver/A_Document_Request', [
            'document_requests' => $documentRequests,
            // convenience props in case frontend needs them
            'document_types' => $allDocumentTypes, // full collection (serialize-friendly)
            'document_types_map' => $documentTypesMap, // id => name
        ]);
    }

    // JSON API endpoint for history/filtering
    private function docuReqJson(Request $request)
    {
        $authUser = $request->user();
        $allowedRoles = [3, 9]; // approver role id (3) and system admin role id (9)
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;

        if (! in_array($userRole, $allowedRoles, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $requestedStatus = $request->query('status', 'Pending');
        // Order by created_at DESC to show most recent first (resubmissions will be at top)
        $query = DocumentRequest::with(['documentType', 'user', 'userCredential', 'attachments', 'approver'])
            ->orderBy('created_at', 'desc');

        if (is_string($requestedStatus) && strtolower($requestedStatus) !== 'all') {
            $statusFilter = ucfirst(strtolower($requestedStatus));
            $query->where('status', $statusFilter);
        }

        $rows = $query->get();

        $documentRequests = $rows->map(function ($r) {
            $docType = $r->documentType;
            $user = $r->user;
            $credential = $r->userCredential;

            $documentTypeObj = $docType ? [
                'id' => $docType->document_type_id,
                'name' => $docType->document_name,
                'processing_fee' => isset($docType->processing_fee) ? (float)$docType->processing_fee : null,
            ] : [
                'id' => $r->fk_document_type_id,
                'name' => null,
                'processing_fee' => null,
            ];

            // Name normalization (same as docuReq)
            $rawFirst  = trim((string) ($r->first_name ?? $user?->first_name ?? ''));
            $rawMiddle = trim((string) ($r->middle_name ?? $user?->middle_name ?? ''));
            $rawLast   = trim((string) ($r->last_name ?? $user?->last_name ?? ''));
            $rawSuffix = trim((string) ($r->suffix ?? $user?->suffix ?? ''));

            if ($rawFirst !== '') {
                $parts = preg_split('/\s+/', $rawFirst, -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) > 1 && empty($rawLast)) {
                    $firstCandidate = array_shift($parts);
                    $lastCandidate  = array_pop($parts);
                    $middleCandidate = count($parts) ? implode(' ', $parts) : $rawMiddle;
                    $rawFirst  = $firstCandidate;
                    $rawLast   = $lastCandidate;
                    if (empty($rawMiddle)) {
                        $rawMiddle = $middleCandidate;
                    }
                } elseif (count($parts) > 1 && $rawLast !== '' ) {
                    if (strcasecmp(end($parts), $rawLast) === 0) {
                        $rawFirst = array_shift($parts);
                        if (empty($rawMiddle) && count($parts) > 0) {
                            array_pop($parts);
                            $rawMiddle = count($parts) ? implode(' ', $parts) : $rawMiddle;
                        }
                    }
                }
            }

            if ($rawMiddle !== '' && $rawLast !== '') {
                $pattern = '/\b' . preg_quote($rawLast, '/') . '$/i';
                if (preg_match($pattern, $rawMiddle)) {
                    $rawMiddle = trim(preg_replace($pattern, '', $rawMiddle));
                }
            }

            $normalize = function ($s) {
                $s = trim((string) $s);
                if ($s === '') return '';
                $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                $words = array_map(function ($w) {
                    $w = mb_strtolower($w);
                    $first = mb_strtoupper(mb_substr($w, 0, 1));
                    $rest  = mb_substr($w, 1);
                    return $first . $rest;
                }, $words);
                return implode(' ', $words);
            };

            $first  = $normalize($rawFirst);
            $middle = $normalize($rawMiddle);
            $last   = $normalize($rawLast);
            $suffix = $normalize($rawSuffix);

            $nameParts = array_filter([$first, $middle, $last, $suffix], function ($p) {
                return $p !== '' && $p !== null;
            });
            $displayName = implode(' ', $nameParts);

            $contactNumber = $r->contact_number ?? $credential?->contact_number ?? $user?->contact_number ?? null;

            $userInfo = [
                'user_id' => $user?->user_id,
                'email' => $user?->email,
                'house_number' => $user?->house_number,
                'street' => $user?->street,
                'phase' => $user?->phase,
                'package' => $user?->package,
                'barangay' => $user?->barangay,
                'city' => $user?->city,
                'province' => $user?->province,
                'zip_code' => $user?->zip_code,
                'profile_pic' => $user?->profile_pic,
                'fk_role_id' => $user?->fk_role_id,
                'role_id' => $user?->role_id,
            ];

            $credentialInfo = $credential ? [
                'contact_number' => $credential->contact_number,
                'secondary_contact_number' => $credential->secondary_contact_number,
            ] : null;

            // Get approver information
            $approver = $r->approver;
            $approverInfo = null;
            if ($approver) {
                $approverFirst = trim((string) ($approver->first_name ?? ''));
                $approverLast = trim((string) ($approver->last_name ?? ''));
                
                $normalizeApprover = function ($s) {
                    $s = trim((string) $s);
                    if ($s === '') return '';
                    $words = preg_split('/\s+/', $s, -1, PREG_SPLIT_NO_EMPTY);
                    $words = array_map(function ($w) {
                        $w = mb_strtolower($w);
                        $first = mb_strtoupper(mb_substr($w, 0, 1));
                        $rest  = mb_substr($w, 1);
                        return $first . $rest;
                    }, $words);
                    return implode(' ', $words);
                };
                
                $approverFirst = $normalizeApprover($approverFirst);
                $approverLast = $normalizeApprover($approverLast);
                
                // Format as "First L." or just "First" if no last name
                $approverDisplayName = $approverFirst;
                if ($approverLast) {
                    $approverDisplayName .= ' ' . mb_substr($approverLast, 0, 1) . '.';
                }
                
                $approverInfo = [
                    'user_id' => $approver->user_id,
                    'name' => $approverDisplayName,
                    'first_name' => $approverFirst,
                    'last_name' => $approverLast,
                ];
            }

            // Process attachments - clean file names for JSON encoding
            $attachments = [];
            if ($r->attachments && $r->attachments->count() > 0) {
                $attachments = $r->attachments->map(function ($attachment) {
                    $fileName = $attachment->file_name ?? '';
                    // Clean file name to ensure valid UTF-8
                    if (!mb_check_encoding($fileName, 'UTF-8')) {
                        $fileName = mb_convert_encoding($fileName, 'UTF-8', 'UTF-8');
                    }
                    if (function_exists('iconv')) {
                        $fileName = @iconv('UTF-8', 'UTF-8//IGNORE', $fileName);
                        if ($fileName === false) {
                            $fileName = '';
                        }
                    }
                    
                    return [
                        'attachment_id' => $attachment->attachment_id,
                        'field_name' => $attachment->field_name,
                        'file_name' => $fileName,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'file_url' => $attachment->file_url,
                        'created_at' => $attachment->created_at?->toDateTimeString(),
                    ];
                })->toArray();
            }

            // Get extra_fields (dynamic fields) - clean for JSON encoding
            $extraFields = $r->extra_fields ?? [];
            if (is_string($extraFields)) {
                // If it's a JSON string, decode and clean it
                try {
                    $decoded = json_decode($extraFields, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $extraFields = $this->cleanArrayForJson($decoded);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to decode extra_fields in docuReqJson: ' . $e->getMessage());
                    $extraFields = [];
                }
            } elseif (is_array($extraFields)) {
                $extraFields = $this->cleanArrayForJson($extraFields);
            }

            return [
                'doc_request_id' => $r->doc_request_id,
                'doc_request_ticket' => $r->doc_request_ticket,
                'fk_user_id' => $r->fk_user_id,
                'last_name' => $last,
                'first_name' => $first,
                'middle_name' => $middle,
                'suffix' => $suffix,
                'name' => $displayName,
                'birthdate' => $r->birthdate ?? $user?->birthdate,
                'sex' => $r->sex ?? $user?->sex,
                'civil_status' => $r->civil_status ?? $user?->civil_status,
                'address' => $r->address ?? ($user?->house_number ? ($user->house_number . ' ' . ($user->street ?? '')) : null),
                'contact_number' => $contactNumber,
                'has_valid_id' => !empty($r->valid_id_content),
                'valid_id_url' => url("/document-requests/{$r->doc_request_id}/valid-id"),
                'valid_id_type' => $r->fk_valid_id_type_id,
                'valid_id_number' => $r->valid_id_number,
                'applied_processing_fee' => $r->applied_processing_fee,
                'purpose' => $r->purpose,
                'reason_type' => $r->reason_type ?? null,
                'pickup_item' => $r->pickup_item,
                'pickup_location' => $r->pickup_location,
                'pickup_start' => $r->pickup_start,
                'pickup_end' => $r->pickup_end,
                'person_to_look' => $r->person_to_look,
                'status' => $r->status,
                'fk_approver_id' => $r->fk_approver_id,
                'reviewed_at' => $r->reviewed_at,
                'admin_feedback' => $r->admin_feedback,
                'created_at' => $r->created_at?->toDateTimeString(),
                'document_type' => $documentTypeObj,
                'extra_fields' => $extraFields,
                'attachments' => $attachments,
                'user_info' => $userInfo,
                'credential_info' => $credentialInfo,
                'approver_info' => $approverInfo,
            ];
        });

        // Clean the response data to ensure valid UTF-8
        $cleanedRequests = array_map(function($request) {
            return $this->cleanArrayForJson($request);
        }, $documentRequests->toArray());
        
        return response()->json([
            'document_requests' => $cleanedRequests,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
    }

    private function looksLikeBase64(string $s): bool
    {
        // Strict base64 check (no false positives)
        if ($s === '') return false;
        if (!preg_match('/^[A-Za-z0-9+\/=\r\n]+$/', $s)) return false;
        $decoded = base64_decode($s, true);
        return $decoded !== false;
    }

    /**
     * Safely format datetime for form input
     */
    private function formatDateTimeForForm($value, $format = 'Y-m-d\TH:i')
    {
        if (!$value) {
            return null;
        }

        // If it's already a Carbon instance or DateTime
        if ($value instanceof \Carbon\Carbon || $value instanceof \DateTime) {
            return $value->format($format);
        }

        // If it's a string, try to parse it
        if (is_string($value)) {
            try {
                $carbon = \Carbon\Carbon::parse($value);
                return $carbon->format($format);
            } catch (\Exception $e) {
                // If parsing fails, return the string as-is or null
                return null;
            }
        }

        return null;
    }

    /**
     * Clean array data to ensure valid UTF-8 for JSON encoding
     */
    private function cleanArrayForJson($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'cleanArrayForJson'], $data);
        } elseif (is_string($data)) {
            // Remove invalid UTF-8 characters
            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
            // Use iconv to remove any remaining invalid characters
            if (function_exists('iconv')) {
                $data = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
                if ($data === false) {
                    $data = '';
                }
            }
            return $data;
        }
        return $data;
    }

    /**
     * Return the stored valid_id_content as an inline file (image/pdf/other).
     */
    public function validIdContent(Request $request, $id)
    {
        $r = \App\Models\DocumentRequest::findOrFail($id);

        $content = $r->valid_id_content;
        if (empty($content)) {
            abort(404, 'No valid id uploaded for this request.');
        }

        // If the content is a data URI: data:<mime>;base64,<data>
        if (preg_match('/^data:(.*?);base64,(.*)$/s', $content, $m)) {
            $mime = $m[1] ?: 'application/octet-stream';
            $binary = base64_decode($m[2]);
        } elseif ($this->looksLikeBase64($content)) {
            // Plain base64 stored in DB
            $binary = base64_decode($content);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($binary) ?: 'application/octet-stream';
        } else {
            // Binary blob stored directly
            $binary = $content; // may already be binary
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($binary) ?: 'application/octet-stream';
        }

        // derive extension for filename
        $ext = explode('/', $mime)[1] ?? 'bin';
        $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', ($r->doc_request_ticket ?? "valid_id_{$id}"));

        return response($binary, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => "inline; filename=\"{$safeName}.{$ext}\"",
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }


    public function approve(Request $request, $id)
    {
        \Log::info('=== DOCUMENT REQUEST APPROVE METHOD CALLED ===', [
            'doc_request_id' => $id,
            'timestamp' => now()->toDateTimeString(),
        ]);

        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'pickup_item'     => ['nullable', 'string', 'max:255'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'pickup_start'    => ['nullable', 'date_format:Y-m-d H:i:s'],
            'pickup_end'      => ['nullable', 'date_format:Y-m-d H:i:s'],
            'person_to_look'  => ['nullable', 'string', 'max:255'],
            'status'          => ['required', 'string', 'in:Approved,Rejected,Pending'],
            'fk_approver_id'  => ['nullable'],
            'reviewed_at'     => ['nullable', 'date_format:Y-m-d H:i:s'],
            'admin_feedback'  => ['nullable', 'string'],
        ]);

        // Load document request with all necessary relationships for document generation
        $doc = DocumentRequest::with(['documentType', 'user', 'attachments', 'approver'])
            ->where('doc_request_id', $id)
            ->firstOrFail();

        $update = [
            'pickup_item'     => $data['pickup_item'] ?? $doc->pickup_item,
            'pickup_location' => $data['pickup_location'] ?? $doc->pickup_location,
            'pickup_start'    => $data['pickup_start'] ?? $doc->pickup_start,
            'pickup_end'      => $data['pickup_end'] ?? $doc->pickup_end,
            'person_to_look'  => $data['person_to_look'] ?? $doc->person_to_look,
            'status'          => $data['status'],
            'fk_approver_id'  => $data['fk_approver_id'] ?? $authUser->getKey(),
            'reviewed_at'     => $data['reviewed_at'] ?? Carbon::now()->toDateTimeString(),
            'admin_feedback'  => $data['admin_feedback'] ?? $doc->admin_feedback,
        ];

        DB::transaction(function () use ($doc, $update, $data) {
            $doc->update($update);
            
            // Create notification for the user
            $userId = $doc->fk_user_id;
            $documentType = $doc->documentType?->document_name ?? 'document';
            $ticket = $doc->doc_request_ticket ?? 'N/A';
            
            if ($data['status'] === 'Approved') {
                $notificationMessage = "Your {$documentType} request (Ticket: {$ticket}) has been APPROVED.";
                if (!empty($data['admin_feedback'])) {
                    $notificationMessage .= " " . substr($data['admin_feedback'], 0, 100);
                }
            } elseif ($data['status'] === 'Rejected') {
                $notificationMessage = "Your {$documentType} request (Ticket: {$ticket}) has been REJECTED.";
                $feedback = $data['admin_feedback'] ?? null;
                if (!empty($feedback)) {
                    $notificationMessage .= " Reason: " . substr($feedback, 0, 100);
                }
            } else {
                $notificationMessage = null;
            }
            
            if ($notificationMessage && $userId) {
                DB::table('notifications')->insert([
                    'fk_user_id' => $userId,
                    'message' => $notificationMessage,
                    'notification_type' => 'DocumentRequest',
                    'notification_reference_id' => $doc->doc_request_id,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }
        });

        // Reload with all necessary relationships for document generation
        $doc = DocumentRequest::with(['documentType', 'user', 'attachments', 'approver'])
            ->where('doc_request_id', $id)
            ->firstOrFail();

        // Generate document if approved
        $documentData = null;
        $documentGenerationError = null;
        if ($data['status'] === 'Approved') {
            try {
                $documentService = new DocumentGenerationService();
                $documentData = $documentService->generateDocument($doc);
                
                \Log::info('Document generated successfully', [
                    'doc_request_id' => $doc->doc_request_id,
                    'docx_path' => $documentData['docx_path'] ?? null,
                    'pdf_path' => $documentData['pdf_path'] ?? null,
                    'docx_url' => $documentData['docx_url'] ?? null,
                ]);
            } catch (\Exception $e) {
                $documentGenerationError = $e->getMessage();
                \Log::error('Failed to generate document: ' . $e->getMessage(), [
                    'doc_request_id' => $doc->doc_request_id,
                    'error' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                
                // Don't fail the approval if document generation fails
                // Just log the error and continue - approval is more important than document generation
            }
        }

        \Log::info('Transaction completed, preparing SMS notification for document request', [
            'doc_request_id' => $id,
            'status' => $data['status'],
        ]);

        // Send SMS notification based on status (outside transaction)
        try {
            // Load user and credential relationships
            $doc = DocumentRequest::with(['user', 'user.credential'])->findOrFail($id);
            $user = $doc->user;
            $credential = $user?->credential;
            
            // Get phone number - try document request contact_number first, then credential, then user
            $phoneNumber = $doc->contact_number ?? $credential?->contact_number ?? $credential?->secondary_contact_number ?? null;
            
            \Log::info('Document request status SMS attempt', [
                'doc_request_id' => $id,
                'status' => $data['status'],
                'has_user' => !empty($user),
                'has_credential' => !empty($credential),
                'has_phone' => !empty($phoneNumber),
                'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
            ]);
            
            if ($phoneNumber && $user) {
                $fullName = trim(implode(' ', array_filter([
                    $user->first_name ?? $doc->first_name ?? '',
                    $user->middle_name ?? $doc->middle_name ?? '',
                    $user->last_name ?? $doc->last_name ?? '',
                    $user->suffix ?? $doc->suffix ?? '',
                ]))) ?: 'User';
                
                $ticket = $doc->doc_request_ticket ?? 'N/A';
                $documentType = $doc->documentType?->document_name ?? 'document';
                
                if ($data['status'] === 'Approved') {
                    $message = "Hello {$fullName}, your {$documentType} request (Ticket: {$ticket}) has been APPROVED.";
                    if (!empty($data['admin_feedback'])) {
                        $message .= " " . substr($data['admin_feedback'], 0, 100);
                    }
                    // Full pickup details from approvers
                    $pickupParts = [];
                    if (!empty($doc->pickup_item)) {
                        $pickupParts[] = 'Item: ' . trim($doc->pickup_item);
                    }
                    if (!empty($doc->pickup_location)) {
                        $pickupParts[] = 'Location: ' . trim($doc->pickup_location);
                    }
                    if ($doc->pickup_start) {
                        $start = $doc->pickup_start instanceof Carbon
                            ? $doc->pickup_start
                            : Carbon::parse($doc->pickup_start);
                        $pickupParts[] = 'Date/Time: ' . $start->format('M j, Y, g:i A');
                    }
                    if ($doc->pickup_end) {
                        $end = $doc->pickup_end instanceof Carbon
                            ? $doc->pickup_end
                            : Carbon::parse($doc->pickup_end);
                        $pickupParts[] = 'Until: ' . $end->format('M j, Y, g:i A');
                    }
                    if (!empty($doc->person_to_look)) {
                        $pickupParts[] = 'Ask for: ' . trim($doc->person_to_look);
                    }
                    if (!empty($pickupParts)) {
                        $message .= ' Pickup: ' . implode('. ', $pickupParts);
                    }
                    $message .= " Thank you!";
                } elseif ($data['status'] === 'Rejected') {
                    $message = "Hello {$fullName}, your {$documentType} request (Ticket: {$ticket}) has been REJECTED.";
                    if (!empty($data['admin_feedback'])) {
                        $message .= " Reason: " . substr($data['admin_feedback'], 0, 100);
                    }
                    $message .= " Please contact the barangay office for more information. Thank you.";
                } else {
                    // Status is Pending or other - skip SMS
                    $message = null;
                }
                
                if ($message) {
                    \Log::info('Calling SMS service for document request status', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message,
                    ]);
                    
                    $smsService = new OtpService();
                    \Log::info('About to call sendSms method for document request', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'status' => $data['status'],
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned for document request', [
                        'result' => $smsResult,
                        'status' => $data['status'],
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send document request status SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                            'doc_request_id' => $id,
                            'status' => $data['status'],
                        ]);
                    }
                }
            } else {
                \Log::warning('No phone number or user available for document request status SMS', [
                    'doc_request_id' => $id,
                    'has_user' => !empty($user),
                    'has_phone' => !empty($phoneNumber),
                    'status' => $data['status'],
                ]);
            }
        } catch (\Throwable $smsEx) {
            // Don't fail the approval/rejection if SMS fails
            \Log::error('SMS notification error during document request status change', [
                'error' => $smsEx->getMessage(),
                'trace' => $smsEx->getTraceAsString(),
                'doc_request_id' => $id,
                'status' => $data['status'] ?? 'unknown',
            ]);
        }

        $responseData = [
            'doc_request_id'  => $doc->doc_request_id,
            'doc_request_ticket' => $doc->doc_request_ticket,
            'status' => $doc->status,
            'pickup_item' => $doc->pickup_item,
            'pickup_location' => $doc->pickup_location,
            'pickup_start' => $doc->pickup_start,
            'pickup_end' => $doc->pickup_end,
            'person_to_look' => $doc->person_to_look,
            'fk_approver_id' => $doc->fk_approver_id,
            'reviewed_at' => $doc->reviewed_at,
            'admin_feedback' => $doc->admin_feedback,
            'updated_at' => $doc->updated_at?->toDateTimeString(),
        ];

        // Add document URLs if document was generated
        if ($documentData) {
            $responseData['document_url'] = $documentData['pdf_url'];
            $responseData['document_docx_url'] = $documentData['docx_url'];
            // Prefer DOCX filename since we want to download DOCX by default
            $responseData['filename'] = $documentData['docx_filename'] ?? $documentData['pdf_filename'];
            $responseData['docx_filename'] = $documentData['docx_filename'];
            $responseData['pdf_filename'] = $documentData['pdf_filename'];
            $responseData['document_generated'] = true;
        } else {
            $responseData['document_generated'] = false;
            if ($documentGenerationError) {
                $responseData['document_generation_error'] = $documentGenerationError;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully.',
            'data' => $responseData,
        ]);
    }

    public function reject(Request $request, $id)
    {
        \Log::info('=== DOCUMENT REQUEST REJECT METHOD CALLED ===', [
            'doc_request_id' => $id,
            'timestamp' => now()->toDateTimeString(),
        ]);

        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'status'         => ['required', 'string', 'in:Approved,Rejected,Pending'],
            'fk_approver_id' => ['nullable'],
            'reviewed_at'    => ['nullable', 'date_format:Y-m-d H:i:s'],
            'rejection_reason' => ['nullable', 'string'],
            'admin_feedback'  => ['nullable', 'string'],
            'incorrect_fields' => ['nullable', 'array'],
            'incorrect_fields.*' => ['nullable', 'string'],
        ]);

        try {
            $doc = DocumentRequest::where('doc_request_id', $id)->firstOrFail();

            // Use rejection_reason or admin_feedback, prioritizing rejection_reason
            $feedback = $data['rejection_reason'] ?? $data['admin_feedback'] ?? null;

            $update = [
                'status'         => $data['status'],
                'fk_approver_id' => $data['fk_approver_id'] ?? $authUser->getKey(),
                'reviewed_at'    => $data['reviewed_at'] ?? Carbon::now()->toDateTimeString(),
                'admin_feedback' => $feedback,
                'incorrect_fields' => $data['incorrect_fields'] ?? null,
            ];

            \Log::info('Storing incorrect_fields in reject method', [
                'doc_request_id' => $id,
                'incorrect_fields_received' => $data['incorrect_fields'] ?? null,
                'incorrect_fields_type' => gettype($data['incorrect_fields'] ?? null),
                'incorrect_fields_is_array' => is_array($data['incorrect_fields'] ?? null),
            ]);

            DB::transaction(function () use ($doc, $update, $feedback, $id) {
                $doc->update($update);
                
                // Refresh the model to get the latest data
                $doc->refresh();
                
                \Log::info('After update - verifying incorrect_fields stored', [
                    'doc_request_id' => $id,
                    'incorrect_fields_received' => $update['incorrect_fields'] ?? null,
                    'incorrect_fields_stored' => $doc->incorrect_fields,
                    'incorrect_fields_type' => gettype($doc->incorrect_fields),
                    'incorrect_fields_is_array' => is_array($doc->incorrect_fields),
                    'full_doc_incorrect_fields' => $doc->getAttributes()['incorrect_fields'] ?? 'not in attributes',
                ]);
                
                // Create notification for the user
                $userId = $doc->fk_user_id;
                $documentType = $doc->documentType?->document_name ?? 'document';
                $ticket = $doc->doc_request_ticket ?? 'N/A';
                
                $notificationMessage = "Your {$documentType} request (Ticket: {$ticket}) has been REJECTED.";
                if (!empty($feedback)) {
                    $notificationMessage .= " Reason: " . substr($feedback, 0, 100);
                }
                $notificationMessage .= " You can fix your uploaded request fields and resubmit your request through the website.";
                
                if ($userId) {
                    DB::table('notifications')->insert([
                        'fk_user_id' => $userId,
                        'message' => $notificationMessage,
                        'notification_type' => 'DocumentRequest',
                        'notification_reference_id' => $doc->doc_request_id,
                        'is_read' => false,
                        'created_at' => now(),
                    ]);
                }
            });

            \Log::info('Transaction completed, preparing SMS notification for document request rejection', [
                'doc_request_id' => $id,
            ]);

            // Send SMS notification for rejection (outside transaction)
            try {
                // Load user and credential relationships
                $doc = DocumentRequest::with(['user', 'user.credential'])->findOrFail($id);
                $user = $doc->user;
                $credential = $user?->credential;
                
                // Get phone number - try document request contact_number first, then credential, then user
                $phoneNumber = $doc->contact_number ?? $credential?->contact_number ?? $credential?->secondary_contact_number ?? null;
                
                \Log::info('Document request rejection SMS attempt', [
                    'doc_request_id' => $id,
                    'has_user' => !empty($user),
                    'has_credential' => !empty($credential),
                    'has_phone' => !empty($phoneNumber),
                    'phone' => $phoneNumber ? substr($phoneNumber, 0, 4) . '****' : null,
                ]);
                
                if ($phoneNumber && $user) {
                    $fullName = trim(implode(' ', array_filter([
                        $user->first_name ?? $doc->first_name ?? '',
                        $user->middle_name ?? $doc->middle_name ?? '',
                        $user->last_name ?? $doc->last_name ?? '',
                        $user->suffix ?? $doc->suffix ?? '',
                    ]))) ?: 'User';
                    
                    $ticket = $doc->doc_request_ticket ?? 'N/A';
                    $documentType = $doc->documentType?->document_name ?? 'document';
                    $message = "Hello {$fullName}, your {$documentType} request (Ticket: {$ticket}) has been REJECTED.";
                    if (!empty($feedback)) {
                        $message .= " Reason: " . substr($feedback, 0, 100);
                    }
                    $message .= " You can fix your uploaded request fields and resubmit your request through the website. Please contact the barangay office for more information. Thank you.";
                    
                    \Log::info('Calling SMS service for document request rejection', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                        'message_length' => strlen($message),
                        'message_preview' => substr($message, 0, 50) . '...',
                        'full_message' => $message,
                    ]);
                    
                    $smsService = new OtpService();
                    \Log::info('About to call sendSms method for document request rejection', [
                        'phone' => substr($phoneNumber, 0, 4) . '****',
                    ]);
                    $smsResult = $smsService->sendSms($phoneNumber, $message);
                    \Log::info('sendSms method returned for document request rejection', [
                        'result' => $smsResult,
                    ]);
                    
                    if (!$smsResult['success']) {
                        \Log::warning('Failed to send document request rejection SMS', [
                            'phone' => substr($phoneNumber, 0, 4) . '****',
                            'error' => $smsResult['message'] ?? 'Unknown error',
                            'doc_request_id' => $id,
                        ]);
                    }
                } else {
                    \Log::warning('No phone number or user available for document request rejection SMS', [
                        'doc_request_id' => $id,
                        'has_user' => !empty($user),
                        'has_phone' => !empty($phoneNumber),
                    ]);
                }
            } catch (\Throwable $smsEx) {
                // Don't fail the rejection if SMS fails
                \Log::error('SMS notification error during document request rejection', [
                    'error' => $smsEx->getMessage(),
                    'trace' => $smsEx->getTraceAsString(),
                    'doc_request_id' => $id,
                ]);
            }

            // Reload the document request to get updated data
            $doc = DocumentRequest::with(['documentType', 'user'])->findOrFail($id);

            $responseData = [
                'doc_request_id'  => $doc->doc_request_id,
                'doc_request_ticket' => $doc->doc_request_ticket,
                'status' => $doc->status,
                'fk_approver_id' => $doc->fk_approver_id,
                'reviewed_at' => $doc->reviewed_at,
                'admin_feedback' => $doc->admin_feedback,
                'incorrect_fields' => $doc->incorrect_fields,
                'updated_at' => $doc->updated_at?->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully.',
                'data' => $responseData,
            ]);
        } catch (\Throwable $ex) {
            \Log::error('Reject exception: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'doc_request_id' => $id,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to reject the request.',
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Show appeal form for rejected request
     * Automatically resets status to Pending when accessed
     */
    public function appealForm(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser) {
            return redirect()->route('login');
        }

        $doc = DocumentRequest::with(['documentType', 'attachments', 'user'])
            ->where('doc_request_id', $id)
            ->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($doc->fk_user_id != $userId) {
            abort(403, 'Unauthorized');
        }

        // Check if request is rejected
        if (strtoupper($doc->status) !== 'REJECTED') {
            return redirect()->back()->with('error', 'Only rejected requests can be appealed.');
        }

        // Log incorrect_fields BEFORE resetting status
        \Log::info('appealForm - BEFORE status reset', [
            'doc_request_id' => $id,
            'incorrect_fields_before' => $doc->incorrect_fields,
            'incorrect_fields_type' => gettype($doc->incorrect_fields),
            'incorrect_fields_is_array' => is_array($doc->incorrect_fields),
        ]);

        // Automatically reset status to Pending when accessing appeal form
        // IMPORTANT: Keep incorrect_fields so user knows which fields to fix
        $doc->status = 'Pending';
        $doc->fk_approver_id = null;
        $doc->reviewed_at = null;
        // DO NOT clear incorrect_fields here - we need them to know which fields are editable
        $doc->save();

        // Log incorrect_fields AFTER resetting status
        \Log::info('appealForm - AFTER status reset', [
            'doc_request_id' => $id,
            'incorrect_fields_after' => $doc->fresh()->incorrect_fields,
            'incorrect_fields_type' => gettype($doc->fresh()->incorrect_fields),
            'incorrect_fields_is_array' => is_array($doc->fresh()->incorrect_fields),
        ]);

        // Get valid ID type name if exists
        $validIdTypeName = null;
        if ($doc->fk_valid_id_type_id) {
            $validIdType = \App\Models\ValidIdType::find($doc->fk_valid_id_type_id);
            $validIdTypeName = $validIdType?->valid_id_type_name ?? null;
        }

        // Prepare request data for editing
        $requestData = [
            'doc_request_id' => $doc->doc_request_id,
            'doc_request_ticket' => $doc->doc_request_ticket,
            'document_type' => $doc->documentType ? [
                'id' => $doc->documentType->document_type_id,
                'name' => $doc->documentType->document_name,
                'processing_fee' => $doc->documentType->processing_fee ?? null,
            ] : null,
            'document_name' => $doc->documentType?->document_name ?? null,
            'fk_document_type_id' => $doc->fk_document_type_id,
            'last_name' => $doc->last_name,
            'first_name' => $doc->first_name,
            'middle_name' => $doc->middle_name,
            'suffix' => $doc->suffix,
            'house_number' => $doc->house_number,
            'phase' => $doc->phase,
            'package' => $doc->package,
            'birthdate' => $this->formatDateTimeForForm($doc->birthdate, 'Y-m-d'),
            'is_requestor_minor' => $doc->is_requestor_minor,
            'sex' => $doc->sex,
            'civil_status' => $doc->civil_status,
            'contact_number' => $doc->contact_number,
            'email' => $doc->email,
            'purpose' => $doc->purpose,
            'reason_type' => $doc->reason_type,
            'id_type' => $validIdTypeName,
            'fk_valid_id_type_id' => $doc->fk_valid_id_type_id,
            'valid_id_number' => $doc->valid_id_number,
            'pickup_item' => $doc->pickup_item,
            'pickup_location' => $doc->pickup_location,
            'pickup_start' => $this->formatDateTimeForForm($doc->pickup_start, 'Y-m-d\TH:i'),
            'pickup_end' => $this->formatDateTimeForForm($doc->pickup_end, 'Y-m-d\TH:i'),
            'person_to_look' => $doc->person_to_look,
            'extra_fields' => $doc->extra_fields ?? [],
            'attachments' => $doc->attachments->map(function ($att) {
                return [
                    'id' => $att->attachment_id,
                    'field_name' => $att->field_name,
                    'file_name' => $att->file_name,
                    'file_path' => $att->file_path,
                    'file_type' => $att->file_type,
                    'url' => $att->file_path ? Storage::url($att->file_path) : null,
                ];
            })->toArray(),
            'admin_feedback' => $doc->admin_feedback,
            'incorrect_fields' => $doc->incorrect_fields ?? [],
        ];

        // Get fresh data to ensure we have the latest incorrect_fields
        $doc->refresh();
        $requestData['incorrect_fields'] = $doc->incorrect_fields ?? [];

        \Log::info('Appeal form accessed, status reset to Pending', [
            'doc_request_id' => $id,
            'user_id' => $userId,
            'incorrect_fields_in_requestData' => $requestData['incorrect_fields'],
            'incorrect_fields_type' => gettype($requestData['incorrect_fields']),
            'incorrect_fields_is_array' => is_array($requestData['incorrect_fields']),
            'incorrect_fields_raw_from_db' => $doc->incorrect_fields,
            'incorrect_fields_raw_type' => gettype($doc->incorrect_fields),
        ]);

        // If this is an AJAX/JSON request, return JSON instead of Inertia page
        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'requestData' => $requestData,
            ]);
        }

        return Inertia::render('User/Resident/R_Document_Request_Appeal', [
            'requestData' => $requestData,
        ]);
    }

    /**
     * Handle appeal submission - reset status to Pending and allow editing
     */
    public function appeal(Request $request, $id)
    {
        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $doc = DocumentRequest::where('doc_request_id', $id)->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($doc->fk_user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request is rejected
        if (strtoupper($doc->status) !== 'REJECTED') {
            return response()->json(['error' => 'Only rejected requests can be appealed.'], 400);
        }

        // Log incorrect_fields BEFORE resetting status
        \Log::info('appeal POST - BEFORE status reset', [
            'doc_request_id' => $id,
            'incorrect_fields_before' => $doc->incorrect_fields,
            'incorrect_fields_type' => gettype($doc->incorrect_fields),
            'incorrect_fields_is_array' => is_array($doc->incorrect_fields),
        ]);

        try {
            // Reset status to Pending and clear review information
            // IMPORTANT: Keep incorrect_fields so user knows which fields to fix
            $doc->status = 'Pending';
            $doc->fk_approver_id = null;
            $doc->reviewed_at = null;
            // Keep admin_feedback for reference but clear it if user wants to resubmit
            // Actually, let's keep it so user can see what was wrong
            // $doc->admin_feedback = null;
            // DO NOT clear incorrect_fields here - we need them to know which fields are editable
            
            $doc->save();

            // Log incorrect_fields AFTER resetting status
            \Log::info('appeal POST - AFTER status reset', [
                'doc_request_id' => $id,
                'incorrect_fields_after' => $doc->fresh()->incorrect_fields,
                'incorrect_fields_type' => gettype($doc->fresh()->incorrect_fields),
                'incorrect_fields_is_array' => is_array($doc->fresh()->incorrect_fields),
            ]);

            \Log::info('Document request appealed', [
                'doc_request_id' => $id,
                'user_id' => $userId,
                'ticket' => $doc->doc_request_ticket,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request has been reset to pending. You can now edit and resubmit it.',
                'data' => [
                    'doc_request_id' => $doc->doc_request_id,
                    'status' => $doc->status,
                ],
            ]);
        } catch (\Throwable $ex) {
            \Log::error('Appeal exception: ' . $ex->getMessage());
            return response()->json(['error' => 'Failed to appeal the request.'], 500);
        }
    }

    /**
     * Update an existing document request (for appeals/resubmissions)
     */
    public function update(Request $request, $id)
    {
        \Log::info('=== DOCUMENT REQUEST UPDATE CALLED ===', [
            'doc_request_id' => $id,
            'user_id' => Auth::id(),
        ]);

        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $doc = DocumentRequest::where('doc_request_id', $id)->firstOrFail();

        // Check if user owns this request
        $userId = $authUser->user_id ?? $authUser->id ?? null;
        if ($doc->fk_user_id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if request is Pending (should be after appeal)
        if (strtoupper($doc->status) !== 'PENDING') {
            return response()->json(['error' => 'Only pending requests can be updated. Please appeal your rejected request first.'], 400);
        }

        // Use the same validation as store method
        try {
            $request->validate([
                'fk_document_type_id' => ['nullable', 'integer', 'exists:document_types,document_type_id'],
                'document_name' => ['nullable', 'string', 'max:150'],
                'last_name' => ['nullable', 'string', 'max:50'],
                'first_name' => ['nullable', 'string', 'max:50'],
                'middle_name' => ['nullable', 'string', 'max:20'],
                'suffix' => ['nullable', 'string', 'max:20'],
                'birthdate' => ['nullable', 'date'],
                'sex' => ['nullable', Rule::in(['Male','Female'])],
                'civil_status' => ['nullable', Rule::in(['Single','Married','Widowed','Separated'])],
                'contact_number' => ['nullable', 'string', 'max:50'],
                'purpose' => ['nullable', 'string'],
                'reason_type' => ['nullable', 'string', 'max:100'],
                'id_type' => ['nullable', 'string', 'max:100'],
                'valid_id_content' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'],
                'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'],
                'id_back'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'],
                'valid_id_number' => ['nullable', 'string', 'max:255'],
                'id_number' => ['nullable', 'string', 'max:255'],
                'pickup_item' => ['nullable', 'string', 'max:255'],
                'pickup_location' => ['nullable', 'string', 'max:255'],
                'pickup_start' => ['nullable', 'date'],
                'pickup_end' => ['nullable', 'date'],
                'person_to_look' => ['nullable', 'string', 'max:255'],
                'extra_fields' => ['nullable', 'array'],
                'extra_fields.*' => ['nullable'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update basic fields (similar to store method logic)
            $updateData = [];
            
            if ($request->filled('purpose')) {
                $updateData['purpose'] = $request->input('purpose');
            }
            if ($request->filled('last_name')) {
                $updateData['last_name'] = $request->input('last_name');
            }
            if ($request->filled('first_name')) {
                $updateData['first_name'] = $request->input('first_name');
            }
            if ($request->filled('middle_name')) {
                $updateData['middle_name'] = $request->input('middle_name');
            }
            if ($request->filled('suffix')) {
                $updateData['suffix'] = $request->input('suffix');
            }
            if ($request->filled('contact_number')) {
                $updateData['contact_number'] = $request->input('contact_number');
            }
            if ($request->filled('valid_id_number')) {
                $updateData['valid_id_number'] = $request->input('valid_id_number');
            }
            if ($request->filled('pickup_item')) {
                $updateData['pickup_item'] = $request->input('pickup_item');
            }
            if ($request->filled('pickup_location')) {
                $updateData['pickup_location'] = $request->input('pickup_location');
            }
            if ($request->filled('person_to_look')) {
                $updateData['person_to_look'] = $request->input('person_to_look');
            }

            // Handle extra_fields - merge with existing, only update provided fields
            if ($request->has('extra_fields')) {
                $newExtraFields = $request->input('extra_fields', []);
                
                // Ensure existing fields are properly decoded if they're stored as JSON string
                $existingExtraFields = $doc->extra_fields ?? [];
                if (is_string($existingExtraFields)) {
                    try {
                        $decoded = json_decode($existingExtraFields, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $existingExtraFields = $decoded;
                        } else {
                            $existingExtraFields = [];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to decode existing extra_fields during update: ' . $e->getMessage());
                        $existingExtraFields = [];
                    }
                }
                if (!is_array($existingExtraFields)) {
                    $existingExtraFields = [];
                }
                
                // Merge: keep ALL existing fields, update only the ones provided in newExtraFields
                // This ensures that fields that weren't changed are preserved
                $mergedExtraFields = array_merge($existingExtraFields, $newExtraFields);
                
                // Remove null/empty string values that might overwrite existing valid values
                // Only remove if the new value is explicitly null or empty string
                foreach ($mergedExtraFields as $key => $value) {
                    // If newExtraFields has this key and value is null/empty, but existing had a value, keep existing
                    if (isset($newExtraFields[$key]) && 
                        ($newExtraFields[$key] === null || $newExtraFields[$key] === '') &&
                        isset($existingExtraFields[$key]) && 
                        $existingExtraFields[$key] !== null && 
                        $existingExtraFields[$key] !== '') {
                        // Keep the existing value if new value is empty/null
                        $mergedExtraFields[$key] = $existingExtraFields[$key];
                    }
                }
                
                $updateData['extra_fields'] = $mergedExtraFields;
            }

            // Handle file uploads - update valid_id_content if new file provided
            if ($request->hasFile('valid_id_content')) {
                $file = $request->file('valid_id_content');
                $updateData['valid_id_content'] = file_get_contents($file->getRealPath(), FILE_BINARY);
            } elseif ($request->hasFile('id_front')) {
                $file = $request->file('id_front');
                $updateData['valid_id_content'] = file_get_contents($file->getRealPath(), FILE_BINARY);
            }

            // Clear incorrect_fields when resubmitting (user has fixed the issues)
            // Keep admin_feedback to identify this as a resubmission
            $updateData['incorrect_fields'] = null;
            
            // Update created_at to current time so resubmission brings the request back to the top
            $updateData['created_at'] = now();
            
            // Update the document request
            $doc->update($updateData);

            // Handle new file attachments in extra_fields
            if ($request->has('extra_fields')) {
                $extraFields = $request->input('extra_fields', []);
                foreach ($extraFields as $fieldName => $value) {
                    if ($request->hasFile("extra_fields.{$fieldName}") || $request->hasFile("extra_fields[{$fieldName}]")) {
                        $file = $request->file("extra_fields.{$fieldName}") ?? $request->file("extra_fields[{$fieldName}]");
                        if ($file && $file->isValid()) {
                            $path = $file->store('document_request_attachments', 'public');
                            
                            // Delete old attachment for this field if exists
                            DocumentRequestAttachment::where('fk_doc_request_id', $doc->doc_request_id)
                                ->where('field_name', $fieldName)
                                ->delete();
                            
                            // Create new attachment
                            DocumentRequestAttachment::create([
                                'fk_doc_request_id' => $doc->doc_request_id,
                                'field_name' => $fieldName,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $file->getMimeType(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            \Log::info('Document request updated successfully', [
                'doc_request_id' => $id,
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully. Your request has been resubmitted for review.',
                'data' => [
                    'doc_request_id' => $doc->doc_request_id,
                    'doc_request_ticket' => $doc->doc_request_ticket,
                    'status' => $doc->status,
                ],
            ]);
        } catch (\Throwable $ex) {
            DB::rollBack();
            \Log::error('Update exception: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update the request: ' . $ex->getMessage()], 500);
        }
    }

    /**
     * Download generated document
     */
    public function download(Request $request, $id, $format = 'pdf')
    {
        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Validate format
        if (!in_array($format, ['pdf', 'docx'])) {
            return response()->json(['error' => 'Invalid format. Use pdf or docx'], 400);
        }

        $doc = DocumentRequest::where('doc_request_id', $id)->firstOrFail();

        // Check if user has permission (approver or request owner)
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;
        $isApprover = in_array($userRole, [3, 9]); // Approver role IDs
        $isOwner = ($doc->fk_user_id == ($authUser->user_id ?? $authUser->id));

        if (!$isApprover && !$isOwner) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if document exists
        $documentService = new DocumentGenerationService();
        $filePath = $documentService->getDocumentPath($doc, $format);

        // If PDF doesn't exist but DOCX does, serve DOCX instead
        $isFallbackToDocx = false;
        if ((!$filePath || !file_exists($filePath)) && $format === 'pdf') {
            $docxPath = $documentService->getDocumentPath($doc, 'docx');
            if ($docxPath && file_exists($docxPath)) {
                \Log::info('PDF not found, serving DOCX instead', [
                    'doc_request_id' => $id,
                ]);
                $filePath = $docxPath;
                $isFallbackToDocx = true;
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return response()->json(['error' => 'Document not found. Please regenerate the document.'], 404);
        }

        // Determine MIME type - use actual file type if we're serving DOCX for PDF request
        if ($isFallbackToDocx) {
            $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            $filename = $documentService->generateFileName($doc, 'docx');
        } else {
            $mimeType = $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            $filename = $documentService->generateFileName($doc, $format);
        }

        // For DOCX files, force download instead of inline display
        // PDFs can be displayed inline, but DOCX should download to open in Word
        $contentDisposition = ($format === 'docx' || $isFallbackToDocx) 
            ? 'attachment; filename="' . $filename . '"'
            : 'inline; filename="' . $filename . '"';

        // Serve file with proper headers
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $contentDisposition,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $fk_user_id = $user->user_id ?? Auth::id();

        $credential = UserCredential::where('fk_user_id', $fk_user_id)->first();

        return Inertia::render('Document/Create', [
            'user' => $user,
            'userCredential' => $credential ? $credential->only(['contact_number', 'secondary_contact_number']) : null,
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('=== DOCUMENT REQUEST STORE CALLED ===', [
            'user_id' => Auth::id(),
            'has_file' => $request->hasFile('valid_id_content'),
            'input_keys' => array_keys($request->all()),
            'all_files_count' => count($request->allFiles()),
            'all_files_keys' => array_keys($request->allFiles()),
        ]);
        
        try {
            $userId = Auth::id();
            if ($userId) {
                // Check if user is restricted from document requests
                $restriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
                if ($restriction && $restriction->restrict_document_request) {
                    return redirect()->back()->with('error', 'You are restricted from making document requests. Please check your notifications for more information.');
                }
            }

            $request->validate([
            'fk_document_type_id' => ['nullable', 'integer', 'exists:document_types,document_type_id'],
            'document_name' => ['nullable', 'string', 'max:150'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'first_name' => ['nullable', 'string', 'max:50'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'birthdate' => ['nullable', 'date'],
            'sex' => ['nullable', Rule::in(['Male','Female'])],
            'civil_status' => ['nullable', Rule::in(['Single','Married','Widowed','Separated'])],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'purpose' => ['required', 'string'],
            'reason_type' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'max:100'],

            // Accept the uploaded file(s). Limit set to 20MB for better MySQL compatibility
            'valid_id_content' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'], // 20MB
            // keep legacy fields if frontend still sends them
            'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'], // 20MB
            'id_back'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf','max:20480'], // 20MB

            // id number fields
            'valid_id_number' => ['nullable', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:255'],

            'pickup_item'         => ['nullable', 'string', 'max:255'],
            'pickup_location'     => ['nullable', 'string', 'max:255'],
            'pickup_start'        => ['nullable', 'date'],
            'pickup_end'          => ['nullable', 'date'],
            'person_to_look'      => ['nullable', 'string', 'max:255'],
            
            // Dynamic extra_fields - can contain text values and file objects
            // Files in extra_fields come as "extra_fields[fieldName]" in FormData
            'extra_fields' => ['nullable', 'array'],
            'extra_fields.*' => ['nullable'], // Allow any nested values (files or text)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for document request', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            
            // Return JSON for AJAX/axios requests
            if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }

        \Log::info('Validation passed, proceeding with document request creation');

        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $fk_user_id = $user->user_id ?? $user->id ?? Auth::id();

        // Resolve document type id by name if necessary
        $fk_document_type_id = $request->input('fk_document_type_id');
        $documentName = $request->input('document_name');
        
        // Handle Permit types: if document_name is "Permit", check extra_fields for permit_type
        if (empty($fk_document_type_id) && $request->filled('document_name')) {
            // Check if this is a Permit request with a specific permit_type in extra_fields
            $extraFields = $request->input('extra_fields', []);
            $permitType = is_array($extraFields) ? ($extraFields['permit_type'] ?? null) : null;
            
            if ($documentName === 'Permit' && $permitType) {
                // Use the specific permit type (Building Permit or Business Permit) as the document name
                $docType = DocumentType::where('document_name', $permitType)->first();
                if ($docType) {
                    $fk_document_type_id = $docType->document_type_id;
                    \Log::info('Resolved Permit to specific permit type', [
                        'permit_type' => $permitType,
                        'document_type_id' => $fk_document_type_id,
                    ]);
                }
            } else {
                // Standard lookup by document_name
                $docType = DocumentType::where('document_name', $documentName)->first();
                if ($docType) {
                    $fk_document_type_id = $docType->document_type_id;
                }
            }
        }
        
        // Final check: if still empty, log error
        if (empty($fk_document_type_id)) {
            \Log::error('Could not resolve document type ID', [
                'document_name' => $documentName,
                'permit_type' => $permitType ?? null,
                'extra_fields' => $request->input('extra_fields', []),
            ]);
        }
        
        // Check if this specific document type is restricted for the user
        if ($fk_document_type_id && $userId) {
            $userRestriction = \App\Models\UserRestriction::where('user_id', $userId)->first();
            if ($userRestriction) {
                $restrictedDocTypeIds = $userRestriction->restricted_document_types ?? [];
                $allowedDocTypeIds = $userRestriction->allowed_document_types ?? [];
                
                // Check if this document type is restricted
                $isRestricted = in_array($fk_document_type_id, $restrictedDocTypeIds);
                $isAllowed = !empty($allowedDocTypeIds) && in_array($fk_document_type_id, $allowedDocTypeIds);
                
                // If restricted and not explicitly allowed, reject the request
                if ($isRestricted && !$isAllowed) {
                    $docType = DocumentType::find($fk_document_type_id);
                    $docTypeName = $docType ? $docType->document_name : 'this document type';
                    
                    \Log::warning('User attempted to request restricted document type', [
                        'user_id' => $userId,
                        'document_type_id' => $fk_document_type_id,
                        'document_name' => $docTypeName,
                    ]);
                    
                    if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                        return response()->json([
                            'success' => false,
                            'message' => "You are restricted from requesting {$docTypeName}. Please contact the admin for more information.",
                            'errors' => ['fk_document_type_id' => ["You are restricted from requesting {$docTypeName}."]]
                        ], 403);
                    }
                    
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "You are restricted from requesting {$docTypeName}. Please contact the admin for more information.");
                }
            }
        }

        // Contact number priority
        $contactNumber = $request->input('contact_number');
        $credential = $user->credential ?? UserCredential::where('fk_user_id', $fk_user_id)->first();

        if (empty($contactNumber)) {
            if ($credential && !empty($credential->contact_number)) {
                $contactNumber = $credential->contact_number;
            } elseif (!empty($user->contact_number)) {
                $contactNumber = $user->contact_number;
            }
        }

        if (empty($contactNumber)) {
            // Return JSON for AJAX/axios requests
            if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact number is required. Please provide it in the form or add it to your profile.',
                    'errors' => ['contact_number' => ['Contact number is required. Please provide it in the form or add it to your profile.']]
                ], 422);
            }
            
            return back()
                ->withInput()
                ->withErrors(['contact_number' => 'Contact number is required. Please provide it in the form or add it to your profile.']);
        }

        // ===== Read raw bytes for the valid_id_content LONGBLOB column =====
        // IMPORTANT: This is BINARY data, NOT text - never sanitize as UTF-8
        $validIdContentBinary = null;
        
        // Maximum file size: 20MB (reduced for better MySQL compatibility)
        $maxFileSize = 20 * 1024 * 1024; // 20MB in bytes

        // Priority: 'valid_id_content' (sent by frontend) -> 'id_front' -> 'id_back'
        if ($request->hasFile('valid_id_content')) {
            $uploaded = $request->file('valid_id_content');
            if ($uploaded && $uploaded->isValid()) {
                $fileSize = $uploaded->getSize();
                if ($fileSize > $maxFileSize) {
                    // Return JSON for AJAX/axios requests
                    if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.',
                            'errors' => ['valid_id_content' => ['File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']]
                        ], 422);
                    }
                    
                    return back()
                        ->withInput()
                        ->withErrors(['valid_id_content' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']);
                }
                // Read as binary - do NOT treat as UTF-8 string
                $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
            }
        } elseif ($request->hasFile('id_front')) {
            $uploaded = $request->file('id_front');
            if ($uploaded && $uploaded->isValid()) {
                $fileSize = $uploaded->getSize();
                if ($fileSize > $maxFileSize) {
                    // Return JSON for AJAX/axios requests
                    if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.',
                            'errors' => ['id_front' => ['File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']]
                        ], 422);
                    }
                    
                    return back()
                        ->withInput()
                        ->withErrors(['id_front' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']);
                }
                $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
            }
        } elseif ($request->hasFile('id_back')) {
            $uploaded = $request->file('id_back');
            if ($uploaded && $uploaded->isValid()) {
                $fileSize = $uploaded->getSize();
                if ($fileSize > $maxFileSize) {
                    // Return JSON for AJAX/axios requests
                    if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.',
                            'errors' => ['id_back' => ['File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']]
                        ], 422);
                    }
                    
                    return back()
                        ->withInput()
                        ->withErrors(['id_back' => 'File size exceeds maximum allowed size of 20MB. Please compress or resize the image.']);
                }
                $validIdContentBinary = file_get_contents($uploaded->getRealPath(), FILE_BINARY);
            }
        }

        $appliedFee = 0.00;
        if ($fk_document_type_id) {
            $appliedFee = optional(DocumentType::find($fk_document_type_id))->processing_fee ?? 0.00;
        }

        // Temporary random token (replaced inside transaction with sequential ticket)
        $ticket = strtoupper(Str::random(10));
        $docRequest = null;

        // Build the base data array with values we want to populate.
        $data = [
            'doc_request_ticket'     => $ticket,
            'fk_user_id'             => $fk_user_id,
            'fk_document_type_id'    => $fk_document_type_id,
            'last_name'              => $request->input('last_name') ?? $user->last_name ?? null,
            'first_name'             => $request->input('first_name') ?? $user->first_name ?? $user->name ?? null,
            'middle_name'            => $request->input('middle_name') ?? $user->middle_name ?? null,
            'suffix'                 => $request->input('suffix') ?? $user->suffix ?? null,
            'birthdate'              => $request->input('birthdate') ?? $user->birthdate ?? null,
            'sex'                    => $request->input('sex') ?? $user->sex ?? null,
            'civil_status'           => $request->input('civil_status') ?? $user->civil_status ?? null,
            'house_number'           => $request->input('house_number') ?? $user->house_number ?? null,
            'phase'                  => $request->input('phase') ?? $user->phase ?? null,
            'package'                => $request->input('package') ?? $user->package ?? null,
            'address'                => $request->input('address') ?? ($user->house_number ? ($user->house_number . ' ' . ($user->street ?? '')) : null),
            'contact_number'         => $contactNumber,
            'email'                  => $request->input('email') ?? $credential?->email ?? $user->email ?? null,

            // IMPORTANT: put the RAW binary into the data array so it goes into the LONGBLOB column
            'valid_id_content'       => $validIdContentBinary,

            // store ID number (prefer valid_id_number input, fallback to id_number)
            'valid_id_number'        => $request->input('valid_id_number') ?? $request->input('id_number') ?? null,

            'applied_processing_fee' => $appliedFee,
            'purpose'                => $request->input('purpose'),
            'reason_type'            => $request->input('reason_type') ?: null,
            'pickup_item'            => $request->input('pickup_item', 'To be confirmed'),
            'pickup_location'        => $request->input('pickup_location', 'To be confirmed'),
            'pickup_start'           => $request->input('pickup_start') ? $request->input('pickup_start') : now(),
            'pickup_end'             => $request->input('pickup_end') ? $request->input('pickup_end') : now()->addDay(),
            'person_to_look'         => $request->input('person_to_look', 'To be assigned'),
            'status'                 => 'Pending',
        ];

        // if first_name still empty, derive from user->name
        if (empty($data['first_name']) && !empty($user->name)) {
            $parts = preg_split('/\s+/', trim($user->name));
            if (count($parts) === 1) {
                $data['first_name'] = $parts[0];
            } elseif (count($parts) === 2) {
                $data['first_name'] = $parts[0];
                $data['last_name']  = $parts[1];
            } else {
                $data['first_name']  = array_shift($parts);
                $data['last_name']   = array_pop($parts);
                if (empty($data['middle_name'])) {
                    $data['middle_name'] = implode(' ', $parts);
                }
            }
        }

        // Process extra_fields: separate files from text values
        // When using forceFormData, Inertia sends nested objects as "extra_fields[fieldName]"
        $extraFieldsData = [];
        $fileAttachments = [];
        
        // Get all files from the request
        $allFiles = $request->allFiles();
        
        \Log::info('Document Request - All files received:', [
            'file_keys' => array_keys($allFiles),
            'file_count' => count($allFiles),
            'all_input_keys' => array_keys($request->all()),
        ]);
        
        // Check for files in extra_fields structure
        // Files come as "extra_fields[fieldName]" in FormData when nested in an object
        foreach ($allFiles as $key => $file) {
            \Log::info('Processing file key:', [
                'key' => $key, 
                'is_array' => is_array($file),
                'is_uploaded_file' => $file instanceof \Illuminate\Http\UploadedFile,
            ]);
            
            // Handle extra_fields as an array of files (nested structure)
            if ($key === 'extra_fields' && is_array($file)) {
                // extra_fields is an array of files, iterate through them
                foreach ($file as $fieldName => $fieldFile) {
                    if ($fieldFile instanceof \Illuminate\Http\UploadedFile) {
                        $fileAttachments[$fieldName] = $fieldFile;
                        \Log::info('Found extra_fields file (nested array):', [
                            'field_name' => $fieldName,
                            'file_name' => $fieldFile->getClientOriginalName(),
                        ]);
                    } elseif (is_array($fieldFile)) {
                        // Handle array of files for the same field
                        foreach ($fieldFile as $idx => $f) {
                            if ($f instanceof \Illuminate\Http\UploadedFile) {
                                $fileAttachments[$fieldName . '_' . $idx] = $f;
                                \Log::info('Found extra_fields file (nested array with index):', [
                                    'field_name' => $fieldName . '_' . $idx,
                                    'file_name' => $f->getClientOriginalName(),
                                ]);
                            }
                        }
                    }
                }
            }
            // Check if this is an extra_fields file: "extra_fields[fieldName]"
            elseif (preg_match('/^extra_fields\[(.+)\]$/', $key, $matches)) {
                $fieldName = $matches[1];
                $fileAttachments[$fieldName] = is_array($file) ? $file[0] : $file;
                \Log::info('Found extra_fields file (bracket format):', [
                    'field_name' => $fieldName,
                    'file_name' => is_array($file) ? $file[0]->getClientOriginalName() : $file->getClientOriginalName(),
                ]);
            } elseif (strpos($key, 'extra_fields_') === 0) {
                // Alternative format: extra_fields_fieldName
                $fieldName = substr($key, 13); // Remove "extra_fields_" prefix
                $fileAttachments[$fieldName] = is_array($file) ? $file[0] : $file;
                \Log::info('Found extra_fields file (prefix format):', [
                    'field_name' => $fieldName,
                    'file_name' => is_array($file) ? $file[0]->getClientOriginalName() : $file->getClientOriginalName(),
                ]);
            } elseif (!in_array($key, ['valid_id_content', 'id_front', 'id_back', 'document'])) {
                // Also check for direct field names that might be attachment fields
                // Common attachment field names from the frontend
                $attachmentFields = ['2x2_photo', 'photo', 'supporting_documents', 'cedula', 'tax_declaration', 
                                     'income_statement', 'birth_certificate', 'proof_of_residency', 
                                     'lease_contract', 'barangay_clearance', 'dti_registration'];
                if (in_array($key, $attachmentFields)) {
                    $fileAttachments[$key] = is_array($file) ? $file[0] : $file;
                    \Log::info('Found direct attachment field:', [
                        'field_name' => $key,
                        'file_name' => is_array($file) ? $file[0]->getClientOriginalName() : $file->getClientOriginalName(),
                    ]);
                } else {
                    \Log::info('File key not recognized as attachment:', ['key' => $key]);
                }
            }
        }
        
        \Log::info('Document Request - File attachments detected:', [
            'count' => count($fileAttachments),
            'field_names' => array_keys($fileAttachments),
            'file_attachments_details' => array_map(function($file, $fieldName) {
                return [
                    'field_name' => $fieldName,
                    'file_name' => $file instanceof \Illuminate\Http\UploadedFile ? $file->getClientOriginalName() : 'unknown',
                    'file_size' => $file instanceof \Illuminate\Http\UploadedFile ? $file->getSize() : 0,
                    'is_valid' => $file instanceof \Illuminate\Http\UploadedFile ? $file->isValid() : false,
                ];
            }, $fileAttachments, array_keys($fileAttachments)),
        ]);
        
        // Process text values from extra_fields
        // Get the extra_fields array from input (excluding files which are in allFiles)
        $extraFields = $request->input('extra_fields', []);
        
        \Log::info('Document Request - Processing extra_fields:', [
            'extra_fields_type' => gettype($extraFields),
            'extra_fields_raw' => $extraFields,
            'extra_fields_keys' => is_array($extraFields) ? array_keys($extraFields) : [],
            'all_input_keys' => array_keys($request->all()),
            'has_building_type' => isset($extraFields['building_type']),
            'has_building_reg_number' => isset($extraFields['building_reg_number']),
        ]);
        
        if (is_array($extraFields)) {
            foreach ($extraFields as $fieldName => $fieldValue) {
                // Skip if this field is a file (already handled above)
                if (isset($fileAttachments[$fieldName])) {
                    \Log::info("Skipping extra_fields[{$fieldName}] - it's a file attachment");
                    continue;
                }
                
                // It's a text value, store in extra_fields JSON
                // Handle arrays (for checkboxes) and other types
                if (is_array($fieldValue)) {
                    if (!empty($fieldValue)) {
                        // Sanitize array values
                        $extraFieldsData[$fieldName] = $fieldValue;
                        \Log::info("Added extra_fields[{$fieldName}] as array:", ['value' => $fieldValue]);
                    }
                } elseif ($fieldValue !== null && $fieldValue !== '') {
                    $extraFieldsData[$fieldName] = $fieldValue;
                    \Log::info("Added extra_fields[{$fieldName}]:", ['value' => $fieldValue]);
                } else {
                    \Log::info("Skipping extra_fields[{$fieldName}] - empty or null:", ['value' => $fieldValue]);
                }
            }
        }
        
        \Log::info('Document Request - Final extraFieldsData to be saved:', [
            'extraFieldsData' => $extraFieldsData,
            'extraFieldsData_keys' => array_keys($extraFieldsData),
        ]);

        // Log file attachments BEFORE transaction to verify they're detected
        \Log::info('Document Request - BEFORE TRANSACTION - File attachments detected:', [
            'count' => count($fileAttachments),
            'field_names' => array_keys($fileAttachments),
            'file_attachments_details' => array_map(function($file, $fieldName) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    return [
                        'field_name' => $fieldName,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'is_valid' => $file->isValid(),
                    ];
                }
                return ['field_name' => $fieldName, 'type' => gettype($file)];
            }, $fileAttachments, array_keys($fileAttachments)),
        ]);
        
        // Make a copy of fileAttachments to ensure it's captured correctly in the closure
        $fileAttachmentsForTransaction = $fileAttachments;
        
        // Set MySQL max_allowed_packet to handle large file uploads (64MB)
        // This prevents "MySQL server has gone away" errors with large files
        try {
            // Ensure we have a fresh connection
            DB::reconnect();
            
            // Check current max_allowed_packet setting first
            $currentSetting = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
            $currentValue = $currentSetting->Value ?? 0;
            $currentValueMB = round($currentValue / 1024 / 1024, 2);
            
            \Log::info('Current MySQL max_allowed_packet: ' . $currentValue . ' bytes (' . $currentValueMB . ' MB)');
            
            // Only set if it's less than what we need
            if ($currentValue < 67108864) {
                \Log::warning('MySQL max_allowed_packet is too low (' . $currentValueMB . ' MB). Attempting to increase...');
                
                // Try to set GLOBAL first (requires SUPER privilege, but persists)
                try {
                    DB::statement('SET GLOBAL max_allowed_packet = 67108864');
                    \Log::info('Successfully set GLOBAL max_allowed_packet to 64MB');
                } catch (\Exception $globalException) {
                    \Log::warning('Could not set GLOBAL max_allowed_packet (may require SUPER privilege): ' . $globalException->getMessage());
                    
                    // Fall back to SESSION (only affects current connection)
                    try {
                        DB::statement('SET SESSION max_allowed_packet = 67108864');
                        \Log::info('Set SESSION max_allowed_packet to 64MB (session only)');
                    } catch (\Exception $sessionException) {
                        \Log::error('Could not set SESSION max_allowed_packet: ' . $sessionException->getMessage());
                        // Continue anyway - might still work if server default is high enough
                    }
                }
            }
            
            // Set timeouts
            try {
                DB::statement('SET SESSION wait_timeout = 600'); // 10 minutes
                DB::statement('SET SESSION interactive_timeout = 600'); // 10 minutes
            } catch (\Exception $e) {
                \Log::warning('Could not set timeout variables: ' . $e->getMessage());
            }
            
            // Verify the setting was applied
            $verifySetting = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
            if ($verifySetting) {
                $finalValue = $verifySetting->Value ?? 0;
                $finalValueMB = round($finalValue / 1024 / 1024, 2);
                \Log::info('Final MySQL max_allowed_packet: ' . $finalValue . ' bytes (' . $finalValueMB . ' MB)');
                
                if ($finalValue < 16777216) { // Less than 16MB
                    \Log::error('⚠️  CRITICAL: MySQL max_allowed_packet is still too low (' . $finalValueMB . ' MB). File uploads may fail.');
                    \Log::error('Please run: php check_mysql_settings.php to diagnose and fix MySQL configuration.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error setting MySQL session variables: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            // Continue anyway - the server might have a higher default
        }
        
        // Transaction: compute next sequential numeric ticket and create the record.
        // Initialize $docRequest to null before transaction
        $docRequest = null;
        
        try {
            DB::transaction(function () use (&$docRequest, $data, $user, $credential, $extraFieldsData, &$fileAttachmentsForTransaction, $request) {
            $tickets = DB::table('document_requests')
                ->whereRaw("doc_request_ticket REGEXP '[0-9]+'")
                ->lockForUpdate()
                ->pluck('doc_request_ticket');

            $maxNum = 0;
            foreach ($tickets as $t) {
                if (preg_match('/\d+/', $t, $m)) {
                    $num = intval($m[0]);
                    if ($num > $maxNum) $maxNum = $num;
                }
            }

            $next = $maxNum + 1;
            $ticket = 'DOC-' . str_pad($next, 3, '0', STR_PAD_LEFT);

            $model = new DocumentRequest();
            $fillable = $model->getFillable();
            $casts = $model->getCasts();

            $dataWithTicket = [];
            foreach ($fillable as $col) {
                if ($col === 'doc_request_ticket') {
                    $dataWithTicket[$col] = $ticket;
                    continue;
                }

                // Skip created_at - let Laravel handle it automatically via timestamps
                if ($col === 'created_at') {
                    continue;
                }

                if ($col === 'extra_fields') {
                    // Store non-file extra_fields as JSON with UTF-8 safe encoding
                    \Log::info('Document Request - Saving extra_fields to database:', [
                        'extraFieldsData' => $extraFieldsData,
                        'extraFieldsData_count' => count($extraFieldsData),
                        'extraFieldsData_keys' => array_keys($extraFieldsData),
                        'has_building_type' => isset($extraFieldsData['building_type']),
                        'has_building_reg_number' => isset($extraFieldsData['building_reg_number']),
                    ]);
                    
                    if (!empty($extraFieldsData)) {
                        // Clean extra_fields data to ensure valid UTF-8
                        $cleanedExtraFields = $this->cleanArrayForJson($extraFieldsData);
                        $json = json_encode($cleanedExtraFields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // Final fallback: try encoding with all error flags
                            $json = json_encode($cleanedExtraFields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE | JSON_PARTIAL_OUTPUT_ON_ERROR);
                        }
                        $dataWithTicket[$col] = $json;
                        \Log::info('Document Request - extra_fields JSON encoded:', [
                            'json' => $json,
                            'json_length' => strlen($json),
                        ]);
                    } else {
                        $dataWithTicket[$col] = null;
                        \Log::warning('Document Request - extraFieldsData is empty, setting extra_fields to null');
                    }
                    continue;
                }
                
                // reason_type is included in fillable and should be saved
                // Check if column exists in database before including it
                if ($col === 'reason_type') {
                    // Check if the column exists in the database
                    try {
                        if (!Schema::hasColumn('document_requests', 'reason_type')) {
                            \Log::warning('reason_type column does not exist. Skipping. Please run migration: php artisan migrate');
                            continue; // Skip this column if it doesn't exist
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Could not check for reason_type column: ' . $e->getMessage());
                        continue; // Skip to be safe
                    }
                    
                    // Column exists, include it
                    $value = $data['reason_type'] ?? null;
                    $dataWithTicket[$col] = $value;
                    continue;
                }

                if (array_key_exists($col, $data)) {
                    $value = $data[$col];
                } elseif (isset($user->{$col})) {
                    $value = $user->{$col};
                } elseif ($credential && isset($credential->{$col})) {
                    $value = $credential->{$col};
                } else {
                    if (isset($casts[$col])) {
                        $cast = strtolower($casts[$col]);
                        if (strpos($cast, 'bool') !== false || $cast === 'boolean') {
                            $value = false;
                        } elseif (strpos($cast, 'float') !== false || strpos($cast, 'double') !== false) {
                            $value = 0.0;
                        } elseif ($cast === 'int' || $cast === 'integer') {
                            $value = 0;
                        } elseif (in_array($cast, ['date','datetime','immutable_date','immutable_datetime'])) {
                            $value = null;
                        } else {
                            $value = '';
                        }
                    } elseif (preg_match('/_id$/', $col)) {
                        $value = null;
                    } else {
                        $value = '';
                    }
                }

                $dataWithTicket[$col] = $value;
            }

            // Log reason_type to verify it's being saved
            \Log::info('Document Request - reason_type value before save:', [
                'reason_type' => $dataWithTicket['reason_type'] ?? 'NOT SET',
                'reason_type_from_request' => $request->input('reason_type'),
            ]);
            
            
            // Final sanitization pass - ensure all string values are valid UTF-8
            // CRITICAL: Skip binary data completely - never sanitize valid_id_content
            foreach ($dataWithTicket as $key => $value) {
                // Skip binary data (LONGBLOB) - NEVER sanitize this
                if ($key === 'valid_id_content') {
                    // This is binary file data - keep as-is, never process as UTF-8
                    continue;
                }
                
                // Skip extra_fields (already JSON encoded)
                if ($key === 'extra_fields') {
                    continue;
                }
                
                // Skip non-string values
                if (!is_string($value)) {
                    continue;
                }
                
                // Skip empty strings, but keep reason_type even if empty (it should be null, not empty string)
                if ($value === '' && $key !== 'reason_type') {
                    continue;
                }
                
                // Convert empty reason_type to null
                if ($key === 'reason_type' && $value === '') {
                    $dataWithTicket[$key] = null;
                    continue;
                }
                
                // Keep value as-is
                $dataWithTicket[$key] = $value;
            }
            
            // Ensure database connection uses UTF-8
            try {
                DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
            } catch (\Exception $e) {
                // Ignore if statement fails, connection might already be set
            }
            
            // Check and set MySQL session variables again inside transaction
            try {
                // Check current setting first
                $currentSetting = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
                $currentValue = $currentSetting->Value ?? 0;
                
                if ($currentValue < 67108864) {
                    // Try to set GLOBAL first (persists until restart)
                    try {
                        DB::statement('SET GLOBAL max_allowed_packet = 67108864');
                        \Log::info('Set GLOBAL max_allowed_packet to 64MB');
                    } catch (\Exception $globalException) {
                        // If GLOBAL fails, try SESSION (only for this connection)
                        try {
                            DB::statement('SET SESSION max_allowed_packet = 67108864');
                            \Log::info('Set SESSION max_allowed_packet to 64MB (session only)');
                        } catch (\Exception $sessionException) {
                            \Log::warning('Could not set max_allowed_packet: ' . $sessionException->getMessage());
                        }
                    }
                }
                
                DB::statement('SET SESSION wait_timeout = 600');
                DB::statement('SET SESSION interactive_timeout = 600');
            } catch (\Exception $e) {
                \Log::warning('Could not set MySQL session variables inside transaction: ' . $e->getMessage());
            }
            
            // create the record using only fillable columns
            // Use raw PDO for binary data to avoid UTF-8 validation
            try {
                // If we have binary data, use a two-step process
                if (isset($dataWithTicket['valid_id_content']) && $dataWithTicket['valid_id_content'] !== null) {
                    $binaryData = $dataWithTicket['valid_id_content'];
                    $fileSize = strlen($binaryData);
                    \Log::info('Attempting to save binary data', ['size_bytes' => $fileSize, 'size_mb' => round($fileSize / 1024 / 1024, 2)]);
                    
                    // Remove binary data from array for initial insert
                    unset($dataWithTicket['valid_id_content']);
                    
                    // Create record without binary data first
                    try {
                        $docRequest = DocumentRequest::create($dataWithTicket);
                        
                        \Log::info('Document Request - Created in DB (with binary, step 1):', [
                            'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                            'doc_request_ticket' => $docRequest->doc_request_ticket ?? 'NOT SET',
                        ]);
                        
                        // Verify the record was actually created
                        if (!$docRequest || !$docRequest->doc_request_id) {
                            \Log::error('DocumentRequest::create() returned null or no ID (binary path)', [
                                'docRequest_is_null' => is_null($docRequest),
                                'data_keys' => array_keys($dataWithTicket),
                                'fk_user_id' => $dataWithTicket['fk_user_id'] ?? null,
                                'purpose' => isset($dataWithTicket['purpose']) ? substr($dataWithTicket['purpose'], 0, 50) : null,
                            ]);
                            throw new \Exception('Failed to create document request - no ID returned');
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        \Log::error('QueryException creating document request (binary path)', [
                            'message' => $e->getMessage(),
                            'sql' => $e->getSql(),
                            'bindings' => $e->getBindings(),
                        ]);
                        throw new \Exception('Database error: ' . $e->getMessage());
                    } catch (\Exception $e) {
                        \Log::error('Exception creating document request (binary path)', [
                            'message' => $e->getMessage(),
                            'class' => get_class($e),
                        ]);
                        throw $e;
                    }
                    
                    // Then update with binary data using raw PDO
                    // IMPORTANT: Don't reconnect inside transaction - use the same connection
                    if ($docRequest && $docRequest->doc_request_id) {
                        try {
                            // Verify max_allowed_packet is sufficient (using current connection)
                            $verifySetting = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
                            $verifyValue = $verifySetting->Value ?? 0;
                            
                            if ($verifyValue < $fileSize) {
                                \Log::error('MySQL max_allowed_packet (' . round($verifyValue / 1024 / 1024, 2) . ' MB) is smaller than file size (' . round($fileSize / 1024 / 1024, 2) . ' MB)');
                                
                                // Try to increase it on current connection (SESSION only, don't reconnect)
                                try {
                                    DB::statement('SET SESSION max_allowed_packet = 67108864');
                                    \Log::info('Set SESSION max_allowed_packet to 64MB');
                                } catch (\Exception $e) {
                                    \Log::warning('Could not set SESSION max_allowed_packet: ' . $e->getMessage());
                                }
                            }
                            
                            // Use the current connection's PDO (don't reconnect - stay in transaction)
                            $pdo = DB::connection()->getPdo();
                            
                            // Prepare and execute the update
                            $stmt = $pdo->prepare('UPDATE document_requests SET valid_id_content = ? WHERE doc_request_id = ?');
                            $stmt->bindValue(1, $binaryData, \PDO::PARAM_LOB);
                            $stmt->bindValue(2, $docRequest->doc_request_id, \PDO::PARAM_INT);
                            
                            \Log::info('Executing PDO update for binary data...');
                            $stmt->execute();
                            \Log::info('PDO update successful');
                            
                            // Refresh to get updated data
                            $docRequest = $docRequest->fresh();
                        } catch (\PDOException $pdoException) {
                            $errorCode = $pdoException->getCode();
                            $errorMessage = $pdoException->getMessage();
                            
                            \Log::error('PDO error updating binary data', [
                                'code' => $errorCode,
                                'message' => $errorMessage,
                                'file_size' => $fileSize,
                                'file_size_mb' => round($fileSize / 1024 / 1024, 2)
                            ]);
                            
                            // Check if it's a "MySQL server has gone away" error
                            if ($errorCode == 2006 || stripos($errorMessage, 'MySQL server has gone away') !== false) {
                                \Log::error('MySQL server has gone away - file may be too large for current connection');
                                
                                // Don't reconnect inside transaction - just throw with helpful message
                                // The transaction will roll back automatically
                                $currentSetting = DB::selectOne("SHOW VARIABLES LIKE 'max_allowed_packet'");
                                $currentValueMB = $currentSetting ? round($currentSetting->Value / 1024 / 1024, 2) : 'unknown';
                                
                                throw new \Exception(
                                    "File upload failed. The file may be too large for the current database configuration. " .
                                    "Your MySQL server's max_allowed_packet is currently {$currentValueMB} MB. " .
                                    "Please try with a smaller file (under 20MB) or contact your database administrator."
                                );
                            } else {
                                // Other PDO errors - rethrow
                                throw $pdoException;
                            }
                        }
                    }
                } else {
                    // No binary data, create normally
                    try {
                        \Log::info('Attempting to create DocumentRequest (no binary data)', [
                            'data_keys' => array_keys($dataWithTicket),
                            'fk_user_id' => $dataWithTicket['fk_user_id'] ?? null,
                            'doc_request_ticket' => $dataWithTicket['doc_request_ticket'] ?? null,
                        ]);
                        
                        $docRequest = DocumentRequest::create($dataWithTicket);
                        
                        \Log::info('Document Request - Created in DB (no binary):', [
                            'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                            'doc_request_ticket' => $docRequest->doc_request_ticket ?? 'NOT SET',
                        ]);
                        
                        // Verify the record was actually created
                        if (!$docRequest || !$docRequest->doc_request_id) {
                            \Log::error('DocumentRequest::create() returned null or no ID (no binary path)', [
                                'docRequest_is_null' => is_null($docRequest),
                                'data_keys' => array_keys($dataWithTicket),
                                'fk_user_id' => $dataWithTicket['fk_user_id'] ?? null,
                                'purpose' => isset($dataWithTicket['purpose']) ? substr($dataWithTicket['purpose'], 0, 50) : null,
                            ]);
                            throw new \Exception('Failed to create document request - no ID returned');
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        \Log::error('QueryException creating document request (no binary path)', [
                            'message' => $e->getMessage(),
                            'sql' => $e->getSql(),
                            'bindings' => $e->getBindings(),
                        ]);
                        throw new \Exception('Database error: ' . $e->getMessage());
                    } catch (\Exception $e) {
                        \Log::error('Exception creating document request (no binary path)', [
                            'message' => $e->getMessage(),
                            'class' => get_class($e),
                        ]);
                        throw $e;
                    }
                }
            } catch (\PDOException $pdoException) {
                $errorMessage = $pdoException->getMessage();
                \Log::error('PDO error creating document request: ' . $errorMessage);
                
                // Check if it's a "MySQL server has gone away" error
                if (stripos($errorMessage, 'MySQL server has gone away') !== false || stripos($errorMessage, '2006') !== false || $pdoException->getCode() == 2006) {
                    \Log::error('MySQL server has gone away (PDOException) - attempting to reconnect and retry');
                    
                    // Reconnect to database
                    try {
                        DB::reconnect();
                        // Set session variables again after reconnection
                        DB::statement('SET SESSION max_allowed_packet = 67108864');
                        DB::statement('SET SESSION wait_timeout = 600');
                        DB::statement('SET SESSION interactive_timeout = 600');
                        
                        // Retry the insert
                        if (isset($dataWithTicket['valid_id_content']) && $dataWithTicket['valid_id_content'] !== null) {
                            $binaryData = $dataWithTicket['valid_id_content'];
                            unset($dataWithTicket['valid_id_content']);
                            $docRequest = DocumentRequest::create($dataWithTicket);
                            
                            if ($docRequest && $docRequest->doc_request_id) {
                                $pdo = DB::connection()->getPdo();
                                $stmt = $pdo->prepare('UPDATE document_requests SET valid_id_content = ? WHERE doc_request_id = ?');
                                $stmt->bindValue(1, $binaryData, \PDO::PARAM_LOB);
                                $stmt->bindValue(2, $docRequest->doc_request_id, \PDO::PARAM_INT);
                                $stmt->execute();
                                $docRequest = $docRequest->fresh();
                            }
                        } else {
                            $docRequest = DocumentRequest::create($dataWithTicket);
                        }
                        
                        if (!$docRequest || !$docRequest->doc_request_id) {
                            throw new \Exception('Failed to create document request after reconnection');
                        }
                        
                        \Log::info('Successfully created document request after PDO reconnection');
                    } catch (\Exception $retryException) {
                        \Log::error('Failed to retry after PDO MySQL reconnection: ' . $retryException->getMessage());
                        throw new \Exception('File upload failed due to database connection issue. Please try again with a smaller file (maximum 20MB) or compress/resize the image.');
                    }
                } else {
                    // Re-throw other PDO errors
                    throw $pdoException;
                }
            } catch (\Illuminate\Database\QueryException $e) {
                $errorMessage = $e->getMessage();
                \Log::error('Database error creating document request: ' . $errorMessage);
                
                // Check if it's a "MySQL server has gone away" error
                if (stripos($errorMessage, 'MySQL server has gone away') !== false || stripos($errorMessage, '2006') !== false) {
                    \Log::error('MySQL server has gone away - attempting to reconnect and retry');
                    
                    // Reconnect to database
                    try {
                        DB::reconnect();
                        // Set session variables again after reconnection
                        DB::statement('SET SESSION max_allowed_packet = 67108864');
                        DB::statement('SET SESSION wait_timeout = 600');
                        DB::statement('SET SESSION interactive_timeout = 600');
                        
                        // Retry the insert
                        if (isset($dataWithTicket['valid_id_content']) && $dataWithTicket['valid_id_content'] !== null) {
                            $binaryData = $dataWithTicket['valid_id_content'];
                            unset($dataWithTicket['valid_id_content']);
                            $docRequest = DocumentRequest::create($dataWithTicket);
                            
                            if ($docRequest && $docRequest->doc_request_id) {
                                $pdo = DB::connection()->getPdo();
                                $stmt = $pdo->prepare('UPDATE document_requests SET valid_id_content = ? WHERE doc_request_id = ?');
                                $stmt->bindValue(1, $binaryData, \PDO::PARAM_LOB);
                                $stmt->bindValue(2, $docRequest->doc_request_id, \PDO::PARAM_INT);
                                $stmt->execute();
                                $docRequest = $docRequest->fresh();
                            }
                        } else {
                            $docRequest = DocumentRequest::create($dataWithTicket);
                        }
                        
                        if (!$docRequest || !$docRequest->doc_request_id) {
                            throw new \Exception('Failed to create document request after reconnection');
                        }
                        
                        \Log::info('Successfully created document request after reconnection');
                    } catch (\Exception $retryException) {
                        \Log::error('Failed to retry after MySQL reconnection: ' . $retryException->getMessage());
                        throw new \Exception('File upload failed due to database connection issue. Please try again with a smaller file (maximum 20MB) or compress/resize the image.');
                    }
                }
                // Check if it's a UTF-8 encoding error
                elseif (stripos($errorMessage, 'utf8') !== false || stripos($errorMessage, 'malformed') !== false || stripos($errorMessage, 'incorrectly encoded') !== false) {
                    // Try to identify which TEXT field is causing the issue (NOT binary fields)
                    foreach ($dataWithTicket as $key => $value) {
                        // Skip binary data - it should never be checked as UTF-8
                        if ($key === 'valid_id_content') {
                            continue;
                        }
                        
                        // Only check string values
                        if (is_string($value) && $value !== '') {
                            if (!mb_check_encoding($value, 'UTF-8')) {
                                \Log::error("Invalid UTF-8 in TEXT field: {$key}, fixing...");
                                // Try to fix it
                                $dataWithTicket[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                                if (function_exists('iconv')) {
                                    $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $dataWithTicket[$key]);
                                    if ($fixed !== false) {
                                        $dataWithTicket[$key] = $fixed;
                                    }
                                }
                            }
                        }
                    }
                    // Retry once after fixing
                    try {
                        $docRequest = DocumentRequest::create($dataWithTicket);
                        if ($docRequest && $docRequest->doc_request_id) {
                            \Log::info('Document request created successfully after UTF-8 fix');
                        } else {
                            throw $e; // Re-throw if still fails
                        }
                    } catch (\Exception $retryError) {
                        \Log::error('Retry after UTF-8 fix also failed: ' . $retryError->getMessage());
                        throw $e; // Throw original error
                    }
                } else {
                    // Not a UTF-8 error, throw as-is
                    throw $e;
                }
            } catch (\Exception $e) {
                \Log::error('Error creating document request: ' . $e->getMessage());
                throw $e;
            }

            // Save ID front and back as separate attachments if they exist
            // Only save if they're not already in fileAttachmentsForTransaction (to avoid duplicates)
            if ($request->hasFile('id_front') && !isset($fileAttachmentsForTransaction['id_front'])) {
                $idFrontFile = $request->file('id_front');
                if ($idFrontFile && $idFrontFile->isValid()) {
                    try {
                        $path = $idFrontFile->store('document_request_attachments', 'public');
                        if ($path && $docRequest && $docRequest->doc_request_id) {
                            DocumentRequestAttachment::create([
                                'fk_doc_request_id' => $docRequest->doc_request_id,
                                'field_name' => 'id_front',
                                'file_name' => $idFrontFile->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $idFrontFile->getMimeType(),
                                'file_size' => $idFrontFile->getSize(),
                            ]);
                            \Log::info('Saved ID front as attachment');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save ID front attachment: ' . $e->getMessage());
                        // Don't throw - attachment failure shouldn't prevent document request creation
                        // But log it for debugging
                    }
                }
            }
            
            if ($request->hasFile('id_back') && !isset($fileAttachmentsForTransaction['id_back'])) {
                $idBackFile = $request->file('id_back');
                if ($idBackFile && $idBackFile->isValid()) {
                    try {
                        $path = $idBackFile->store('document_request_attachments', 'public');
                        if ($path && $docRequest && $docRequest->doc_request_id) {
                            DocumentRequestAttachment::create([
                                'fk_doc_request_id' => $docRequest->doc_request_id,
                                'field_name' => 'id_back',
                                'file_name' => $idBackFile->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $idBackFile->getMimeType(),
                                'file_size' => $idBackFile->getSize(),
                            ]);
                            \Log::info('Saved ID back as attachment');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save ID back attachment: ' . $e->getMessage());
                        // Don't throw - attachment failure shouldn't prevent document request creation
                        // But log it for debugging
                    }
                }
            }

            // Save file attachments AFTER the document request is created
            \Log::info('Document Request - About to save attachments (INSIDE TRANSACTION):', [
                'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                'fileAttachmentsForTransaction_empty_check' => empty($fileAttachmentsForTransaction),
                'fileAttachmentsForTransaction_count' => count($fileAttachmentsForTransaction),
                'fileAttachmentsForTransaction_keys' => array_keys($fileAttachmentsForTransaction),
                'fileAttachmentsForTransaction_is_array' => is_array($fileAttachmentsForTransaction),
            ]);
            
            $savedAttachments = [];
            if (!empty($fileAttachmentsForTransaction) && is_array($fileAttachmentsForTransaction) && count($fileAttachmentsForTransaction) > 0) {
                \Log::info('Document Request - Starting to save attachments:', [
                    'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                    'file_count' => count($fileAttachmentsForTransaction),
                    'field_names' => array_keys($fileAttachmentsForTransaction),
                ]);
                
                foreach ($fileAttachmentsForTransaction as $fieldName => $file) {
                    if (!$file) {
                        \Log::warning('Null file for field:', ['field_name' => $fieldName]);
                        continue;
                    }
                    
                    if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                        \Log::warning('File is not UploadedFile instance:', [
                            'field_name' => $fieldName,
                            'file_type' => gettype($file),
                        ]);
                        continue;
                    }
                    
                    if (!$file->isValid()) {
                        \Log::warning('Invalid file skipped:', [
                            'field_name' => $fieldName,
                            'error' => $file->getError(),
                            'error_message' => $file->getErrorMessage(),
                        ]);
                        continue;
                    }
                    
                    try {
                        // Store file in storage
                        $path = $file->store('document_request_attachments', 'public');
                        
                        if (!$path) {
                            \Log::error('File storage returned empty path:', ['field_name' => $fieldName]);
                            continue;
                        }
                        
                        // Only create attachment if docRequest exists
                        if ($docRequest && $docRequest->doc_request_id) {
                            // Create attachment record
                            $attachment = DocumentRequestAttachment::create([
                                'fk_doc_request_id' => $docRequest->doc_request_id,
                                'field_name' => $fieldName,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_type' => $file->getMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                            
                            $savedAttachments[] = $attachment->attachment_id;
                            \Log::info('Successfully saved attachment:', [
                                'attachment_id' => $attachment->attachment_id,
                                'field_name' => $fieldName,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $path,
                                'file_size' => $file->getSize(),
                            ]);
                        } else {
                            \Log::warning('Cannot save attachment - docRequest is null or missing ID:', [
                                'field_name' => $fieldName,
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to save attachment:', [
                            'field_name' => $fieldName,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
                
                \Log::info('Document Request - Attachments save process completed:', [
                    'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                    'saved_count' => count($savedAttachments),
                    'attachment_ids' => $savedAttachments,
                ]);
            } else {
                \Log::info('Document Request - No file attachments to save:', [
                    'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                ]);
            }
            
            // Verify attachments were saved
            if ($docRequest && $docRequest->doc_request_id) {
                $verifyAttachments = DocumentRequestAttachment::where('fk_doc_request_id', $docRequest->doc_request_id)->get();
                \Log::info('Document Request - Verified attachments in DB:', [
                    'doc_request_id' => $docRequest->doc_request_id,
                    'count' => $verifyAttachments->count(),
                    'attachments' => $verifyAttachments->map(function($a) {
                        return [
                            'id' => $a->attachment_id,
                            'field_name' => $a->field_name,
                            'file_name' => $a->file_name,
                            'file_path' => $a->file_path,
                        ];
                    })->toArray(),
                ]);
            }
            
            \Log::info('Document Request - Transaction about to complete:', [
                'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
                'attachments_saved_count' => count($savedAttachments),
            ]);
        });
        
        \Log::info('Document Request - Transaction completed successfully:', [
            'doc_request_id' => $docRequest->doc_request_id ?? 'NOT SET',
            'doc_request_ticket' => $docRequest->doc_request_ticket ?? 'NOT SET',
        ]);
        
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = $e->getMessage();
            
            // Handle "MySQL server has gone away" error
            if (stripos($errorMessage, 'MySQL server has gone away') !== false || stripos($errorMessage, '2006') !== false) {
                \Log::error('MySQL server has gone away during transaction - file may be too large');
                
                // Return JSON for AJAX/axios requests
                if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload failed due to database connection issue. The file may be too large. Please try with a smaller file (under 50MB) or contact support.',
                        'errors' => ['valid_id_content' => ['File upload failed due to database connection issue. The file may be too large. Please try with a smaller file (under 50MB) or contact support.']]
                    ], 500);
                }
                
                return back()
                    ->withInput()
                    ->withErrors(['valid_id_content' => 'File upload failed due to database connection issue. The file may be too large. Please try with a smaller file (under 50MB) or contact support.']);
            }
            
            // Re-throw other database errors to be caught by the general exception handler
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error in document request transaction: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'docRequest_is_null' => is_null($docRequest),
                'docRequest_id' => $docRequest->doc_request_id ?? 'NOT SET',
            ]);
            
            // Return JSON for AJAX/axios requests
            if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                $errorMessage = $e->getMessage() ?: 'An error occurred while processing your request. Please try again.';
                
                // Provide more user-friendly messages for common errors
                if (stripos($errorMessage, 'max_allowed_packet') !== false) {
                    $errorMessage = 'File upload failed. The file may be too large. Please try with a smaller file (under 20MB) or compress/resize the image.';
                } elseif (stripos($errorMessage, 'MySQL server has gone away') !== false) {
                    $errorMessage = 'File upload failed due to database connection issue. Please try again with a smaller file.';
                } elseif (stripos($errorMessage, 'file') !== false || stripos($errorMessage, 'upload') !== false) {
                    $errorMessage = 'File upload failed. Please check the file and try again.';
                } elseif (stripos($errorMessage, 'Database configuration') !== false) {
                    // Keep the detailed error message for database configuration issues
                    // $errorMessage is already set from the exception
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => [$errorMessage]]
                ], 500);
            }
            
            // For non-AJAX requests, re-throw to let Laravel handle it
            throw $e;
        }
        
        // Verify docRequest was created successfully after transaction
        if (!$docRequest || !$docRequest->doc_request_id) {
            \Log::error('Document request was not created after transaction - docRequest is null or missing ID', [
                'docRequest_is_null' => is_null($docRequest),
                'docRequest_id' => $docRequest->doc_request_id ?? 'NOT SET',
            ]);
            
            // Return JSON for AJAX/axios requests
            if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create document request. Please try again.',
                    'errors' => ['general' => ['Failed to create document request. Please try again.']]
                ], 500);
            }
            
            return back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create document request. Please try again.']);
        }

        // after $docRequest is created:
        if (!$docRequest || !$docRequest->doc_request_id) {
            \Log::error('Document request was not created - docRequest is null or missing ID');
            
            // Return JSON for AJAX/axios requests
            if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create document request. Please try again.',
                    'errors' => ['general' => ['Failed to create document request. Please try again.']]
                ], 500);
            }
            
            return back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create document request. Please try again.']);
        }

        $ticket = $docRequest->doc_request_ticket;

        // Check if this is an AJAX/axios request (not Inertia)
        if ($request->wantsJson() || $request->ajax() || !$request->header('X-Inertia')) {
            try {
                // Ensure ticket is valid UTF-8 before encoding
                $ticketSafe = $ticket;
                if (!mb_check_encoding($ticketSafe, 'UTF-8')) {
                    $ticketSafe = mb_convert_encoding($ticketSafe, 'UTF-8', 'UTF-8');
                }
                
                $responseData = [
                    'success' => true,
                    'ticket' => $ticketSafe,
                    'message' => 'Document request submitted successfully.'
                ];
                
                // Use JSON encoding flags to handle invalid UTF-8
                return response()->json($responseData, 201, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
            } catch (\Exception $e) {
                \Log::error('Error encoding JSON response: ' . $e->getMessage());
                // Fallback response with safe values
                return response()->json([
                    'success' => true,
                    'ticket' => preg_replace('/[^\x20-\x7E]/', '', $ticket), // ASCII only fallback
                    'message' => 'Document request submitted successfully.'
                ], 201, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
            }
        }

        // Inertia request
        if ($request->header('X-Inertia')) {
            return Inertia::render('User/Resident/R_Document_Request_Select', [
                'ticket' => $ticket,
            ])->with('success', 'Document request submitted.');
        }

        return redirect()->back()
            ->with('success', 'Document request submitted.')
            ->with('ticket', $docRequest->doc_request_ticket);
    }

    public function selectPage(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            abort(403, 'Unauthorized');
        }

        // Helper: sanitize string to UTF-8
        $sanitizeString = function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            if (mb_check_encoding($value, 'UTF-8')) {
                return $value;
            }
            $tryEncodings = ['Windows-1252', 'ISO-8859-1', 'CP1252'];
            foreach ($tryEncodings as $enc) {
                $converted = @mb_convert_encoding($value, 'UTF-8', $enc);
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
            $stripped = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            return $stripped !== false ? $stripped : mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        };

        // Fetch document requests with payment info
        $documentRequests = DocumentRequest::with(['documentType'])
            ->where('fk_user_id', $userId)
            ->orderByDesc('doc_request_id')
            ->limit(100)
            ->get();

        $docRequestIds = $documentRequests->pluck('doc_request_id')->filter()->values()->all();

        $payments = \App\Models\Payment::select([
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
            ->limit(100)
            ->get();

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

        $paymentsArray = $payments->map(function ($m) use ($sanitizeString) {
            $receiptImage = null;
            if (!empty($m->receipt_content)) {
                if (Str::startsWith($m->receipt_content, ['http://', 'https://', '/'])) {
                    $receiptImage = $m->receipt_content;
                } else {
                    $receiptImage = asset('storage/' . ltrim($m->receipt_content, '/'));
                }
            }
            
            return [
                'payment_id' => $m->payment_id,
                'fk_doc_request_id' => $m->fk_doc_request_id,
                'fk_user_id' => $m->fk_user_id,
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'amount' => $m->paid_amount,
                'paid_amount' => $m->paid_amount,
                'transaction_ref' => $sanitizeString($m->transaction_ref ?? ''),
                'receipt_path' => $receiptImage,
                'receipt_content' => $sanitizeString($m->receipt_content ?? ''),
                'receipt_image' => $receiptImage,
                'paid_at' => $m->paid_at ? $m->paid_at->toIso8601String() : null,
                'updated_at' => $m->updated_at ? $m->updated_at->toIso8601String() : null,
            ];
        })->all();

        // Get user data
        $user = auth()->user();
        $userData = $user ? [
            'id' => $user->user_id,
            'name' => $sanitizeString($user->name ?? ''),
            'email' => $sanitizeString($user->email ?? ''),
            'fk_role_id' => $user->fk_role_id ?? null,
            'profile_pic' => $sanitizeString($user->profile_pic ?? ''),
        ] : null;

        // Get user restrictions
        $restrictions = null;
        $availableDocumentTypes = [];
        $restrictedDocumentTypeIds = [];
        
        if ($user) {
            $restriction = \App\Models\UserRestriction::where('user_id', $user->user_id)->first();
            if ($restriction) {
                $restrictions = [
                    'restrict_posting' => $restriction->restrict_posting ?? false,
                    'restrict_commenting' => $restriction->restrict_commenting ?? false,
                    'restrict_document_request' => $restriction->restrict_document_request ?? false,
                    'restrict_event_assistance_request' => $restriction->restrict_event_assistance_request ?? false,
                ];
                
                // Get restricted document type IDs
                $restrictedDocumentTypeIds = $restriction->restricted_document_types ?? [];
                $allowedDocumentTypeIds = $restriction->allowed_document_types ?? [];
            }
        }
        
        // Get all document types from database
        $allDocumentTypes = DocumentType::all();
        
        // Build document types list with restriction status (show all, but mark restricted ones)
        $documentTypesList = [];
        foreach ($allDocumentTypes as $docType) {
            $docTypeId = $docType->document_type_id;
            $docName = $docType->document_name;
            
            // Check if this document type is restricted
            $isRestricted = in_array($docTypeId, $restrictedDocumentTypeIds);
            
            // Check if this document type is explicitly allowed (override)
            $isAllowed = !empty($allowedDocumentTypeIds) && in_array($docTypeId, $allowedDocumentTypeIds);
            
            // Determine if it's actually restricted (restricted but not allowed)
            $actuallyRestricted = $isRestricted && !$isAllowed;
            
            // Add to list with restriction status
            $documentTypesList[] = [
                'id' => $docTypeId,
                'name' => $docName,
                'processing_fee' => $docType->processing_fee ?? null,
                'restricted' => $actuallyRestricted,
                'available' => !$actuallyRestricted,
            ];
        }
        
        // Only show document types that exist in the database
        // No hardcoded fallback - only show what's actually in the database
        
        // For backward compatibility, also provide availableDocumentTypes (only non-restricted)
        $availableDocumentTypes = collect($documentTypesList)->where('available', true)->values()->toArray();

        return Inertia::render('User/Resident/R_Document_Request_Select', [
            'documentRequests' => $documentRequestsArray,
            'document_requests' => $documentRequestsArray,
            'payments' => $paymentsArray,
            'restrictions' => $restrictions,
            'documentTypes' => $documentTypesList, // All document types with restriction status
            'availableDocumentTypes' => $availableDocumentTypes, // Only available ones (for backward compatibility)
            'restrictedDocumentTypeIds' => $restrictedDocumentTypeIds,
            'auth' => ['user' => $userData],
        ]);
    }

}
