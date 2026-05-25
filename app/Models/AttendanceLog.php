<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'work_schedule_id',
        'company_location_id',
        'type',
        'scan_time',
        'latitude',
        'longitude',
        'distance_meter',
        'ip_address',
        'device_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function companyLocation()
    {
        return $this->belongsTo(CompanyLocation::class);
    }
}
