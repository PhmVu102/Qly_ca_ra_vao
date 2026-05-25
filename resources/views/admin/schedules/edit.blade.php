@extends('layouts.admin')

@section('title', 'Sửa Phân Công')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6 lg:p-8">

    <!-- Header -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 mb-8">

        <div>
            <a href="{{ route('admin.schedules.index') }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium mb-4 transition">
                <i class="ti ti-arrow-left text-lg"></i>
                Quay lại danh sách phân công
            </a>

            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-gray-800">
                        Sửa Phân Công
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Cập nhật lịch làm việc cho
                        <span class="font-semibold text-gray-700">
                            {{ $schedule->user->name }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- User Card -->
        <div class="bg-white/80 backdrop-blur border border-white rounded-3xl p-5 shadow-xl shadow-slate-200/40 min-w-[320px]">
            <div class="flex items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">
                        Tên nhân viên: {{ $schedule->user->name }}
                    </h3>

                    <p class="text-sm text-gray-500">
                        Mã nhân viên: {{ $schedule->user->employee_code }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-3xl p-5 shadow-sm">

            <div class="flex items-start gap-4">

                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-circle text-2xl"></i>
                </div>

                <div>
                    <h3 class="font-bold text-red-700 mb-2">
                        Có lỗi xảy ra
                    </h3>

                    <ul class="space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Card -->
    <div class="max-w-5xl">

        <div class="bg-white/90 backdrop-blur-xl rounded-[36px] shadow-2xl shadow-slate-200/50 border border-white overflow-hidden" style="padding: 10px">
            <!-- Form -->
            <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="p-8 lg:p-10">
                @csrf
                @method('PUT')
                <!-- Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
                    <!-- Employee -->
                    <div class="lg:col-span-2">
                        <label for="user_id" class="block text-sm font-bold text-gray-700 mb-3">
                            Nhân Viên <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select id="user_id"
                                    name="user_id"
                                    class="w-full h-16 pl-14 pr-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all appearance-none @error('user_id') border-red-500 ring-red-100 @enderror">

                                <option value="">-- Chọn nhân viên --</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id', $schedule->user_id) == $user->id ? 'selected' : '' }}>

                                        {{ $user->name }} ({{ $user->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('user_id')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="ti ti-alert-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Shift -->
                    <div>
                        <label for="shift_id" class="block text-sm font-bold text-gray-700 mb-3">
                            Ca Làm <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select id="shift_id"
                                    name="shift_id"
                                    class="w-full h-16 pl-14 pr-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all appearance-none @error('shift_id') border-red-500 @enderror">

                                <option value="">-- Chọn ca --</option>

                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}"
                                        {{ old('shift_id', $schedule->shift_id) == $shift->id ? 'selected' : '' }}>

                                        {{ $shift->name }}
                                        ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('shift_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location_id" class="block text-sm font-bold text-gray-700 mb-3">
                            Vị Trí Làm Việc <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <select id="location_id"
                                    name="location_id"
                                    class="w-full h-16 pl-14 pr-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all appearance-none @error('location_id') border-red-500 @enderror">

                                <option value="">-- Chọn vị trí --</option>

                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}"
                                        {{ old('location_id', $schedule->location_id) == $location->id ? 'selected' : '' }}>

                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('location_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Work Date -->
                    <div class="lg:col-span-2">
                        <label for="work_date" class="block text-sm font-bold text-gray-700 mb-3">
                            Ngày Làm Việc <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <input type="date"
                                   id="work_date"
                                   name="work_date"
                                   value="{{ old('work_date', $schedule->work_date->format('Y-m-d')) }}"
                                   class="w-full h-16 pl-14 pr-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all @error('work_date') border-red-500 @enderror">
                        </div>

                        @error('work_date')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div class="lg:col-span-2">
                        <label for="note" class="block text-sm font-bold text-gray-700 mb-3">
                            Ghi Chú
                        </label>

                        <div class="relative">
                            <textarea id="note"
                                      name="note"
                                      rows="5"
                                      placeholder="Ví dụ: Tăng ca, ca hỗ trợ, thay ca..."
                                      class="w-full pl-14 pr-5 py-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all resize-none">{{ old('note', $schedule->note) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action -->
                <div class="flex flex-col sm:flex-row gap-4 mt-10">

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 text-white to-indigo-600 :hoverfrom-blue-700 hover:to-indigo-700 px-8 py-4 rounded-2xl font-bold shadow-xl shadow-blue-500/30 hover:scale-[1.02] transition-all">
                        Cập Nhật Phân Công
                    </button>

                    <a href="{{ route('admin.schedules.index') }}"
                       class="inline-flex items-center justify-center gap-3 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-bold transition-all">

                        <i class="ti ti-x text-lg"></i>
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
