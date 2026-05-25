<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'work_schedule_id', 'work_date', 'check_in_time',
        'check_out_time', 'late_minutes', 'early_leave_minutes',
        'total_work_minutes', 'status', 'note'
    ];

    // === RELATIONSHIPS ===
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
