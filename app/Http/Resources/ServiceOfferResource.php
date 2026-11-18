<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category?->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'price_type' => $this->price_type,
            'work_mode' => $this->work_mode,
            'location' => $this->location,
            'portfolio_url' => $this->portfolio_url,
            'status' => $this->status,
            'provider' => new UserResource($this->whenLoaded('provider')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

