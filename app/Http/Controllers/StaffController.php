<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\AttendanceLog;

class StaffController extends Controller
{
    public function dashboard()
    {
        $this->autoCloseStuckAttendance();
        $today = now()->toDateString();

        // ===== NHIỀU CA HÔM NAY =====
        $todayShifts = DB::table('work_schedules')
            ->join('shifts', 'work_schedules.shift_id', '=', 'shifts.id')
            ->where('work_schedules.user_id', auth()->id())
            ->where('work_schedules.work_date', $today)
            ->select(
                'shifts.id',
                'shifts.name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.late_allow_minutes',
                'shifts.early_leave_allow_minutes'
            )
            ->orderBy('shifts.start_time')
            ->get();

        // Ca đang chạy (chưa check-out)
        $todayAttendance = DB::table('attendances')
            ->join('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->where('attendances.user_id', auth()->id())
            ->whereNull('attendances.check_out_time')
            ->select('attendances.*', 'shifts.name as shift_name')
            ->first();

        // Ca cuối cùng (để hiển thị giờ vào/ra)
        $lastAttendance = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->where('work_date', $today)
            ->orderByDesc('check_in_time')
            ->first();

        // Còn ca nào chưa check-in không
        $checkedInShiftIds = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->where('work_date', $today)
            ->pluck('shift_id')
            ->toArray();

        $now = now();
        
        $hasMoreShifts = $todayShifts->filter(function ($s) use ($checkedInShiftIds, $today, $now) {
            if (in_array($s->id, $checkedInShiftIds)) return false;
            $end   = \Carbon\Carbon::parse($today . ' ' . $s->end_time);
            $start = \Carbon\Carbon::parse($today . ' ' . $s->start_time);
            if ($end->lt($start)) $end->addDay();
            return $now->lte($end);
        })->isNotEmpty();


        $department = DB::table('departments')
            ->where('id', auth()->user()->department_id)
            ->value('name');

        $nextShift = $todayShifts->first(function ($s) use ($checkedInShiftIds, $today, $now) {
            if (in_array($s->id, $checkedInShiftIds)) return false;
            $end   = \Carbon\Carbon::parse($today . ' ' . $s->end_time);
            $start = \Carbon\Carbon::parse($today . ' ' . $s->start_time);
            if ($end->lt($start)) $end->addDay();
            return $now->lte($end);
        });

        return view('staff.dashboard', compact(
            'todayShifts',
            'todayAttendance',
            'lastAttendance',
            'hasMoreShifts',
            'nextShift',
            'department'
        ));
    }
    public function checkIn(Request $request)
    {
        // ===== VALIDATE GPS =====
        if (
            is_null($request->latitude) ||
            is_null($request->longitude) ||
            !is_numeric($request->latitude) ||
            !is_numeric($request->longitude) ||
            $request->latitude < -90  || $request->latitude > 90 ||
            $request->longitude < -180 || $request->longitude > 180
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tọa độ GPS không hợp lệ, vui lòng bật GPS và thử lại'
            ]);
        }

        $today = now()->toDateString();
        $now   = now();

        // ===== KIỂM TRA CA CHƯA CHECK-OUT =====
        $stuckAttendance = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->whereNull('check_out_time')
            ->first();

