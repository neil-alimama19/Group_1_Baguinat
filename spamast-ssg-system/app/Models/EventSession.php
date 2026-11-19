<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSession extends Model
{
    protected $fillable = [
        'event_id',
        'period',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'check_in_open',
        'check_out_open',
    ];

    protected function casts(): array
    {
        return [
            'check_in_open' => 'boolean',
            'check_out_open' => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if the session is currently open for check-in.
     */
    public function isCheckInOpen(): bool
    {
        return $this->isOpen('check_in');
    }

    /**
     * Check if the session is currently open for check-out.
     */
    public function isCheckOutOpen(): bool
    {
        return $this->isOpen('check_out');
    }

    /**
     * Private helper to determine open status based on manual/scheduled settings.
     */
    private function isOpen(string $type): bool
    {
        $event = $this->event;

        // 1. Manual availability: rely on the boolean toggle set by the Officer
        if ($event->availability === 'manual') {
            return $type === 'check_in' ? $this->check_in_open : $this->check_out_open;
        }

        // 2. Scheduled availability: checks the date and time window
        // It must be today
        if (!$event->event_date->isToday()) {
            return false;
        }

        $now = now()->format('H:i:s');
        $startField = "{$type}_start";
        $endField = "{$type}_end";

        $start = $this->$startField;
        $end = $this->$endField;

        if (!$start || !$end) {
            return false;
        }

        return $now >= $start && $now <= $end;
    }
}