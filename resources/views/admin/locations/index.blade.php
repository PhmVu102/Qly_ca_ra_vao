@extends('layouts.admin')

@section('title', 'Quản lý Vị trí GPS')

@section('content')
<div class="p-6 sm:p-10 bg-[#FDFDFC] min-h-screen">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#1b1b18] tracking-tight">Quản lý Vị trí GPS</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách các khu vực tọa độ cho phép nhân viên chấm công.</p>
        </div>
        <a href="{{ route('admin.locations.create') }}"
           class="bg-[#1b1b18] hover:bg-black text-white px-6 py-3 rounded-2xl flex items-center gap-2 transition-all active:scale-95 shadow-sm">
            <i class="ti ti-plus text-lg"></i>
            <span class="font-semibold text-sm">Thêm vị trí mới</span>
        </a>
    </div>

    <!-- Thanh tìm kiếm (Bổ sung thêm để UI cân đối giống trang nhân viên) -->
    <div class="mb-6 flex gap-4">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="locationSearchInput" placeholder="Tìm kiếm tên vị trí..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] outline-none transition-all text-sm">
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2rem] shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Tên vị trí</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider">Địa chỉ chi tiết</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Bán kính</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-5 text-[13px] font-bold text-gray-500 uppercase tracking-wider text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($locations as $location)
                    <tr class="hover:bg-gray-50/80 transition-colors group">

                        <!-- Tên vị trí -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#b0ffc3]/30 flex items-center justify-center text-[#1b1b18]">
                                    <i class="ti ti-map-pin text-lg"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-[#1b1b18] text-sm">{{ $location->name }}</span>
                                    @if($location->is_main)
                                        <div class="text-[11px] text-emerald-600 font-bold mt-0.5">Vị trí chính</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Địa chỉ -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate" title="{{ $location->address }}">
                                {{ $location->address ?? 'Chưa cập nhật' }}
                            </div>
                        </td>

                        <!-- Bán kính -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-3 py-1.5 rounded-xl text-xs font-semibold border border-gray-200">
                                <i class="ti ti-radar text-gray-400"></i>
                                {{ $location->radius_meter }} mét
                            </span>
                        </td>

                        <!-- Trạng thái -->
                        <td class="px-6 py-4 text-center">
                            @if($location->status)
                                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-500 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-gray-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Tắt
                                </span>
                            @endif
                        </td>

                        <!-- Thao tác (Ẩn hiện khi hover) -->
                        <td class="px-6 py-4 text-center">
                            <div class="">
                                <a href="{{ route('admin.locations.edit', $location->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                    <i class="ti ti-edit text-lg"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.locations.destroy', $location->id) }}" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Xóa vị trí này?')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <!-- Trạng thái trống (Empty State) -->
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="ti ti-map-2 text-3xl opacity-40"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Chưa có vị trí GPS nào</p>
                                <p class="text-xs mt-1 text-gray-400">Vui lòng thêm vị trí đầu tiên để hệ thống hoạt động.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        @if($locations->hasPages())
        <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
            {{ $locations->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('locationSearchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Skip the empty state row
            if (row.querySelector('td[colspan]')) {
                return;
            }

            // Get text from relevant columns (name, address)
            const cells = row.querySelectorAll('td');
            let rowText = '';

            if (cells.length > 0) {
                // Name (column 0)
                rowText += cells[0]?.textContent || '';
                // Address (column 1)
                rowText += ' ' + (cells[1]?.textContent || '');
            }

            const matches = rowText.toLowerCase().includes(searchValue);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        // Show empty state if no results
        if (visibleCount === 0 && searchValue !== '') {
            let emptyRow = document.querySelector('table tbody tr[data-empty-state]');
            if (!emptyRow) {
                const tbody = document.querySelector('table tbody');
                emptyRow = document.createElement('tr');
                emptyRow.setAttribute('data-empty-state', 'true');
                emptyRow.innerHTML = `
                    <td colspan="5" class="py-20 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="ti ti-search text-3xl opacity-40"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600">Không tìm thấy vị trí nào</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else {
            const emptyRow = document.querySelector('table tbody tr[data-empty-state]');
            if (emptyRow) emptyRow.style.display = 'none';
        }
    });
</script>
@endsection
