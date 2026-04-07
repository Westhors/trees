<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberTreeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'national_id' => $this->national_id,
            'date_of_birth' => $this->date_of_birth,
            'city' => $this->city,
            'mother_name' => $this->mother_name,
            'wife_name' => $this->wife_name,
            'active' => (bool) $this->active,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name
            ] : null,
            'children' => MemberTreeResource::collection($this->children)
        ];
    }
}
