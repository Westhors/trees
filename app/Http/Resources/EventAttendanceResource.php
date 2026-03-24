<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventAttendanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event' => new EventResource($this->whenLoaded('event')),
            'member' => new MemberResource($this->whenLoaded('member')),
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
}
