<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mimeType' => $this->mime_type,
            'size' => $this->size,
            'authorId' => $this->author_id ?? null,
            'previewUrl' => Storage::url($this->preview_url),
            'fullUrl' => $this->full_url,
            'createdAt' => $this->created_at->format('d F, Y'),

        ];
    }
}
