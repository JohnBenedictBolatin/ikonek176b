<?php

namespace App\Http\Controllers;

use App\Models\EventAssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventAssistanceRequestController extends Controller
{
    public function store(Request $request)
    {
        // basic validation - adjust rules as needed
        $validated = $request->validate([
            'event_type' => 'required|string',
            'purpose' => 'required|string',
            'others' => 'nullable|string',
            'location' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'start_datetime' => 'nullable|date',
            'duration' => 'nullable|numeric',
            'attendees' => 'nullable|integer',
        ]);

        // Generate next ticket number
        $lastRequest = EventAssistanceRequest::orderBy('eventassist_request_id', 'desc')->first();

        if ($lastRequest && preg_match('/EA-(\d+)/', $lastRequest->eventassist_request_ticket, $matches)) {
            $lastNumber = intval($matches[1]);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $ticket = "EA-" . $nextNumber;

        // Build start_datetime: prefer combined start_datetime if passed, otherwise combine date+time
        if (!empty($request->start_datetime)) {
            $start = Carbon::parse($request->start_datetime);
        } elseif (!empty($request->event_date) && !empty($request->event_time)) {
            $start = Carbon::parse($request->event_date . ' ' . $request->event_time);
        } else {
            $start = null;
        }

        // Compute end datetime if duration provided (in hours)
        $end = null;
        if ($start && !empty($request->duration)) {
            $end = $start->copy()->addHours(floatval($request->duration));
        }

        $created = EventAssistanceRequest::create([
            'eventassist_request_ticket' => $ticket,
            'fk_user_id' => $request->user()->user_id ?? $request->user()->id ?? auth()->id(),
            'purpose' => $request->purpose,
            'others' => $request->others ?? null,
            'location' => $request->location ?? $request->event_type,
            'status' => 'Pending',
            'fk_approver_id' => null,
            'start_datetime' => $start ? $start->toDateTimeString() : null,
            'end_datetime' => $end ? $end->toDateTimeString() : null,
            'created_at' => now(),
            'reviewed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'id' => $created->eventassist_request_id,
        ], 201);
    }
}
