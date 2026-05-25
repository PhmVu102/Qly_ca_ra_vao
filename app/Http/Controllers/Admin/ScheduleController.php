<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyLocation;
use App\Models\Shift;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = WorkSchedule::with('user', 'shift', 'location')
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $users = User::where('role_id', 2)
            ->where('status', true)
            ->where('is_locked', false)
            ->orderBy('name')
            ->get();

        $shifts = Shift::where('status', true)
            ->orderBy('start_time')
            ->get();

        $locations = CompanyLocation::where('status', true)
            ->get();

        return view('admin.schedules.create', compact('users', 'shifts', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'location_id' => 'required|exists:company_locations,id',
            'work_date' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string',
        ]);

        $existingUserIds = WorkSchedule::whereIn('user_id', $request->user_ids)
            ->where('shift_id', $request->shift_id)
            ->where('work_date', $request->work_date)
            ->pluck('user_id')
            ->all();

        if (!empty($existingUserIds)) {
            $existingNames = User::whereIn('id', $existingUserIds)
                ->orderBy('name')
                ->pluck('name')
                ->implode(', ');

            return redirect()->back()
                ->with('error', 'Nhân viên đã được phân công ca này trong ngày đã chọn: ' . $existingNames)
                ->withInput();
        }

        foreach ($request->user_ids as $userId) {
            WorkSchedule::create([
                'user_id' => $userId,
                'shift_id' => $request->shift_id,
                'location_id' => $request->location_id,
                'work_date' => $request->work_date,
                'note' => $request->note,
            ]);
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Đã phân công ca làm việc thành công!');
    }

    public function edit(WorkSchedule $schedule)
    {
        $users = User::where('role_id', 2)
            ->where('status', true)
            ->where('is_locked', false)
            ->orderBy('name')
            ->get();

        $shifts = Shift::where('status', true)
            ->orderBy('start_time')
            ->get();

        $locations = CompanyLocation::where('status', true)
            ->get();

        return view('admin.schedules.edit', compact('schedule', 'users', 'shifts', 'locations'));
    }

    public function update(Request $request, WorkSchedule $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'location_id' => 'required|exists:company_locations,id',
            'work_date' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string',
        ]);

        // Allow multiple shifts in the same day, but prevent the same shift twice.
        $existing = WorkSchedule::where('user_id', $request->user_id)
            ->where('shift_id', $request->shift_id)
            ->where('work_date', $request->work_date)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Nhân viên đã được phân công ca này trong ngày đã chọn!')
                ->withInput();
        }

        $schedule->update([
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'location_id' => $request->location_id,
            'work_date' => $request->work_date,
            'note' => $request->note,
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Đã cập nhật phân công thành công!');
    }

    public function destroy(WorkSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Đã xóa phân công!');
    }

    public function getAssignedUsers(Request $request)
    {
        $shiftId = $request->query('shift_id');
        $workDate = $request->query('work_date');

        if (!$shiftId || !$workDate) {
            return response()->json([]);
        }

        try {
            // Chuyển đổi ngày nhận từ Input sang định dạng chuẩn Y-m-d của MySQL
            $formattedDate = Carbon::parse($workDate)->format('Y-m-d');

            // Lấy danh sách ID nhân viên đã bận lịch ca này, ngày này
            $assignedUserIds = \App\Models\WorkSchedule::where('shift_id', $shiftId)
                ->whereDate('work_date', $formattedDate) // Sử dụng whereDate để so sánh ngày chuẩn xác hơn
                ->pluck('user_id')
                ->map(function($id) {
                    return (int) $id; // Ép kiểu về số nguyên (int) để đồng bộ với JS
                })
                ->toArray();

            return response()->json($assignedUserIds);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
