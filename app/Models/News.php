<?php

namespace App\Models;

use App\Traits\HasMedia;

class News extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];
}
