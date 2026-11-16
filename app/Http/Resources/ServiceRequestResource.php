<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category?->name,
            'category_id' => $this->category_id,
            'budget_min' => $this->budget_min,
            'budget_max' => $this->budget_max,
            'work_mode' => $this->work_mode,
            'location' => $this->location,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

