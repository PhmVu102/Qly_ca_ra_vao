<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        // Tổng nhân viên
        $totalStaff = User::where('role_id', 2)->count();

        // Hôm nay
        $scheduledToday = Attendance::whereDate('work_date', $today)->count();
        $presentToday   = Attendance::whereDate('work_date', $today)
                            ->whereNotNull('check_in_time')
                            ->count();

        $lateToday      = Attendance::whereDate('work_date', $today)
                            ->where('late_minutes', '>', 0)
                            ->count();

        $earlyLeaveToday = Attendance::whereDate('work_date', $today)
                            ->where('early_leave_minutes', '>', 0)
                            ->count();

        $absentToday    = $scheduledToday - $presentToday;
        $incompleteToday = Attendance::whereDate('work_date', $today)
                            ->whereNull('check_out_time')
                            ->count();

        // Logs gần nhất
        $recentLogs = AttendanceLog::with([
            'user:id,name,department_id',
            'user.department:id,name'
        ])
        ->latest('scan_time')
        ->take(8)
        ->get();

        // Nhân viên đi muộn hôm nay
        $lateStaff = Attendance::with('user.department')
                        ->whereDate('work_date', $today)
                        ->where('late_minutes', '>', 0)
                        ->orderBy('late_minutes', 'desc')
                        ->take(6)
                        ->get();

        // Dữ liệu biểu đồ 7 ngày
        $weekLabels = [];
        $weekPresent = [];
        $weekLate = [];
        $weekAbsent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weekLabels[] = $date->format('D');

            $present = Attendance::whereDate('work_date', $date)->where('late_minutes', 0)->count();
            $late    = Attendance::whereDate('work_date', $date)->where('late_minutes', '>', 0)->count();
            $absent  = User::where('role_id', 2)->count() - ($present + $late); // tạm tính

            $weekPresent[] = $present;
            $weekLate[]    = $late;
            $weekAbsent[]  = $absent;
        }

        return view('admin.dashboard', compact(
            'totalStaff',
            'presentToday',
            'scheduledToday',
            'lateToday',
            'earlyLeaveToday',
            'absentToday',
            'incompleteToday',
            'recentLogs',
            'lateStaff',
            'weekLabels',
            'weekPresent',
            'weekLate',
            'weekAbsent'
        ));
    }
}
