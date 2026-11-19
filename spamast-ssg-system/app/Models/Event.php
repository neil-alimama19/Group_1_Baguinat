<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'venue',
        'event_date',
        'day_type',
        'availability',
        'fine_per_miss',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'fine_per_miss' => 'decimal:2',
        ];
    }

    public function sessions()
    {
        return $this->hasMany(EventSession::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, EventSession::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}