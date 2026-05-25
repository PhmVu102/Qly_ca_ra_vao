@extends('layouts.admin')

@section('title', 'Chỉnh sửa Nhân viên')

@section('content')
<div class="p-6 lg:p-10 max-w-4xl mx-auto min-h-screen bg-[#FDFDFC]">

    <!-- Breadcrumb & Header -->
    <div class="mb-10">
        <a href="{{ route('admin.staff.index') }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-[#1b1b18] mb-6 transition-colors group">
            <i class="ti ti-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="text-sm font-medium">Quay lại danh sách</span>
        </a>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-[#b0ffc3] rounded-2xl flex items-center justify-center shadow-sm">
                <i class="ti ti-user-edit text-2xl text-[#1b1b18]"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Chỉnh sửa tài khoản</h1>
                <p class="text-gray-500 mt-1">Đang cập nhật hồ sơ: <span class="font-bold text-gray-700">{{ $staff->name }}</span></p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 p-8 md:p-12">

        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-10">
            @csrf
            @method('PUT')

            <!-- Section: Thông tin định danh -->
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1.5 h-6 bg-[#b0ffc3] rounded-full"></div>
                    <h2 class="text-lg font-bold text-[#1b1b18]">Thông tin tài khoản</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Họ và tên -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all"
                               pattern="^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸỳỵỷỹ\s]+$"
                               required
                            >
                    </div>

                    <!-- Mã nhân viên -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Mã tài khoản <span class="text-red-500">*</span></label>
                        <input type="text" name="employee_code" value="{{ old('employee_code', $staff->employee_code) }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all font-mono text-sm uppercase" required>
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Địa chỉ Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all" required>
                    </div>
                </div>
            </div>

            <!-- Section: Vị trí & Trạng thái -->
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1.5 h-6 bg-[#b0ffc3] rounded-full"></div>
                    <h2 class="text-lg font-bold text-[#1b1b18]">Công việc & Phân quyền</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Phòng ban -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Phòng ban <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="department_id" class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all appearance-none cursor-pointer" required>
                                <option value="">-- Chọn phòng ban --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $staff->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Phân quyền -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Phân quyền hệ thống <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="role_id" class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all appearance-none cursor-pointer" required>
                                <option value="1" {{ old('role_id', $staff->role_id) == 1 ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                                <option value="2" {{ old('role_id', $staff->role_id) == 2 ? 'selected' : '' }}>Nhân viên</option>
                            </select>
                        </div>
                    </div>

                    <!-- Trạng thái làm việc -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Trạng thái hiện tại</label>
                        <div class="relative">
                            <select name="status" class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all appearance-none cursor-pointer">
                                <option value="1" {{ old('status', $staff->status) == 1 ? 'selected' : '' }}>Đang làm việc</option>
                                <option value="0" {{ old('status', $staff->status) == 0 ? 'selected' : '' }}>Đã nghỉ việc / Khóa tài khoản</option>
                            </select>
                        </div>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Số điện thoại liên hệ</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all" placeholder="09xx xxx xxx">
                    </div>

                    <!-- Ngày vào làm -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Ngày vào làm</label>
                        <input type="date"
                               name="hire_date"
                               value="{{ old('hire_date', $staff->hire_date ? \Carbon\Carbon::parse($staff->hire_date)->format('Y-m-d') : '') }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-10 border-t border-gray-100">
                <a href="{{ route('admin.staff.index') }}"
                   class="w-full sm:w-auto px-10 py-4 text-sm font-bold text-gray-500 hover:text-black hover:bg-gray-50 rounded-2xl transition-all text-center">
                    Hủy bỏ
                </a>
                <button type="submit"
                        class="w-full sm:w-auto bg-[#1b1b18] hover:bg-black text-white px-10 py-4 rounded-2xl font-bold text-sm shadow-xl shadow-black/5 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy text-lg"></i>
                    Lưu các thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
