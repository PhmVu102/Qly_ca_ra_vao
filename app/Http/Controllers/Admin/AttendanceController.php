<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month   = $request->input('month');
        $date    = $request->input('date');
        $status  = $request->input('status');
        $userId  = $request->input('user_id');
        $search  = $request->input('search');

        $query = WorkSchedule::with([
            'user.department',
            'shift',
            'location',
        ]);

        // ==================== FILTER DATE ====================
        if ($month) {
            $query->whereYear('work_date', substr($month, 0, 4))
                ->whereMonth('work_date', substr($month, 5, 2));
        } elseif ($date) {
            $query->whereDate('work_date', $date);
        }

        // ==================== FILTER USER ====================
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // ==================== FILTER SEARCH (MỚI THÊM) ====================
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // ==================== FILTER STATUS ====================
        // VẮNG MẶT
        if ($status === 'absent') {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id');
            })
            // CHỈ TÍNH VẮNG KHI ĐÃ HẾT CA
            ->where(function ($q) {
                // Ngày đã qua
                $q->whereDate('work_date', '<', now()->toDateString())
                    // Hoặc hôm nay nhưng đã quá giờ kết thúc ca
                    ->orWhere(function ($q2) {
                        $q2->whereDate('work_date', now()->toDateString())
                            ->whereHas('shift', function ($shiftQuery) {
                                $shiftQuery->whereTime(
                                    'end_time',
                                    '<',
                                    now()->format('H:i:s')
                                );
                            });
                    });
            });
        }
        // HOÀN THÀNH
        elseif ($status === 'present') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                    ->where('late_minutes', 0)
                    ->where('early_leave_minutes', 0)
                    ->whereNotNull('check_in_time')
                    ->whereNotNull('check_out_time');
            });
        }
        // ĐI MUỘN
        elseif ($status === 'late') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                    ->where('late_minutes', '>', 0);
            });
        }
        // VỀ SỚM
        elseif ($status === 'early_leave') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                    ->where('early_leave_minutes', '>', 0);
            });
        }
        // ĐI MUỘN + VỀ SỚM
        elseif ($status === 'late_early_leave') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                    ->where('late_minutes', '>', 0)
                    ->where('early_leave_minutes', '>', 0);
            });
        }
        // CHƯA CHECKOUT
        elseif ($status === 'incomplete') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('attendances')
                    ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                    ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                    ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                    ->whereNotNull('check_in_time')
                    ->whereNull('check_out_time');
            });
        }

        // ==================== PAGINATE ====================
        $attendances = $query->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get();

        // ==================== LOAD ATTENDANCE ====================
        $pageAttendances = Attendance::whereIn(
            'user_id',
            $attendances->pluck('user_id')->unique()
        )
            ->whereIn(
                'work_date',
                $attendances->pluck('work_date')
                    ->map(fn ($date) => $date->format('Y-m-d'))
                    ->unique()
            )
            ->whereIn(
                'shift_id',
                $attendances->pluck('shift_id')->unique()
            )
            ->get()
            ->keyBy(fn ($attendance) =>
                $attendance->user_id . '|' .
                $attendance->work_date . '|' .
                $attendance->shift_id
            );

        // ==================== MAP ATTENDANCE ====================
        $attendances->transform(function ($schedule) use ($pageAttendances) {
            $schedule->attendance_record = $pageAttendances->get(
                $schedule->user_id . '|' .
                $schedule->work_date->format('Y-m-d') . '|' .
                $schedule->shift_id
            );
            return $schedule;
        });

        // ==================== SUMMARY (Cập nhật đồng bộ với bộ lọc) ====================
        $summaryScheduleQuery = WorkSchedule::query();
        $summaryAttendanceQuery = Attendance::query();

        if ($month) {
            $summaryScheduleQuery->whereYear('work_date', substr($month, 0, 4))
                ->whereMonth('work_date', substr($month, 5, 2));
            $summaryAttendanceQuery->whereYear('work_date', substr($month, 0, 4))
                ->whereMonth('work_date', substr($month, 5, 2));
        } elseif ($date) {
            $summaryScheduleQuery->whereDate('work_date', $date);
            $summaryAttendanceQuery->whereDate('work_date', $date);
        }

        if ($userId) {
            $summaryScheduleQuery->where('user_id', $userId);
            $summaryAttendanceQuery->where('user_id', $userId);
        }

        // Áp dụng search vào summary luôn để số liệu thống kê chuẩn xác theo từ khóa tìm kiếm
        if (!empty($search)) {
            $summaryScheduleQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
            $summaryAttendanceQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        $summary = [
            'total'       => (clone $summaryScheduleQuery)->count(),
            'present'     => (clone $summaryAttendanceQuery)
                ->whereNotNull('check_in_time')
                ->count(),
            'late'        => (clone $summaryAttendanceQuery)
                ->where('late_minutes', '>', 0)
                ->count(),
            'early_leave' => (clone $summaryAttendanceQuery)
                ->where('early_leave_minutes', '>', 0)
                ->count(),
            'incomplete'  => (clone $summaryAttendanceQuery)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->count(),
        ];

        // ==================== USERS ====================
        $users = User::where('role_id', 2)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'employee_code'
            ]);

        // ==================== STATUS ====================
        $statuses = [
            'present'          => 'Hoàn thành',
            'late'             => 'Đi muộn',
            'early_leave'      => 'Về sớm',
            'late_early_leave' => 'Đi muộn và về sớm',
            'absent'           => 'Vắng mặt',
            'incomplete'       => 'Muộn quá nửa ca',
            'forgot_checkout'  => 'Chưa check-out (tự đóng ca)',
            'working'          => 'Đang làm',
            'upcoming'         => 'Sắp tới',
        ];

        return view('admin.attendance.index', compact(
            'attendances',
            'users',
            'statuses',
            'summary',
            'date',
            'month',
            'status',
            'userId',
            'search'
        ));
    }
}
