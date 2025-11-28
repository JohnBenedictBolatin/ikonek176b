<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EventAssistanceItem;
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
        // load items for the items dropdown
        $items = EventAssistanceItem::select('item_id','item_name','category')->get();

        // Inertia render the Vue page and pass items
        return Inertia::render('User/Resident/R_Event_Assist', [
            'items' => $items,
            // You can pass other props as needed
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
            'items' => 'nullable|array',
            'items.*.item_id' => 'required_with:items|integer|exists:event_assistance_items,item_id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
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
}
