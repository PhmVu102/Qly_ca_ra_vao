@extends('layouts.admin')

@section('title', 'Sửa Ca Làm Việc')

@section('content')
<div class="p-6 lg:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">

        <!-- Header -->
        <div class="mb-10">
            <a href="{{ route('admin.shifts.index') }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-6 transition">
                <i class="ti ti-arrow-left"></i>
                Quay lại danh sách ca
            </a>

            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Sửa Ca Làm Việc</h1>
                    <p class="text-gray-500 mt-1">{{ $shift->name }}</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl shadow border border-gray-100 p-8 md:p-10">

            <form action="{{ route('admin.shifts.update', $shift) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Tên ca -->
                <div class="mb-8">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tên Ca Làm Việc <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $shift->name) }}"
                           class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                           placeholder="VD: Ca Sáng Chính Thức">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thời gian -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                            Giờ Bắt Đầu <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="start_time" name="start_time"
                               value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
                               class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        @error('start_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                            Giờ Kết Thúc <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="end_time" name="end_time"
                               value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
                               class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        @error('end_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quy định chấm công -->
                <div class="bg-gray-50 border border-gray-100 rounded-3xl p-6 mb-8">
                    <h3 class="font-semibold text-gray-800 mb-4">Quy định chấm công</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="late_allow_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Cho phép đi muộn
                            </label>
                            <div class="relative">
                                <input type="number" id="late_allow_minutes" name="late_allow_minutes"
                                       value="{{ old('late_allow_minutes', $shift->late_allow_minutes ?? 10) }}" min="0"
                                       class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">phút</span>
                            </div>
                        </div>

                        <div>
                            <label for="early_leave_allow_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Cho phép về sớm
                            </label>
                            <div class="relative">
                                <input type="number" id="early_leave_allow_minutes" name="early_leave_allow_minutes"
                                       value="{{ old('early_leave_allow_minutes', $shift->early_leave_allow_minutes ?? 10) }}" min="0"
                                       class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">phút</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="mb-8">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Mô Tả</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500 resize-none"
                              placeholder="Mô tả chi tiết về ca làm việc...">{{ old('description', $shift->description) }}</textarea>
                </div>

                <input type="hidden" name="status" value="1">

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-semibold transition flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i>
                        Cập Nhật Ca Làm Việc
                    </button>

                    <a href="{{ route('admin.shifts.index') }}"
                       class="flex-1 text-center py-4 border border-gray-300 rounded-2xl font-medium hover:bg-gray-50 transition">
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
