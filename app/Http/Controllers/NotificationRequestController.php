<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\EventAssistanceRequest;
use Inertia\Inertia;

class NotificationRequestController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        if (! $userId) {
            abort(403, 'Unauthorized');
        }

        // Document requests for the authenticated user
        $documentRequests = DocumentRequest::with(['documentType','user','userCredential'])
            ->where('fk_user_id', $userId)
            ->orderByDesc('doc_request_id')
            ->get();

        // Event assistance requests for the authenticated user
        // Make sure you have an App\Models\EventAssistanceRequest model
        $eventAssistanceRequests = EventAssistanceRequest::query()
            // ->with(['user', 'approver']) // uncomment/adjust if you add relationships
            ->where('fk_user_id', $userId)
            ->orderByDesc('eventassist_request_id')
            ->get();

        return Inertia::render('User/Resident/R_Notification_Request', [
            'document_requests' => $documentRequests,
            'documentRequests'  => $documentRequests,
            'event_assistance_requests' => $eventAssistanceRequests,
            'eventAssistanceRequests' => $eventAssistanceRequests,
            'auth' => ['user' => auth()->user()],
        ]);
    }

    public function OfficialReq()
    {
        $userId = auth()->id();
        if (! $userId) {
            abort(403, 'Unauthorized');
        }

        // Document requests for the authenticated user
        $documentRequests = DocumentRequest::with(['documentType','user','userCredential'])
            ->where('fk_user_id', $userId)
            ->orderByDesc('doc_request_id')
            ->get();

        // Event assistance requests for the authenticated user
        // Make sure you have an App\Models\EventAssistanceRequest model
        $eventAssistanceRequests = EventAssistanceRequest::query()
            // ->with(['user', 'approver']) // uncomment/adjust if you add relationships
            ->where('fk_user_id', $userId)
            ->orderByDesc('eventassist_request_id')
            ->get();

        return Inertia::render('User/Resident/E_Notification_Request', [
            'document_requests' => $documentRequests,
            'documentRequests'  => $documentRequests,
            'event_assistance_requests' => $eventAssistanceRequests,
            'eventAssistanceRequests' => $eventAssistanceRequests,
            'auth' => ['user' => auth()->user()],
        ]);
    }
}
