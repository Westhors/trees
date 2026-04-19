<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title ?? null,
            'summary'       => $this->summary ?? null,
            'content'       => $this->content ?? null,
            'active'        => (bool) $this->active,
            'gallery' => $this->getMediaResource('gallery') ?: null,
            'createdAt' => $this->created_at ? $this->created_at->format('F d, Y - h:i A') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('F d, Y - h:i A') : null,
        ];
    }
}


