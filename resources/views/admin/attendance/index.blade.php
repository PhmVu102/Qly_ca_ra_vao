@extends('layouts.admin')

@section('title', 'Quản Lý Chấm Công')

@section('content')
<div class="p-4 lg:p-8 bg-gray-50 min-h-screen">

    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                Quản Lý Chấm Công
            </h1>

            <p class="text-gray-500 mt-1">
                @if($date)
                    Đang xem ngày:
                    <span class="font-semibold text-blue-600">
                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                    </span>
                @elseif($month)
                    Đang xem tháng:
                    <span class="font-semibold text-blue-600">
                        {{ \Carbon\Carbon::parse($month.'-01')->format('m/Y') }}
                    </span>
                @else
                    Tất cả dữ liệu
                @endif

                @if(!empty($search))
                    • Tìm kiếm từ khóa: <span class="font-semibold text-blue-600">"{{ $search }}"</span>
                @endif
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.attendance.index') }}"
               class="inline-flex items-center gap-3 bg-white hover:bg-gray-50 border border-gray-200 px-6 py-4 rounded-3xl font-semibold shadow-sm hover:shadow transition-all">
                <i class="ti ti-refresh"></i>
                Làm mới
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-10">
        <div class="bg-white rounded-3xl p-6 shadow shadow-gray-100 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tổng ca</p>
            <h3 class="text-4xl font-bold text-gray-900 mt-3">
                {{ $summary['total'] ?? 0 }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow shadow-gray-100 border border-gray-100">
            <p class="text-sm font-medium text-emerald-600">Đúng giờ</p>
            <h3 class="text-4xl font-bold text-emerald-600 mt-3">
                {{ $summary['present'] ?? 0 }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow shadow-gray-100 border border-gray-100">
            <p class="text-sm font-medium text-orange-600">Đi muộn</p>
            <h3 class="text-4xl font-bold text-orange-600 mt-3">
                {{ $summary['late'] ?? 0 }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow shadow-gray-100 border border-gray-100">
            <p class="text-sm font-medium text-sky-600">Về sớm</p>
            <h3 class="text-4xl font-bold text-sky-600 mt-3">
                {{ $summary['early_leave'] ?? 0 }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow shadow-gray-100 border border-gray-100">
            <p class="text-sm font-medium text-red-600">Chưa hoàn thành</p>
            <h3 class="text-4xl font-bold text-red-600 mt-3">
                {{ $summary['incomplete'] ?? 0 }}
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow shadow-gray-100 border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 lg:p-8 border-b border-gray-100 bg-gray-50">

            <form method="GET" id="filter-form" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 items-end">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Theo Tháng</label>
                    <input type="month" name="month" value="{{ $month ?? '' }}" class="w-full h-12 px-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Theo Ngày</label>
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="w-full h-12 px-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nhân viên</label>
                    <select name="user_id" class="w-full h-12 px-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        <option value="">Tất cả nhân viên</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string)$userId === (string)$user->id ? 'selected' : '' }}>
                                {{ $user->name }} {{ $user->employee_code ? '(' . $user->employee_code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái</label>
                    <select name="status" class="w-full h-12 px-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tìm kiếm</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            {{-- Sử dụng old() hoặc dùng hàm e() để bảo vệ các ký tự đặc biệt như " hoặc ' không bị lỗi HTML --}}
                            value="{{ old('search', $search ?? '') }}"
                            placeholder="Tên hoặc mã nhân viên..."
                            class="w-full h-12 pl-12 pr-4 rounded-2xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            @if(!empty($search)) autofocus @endif
                            onfocus="this.setSelectionRange(this.value.length, this.value.length);"
                        >
                        <i class="ti ti-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="lg:col-span-6 flex gap-3 justify-end">
                    <button type="submit" class="h-12 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl transition">
                        <i class="ti ti-filter mr-1"></i> Lọc dữ liệu
                    </button>
                    <a href="{{ route('admin.attendance.index') }}" class="h-12 px-6 border border-gray-300 hover:bg-gray-50 rounded-2xl flex items-center justify-center text-gray-600 font-medium">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>

        <div class="px-6 py-4 bg-white border-b flex gap-2 flex-wrap">
            <a href="{{ route('admin.attendance.index', ['month' => date('Y-m')]) }}" class="px-4 py-2 text-sm bg-white border border-gray-200 hover:border-blue-300 rounded-xl transition">
                Tháng này
            </a>
            <a href="{{ route('admin.attendance.index', ['month' => date('Y-m', strtotime('-1 month'))]) }}" class="px-4 py-2 text-sm bg-white border border-gray-200 hover:border-blue-300 rounded-xl transition">
                Tháng trước
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600 w-64">Nhân viên</th>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600 w-40">Ngày</th>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600">Ca làm</th>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600">Check-in</th>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600">Check-out</th>
                        <th class="px-6 py-5 text-center font-semibold text-gray-600">Tổng công</th>
                        <th class="px-6 py-5 text-left font-semibold text-gray-600">Vi phạm</th>
                        <th class="px-6 py-5 text-center font-semibold text-gray-600 w-40">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $schedule)
                        @php
                            $record = $schedule->attendance_record;
                            $shiftStart = \Carbon\Carbon::parse($schedule->work_date->format('Y-m-d') . ' ' . $schedule->shift->start_time);
                            $shiftEnd = \Carbon\Carbon::parse($schedule->work_date->format('Y-m-d') . ' ' . $schedule->shift->end_time);
                            $now = now();

                            if (!$record) {
                                if ($now->lt($shiftStart)) {
                                    $currentStatus = 'upcoming';
                                } elseif ($now->between($shiftStart, $shiftEnd)) {
                                    $currentStatus = 'working';
                                } else {
                                    $currentStatus = 'absent';
                                }
                            } else {
                                $currentStatus = $record->status;
                                if ($record->check_in_time && !$record->check_out_time && $now->lte($shiftEnd)) {
                                    $currentStatus = 'working';
                                }
                                if ($record->check_in_time && !$record->check_out_time && $now->gt($shiftEnd) && !in_array($record->status, ['forgot_checkout', 'incomplete'])) {
                                    $currentStatus = 'incomplete';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-6">
                                <div class="font-semibold">{{ $schedule->user?->name ?? 'Không rõ' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $schedule->user?->employee_code }}
                                    @if($schedule->user?->department)
                                        • {{ $schedule->user->department->name }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span class="font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($schedule->work_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                @if($schedule->shift)
                                    <div class="text-sm">
                                        <span class="font-medium">{{ $schedule->shift->name }}</span><br>
                                        <span class="text-gray-500">
                                            {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-6 font-mono">
                                {{ $record?->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '--:--' }}
                            </td>
                            <td class="px-6 py-6 font-mono">
                                {{ $record?->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '--:--' }}
                            </td>
                            <td class="px-6 py-6 text-center font-medium">
                                {{ intdiv($record?->total_work_minutes ?? 0, 60) }}h {{ ($record?->total_work_minutes ?? 0) % 60 }}p
                            </td>
                            <td class="px-6 py-6 text-sm">
                                @if($record?->late_minutes)
                                    <div class="text-orange-600">Muộn: {{ $record->late_minutes }}p</div>
                                @endif
                                @if($record?->early_leave_minutes)
                                    <div class="text-sky-600">Sớm: {{ $record->early_leave_minutes }}p</div>
                                @endif
                            </td>
                            <td class="px-6 py-6 text-center">
                                @php
                                    $badgeStyle = match($currentStatus) {
                                        'present'          => 'background:#d1fae5; color:#065f46',
                                        'working'          => 'background:#dbeafe; color:#1e40af',
                                        'upcoming'         => 'background:#f3f4f6; color:#6b7280',
                                        'late'             => 'background:#ffedd5; color:#c2410c',
                                        'early_leave'      => 'background:#e0f2fe; color:#0369a1',
                                        'late_early_leave' => 'background:#fef3c7; color:#b45309',
                                        'incomplete'       => 'background:#ffedd5; color:#ea580c',
                                        'forgot_checkout'  => 'background:#f3e8ff; color:#7e22ce',
                                        'absent'           => 'background:#fee2e2; color:#b91c1c',
                                        default            => 'background:#f3f4f6; color:#374151',
                                    };
                                @endphp
                                <span class="inline-block px-5 py-2 text-xs font-semibold rounded-3xl" style="{{ $badgeStyle }}">
                                    {{ $statuses[$currentStatus] ?? ucfirst($currentStatus) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-32 text-center">
                                <i class="ti ti-clipboard-off text-7xl text-gray-200 mb-4"></i>
                                <p class="text-xl font-medium text-gray-400">Chưa có dữ liệu chấm công</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-7 py-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Hiển thị từ <span class="font-semibold text-gray-800">{{ $attendances->firstItem() ?? 0 }}</span>
                đến <span class="font-semibold text-gray-800">{{ $attendances->lastItem() ?? 0 }}</span>
                trong tổng số <span class="font-semibold text-gray-800">{{ $attendances->total() }}</span> bản ghi
            </div>
            {{ $attendances->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const filterForm = document.getElementById('filter-form');
        let typingTimer;
        const doneTypingInterval = 500; // Thời gian chờ người dùng ngừng gõ (500ms)

        if (searchInput && filterForm) {
            searchInput.addEventListener('input', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    filterForm.submit(); // Tự động gửi form đi để lọc dữ liệu
                }, doneTypingInterval);
            });
        }
    });
</script>
@endsection
