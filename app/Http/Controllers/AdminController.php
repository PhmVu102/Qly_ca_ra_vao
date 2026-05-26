<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Shift;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Tổng nhân sự
        |--------------------------------------------------------------------------
        */
        $totalStaff = User::where('role_id', 2)->count();

        /*
        |--------------------------------------------------------------------------
        | Phân ca hôm nay (WORK SCHEDULE)
        |--------------------------------------------------------------------------
        */
        $todaySchedules = WorkSchedule::whereDate('work_date', $today);

        // Tổng số ca đã phân
        $scheduledToday = (clone $todaySchedules)->count();

        /*
        |--------------------------------------------------------------------------
        | Attendance hôm nay
        |--------------------------------------------------------------------------
        */
        $todayAttendance = Attendance::whereDate('work_date', $today);

        // Có check-in
        $presentToday = (clone $todayAttendance)
            ->whereNotNull('check_in_time')
            ->count();

        // Đúng giờ
        $onTimeToday = (clone $todayAttendance)
            ->whereNotNull('check_in_time')
            ->where(function ($q) {
                $q->where('late_minutes', 0)
                  ->orWhereNull('late_minutes');
            })
            ->count();

        // Đi muộn
        $lateToday = (clone $todayAttendance)
            ->where('late_minutes', '>', 0)
            ->count();

        // Về sớm
        $earlyLeaveToday = (clone $todayAttendance)
            ->where('early_leave_minutes', '>', 0)
            ->count();

        // Vắng mặt = có phân ca nhưng chưa check-in
        $absentToday = max(0, $scheduledToday - $presentToday);

        // Thiếu log = đã checkin nhưng chưa checkout
        $incompleteToday = (clone $todayAttendance)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Ca làm việc active
        |--------------------------------------------------------------------------
        */
        $activeShifts = Shift::where('status', 1)
            ->orderBy('start_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Realtime logs hôm nay
        |--------------------------------------------------------------------------
        */
        // AdminController.php
        $recentLogs = AttendanceLog::with([
                'user:id,name,department_id',
                'shift:id,name',
                'workSchedule.shift:id,name',
                'workSchedule.attendanceRecord', // join qua work_schedule_id
            ])
            ->whereDate('scan_time', $today)
            ->latest('scan_time')
            ->take(8)
            ->get();

        // Sau khi load, fallback bằng composite key cho những schedule thiếu work_schedule_id
        $missingIds = $recentLogs
            ->pluck('workSchedule')
            ->filter(fn($ws) => $ws && !$ws->attendanceRecord)
            ->pluck('id');

        if ($missingIds->isNotEmpty()) {
            $fallbackSchedules = WorkSchedule::whereIn('id', $missingIds)->get();

            $fallbackAttendances = \App\Models\Attendance::where(function ($q) use ($fallbackSchedules) {
                foreach ($fallbackSchedules as $s) {
                    $q->orWhere(function ($sub) use ($s) {
                        $sub->where('user_id', $s->user_id)
                            ->whereDate('work_date', $s->work_date)
                            ->where('shift_id', $s->shift_id);
                    });
                }
            })->get()->keyBy(fn($a) => $a->user_id . '|' . \Carbon\Carbon::parse($a->work_date)->format('Y-m-d') . '|' . $a->shift_id);

            $recentLogs->each(function ($log) use ($fallbackAttendances) {
                $ws = $log->workSchedule;
                if ($ws && !$ws->attendanceRecord) {
                    $key = $ws->user_id . '|' . \Carbon\Carbon::parse($ws->work_date)->format('Y-m-d') . '|' . $ws->shift_id;
                    $ws->setRelation('attendanceRecord', $fallbackAttendances->get($key));
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Nhân viên đi muộn hôm nay
        |--------------------------------------------------------------------------
        */
        $lateStaff = Attendance::with([
                'user.department'
            ])
            ->whereDate('work_date', $today)
            ->where('late_minutes', '>', 0)
            ->orderByDesc('late_minutes')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Biểu đồ 7 ngày
        |--------------------------------------------------------------------------
        */
        $weekLabels = [];
        $weekPresent = [];
        $weekLate = [];
        $weekAbsent = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $weekLabels[] = $date->translatedFormat('D');

            $daySchedules = WorkSchedule::whereDate('work_date', $date);

            $dayAttendance = Attendance::whereDate('work_date', $date);

            // Tổng ca đã phân ngày đó
            $dayScheduledCount = (clone $daySchedules)->count();

            // Đúng giờ
            $present = (clone $dayAttendance)
                ->whereNotNull('check_in_time')
                ->where(function ($q) {
                    $q->where('late_minutes', 0)
                      ->orWhereNull('late_minutes');
                })
                ->count();

            // Đi muộn
            $late = (clone $dayAttendance)
                ->where('late_minutes', '>', 0)
                ->count();

            // Vắng
            $absent = max(0, $dayScheduledCount - ($present + $late));

            $weekPresent[] = $present;
            $weekLate[] = $late;
            $weekAbsent[] = $absent;
        }

        return view('admin.dashboard', compact(
            'totalStaff',
            'scheduledToday',
            'presentToday',
            'onTimeToday',
            'lateToday',
            'earlyLeaveToday',
            'absentToday',
            'incompleteToday',
            'activeShifts',
            'recentLogs',
            'lateStaff',
            'weekLabels',
            'weekPresent',
            'weekLate',
            'weekAbsent'
        ));
    }
}
