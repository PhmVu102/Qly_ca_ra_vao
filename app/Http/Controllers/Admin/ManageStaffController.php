<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class ManageStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['department', 'role'])
            ->orderByRaw('CASE WHEN role_id = 1 THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.staff.index', [
            'staff' => $users,
            'title' => 'Quản lý Tài khoản'
        ]);
    }

    public function create()
    {
        $nextCode    = $this->generateEmployeeCode();
        $departments = Department::orderBy('name')->get();
        $roles       = \App\Models\Role::orderBy('name')->get();

        return view('admin.staff.create', compact('nextCode', 'departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => 'required|exists:roles,id',
            'employee_code' => 'required|string|max:50|unique:users,employee_code',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
        ]);
        dd('VALIDATION PASSED');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'employee_code' => $request->employee_code,
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'password' => bcrypt('password123'),
        ]);
        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Thêm nhân viên thành công!');
    }

    public function edit(User $staff)
    {
        $departments = Department::all();

        if ($staff->hire_date) {
            $staff->hire_date = \Carbon\Carbon::parse($staff->hire_date);
        }

        return view('admin.staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email,' . $staff->id,
            ],
            'employee_code' => [
                'required',
                'string',
                'max:50',
                'unique:users,employee_code,' . $staff->id,
            ],
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
            'role_id' => [
                'required',
                'exists:roles,id',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'hire_date' => [
                'nullable',
                'date',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ], [
            'email.regex' => 'Email không đúng định dạng.',
        ]);
        $staff->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'employee_code' => $request->employee_code,
            'department_id' => $request->department_id,
            'role_id'       => $request->role_id,
            'phone'         => $request->phone,
            'hire_date'     => $request->hire_date,
            'status'        => $request->status,
        ]);
        return redirect()->route('admin.staff.index')
            ->with('success', 'Cập nhật nhân viên thành công!');
    }

    public function destroy(User $staff)
    {
        if ($staff->role_id == 1) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Không được xóa tài khoản Admin!');
        }
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Đã xóa nhân viên thành công!');
    }

    /**
     * Toggle lock/unlock staff account
     */
    public function toggleLock(User $staff)
    {
        // Không cho khóa Admin chính
        if ($staff->role_id == 1) {
            return redirect()->route('admin.staff.index')
                            ->with('error', 'Không được khóa tài khoản Admin!');
        }

        $staff->update(['is_locked' => !$staff->is_locked]);

        $message = $staff->is_locked ? 'Nhân viên đã được khóa.' : 'Nhân viên đã được mở khóa.';

        return redirect()->route('admin.staff.index')->with('success', $message);
    }

    private function generateEmployeeCode(): string
    {
        $prefix = 'NV';

        $last = \App\Models\User::withTrashed()
            ->where('employee_code', 'like', $prefix . '%')
            ->orderByDesc('employee_code')
            ->value('employee_code');

        $nextNumber = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;
        $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        while (\App\Models\User::withTrashed()->where('employee_code', $code)->exists()) {
            $nextNumber++;
            $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
