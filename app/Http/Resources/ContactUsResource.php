<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'subject' => $this->subject,
            'message' => $this->message,
            'type' => $this->type,
            'deleted' => isset($this->deleted_at),
            'deletedAt' => $this->deleted_at ? $this->deleted_at->format('d M, Y - H:i A') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('Y-M-d H:i:s A') : null,
            'sentSince' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'createdAt' => $this->created_at ? $this->created_at->format('F d, Y - h:i A') : null,
        ];
    }
}

