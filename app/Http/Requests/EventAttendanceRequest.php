<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'status' => 'required|in:attending,not_attending,pending',
            'note' => 'nullable|string',
        ];
    }
}
