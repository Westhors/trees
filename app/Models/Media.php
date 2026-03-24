<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $file_name
 * @property string|null $file_path
 * @property string $mime_type
 * @property int $size
 * @property int|null $author_id
 * @property string|null $disk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $file_type
 * @property-read string $full_url
 * @property-read string $path
 * @property-read mixed $preview_url
 * @property-read string $url
 * @property-read Model|Eloquent $modelable
 * @method static Builder|Media month($date)
 * @method static Builder|Media newModelQuery()
 * @method static Builder|Media newQuery()
 * @method static Builder|Media query()
 * @method static Builder|Media search($term)
 * @method static Builder|Media type($type)
 * @method static Builder|Media whereAuthorId($value)
 * @method static Builder|Media whereCreatedAt($value)
 * @method static Builder|Media whereDisk($value)
 * @method static Builder|Media whereFileName($value)
 * @method static Builder|Media whereFilePath($value)
 * @method static Builder|Media whereId($value)
 * @method static Builder|Media whereMimeType($value)
 * @method static Builder|Media whereName($value)
 * @method static Builder|Media whereSize($value)
 * @method static Builder|Media whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Media extends Model
{
    protected $guarded = [];

    protected static array $types = [
        'image' => [
            'image/gif',
            'image/avif',
            'image/apng',
            'image/png',
            'image/svg+xml',
            'image/webp',
            'image/jpeg'
        ],
        'audio' => [
            'audio/mpeg',
            'audio/aac',
            'audio/wav',
        ],
        'video' => [
            'video/mp4',
            'video/webm',
            'video/mpeg',
            'video/x-msvideo',
        ],
        'document' => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ],
        'archive' => [
            'application/zip',
            'application/x-7z-compressed',
            'application/gzip',
            'application/vnd.rar',
        ],
    ];

    public function getFileTypeAttribute(): string
    {
        foreach (self::$types as $type => $mimes) {
            if (in_array($this->mime_type, $mimes, true)) {
                return $type;
            }
        }
        return 'other';
    }

    public function getPreviewUrlAttribute()
    {
        $urls = collect([
            'image' => Storage::url($this->file_path),
            'audio' => asset('images/file-type-audio.svg'),
            'video' => asset('images/file-type-video.svg'),
            'document' => asset('images/file-type-document.svg'),
            'archive' => asset('images/file-type-archive.svg'),
            'other' => asset("images/file-type-other.svg")
        ]);

        return $urls[$this->file_type];
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }


    public function getFullUrlAttribute(): string
    {
        return url('/')  . Storage::url($this->file_path);
    }


    public function getPathAttribute(): string
    {
        return (string)$this->file_path;
    }

    public static function getMimes($fileType)
    {
        return self::$types[$fileType] ?? [];
    }

    public function scopeType(Builder $builder, $type): Builder
    {
        if (!is_null($type)) {
            $builder->whereIn('mime_type', self::getMimes($type));
        }

        return $builder;
    }

    public function scopeMonth(Builder $builder, $date): Builder
    {
        if (!is_null($date)) {
            $builder->whereBetween('created_at', [
                Carbon::createFromFormat('d-m-Y', $date)?->startOfMonth(),
                Carbon::createFromFormat('d-m-Y', $date)?->endOfMonth(),
            ]);
        }

        return $builder;
    }

    public function scopeSearch(Builder $builder, $term): Builder
    {
        if (!is_null($term)) {
            $builder->where('name', 'LIKE', "%$term%");
        }

        return $builder;
    }

    public function modelable(): MorphTo
    {
        return $this->morphTo();
    }

}
