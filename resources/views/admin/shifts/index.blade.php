@extends('layouts.admin')

@section('title', 'Quản lý Ca Làm Việc')

@section('content')
<div class="p-6 sm:p-10 bg-[#FDFDFC] min-h-screen">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#1b1b18] tracking-tight">Quản lý Ca Làm Việc</h1>
            <p class="text-sm text-gray-500 mt-1">Hệ thống có tổng cộng <span class="font-bold text-[#1b1b18]">{{ $shifts->total() }}</span> ca cấu hình.</p>
        </div>
        <a href="{{ route('admin.shifts.create') }}"
           class="bg-[#1b1b18] hover:bg-black text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition-all active:scale-95 shadow-sm">
            <i class="ti ti-plus text-lg"></i>
            <span class="font-semibold text-sm">Thêm ca mới</span>
        </a>
    </div>

    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="searchInput" placeholder="Tìm kiếm tên ca làm việc..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] outline-none transition-all text-sm">
        </div>

        <button class="w-fit px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2">
            <i class="ti ti-filter text-gray-400"></i>
            Bộ lọc nâng cao
        </button>
    </div>

    <div class="bg-white rounded-[2rem] shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tên Ca</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Đi muộn/Về sớm</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Ghi chú</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-[#1b1b18] group-hover:bg-[#b0ffc3] transition-colors">
                                    <i class="ti ti-clock-play text-lg"></i>
                                </div>
                                <span class="font-bold text-[#1b1b18] text-sm">{{ $shift->name }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700">{{ $shift->start_time }} - {{ $shift->end_time }}</span>
                                <span class="text-[11px] text-gray-400 uppercase font-medium tracking-tighter">Giờ hành chính</span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <span class="px-2.5 py-1 bg-orange-50 text-orange-600 rounded-lg text-xs font-bold border border-orange-100" title="Cho phép đi muộn">
                                    -{{ $shift->late_allow_minutes }}m
                                </span>
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-100" title="Cho phép về sớm">
                                    +{{ $shift->early_leave_allow_minutes }}m
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-500 max-w-[150px] truncate">{{ $shift->description ?? '—' }}</p>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($shift->status)
                                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Đang chạy
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-400 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-gray-100">
                                    Tạm dừng
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center transition-opacity">
                                <a href="{{ route('admin.shifts.edit', $shift->id) }}"
                                   class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="inline" onsubmit="return confirm('Xóa ca làm việc này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="ti ti-clock-off text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Chưa có dữ liệu ca làm việc</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const rows = document.querySelectorAll('tbody tr');
        const searchTerm = this.value.toLowerCase();
        rows.forEach(row => {
            const name = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endsection
