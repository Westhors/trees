<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'role'       => $this->role ?? null,
            'phone' => $this->phone,
            'active' => $this->active ?? null,
            'createdAt' => $this->created_at ? $this->created_at->format('d-M-Y H:i A') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('d-M-Y H:i A') : null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
        ];
    }
}


