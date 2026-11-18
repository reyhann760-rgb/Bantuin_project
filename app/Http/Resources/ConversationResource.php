<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'provider' => new UserResource($this->whenLoaded('provider')),
            'service_request_id' => $this->service_request_id,
            'service_request_title' => $this->serviceRequest?->title,
            'service_offer_id' => $this->service_offer_id,
            'service_offer_title' => $this->serviceOffer?->title,
            'status' => $this->status,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'last_message' => new MessageResource($this->messages->first()),
        ];
    }
}

