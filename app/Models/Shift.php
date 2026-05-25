<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_allow_minutes',
        'early_leave_allow_minutes',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'late_allow_minutes' => 'integer',
            'early_leave_allow_minutes' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }
}
