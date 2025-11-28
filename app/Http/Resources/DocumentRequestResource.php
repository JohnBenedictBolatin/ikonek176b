<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->doc_request_id,
            'doc_request_id' => $this->doc_request_id,
            'doc_request_ticket' => $this->doc_request_ticket,
            'request_number' => $this->doc_request_ticket ?? $this->doc_request_id,
            'purpose' => $this->purpose,
            'title' => $this->purpose ?? $this->documentType?->name ?? $this->pickup_item,
            'applied_processing_fee' => $this->applied_processing_fee,
            'amount' => $this->applied_processing_fee,
            'pickup_location' => $this->pickup_location,
            'pickup_start' => $this->pickup_start,
            'pickup_end' => $this->pickup_end,
            'person_to_look' => $this->person_to_look,
            'status' => $this->status,
            'type' => 'document',
            'created_at' => $this->doc_request_created_at ?? $this->created_at,
            'document_type' => $this->whenLoaded('documentType', fn() => [
            'id' => $this->documentType?->id ?? null,
            'name' => $this->documentType?->name ?? null,
            ]),
            'user' => $this->whenLoaded('user', fn() => [
            'id' => $this->user?->id ?? $this->fk_user_id,
            'first_name' => $this->user?->first_name,
            'last_name' => $this->user?->last_name,
        ]),
        ];
    }
}
