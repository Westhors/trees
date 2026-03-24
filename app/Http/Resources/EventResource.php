<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        // جلب حالة حضور العضو الحالي
        $myAttendance = null;
        if (auth()->check()) {
            $attendance = $this->attendances->where('member_id', auth()->id())->first();
            if ($attendance) {
                $myAttendance = $attendance->status;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time ? date('H:i', strtotime($this->time)) : null,
            'location' => $this->location,
            'type' => $this->type,
            'active' => (bool) $this->active,
            'created_at' => $this->created_at,

            'my_attendance' => $myAttendance,

            'statistics' => [
                'total' => $this->attendances->count(),
                'attending' => $this->attendances->where('status', 'attending')->count(),
                'not_attending' => $this->attendances->where('status', 'not_attending')->count(),
                'pending' => $this->attendances->where('status', 'pending')->count(),
            ],

            'attendees' => MemberResource::collection(
                $this->whenLoaded('attendees', function () {
                    return $this->attendees->where('pivot.status', 'attending');
                })
            ),
        ];
    }
}
