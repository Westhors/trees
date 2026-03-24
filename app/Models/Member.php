<?php

namespace App\Models;

use App\Traits\HasMedia;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;

class Member extends BaseModel
{
    use HasMedia , HasApiTokens  , AuthAuthenticatable;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    public function father()
    {
        return $this->belongsTo(Member::class,'father_id');
    }

    public function children()
    {
        return $this->hasMany(Member::class,'father_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
