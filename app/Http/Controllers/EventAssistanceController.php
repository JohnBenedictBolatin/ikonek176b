<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
// use App\Models\EventAssistanceItem; // Table doesn't exist - not used in current implementation
use App\Models\EventAssistanceRequest;
use App\Models\EventAssistanceDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventAssistanceController extends Controller
{
    public function create(Request $request)
    {
        // Items are not used in the current implementation - event types are used instead
        // Inertia render the Vue page
        return Inertia::render('User/Resident/R_Event_Assist', [
            'items' => [], // Empty array since table doesn't exist and isn't used
        ]);
    }

    public function store(Request $request)
    {
        // Basic validation - modify as needed
        $validated = $request->validate([
            'lastName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'primaryNumber' => 'required|string|max:50',
            'eventDate' => 'required|date',
            'eventTime' => 'required',
            'purpose' => 'required|in:Personal Celebration,Sports Activity,Barangay Escort,Community Event,Religious or Cultural Activity,Logistical Support',
            // Items validation removed - table doesn't exist and isn't used in current implementation
            // 'items' => 'nullable|array',
            // 'items.*.item_id' => 'required_with:items|integer|exists:event_assistance_items,item_id',
            // 'items.*.quantity' => 'required_with:items|integer|min:1',
            'documents.*' => 'nullable|file|max:10240', // adjust file validation
        ]);

        DB::beginTransaction();

        try {
            // Example: create ticket number
            $ticket = 'EA-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

            // Save uploaded documents and collect names
            $uploadedFiles = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('event_documents', 'public'); // adjust disk/path
                    $uploadedFiles[] = $path;
                }
            }

            // Create request row
            $eaRequest = EventAssistanceRequest::create([
                'eventassist_request_ticket' => $ticket,
                'fk_user_id' => $request->user()->user_id ?? null, // use authenticated user id
                'purpose' => $request->input('purpose'),
                'others' => json_encode($uploadedFiles), // store uploaded filenames as JSON in 'others' (or change per schema)
                'location' => $request->input('location', null),
                'status' => 'Pending',
                'fk_approver_id' => null,
                'start_datetime' => $request->input('eventDate') . ' ' . $request->input('eventTime'),
                'end_datetime' => null,
                'created_at' => now(),
                'reviewed_at' => null,
            ]);

            // Create details rows if items provided
            if ($request->filled('items')) {
                foreach ($request->input('items') as $it) {
                    EventAssistanceDetail::create([
                        'fk_eventassist_request_id' => $eaRequest->eventassist_request_id,
                        'fk_item_id' => $it['item_id'],
                        'quantity' => $it['quantity'],
                    ]);
                }
            }

            DB::commit();

            // Return success - Inertia will receive this
            return response()->json([
                'success' => true,
                'request_id' => $eaRequest->eventassist_request_id,
                'ticket' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // log error, then return error
            \Log::error('EA Store error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to create request. '.$e->getMessage()
            ], 500);
        }
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

        // Fetch event assistance requests
        $eventAssistanceRequests = EventAssistanceRequest::select([
                'event_assist_request_id',
                'event_assist_request_ticket',
                'fk_user_id',
                'purpose',
                'event_location',
                'status',
                'extra_fields',
                'created_at',
                'reviewed_at',
                'admin_feedback',
            ])
            ->where('fk_user_id', $userId)
            ->orderByDesc('event_assist_request_id')
            ->limit(100)
            ->get();

        $eventAssistanceRequestsArray = $eventAssistanceRequests->map(function ($m) use ($sanitizeString) {
            // Extract event_type from extra_fields for the title
            $extraFields = $m->extra_fields ?? [];
            $eventType = is_array($extraFields) ? ($extraFields['event_type'] ?? null) : null;
            $title = $eventType ?? $sanitizeString($m->purpose ?? 'Event Assistance Request');
            
            return [
                'event_assist_request_id' => $m->event_assist_request_id,
                'eventassist_request_ticket' => $sanitizeString($m->event_assist_request_ticket ?? ''),
                'fk_user_id' => $m->fk_user_id,
                'purpose' => $sanitizeString($m->purpose ?? ''),
                'title' => $title,
                'event_type' => $eventType,
                'event_location' => $sanitizeString($m->event_location ?? ''),
                'status' => $sanitizeString($m->status ?? 'PENDING'),
                'created_at' => $m->created_at ? $m->created_at->toIso8601String() : null,
                'reviewed_at' => $m->reviewed_at ? $m->reviewed_at->toIso8601String() : null,
                'admin_feedback' => $sanitizeString($m->admin_feedback ?? ''),
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

        // Items are not used in the current implementation - event types are used instead
        // If needed in the future, create the event_assistance_items table migration first

        return Inertia::render('User/Resident/R_Event_Assist', [
            'eventAssistanceRequests' => $eventAssistanceRequestsArray,
            'event_assistance_requests' => $eventAssistanceRequestsArray,
            'items' => [], // Empty array since table doesn't exist and isn't used
            'auth' => ['user' => $userData],
        ]);
    }
}
