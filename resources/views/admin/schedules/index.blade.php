@extends('layouts.admin')

@section('title', 'Phân Công Ca Làm Việc')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6 lg:p-8">

    <!-- Header -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 mb-8">

        <div>
            <div class="flex items-center gap-3 mb-3" style="padding: 10px">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-gray-800">
                        Phân Công Ca Làm Việc
                    </h1>
                </div>
            </div>

            <p class="text-gray-500 text-base">
                Quản lý lịch làm việc nhân viên • Tổng
                <span class="font-bold text-blue-600">{{ $schedules->total() }}</span>
                phân công
            </p>
        </div>

        <a href="{{ route('admin.schedules.create') }}"
           class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 px-7 py-4 rounded-2xl font-semibold shadow-xl shadow-blue-500/30 hover:scale-[1.02] transition-all duration-300">

            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="ti ti-plus text-xl"></i>
            </div>

            <span>Phân công mới</span>
        </a>
    </div>

    <!-- Alert -->
    @if ($message = Session::get('success'))
        <div class="mb-6 flex items-center gap-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center">
                <i class="ti ti-circle-check text-2xl"></i>
            </div>

            <div>
                <p class="font-semibold">Thành công</p>
                <p class="text-sm">{{ $message }}</p>
            </div>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="mb-6 flex items-center gap-4 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center">
                <i class="ti ti-alert-circle text-2xl"></i>
            </div>

            <div>
                <p class="font-semibold">Có lỗi xảy ra</p>
                <p class="text-sm">{{ $message }}</p>
            </div>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white/90 backdrop-blur-xl rounded-[32px] shadow-2xl shadow-slate-200/50 border border-white overflow-hidden">

        <!-- Top -->
        <div class="p-7 border-b border-gray-100">

            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-5">

                <!-- Search -->
                <div class="relative w-full xl:max-w-lg">
                    <input type="text"
                           id="searchInput"
                           placeholder="Tìm kiếm nhân viên, mã nhân viên hoặc ca làm..."
                           class="w-full h-14 pl-14 pr-5 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                </div>

                <!-- Stats -->
                <div class="flex items-center gap-4">

                    <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-3 min-w-[140px]">
                        <p class="text-xs uppercase font-semibold text-blue-500 mb-1">
                            Tổng lịch
                        </p>

                        <h3 class="text-2xl font-black text-blue-700">
                            {{ $schedules->total() }}
                        </h3>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl px-5 py-3 min-w-[140px]">
                        <p class="text-xs uppercase font-semibold text-indigo-500 mb-1">
                            Trang hiện tại
                        </p>

                        <h3 class="text-2xl font-black text-indigo-700">
                            {{ $schedules->currentPage() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">

                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-left">

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Nhân viên
                        </th>

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Ca làm
                        </th>

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Thời gian
                        </th>

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Vị trí
                        </th>

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Ngày làm
                        </th>

                        <th class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-gray-500">
                            Ghi chú
                        </th>

                        <th class="px-6 py-5 text-center text-xs font-bold uppercase tracking-wider text-gray-500">
                            Hành động
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                    <tr class="group hover:bg-blue-50/50 transition-all duration-200">

                        <!-- Employee -->
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div>
                                    <h3 class="font-bold text-gray-800 group-hover:text-blue-700 transition">
                                        {{ $schedule->user->name ?? 'Không xác đinh' }}
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-0.5">
                                        {{ $schedule->user->employee_code ?? 'Không xác đinh' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Shift -->
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                {{ $schedule->shift->name }}
                            </span>
                        </td>

                        <!-- Time -->
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-xl text-blue-600 flex items-center justify-center">
                                    <i class="ti ti-clock"></i>
                                </div>

                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Giờ làm việc
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Location -->
                        <td class="px-6 py-5">
                            @if($schedule->location)
                                <div class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium">
                                    <i class="ti ti-map-pin text-blue-500"></i>
                                    {{ $schedule->location->name }}
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-5">
                            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold">
                                <i class="ti ti-calendar-event"></i>

                                {{ \Carbon\Carbon::parse($schedule->work_date)->format('d/m/Y') }}
                            </div>
                        </td>

                        <!-- Note -->
                        <td class="px-6 py-5">
                            <div class="max-w-[220px]">
                                @if($schedule->note)
                                    <p class="text-sm text-gray-600 line-clamp-2">
                                        {{ $schedule->note }}
                                    </p>
                                @else
                                    <span class="text-gray-400">Không có ghi chú</span>
                                @endif
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-3">

                                <!-- Edit -->
                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}"
                                   class="w-11 h-11 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-all hover:scale-110">

                                    <i class="ti ti-edit text-lg"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}"
                                      method="POST"
                                      onsubmit="return confirm('Xóa phân công này?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="w-11 h-11 rounded-2xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-all hover:scale-110">

                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="py-28 text-center">

                            <div class="flex flex-col items-center">

                                <div class="w-28 h-28 rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                    <i class="ti ti-calendar-off text-6xl text-gray-300"></i>
                                </div>

                                <h3 class="text-2xl font-bold text-gray-700 mb-2">
                                    Chưa có phân công nào
                                </h3>

                                <p class="text-gray-500 mb-6">
                                    Hãy tạo phân công đầu tiên cho nhân viên
                                </p>

                                <a href="{{ route('admin.schedules.create') }}"
                                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-semibold transition">

                                    <i class="ti ti-plus"></i>
                                    Tạo phân công
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{--    phân trang    --}}
        <div class="px-7 py-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Hiển thị số liệu thống kê --}}
            <div class="text-sm text-gray-600">
                Hiển thị từ <span class="font-semibold text-gray-800">{{ $schedules->firstItem() ?? 0 }}</span>
                đến <span class="font-semibold text-gray-800">{{ $schedules->lastItem() ?? 0 }}</span>
                trong tổng số <span class="font-semibold text-gray-800">{{ $schedules->total() }}</span> bản ghi
            </div>

            {{-- Khối điều hướng trang --}}
            @if ($schedules->hasPages())
                <nav class="flex items-center space-x-1" role="navigation" aria-label="Pagination Navigation">

                    {{-- Nút TRƯỚC --}}
                    @if ($schedules->onFirstPage())
                        <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm border border-gray-200 cursor-not-allowed select-none">
                    &laquo; Trước
                </span>
                    @else
                        <a href="{{ $schedules->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            &laquo; Trước
                        </a>
                    @endif

                    {{-- DANH SÁCH CÁC NÚT SỐ TRANG --}}
                    @foreach ($schedules->getUrlRange(max(1, $schedules->currentPage() - 2), min($schedules->lastPage(), $schedules->currentPage() + 2)) as $page => $url)
                        @if ($page == $schedules->currentPage())
                            {{-- Trang hiện tại đang xem (Active) --}}
                            <span class="px-3 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm border border-blue-600 select-none">
                        {{ $page }}
                    </span>
                        @else
                            {{-- Các trang khác --}}
                            <a href="{{ $url }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Nút SAU --}}
                    @if ($schedules->hasMorePages())
                        <a href="{{ $schedules->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            Sau &raquo;
                        </a>
                    @else
                        <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm border border-gray-200 cursor-not-allowed select-none">
                    Sau &raquo;
                </span>
                    @endif

                </nav>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {

        const rows = document.querySelectorAll('tbody tr');
        const keyword = this.value.toLowerCase();

        rows.forEach(row => {

            const employee = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
            const shift = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            const location = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';

            const match =
                employee.includes(keyword) ||
                shift.includes(keyword) ||
                location.includes(keyword);

            row.style.display = match ? '' : 'none';
        });
    });
</script>
@endsection
