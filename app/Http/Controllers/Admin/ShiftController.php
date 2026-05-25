<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('start_time')
            ->paginate(15);

        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name',
            'start_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'late_allow_minutes' => 'nullable|integer|min:0',
            'early_leave_allow_minutes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $startTime = $this->normalizeTime($request->start_time);
        $endTime = $this->normalizeTime($request->end_time);
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        if ($end->equalTo($start)) {
            return back()->withInput()->withErrors(['end_time' => 'Giờ kết thúc phải khác giờ bắt đầu.']);
        }

        // ==================== CHECK TRÙNG THỜI GIAN (STORE) ====================
        if ($request->boolean('status')) {
            $isOverlapping = Shift::where('status', 1)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })->exists();

            if ($isOverlapping) {
                return back()->withInput()->withErrors([
                    'start_time' => 'Khung giờ này đã bị trùng hoặc giao thoa với một ca làm việc khác đang hoạt động!'
                ]);
            }
        }

        Shift::create([
            'name' => $request->name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'late_allow_minutes' => $request->late_allow_minutes ?? 5,
            'early_leave_allow_minutes' => $request->early_leave_allow_minutes ?? 5,
            'description' => $request->description,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Ca làm việc đã được tạo thành công!');
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,' . $shift->id,
            'start_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'late_allow_minutes' => 'nullable|integer|min:0',
            'early_leave_allow_minutes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $startTime = $this->normalizeTime($request->start_time);
        $endTime = $this->normalizeTime($request->end_time);
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        if ($end->equalTo($start)) {
            return back()->withInput()->withErrors(['end_time' => 'Giờ kết thúc phải khác giờ bắt đầu.']);
        }

        // ==================== CHECK TRÙNG THỜI GIAN (UPDATE) ====================
        if ($request->boolean('status')) {
            $isOverlapping = Shift::where('status', 1)
                ->where('id', '!=', $shift->id) // Bỏ qua chính ca đang sửa này
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })->exists();

            if ($isOverlapping) {
                return back()->withInput()->withErrors([
                    'start_time' => 'Khung giờ cập nhật bị trùng hoặc giao thoa với một ca làm việc khác đang hoạt động!'
                ]);
            }
        }

        $shift->update([
            'name' => $request->name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'late_allow_minutes' => $request->late_allow_minutes ?? 5,
            'early_leave_allow_minutes' => $request->early_leave_allow_minutes ?? 5,
            'description' => $request->description,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Ca làm việc đã được cập nhật!');
    }

    public function destroy(Shift $shift)
    {
        if ($shift->workSchedules()->exists()) {
            return redirect()->route('admin.shifts.index')
                ->with('error', 'Không thể xóa ca làm việc đang được sử dụng!');
        }

        $shift->delete();

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Ca làm việc đã được xóa!');
    }

    private function normalizeTime(string $time): string
    {
        return Carbon::parse($time)->format('H:i');
    }
}
