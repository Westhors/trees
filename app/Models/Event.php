<?php

namespace App\Models;

use App\Traits\HasMedia;

class Event extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];
    protected $guarded = ['id'];

      protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'active' => 'boolean',
    ];

    // العلاقات
    public function attendances()
    {
        return $this->hasMany(EventAttendance::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(Member::class, 'event_attendances')
            ->withPivot('status', 'note')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'event_attendances')
            ->withPivot('status', 'note')
            ->withTimestamps();
    }

    public function attendingMembers()
    {
        return $this->belongsToMany(Member::class, 'event_attendances')
            ->wherePivot('status', 'attending');
    }

    // Scope للفعاليات النشطة
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope للفعاليات القادمة
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now());
    }
}
