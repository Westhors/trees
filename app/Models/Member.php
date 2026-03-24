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

    protected $casts = [
        'dead' => 'boolean',
        'active' => 'boolean',
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

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_attendances')
            ->withPivot('status', 'note')
            ->withTimestamps();
    }

    public function eventAttendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_attendances')
            ->wherePivot('status', 'attending');
    }
}