        if ($stuckAttendance) {
            $shift = DB::table('shifts')
                ->where('id', $stuckAttendance->shift_id)
                ->first();

            if ($shift) {
                $shiftEnd = \Carbon\Carbon::parse(
                    $stuckAttendance->work_date . ' ' . $shift->end_time
                );
                if (\Carbon\Carbon::parse($shift->end_time)->lt(\Carbon\Carbon::parse($shift->start_time)))
                    $shiftEnd->addDay();

                $allowUntil = $shiftEnd->copy()->addMinutes(120);
                if (now()->gt($allowUntil)) {
                    // Ca cũ đã qua giờ kết thúc → tự close
                    DB::table('attendances')
                        ->where('id', $stuckAttendance->id)
                        ->update([
                            'check_out_time'      => $shiftEnd,
                            'total_work_minutes'  => \Carbon\Carbon::parse($stuckAttendance->check_in_time)
                                ->diffInMinutes($shiftEnd),
                            'status'              => 'forgot_checkout',
                            'updated_at'          => now(),
                        ]);
                    // Tiếp tục check-in ca mới bên dưới
                } else {
                    // Ca cũ chưa kết thúc → chặn
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đang trong ca làm, hãy check-out trước'
                    ]);
                }
            }
        }

        // ===== LẤY CÁC CA HÔM NAY =====
        $schedules = DB::table('work_schedules')
            ->join('shifts', 'work_schedules.shift_id', '=', 'shifts.id')
            ->where('work_schedules.user_id', auth()->id())
            ->where('work_schedules.work_date', $today)
            ->select(
                'shifts.id',
                'shifts.name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.late_allow_minutes'
            )
            ->orderBy('shifts.start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Hôm nay bạn không có ca làm'
            ]);
        }

        // ===== LẤY CÁC CA ĐÃ CHECK-IN HÔM NAY =====
        $checkedInShiftIds = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->where('work_date', $today)
            ->pluck('shift_id')
            ->toArray();

        // ===== TÌM CA PHÙ HỢP =====
        $schedule = null;

        foreach ($schedules as $s) {
            // Bỏ qua ca đã check-in rồi
            if (in_array($s->id, $checkedInShiftIds)) continue;

            $start = \Carbon\Carbon::parse($today . ' ' . $s->start_time);
            $end   = \Carbon\Carbon::parse($today . ' ' . $s->end_time);

            if ($end->lt($start)) $end->addDay();

            $allowCheckInFrom = $start->copy()->subMinutes(15);

            // Ca đã qua → bỏ qua
            if ($now->gt($end)) continue;

            // Chưa tới giờ check-in → bỏ qua
            if ($now->lt($allowCheckInFrom)) continue;

            $schedule = $s;
            break;
        }

        if (!$schedule) {
            // Kiểm tra xem còn ca nào chưa check-in không
            $remaining = collect($schedules)->filter(function ($s) use ($checkedInShiftIds) {
                return !in_array($s->id, $checkedInShiftIds);
            });

            if ($remaining->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã hoàn thành tất cả ca hôm nay'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không có ca phù hợp để check-in lúc này'
            ]);
        }

        // ===== GPS — chỉ check đúng location của ca được phân =====
        $companyLocation = DB::table('company_locations')
            ->join('work_schedules', 'work_schedules.location_id', '=', 'company_locations.id')
            ->where('work_schedules.user_id', auth()->id())
            ->where('work_schedules.shift_id', $schedule->id)
            ->where('work_schedules.work_date', $today)
            ->where('company_locations.status', 1)
            ->select('company_locations.*')
            ->first();

        if (!$companyLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Ca làm chưa được gắn vị trí, liên hệ quản trị viên'
            ]);
        }

        $distance = $this->calculateDistance(
            $request->latitude, $request->longitude,
            $companyLocation->latitude, $companyLocation->longitude
        );

        if ($distance > $companyLocation->radius_meter) {
            $distanceText = $distance >= 1000
                ? number_format($distance / 1000, 1) . 'km'
                : round($distance) . 'm';

            return response()->json([
                'success' => false,
                'message' => 'Bạn không ở trong phạm vi công ty (' . $distanceText . ')'
            ]);
        }
        // ===== TÍNH ĐI MUỘN =====
        $shiftStart = \Carbon\Carbon::parse($today . ' ' . $schedule->start_time);
        $shiftEnd   = \Carbon\Carbon::parse($today . ' ' . $schedule->end_time);
        if ($shiftEnd->lt($shiftStart)) $shiftEnd->addDay();

        $lateMinutes = 0;
        if ($now->gt($shiftStart)) {
            $lateMinutes = $shiftStart->diffInMinutes($now);
        }

        $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);
        $halfShift     = $shiftDuration / 2;

        if ($lateMinutes > $halfShift) {
            $status = 'incomplete';
        } elseif ($lateMinutes > ($schedule->late_allow_minutes ?? 0)) {
            $status = 'late';
        } else {
            $status = 'present';
        }

        // ===== INSERT ATTENDANCE =====
        DB::table('attendances')->insert([
            'user_id'            => auth()->id(),
            'shift_id'           => $schedule->id,
            'work_date'          => $today,
            'check_in_time'      => $now,
            'check_in_latitude'  => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'late_minutes'       => $lateMinutes,
            'status'             => $status,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        // ===== INSERT LOG =====
        AttendanceLog::create([
            'user_id'             => auth()->id(),
            'shift_id'            => $schedule->id,
            'company_location_id' => $companyLocation->id,
            'type'                => 'check_in',
            'scan_time'           => $now,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'distance_meter'      => round($distance),
            'ip_address'          => $request->ip(),
            'device_name'         => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in ca ' . $schedule->name . ' thành công'
        ]);
    }

    public function checkOut(Request $request)
    {
        // ===== VALIDATE GPS =====
        if (
            is_null($request->latitude) ||
            is_null($request->longitude) ||
            !is_numeric($request->latitude) ||
            !is_numeric($request->longitude) ||
            $request->latitude < -90  || $request->latitude > 90 ||
            $request->longitude < -180 || $request->longitude > 180
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tọa độ GPS không hợp lệ, vui lòng bật GPS và thử lại'
            ]);
        }
        $today = now()->toDateString();

        // ===== KIỂM TRA ĐÃ CHECK-IN CHƯA =====

        $attendance = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->whereNull('check_out_time')
            ->orderByDesc('work_date')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa check-in'
            ]);
        }

        // ===== KIỂM TRA CÓ CA LÀM KHÔNG =====

        $schedule = DB::table('shifts')
            ->where('id', $attendance->shift_id)
            ->select(
                'start_time',
                'end_time',
                'early_leave_allow_minutes',
                'late_allow_minutes'
            )
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ca làm'
            ]);
        }

        // ===== GPS — chỉ check đúng location của ca đang check-out =====
        $companyLocation = DB::table('company_locations')
            ->join('work_schedules', 'work_schedules.location_id', '=', 'company_locations.id')
            ->where('work_schedules.user_id', auth()->id())
            ->where('work_schedules.shift_id', $attendance->shift_id)
            ->where('work_schedules.work_date', $attendance->work_date)
            ->where('company_locations.status', 1)
            ->select('company_locations.*')
            ->first();

        if (!$companyLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vị trí ca làm, liên hệ quản trị viên'
            ]);
        }

        $distance = $this->calculateDistance(
            $request->latitude, $request->longitude,
            $companyLocation->latitude, $companyLocation->longitude
        );

        if ($distance > $companyLocation->radius_meter) {
            $distanceText = $distance >= 1000
                ? number_format($distance / 1000, 1) . 'km'
                : round($distance) . 'm';

            return response()->json([
                'success' => false,
                'message' => 'Bạn không ở trong phạm vi công ty (' . $distanceText . ')'
            ]);
        }

        $checkIn  = \Carbon\Carbon::parse($attendance->check_in_time);
        $checkOut = now();

        // ===== TÍNH TỔNG PHÚT LÀM =====

        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        // ===== TÍNH THỜI GIAN CHECK-OUT =====

        $workDate = $attendance->work_date;

        $shiftStart = \Carbon\Carbon::parse(
            $workDate . ' ' . $schedule->start_time
        );

        $shiftEnd = \Carbon\Carbon::parse(
            $workDate . ' ' . $schedule->end_time
        );

        // ca qua đêm — nếu end < start thì cộng 1 ngày
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }

        // ===== CHẶN CHECKOUT QUÁ MUỘN =====
        $allowCheckOutUntil = $shiftEnd->copy()->addMinutes(120);

        if ($checkOut->gt($allowCheckOutUntil)) {
            return response()->json([
                'success' => false,
                'message' => 'Đã quá 2 tiếng kể từ khi ca kết thúc, vui lòng liên hệ quản trị viên'
            ]);
        }

        // Cho phép checkout sớm theo cấu hình

        $allowEarlyLeaveMinutes = $schedule->early_leave_allow_minutes ?? 0;

        $allowCheckOutStart = $shiftEnd
            ->copy()
            ->subMinutes($allowEarlyLeaveMinutes);

        if ($checkOut->lt($allowCheckOutStart)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa tới thời gian check-out'
            ]);
        }

        // ===== TÍNH VỀ SỚM =====
        $earlyLeaveMinutes = 0;

        if ($checkOut->lt($shiftEnd)) {
            $earlyLeaveMinutes = $checkOut->diffInMinutes($shiftEnd);
        }

        // ===== STATUS =====
        $isLate       = $attendance->late_minutes > ($schedule->late_allow_minutes ?? 0);
        $isEarlyLeave = $earlyLeaveMinutes > ($schedule->early_leave_allow_minutes ?? 0);

        // Nếu đã incomplete thì giữ nguyên
        if ($attendance->status === 'incomplete') {
            $status = 'incomplete';
        } elseif ($isLate && $isEarlyLeave) {
            $status = 'late_early_leave';
        } elseif ($isLate) {
            $status = 'late';
        } elseif ($isEarlyLeave) {
            $status = 'early_leave';
        } else {
            $status = 'present';
        }
        // ===== UPDATE =====

        DB::table('attendances')
            ->where('id', $attendance->id)
            ->update([
                'check_out_time'      => $checkOut,
                'check_out_latitude'  => $request->latitude,
                'check_out_longitude' => $request->longitude,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'total_work_minutes'  => $totalMinutes,
                'status'              => $status,
                'updated_at'          => now(),
            ]);

        // ===== INSERT LOG =====

        AttendanceLog::create([
            'user_id'             => auth()->id(),
            'shift_id'            => $attendance->shift_id,
            'company_location_id' => $companyLocation->id,
            'type'                => 'check_out',
            'scan_time'           => $checkOut,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'distance_meter'      => round($distance),
            'ip_address'          => $request->ip(),
            'device_name'         => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out thành công'
        ]);
    }
    public function history()
    {
        $this->autoCloseStuckAttendance();
        $attendances = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->orderByDesc('work_date')
            ->orderByDesc('check_in_time')
            ->selectRaw("*, IF(check_out_time IS NULL, 'working', status) as status")  // thêm dòng này
            ->paginate(15);

        return view('staff.history', compact('attendances'));
}
    private function calculateDistance(
        $lat1,
        $lon1,
        $lat2,
        $lon2
    ) {

        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    public function profile()
    {
        $user = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.id', auth()->id())
            ->select(
                'users.*',
                'roles.name as role_name',
                'departments.name as department_name'
            )
            ->first();

        return view('profile.index', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'       => 'required|max:100|regex:/^[\pL\s]+$/u',
            'phone'      => 'nullable|regex:/^(0[35789])[0-9]{8}$/|unique:users,phone,' . auth()->id(),
            'gender'     => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date|before:today',
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = DB::table('users')
            ->where('id', auth()->id())
            ->first();

        $avatarPath = $user->avatar;

        // ===== UPLOAD AVATAR =====

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file     = $request->file('avatar');
            $mime     = $file->getMimeType();
            $filename = 'avatars/' . \Str::random(40) . '.jpg';
            $savePath = storage_path('app/public/' . $filename);

            $src = match($mime) {
                'image/jpeg' => imagecreatefromjpeg($file->getRealPath()),
                'image/png'  => imagecreatefrompng($file->getRealPath()),
                default      => imagecreatefromjpeg($file->getRealPath()),
            };

            $w = imagesx($src);
            $h = imagesy($src);

            $size   = min($w, $h);
            $startX = (int)(($w - $size) / 2);
            $startY = (int)(($h - $size) / 2);

            $dst = imagecreatetruecolor(300, 300);
            imagecopyresampled($dst, $src, 0, 0, $startX, $startY, 300, 300, $size, $size);

            imagejpeg($dst, $savePath, 90);
            imagedestroy($src);
            imagedestroy($dst);

            $avatarPath = $filename;
        }

        // ===== UPDATE USER =====

        DB::table('users')
            ->where('id', auth()->id())
            ->update([

                'name' => $request->name,

                'phone' => $request->phone,

                'gender' => $request->gender,

                'birth_date' => $request->birth_date,

                'avatar' => $avatarPath,

                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            'Cập nhật hồ sơ thành công'
        );
    }
    public function currentShift()
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $now       = now();

        // ===== ƯU TIÊN: đang có attendance chưa check-out =====
        $activeAttendance = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->whereNull('check_out_time')
            ->orderByDesc('work_date')
            ->first();

        if ($activeAttendance) {
            $shift = DB::table('shifts')
                ->where('id', $activeAttendance->shift_id)
                ->select(
                    'name',
                    'start_time',
                    'end_time',
                    'late_allow_minutes',
                    'early_leave_allow_minutes'
                )
                ->first();

            if ($shift) {
                $shift->work_date = $activeAttendance->work_date;
                return response()->json($shift);
            }
        }

        // ===== FALLBACK: tìm ca tiếp theo =====
        $shifts = DB::table('work_schedules')
            ->join('shifts', 'work_schedules.shift_id', '=', 'shifts.id')
            ->where('work_schedules.user_id', auth()->id())
            ->whereIn('work_schedules.work_date', [$today, $yesterday])
            ->select(
                'shifts.name',
                'shifts.start_time',
                'shifts.end_time',
                'shifts.late_allow_minutes',
                'shifts.early_leave_allow_minutes',
                'work_schedules.work_date'
            )
            ->orderBy('work_schedules.work_date')
            ->orderBy('shifts.start_time')
            ->get();

        if ($shifts->isEmpty()) {
            return response()->json(null);
        }

        $activeShift = null;

        foreach ($shifts as $shift) {
            $start = \Carbon\Carbon::parse($shift->work_date . ' ' . $shift->start_time);
            $end   = \Carbon\Carbon::parse($shift->work_date . ' ' . $shift->end_time);

            if ($end->lt($start)) $end->addDay();

            if ($now->gt($end)) continue;

            if ($now->between($start, $end) || $now->lt($start)) {
                $activeShift = $shift;
                break;
            }
        }

        return response()->json($activeShift);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = DB::table('users')->where('id', auth()->id())->first();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        DB::table('users')
            ->where('id', auth()->id())
            ->update([
                'password'   => \Hash::make($request->password),
                'updated_at' => now(),
            ]);

        return back()->with('success_password', 'Đổi mật khẩu thành công');
    }
    public function checkCurrentPassword(Request $request)
    {
        $user = DB::table('users')->where('id', auth()->id())->first();
        $correct = \Hash::check($request->password, $user->password);
        return response()->json(['correct' => $correct]);
    }
    private function autoCloseStuckAttendance()
    {
        $stuck = DB::table('attendances')
            ->where('user_id', auth()->id())
            ->whereNull('check_out_time')
            ->first();

        if (!$stuck) return;

        $shift = DB::table('shifts')->where('id', $stuck->shift_id)->first();
        if (!$shift) return;

        $shiftEnd = \Carbon\Carbon::parse($stuck->work_date . ' ' . $shift->end_time);
        if (\Carbon\Carbon::parse($shift->end_time)->lt(\Carbon\Carbon::parse($shift->start_time)))
            $shiftEnd->addDay();

        $allowUntil = $shiftEnd->copy()->addMinutes(120);

        if (now()->gt($allowUntil)) {
            DB::table('attendances')
                ->where('id', $stuck->id)
                ->update([
                    'check_out_time'     => $shiftEnd,
                    'total_work_minutes' => \Carbon\Carbon::parse($stuck->check_in_time)->diffInMinutes($shiftEnd),
                    'status'             => 'forgot_checkout',
                    'updated_at'         => now(),
                ]);
        }
    }

}
