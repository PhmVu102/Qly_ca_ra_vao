<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Danh sách phòng ban
     */
    public function index()
    {
        $departments = Department::latest()->paginate(12);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Form thêm phòng ban
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Lưu phòng ban mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                // Không cho ký tự đặc biệt
                'regex:/^[\pL\s0-9\-.]+$/u',

                'unique:departments,name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Tên phòng ban không được để trống.',
            'name.unique' => 'Tên phòng ban đã tồn tại.',
            'name.regex' => 'Tên phòng ban không được chứa ký tự đặc biệt.',
            'name.max' => 'Tên phòng ban không được vượt quá 100 ký tự.',

            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        Department::create($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Thêm phòng ban thành công!');
    }

    /**
     * Form sửa phòng ban
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Cập nhật phòng ban
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                // Không cho ký tự đặc biệt
                'regex:/^[\pL\s0-9\-.]+$/u',

                Rule::unique('departments', 'name')
                    ->ignore($department->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Tên phòng ban không được để trống.',
            'name.unique' => 'Tên phòng ban đã tồn tại.',
            'name.regex' => 'Tên phòng ban không được chứa ký tự đặc biệt.',
            'name.max' => 'Tên phòng ban không được vượt quá 100 ký tự.',

            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $department->update($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Cập nhật phòng ban thành công!');
    }

    /**
     * Xóa phòng ban
     */
    public function destroy(Department $department)
    {
        // Kiểm tra còn nhân viên hay không
        if ($department->users()->exists()) {

            return redirect()
                ->route('admin.departments.index')
                ->with('error', 'Không thể xóa phòng ban đang có nhân viên!');
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Xóa phòng ban thành công!');
    }
}
