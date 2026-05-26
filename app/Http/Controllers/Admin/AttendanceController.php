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
            'attendanceRecord',
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
        // ĐANG LÀM
        elseif ($status === 'working') {
            $query->whereHas('shift', function ($shiftQuery) {
                $shiftQuery->whereRaw("
                    NOW() BETWEEN TIMESTAMP(work_schedules.work_date, shifts.start_time)
                    AND CASE
                        WHEN shifts.end_time < shifts.start_time
                            THEN DATE_ADD(TIMESTAMP(work_schedules.work_date, shifts.end_time), INTERVAL 1 DAY)
                        ELSE TIMESTAMP(work_schedules.work_date, shifts.end_time)
                    END
                ");
            })->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('attendances')
                        ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                        ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                        ->whereColumn('attendances.shift_id', 'work_schedules.shift_id')
                        ->whereNotNull('check_in_time')
                        ->whereNull('check_out_time');
                })->orWhereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('attendances')
                        ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                        ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                        ->whereColumn('attendances.shift_id', 'work_schedules.shift_id');
                });
            });
        }
        // SẮP TỚI
        elseif ($status === 'upcoming') {
            $query->whereDoesntHave('attendanceRecord')
                ->whereHas('shift', function ($shiftQuery) {
                    $shiftQuery->whereRaw("NOW() < TIMESTAMP(work_schedules.work_date, shifts.start_time)");
                })
                ->whereNotExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('attendances')
                        ->whereColumn('attendances.user_id', 'work_schedules.user_id')
                        ->whereColumn('attendances.work_date', 'work_schedules.work_date')
                        ->whereColumn('attendances.shift_id', 'work_schedules.shift_id');
                });
        }

        // ==================== PAGINATE ====================
        $attendances = $query->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get();

        // ==================== LOAD ATTENDANCE ====================
        // Lấy danh sách ID lịch trình để map chính xác tuyệt đối, tránh việc shift_id bị NULL
        $workScheduleIds = $attendances->pluck('id')->unique()->filter()->toArray();

        $pageAttendances = Attendance::whereIn('work_schedule_id', $workScheduleIds)
            ->orWhere(function ($q) use ($attendances) {
                $q->where(function ($sub) use ($attendances) {
                    foreach ($attendances as $schedule) {
                        $sub->orWhere(function ($item) use ($schedule) {
                            $item->where('user_id', $schedule->user_id)
                                ->whereDate('work_date', $schedule->work_date)
                                ->where('shift_id', $schedule->shift_id);
                        });
                    }
                });
            })
            ->get();

        // Map attendances theo key để gán lại cho work_schedules chính xác tuyệt đối
        $mappedAttendances = [];
        foreach ($pageAttendances as $att) {
            if ($att->work_schedule_id) {
                $mappedAttendances['schedule_' . $att->work_schedule_id] = $att;
            }
            $dateStr = \Carbon\Carbon::parse($att->work_date)->format('Y-m-d');

            $mappedAttendances[
                'user_' . $att->user_id .
                '|date_' . $dateStr .
                '|shift_' . $att->shift_id
            ] = $att;
        }

        // ==================== MAP ATTENDANCE ====================
        $attendances->transform(function ($schedule) use ($mappedAttendances) {
            $keyBySchedule = 'schedule_' . $schedule->id;
            $keyByUser =
                'user_' . $schedule->user_id .
                '|date_' . \Carbon\Carbon::parse($schedule->work_date)->format('Y-m-d') .
                '|shift_' . $schedule->shift_id;

            // Tìm bản ghi chấm công khớp nhất
            $attendanceRecord = $mappedAttendances[$keyBySchedule] ?? ($mappedAttendances[$keyByUser] ?? null);

            $schedule->attendance_record = $attendanceRecord;

            return $schedule;
        });

        // ==================== SUMMARY (Tính toán dựa trên trạng thái thực tế của data) ====================
        $summaryScheduleQuery = WorkSchedule::query();

        if ($month) {
            $summaryScheduleQuery->whereYear('work_date', substr($month, 0, 4))
                ->whereMonth('work_date', substr($month, 5, 2));
        } elseif ($date) {
            $summaryScheduleQuery->whereDate('work_date', $date);
        }

        if ($userId) {
            $summaryScheduleQuery->where('user_id', $userId);
        }

        if (!empty($search)) {
            $summaryScheduleQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%");
            });
        }

        // Lấy toàn bộ lịch trình kèm chấm công để tự đếm số lượng theo trạng thái mà không phụ thuộc cột status của DB
        $summarySchedules = $summaryScheduleQuery->get();
        $summaryScheduleIds = $summarySchedules->pluck('id')->toArray();

        $summaryAttendances = Attendance::whereIn('work_schedule_id', $summaryScheduleIds)
            ->orWhere(function($q) use ($summarySchedules) {
                $q->whereIn('user_id', $summarySchedules->pluck('user_id')->unique())
                  ->whereIn('work_date', $summarySchedules->pluck('work_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->unique());
            })->get();

        $attLookUp = [];
        foreach ($summaryAttendances as $att) {
            if ($att->work_schedule_id) $attLookUp['schedule_'.$att->work_schedule_id] = $att;
            $attLookUp['user_'.$att->user_id.'|'.\Carbon\Carbon::parse($att->work_date)->format('Y-m-d')] = $att;
        }

        // Khởi tạo bộ đếm danh mục trạng thái tương thích hoàn toàn với Controller của bạn
        $total = $summarySchedules->count();
        $presentCount = 0;
        $lateCount = 0;
        $earlyCount = 0;
        $incompleteCount = 0;

        foreach ($summarySchedules as $s) {
            $record = $attLookUp['schedule_'.$s->id] ?? ($attLookUp['user_'.$s->user_id.'|'.\Carbon\Carbon::parse($s->work_date)->format('Y-m-d')] ?? null);
            if ($record) {
                if ($record->check_in_time) {
                    $presentCount++; // Đã check-in ít nhất 1 lần
                }
                if ($record->late_minutes > 0) {
                    $lateCount++;
                }
                if ($record->early_leave_minutes > 0) {
                    $earlyCount++;
                }
                if ($record->check_in_time && !$record->check_out_time) {
                    $incompleteCount++; // Chưa check-out
                }
            }
        }

        $summary = [
            'total'       => $total,
            'present'     => $presentCount,
            'late'        => $lateCount,
            'early_leave' => $earlyCount,
            'incomplete'  => $incompleteCount,
        ];

        // ==================== USERS ====================
        $users = User::where('role_id', 2)
            ->orderBy('name')
            ->get(['id', 'name', 'employee_code']);

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
