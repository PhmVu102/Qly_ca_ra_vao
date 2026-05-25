@extends('layouts.admin')

@section('title', 'Thêm Nhân viên mới')

@section('content')
<div class="p-6 lg:p-10 max-w-4xl mx-auto min-h-screen bg-[#FDFDFC]">

    <!-- Breadcrumb & Header -->
    <div class="mb-10">
        <a href="{{ route('admin.staff.index') }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-[#1b1b18] mb-6 transition-colors group">
            <i class="ti ti-arrow-left transition-transform group-hover:-translate-x-1"></i>

            <span class="text-sm font-medium">Quay lại danh sách</span>
        </a>

        <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Thêm Nhân viên mới</h1>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 p-8 md:p-12">

        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-10">
            @csrf
            <!-- Section: Thông tin cá nhân -->
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Họ và tên -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all placeholder:text-gray-400"
                               placeholder="Nguyễn Văn A" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mã nhân viên — tự sinh, readonly -->
                    <div class="mb-6">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">
                            Mã Nhân Viên
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                value="{{ $nextCode }}"
                                class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all placeholder:text-gray-400"
                                readonly
                            >
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Mã được tạo tự động theo thứ tự</p>

                        {{-- Truyền mã vào form để lưu --}}
                        <input type="hidden" name="employee_code" value="{{ $nextCode }}">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">
                            Địa chỉ Email <span class="text-red-500">*</span>
                        </label>

                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all"
                               placeholder="example@company.com" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Công việc -->
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Phòng ban -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">
                            Phòng ban <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select name="department_id"
                                    class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all appearance-none cursor-pointer">
                                <option value="">-- Chọn phòng ban --</option>

                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('department_id')
                            <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quyền -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">
                            Quyền <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select name="role_id"
                                    class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all appearance-none cursor-pointer">
                                <option value="">-- Chọn quyền --</option>

                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('role_id')
                            <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all"
                               placeholder="0123 456 789">
                    </div>

                    <!-- Ngày vào làm -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Ngày vào làm</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date') }}"
                               class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all cursor-pointer">
                    </div>

                    <!-- Mật khẩu -->
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-gray-700 ml-1">Mật khẩu mặc định</label>
                        <div class="relative">
                            <input type="text" value="password123" readonly
                                   class="w-full px-6 py-4 bg-gray-100 border border-transparent rounded-2xl text-gray-500 font-medium cursor-not-allowed">
                        </div>
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
                    <i class="ti ti-user-plus text-lg"></i>
                    Xác nhận thêm mới
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
