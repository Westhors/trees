<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name ?? null,
            'createdAt' => $this->created_at ? $this->created_at->format('F d, Y - h:i A') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('F d, Y - h:i A') : null,
        ];
    }
}


