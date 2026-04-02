<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name ?? null,
            'gender'        => $this->gender ?? null,
            'phone'         => $this->phone ?? null,
            'national_id'   => $this->national_id ?? null,
            'date_of_birth' => $this->date_of_birth ?? null,
            'city'          => $this->city ?? null,
            'mother_name'   => $this->mother_name ?? null,
            'wife_name'     => $this->wife_name ?? null,
            'active'        => (bool) $this->active,
            'personal_relationships' => $this->personal_relationships ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'branch' => $this->branch
                ? [
                    'id'   => $this->branch->id,
                    'name' => $this->branch->name,
                ]
                : null,

            'father' => $this->father
                ? [
                    'id'   => $this->father->id,
                    'name' => $this->father->name,
                ]
                : null,
            'dead' => (bool) $this->dead,
            // // عرض الأبناء بشكل متداخل
            // 'children' => MemberResource::collection($this->whenLoaded('children')),
        ];
    }
}
