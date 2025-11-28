<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\UserCredential;
use App\Models\User;

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
            // Return empty props to default (R) component for compatibility
            return Inertia::render('User/Resident/R_Notification_Request', [
                'documentRequests' => [],
                'document_requests' => [],
                'eventRequests' => [],
                'event_requests' => [],
            ]);
        }

        // fetch all rows for this user
        $rows = \App\Models\DocumentRequest::with('documentType')
            ->where('fk_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('DocumentRequestController@index - fetched rows count: ' . $rows->count());

        // helper to map a raw row -> shared presentation array
        $mapRow = function ($r) {
            $amountNumeric = $r->processing_fee ?? $r->applied_processing_fee ?? ($r->documentType?->processing_fee ?? null);
            return [
                'id' => $r->doc_request_id,
                'requestNumber' => $r->doc_request_ticket,
                'doc_request_ticket' => $r->doc_request_ticket,
                'title' => $r->documentType?->document_name ?? $r->purpose ?? 'Request',
                'date' => $r->created_at?->format('M d, Y'),
                'time' => $r->created_at?->format('g:i A'),
                'status' => strtoupper($r->status ?? 'PENDING'),
                'type' => $r->fk_document_type_id ? 'document' : 'event',
                // formatted amount string for display (or null if no fee)
                'amount' => $amountNumeric !== null ? number_format((float)$amountNumeric, 2, '.', '') : null,
                // also include raw numeric processing fee for client-side fallback if needed:
                'processing_fee' => $amountNumeric !== null ? (float)$amountNumeric : null,
                // optionally include a minimal documentType object for debug or future uses:
                'document_type' => $r->documentType ? [
                    'document_type_id' => $r->documentType->document_type_id,
                    'document_name' => $r->documentType->document_name,
                    'processing_fee' => $r->documentType->processing_fee,
                ] : null,
            ];
        };

        // split rows into documents and events
        $documents = $rows->filter(function ($r) { return (bool) $r->fk_document_type_id; })
            ->map($mapRow)
            ->values();

        $events = $rows->filter(function ($r) { return ! (bool) $r->fk_document_type_id; })
            ->map($mapRow)
            ->values();

        // decide which Vue/Inertia component to render:
        // - query param 'target=event' -> E_Notification_Request
        // - otherwise -> R_Notification_Request
        $target = $request->query('target', $request->route('target') ?: 'document');
        $component = ($target === 'event' || $target === 'events')
            ? 'User/Resident/E_Notification_Request'
            : 'User/Resident/R_Notification_Request';

        // Keep multiple keys for backward compatibility
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
        $allowedRoles = [3]; // approver role id(s)
        $userRole = $authUser->fk_role_id ?? $authUser->role_id ?? null;

        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized.');
        }

        $requestedStatus = $request->query('status', 'Pending');

        // Eager-load documentType relationship and optionally filter by status
        $query = DocumentRequest::with('documentType')->orderBy('created_at', 'desc');

        if (is_string($requestedStatus) && strtolower($requestedStatus) !== 'all') {
            // Normalize to the same case as stored in DB if needed — here we assume uppercase convention
            $statusFilter = strtoupper($requestedStatus);
            $query->where('status', $statusFilter);
        }

        $rows = $query->get();

        // Also fetch all document types once (for mapping / selection in UI)
        $allDocumentTypes = DocumentType::orderBy('document_name')->get();

        // Build a map id => name (useful on frontend)
        $documentTypesMap = $allDocumentTypes->pluck('document_name', 'document_type_id')->toArray();

        $documentRequests = $rows->map(function ($r) {
            // get relationship (may be null)
            $docType = $r->documentType;

            // build a small object for document_type
            $documentTypeObj = null;
            if ($docType) {
                $documentTypeObj = [
                    'id' => $docType->document_type_id,
                    'name' => $docType->document_name,
                    'processing_fee' => isset($docType->processing_fee) ? (float)$docType->processing_fee : null,
                ];
            } else {
                // fallback: null or supply default
                $documentTypeObj = [
                    'id' => $r->fk_document_type_id,
                    'name' => null,
                    'processing_fee' => null,
                ];
            }

            // --- Name normalization/fix starts here ---
            $rawFirst  = trim((string) ($r->first_name ?? ''));
            $rawMiddle = trim((string) ($r->middle_name ?? ''));
            $rawLast   = trim((string) ($r->last_name ?? ''));
            $rawSuffix = trim((string) ($r->suffix ?? ''));

            // If first contains multiple words and last is empty, or the first contains the last again,
            // attempt to split first into [first, middle..., last]
            if ($rawFirst !== '') {
                $parts = preg_split('/\s+/', $rawFirst, -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) > 1 && empty($rawLast)) {
                    // assign first -> first element, last -> last element, middle -> join(remaining)
                    $firstCandidate = array_shift($parts);
                    $lastCandidate  = array_pop($parts);
                    $middleCandidate = count($parts) ? implode(' ', $parts) : $rawMiddle;

                    $rawFirst  = $firstCandidate;
                    $rawLast   = $lastCandidate;
                    if (empty($rawMiddle)) {
                        $rawMiddle = $middleCandidate;
                    }
                } elseif (count($parts) > 1 && $rawLast !== '' ) {
                    // Sometimes first contains "First Last" and last also present; if so, remove duplicate from first
                    // e.g. first = "josh jocson", last = "jocson" -> keep only the first token as first name
                    if (strcasecmp(end($parts), $rawLast) === 0) {
                        $rawFirst = array_shift($parts);
                        if (empty($rawMiddle) && count($parts) > 0) {
                            // remaining parts aside from the popped last may be middle
                            array_pop($parts); // remove the duplicate-last we already checked
                            $rawMiddle = count($parts) ? implode(' ', $parts) : $rawMiddle;
                        }
                    }
                }
            }

            // If middle ends with last (duplicate), strip the trailing last from middle
            if ($rawMiddle !== '' && $rawLast !== '') {
                $pattern = '/\b' . preg_quote($rawLast, '/') . '$/i';
                if (preg_match($pattern, $rawMiddle)) {
                    $rawMiddle = trim(preg_replace($pattern, '', $rawMiddle));
                }
            }

            // Title-case each part (multibyte-safe)
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

            // Build a clean "First Middle Last [Suffix]" name:
            $nameParts = array_filter([$first, $middle, $last, $suffix], function ($p) {
                return $p !== '' && $p !== null;
            });
            $displayName = implode(' ', $nameParts);
            // --- Name normalization/fix ends here ---

            return [
                'doc_request_id' => $r->doc_request_id,
                'doc_request_ticket' => $r->doc_request_ticket,
                'fk_user_id' => $r->fk_user_id,
                'last_name' => $r->last_name,
                'first_name' => $r->first_name,
                'middle_name' => $r->middle_name,
                'suffix' => $r->suffix,
                // Send the normalized display name (First Middle Last [Suffix])
                'name' => $displayName,
                'birthdate' => $r->birthdate,
                'sex' => $r->sex,
                'civil_status' => $r->civil_status,
                'address' => $r->address,
                'contact_number' => $r->contact_number,
                'valid_id_path' => $r->valid_id_path,
                'applied_processing_fee' => $r->applied_processing_fee,
                'purpose' => $r->purpose,
                'pickup_item' => $r->pickup_item,
                'pickup_location' => $r->pickup_location,
                'pickup_start' => $r->pickup_start,
                'pickup_end' => $r->pickup_end,
                'person_to_look' => $r->person_to_look,
                'status' => $r->status,
                'fk_approver_id' => $r->fk_approver_id,
                'reviewed_at' => $r->reviewed_at,
                'created_at' => $r->created_at?->toDateTimeString(),
                // Attach the document type object derived from DocumentType model
                'document_type' => $documentTypeObj,
            ];
        });

        return Inertia::render('Admin/Approver/A_Document_Request', [
            'document_requests' => $documentRequests,
            // convenience props in case frontend needs them
            'document_types' => $allDocumentTypes, // full collection (serialize-friendly)
            'document_types_map' => $documentTypesMap, // id => name
        ]);
    }

    public function approve(Request $request, $id)
    {
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
        ]);

        $doc = \App\Models\DocumentRequest::where('doc_request_id', $id)->firstOrFail();

        $update = [
            'pickup_item'     => $data['pickup_item'] ?? $doc->pickup_item,
            'pickup_location' => $data['pickup_location'] ?? $doc->pickup_location,
            'pickup_start'    => $data['pickup_start'] ?? $doc->pickup_start,
            'pickup_end'      => $data['pickup_end'] ?? $doc->pickup_end,
            'person_to_look'  => $data['person_to_look'] ?? $doc->person_to_look,
            'status'          => $data['status'],
            'fk_approver_id'  => $data['fk_approver_id'] ?? $authUser->getKey(),
            'reviewed_at'     => $data['reviewed_at'] ?? Carbon::now()->toDateTimeString(),
        ];

        DB::transaction(function () use ($doc, $update) {
            $doc->update($update);
        });

        // refresh model to return latest fields
        $doc = $doc->fresh();

        // Return the updated resource to client (frontend onSuccess will receive this)
        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully.',
            'data'    => [
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
                'updated_at' => $doc->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    public function reject(Request $request, $id)
    {
        $authUser = $request->user();
        if (! $authUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'status'         => ['required', 'string', 'in:Approved,Rejected,Pending'],
            'fk_approver_id' => ['nullable'],
            'reviewed_at'    => ['nullable', 'date_format:Y-m-d H:i:s'],
            // optionally validate rejection_reason if you add that in front-end payload
        ]);

        $doc = \App\Models\DocumentRequest::where('doc_request_id', $id)->firstOrFail();

        $update = [
            'status'         => $data['status'],
            'fk_approver_id' => $data['fk_approver_id'] ?? $authUser->getKey(),
            'reviewed_at'    => $data['reviewed_at'] ?? Carbon::now()->toDateTimeString(),
        ];

        DB::transaction(function () use ($doc, $update) {
            $doc->update($update);
        });

        $doc = $doc->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully.',
            'data'    => [
                'doc_request_id' => $doc->doc_request_id,
                'doc_request_ticket' => $doc->doc_request_ticket,
                'status' => $doc->status,
                'fk_approver_id' => $doc->fk_approver_id,
                'reviewed_at' => $doc->reviewed_at,
                'updated_at' => $doc->updated_at?->toDateTimeString(),
            ],
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
            // any other props you need...
        ]);
    }

    public function store(Request $request)
    {
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
            'purpose' => ['nullable', 'string'],
            'id_type' => ['nullable', 'string', 'max:100'],
            'document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx','max:10240'],
            'pickup_item'         => ['nullable', 'string', 'max:255'],
            'pickup_location'     => ['nullable', 'string', 'max:255'],
            'pickup_start'        => ['nullable', 'date'],
            'pickup_end'          => ['nullable', 'date'],
            'person_to_look'      => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Determine the user's id robustly (in case your User PK is user_id)
        $fk_user_id = $user->user_id ?? $user->id ?? Auth::id();

        // Resolve document type id by name if necessary
        $fk_document_type_id = $request->input('fk_document_type_id');
        if (empty($fk_document_type_id) && $request->filled('document_name')) {
            $docType = DocumentType::where('document_name', $request->input('document_name'))->first();
            if ($docType) {
                $fk_document_type_id = $docType->document_type_id;
            }
        }

        // Contact number priority
        $contactNumber = $request->input('contact_number');
        if (empty($contactNumber)) {
            $credential = UserCredential::where('fk_user_id', $fk_user_id)->first();
            if ($credential && !empty($credential->contact_number)) {
                $contactNumber = $credential->contact_number;
            } elseif (!empty($user->contact_number)) {
                $contactNumber = $user->contact_number;
            }
        }

        if (empty($contactNumber)) {
            return back()
                ->withInput()
                ->withErrors(['contact_number' => 'Contact number is required. Please provide it in the form or add it to your profile.']);
        }

        // Handle file upload
        $validIdPath = null;
        if ($request->hasFile('document')) {
            $validIdPath = $request->file('document')->store('valid_ids', 'public');
        }

        $appliedFee = 0.00;
        if ($fk_document_type_id) {
            $appliedFee = optional(DocumentType::find($fk_document_type_id))->processing_fee ?? 0.00;
        }

        $ticket = strtoupper(Str::random(10));

        $docRequest = null;

        

        // Build the base data array (doc_request_ticket will be set inside the transaction)
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
            'address'                => $request->input('address') ?? $user->address ?? null,
            'contact_number'         => $contactNumber,
            'valid_id_path'          => $validIdPath,
            'applied_processing_fee' => $appliedFee,
            'purpose'                => $request->input('purpose'),
            'pickup_item'            => $request->input('pickup_item', 'To be confirmed'),
            'pickup_location'        => $request->input('pickup_location', 'To be confirmed'),
            'pickup_start'           => $request->input('pickup_start', now()),
            'pickup_end'             => $request->input('pickup_end', now()->addDay()),
            'person_to_look'         => $request->input('person_to_look', 'To be assigned'),
            'status'                 => 'Pending',
        ];

        // after $data is built, add:
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


        // Transaction: compute next sequential numeric ticket (no 'Req' prefix) and create the record.
        // Resulting ticket format: zero-padded 3 digits (e.g. "001", "002", "123").
        DB::transaction(function () use (&$docRequest, $data) {
            // Lock only rows that contain at least one digit (safer than relying on a specific prefix).
            // Then compute the max numeric portion in PHP using preg_match — compatible with most MySQL versions.
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
            // pad to 3 digits as requested (001, 002, ...). Increase pad length if you want more digits later.
            $ticket = str_pad($next, 3, '0', STR_PAD_LEFT);

            $dataWithTicket = $data;
            $dataWithTicket['doc_request_ticket'] = $ticket;

            $docRequest = DocumentRequest::create($dataWithTicket);
        });

        // after $docRequest is created:
        $ticket = $docRequest->doc_request_ticket;

        // If the request is an Inertia navigation, render the same component and include the ticket prop:
        if ($request->header('X-Inertia')) {
            return Inertia::render('User/Resident/R_Document_Request_Select', [
                // include any props your page expects (user, etc.). Minimal example:
                'ticket' => $ticket,
                // optional: you can also pass the authenticated user and other required props here
                // 'user' => Auth::user(),
            ])->with('success', 'Document request submitted.');
        }

        // Always flash the ticket and redirect to the document-request page so Inertia receives it.
        // Use the named route that renders the R_Document_Request_Select view (adjust route name if needed).
        $flashData = ['success' => 'Document request submitted.', 'ticket' => $docRequest->doc_request_ticket];

        // If this is an Inertia request, redirect to the same Inertia page (Inertia will follow the redirect)
        if ($request->header('X-Inertia')) {
            return redirect()->route('document_request_select_resident')->with($flashData);
        }

        // // If it's an AJAX/JSON request (e.g., not Inertia but an XHR), return JSON containing the ticket.
        // if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Document request submitted.',
        //         'ticket' => $docRequest->doc_request_ticket,
        //         'data' => ['doc_request_id' => $docRequest->doc_request_id],
        //     ]);
        // }

        // // Determine role (adjust to match your database)
        // $userRole = $user->fk_role_id ?? $user->role_id ?? null;

        // // If employee/approver, redirect to their page
        // if (in_array($userRole, [2, 3])) { // change IDs if needed
        //     if ($request->header('X-Inertia')) {
        //         return redirect()->route('document_request_employee')->with($flashData);
        //     }
        //     return redirect()->route('document_request_employee')
        //             ->with($flashData);
        // }

        // // Otherwise, default resident redirect
        // if ($request->header('X-Inertia')) {
        //     return redirect()->route('document_request_select_resident')->with($flashData);
        // }

            return redirect()->back()
                ->with('success', 'Document request submitted.')
                ->with('ticket', $docRequest->doc_request_ticket);
    } 
}
