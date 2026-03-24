<?php

namespace App\Traits;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

trait HasMedia
{

    public function hasMedia(): bool
    {
        if($this->media->count()){
            return true ;
        }
        return false ;
    }

    public function getMediaResource($collection = 'default'): AnonymousResourceCollection
    {
        return MediaResource::collection($this->getMedia($collection));
    }

    public function getFirstMediaResource($collection = 'default'): MediaResource
    {
        return new MediaResource($this->getFirstMedia($collection));
    }

    public function getFirstMediaUrl($collection = 'default'): string
    {
        return $this->hasMedia() && $this->getFirstMedia($collection) ? $this->getFirstMedia($collection)->full_url  :  url('/') . Storage::url('/images/default-logo.png');
    }

    public function getFirstMediaUrlTeam($collection = 'default'): string
    {
        return $this->hasMedia() && $this->getFirstMedia($collection) ? $this->getFirstMedia($collection)->full_url  :  url('/') . Storage::url('/images/profile-avatar-male.png');
    }

    public function getFirstMediaUrlNot($collection = 'default')
    {
        return $this->hasMedia() && $this->getFirstMedia($collection) ? $this->getFirstMedia($collection)->full_url  : null;
    }

    public function getMedia($collection = 'default')
    {
        return $this->media->where('pivot.collection' , $collection);
    }

    public function getFirstMedia($collection = 'default')
    {
        return $this->media->where('pivot.collection' , $collection)->first();
    }

    public function getAllMedia()
    {
        return $this->media;
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(
            Media::class,
            'model',
            'mediable',
            'model_id',
            'media_id'
        )->withPivot(['collection']);
    }

}
