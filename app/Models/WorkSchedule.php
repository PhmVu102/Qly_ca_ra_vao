<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'location_id',
        'work_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function location()
    {
        return $this->belongsTo(CompanyLocation::class);
    }

    public function attendanceRecord()
    {
        return $this->hasOne(
            \App\Models\Attendance::class,
            'work_schedule_id',
            'id'
        );
    }

    public function getAttendanceAttribute(): ?\App\Models\Attendance
    {
        if ($this->relationLoaded('attendanceRecord') && $this->attendanceRecord) {
            return $this->attendanceRecord;
        }

        return \App\Models\Attendance::where('user_id', $this->user_id)
            ->whereDate('work_date', $this->work_date)
            ->where('shift_id', $this->shift_id)
            ->first();
    }
}
