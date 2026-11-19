<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'event_session_id',
        'student_id',
        'check_in_at',
        'check_out_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function session()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}