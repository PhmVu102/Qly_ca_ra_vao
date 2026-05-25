@extends('layouts.admin')

@section('title', 'Danh sách phòng ban')

@section('content')
<div class="p-6 lg:p-10 mx-auto min-h-screen bg-[#FDFDFC]">
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Danh sách phòng ban</h1>

            <p class="text-sm text-gray-500 mt-2">Quản lý danh sách phòng ban trong hệ thống.</p>
        </div>

        <a href="{{ route('admin.departments.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-2xl font-semibold shadow-md hover:bg-blue-700 hover:shadow-lg transition-all duration-200">
            <i class="ti ti-plus"></i>
            Thêm phòng ban mới
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 p-8 md:p-10">

        <!-- Search Bar -->
        <div class="mb-6 flex gap-4">
            <div class="relative flex-1 max-w-sm">
                <input type="text" id="departmentSearchInput" placeholder="Tìm kiếm phòng ban..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
            </div>
        </div>

        @if($departments->count())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-gray-600">
                    <thead class="border-b border-gray-200 text-xs uppercase tracking-[0.2em] text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Tên phòng ban</th>
                            <th class="px-6 py-4">Mô tả</th>
                            <th class="px-6 py-4">Tạo lúc</th>
                            <th class="px-6 py-4">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($departments as $department)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $department->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $department->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $department->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.departments.edit', $department) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Chỉnh sửa">
                                            <i class="ti ti-edit text-lg"></i>
                                        </a>

                                        <form action="{{ route('admin.departments.destroy', $department) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng ban này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                                <i class="ti ti-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $departments->links() }}
            </div>
        @else
            <p class="text-gray-500">Chưa có phòng ban nào. Hãy thêm mới.</p>
        @endif
    </div>
</div>

<script>
    document.getElementById('departmentSearchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Skip the empty state row
            if (row.querySelector('td[colspan]')) {
                return;
            }

            // Get text from relevant columns (name, description, created_at)
            const cells = row.querySelectorAll('td');
            let rowText = '';

            if (cells.length > 0) {
                // Name (column 0)
                rowText += cells[0]?.textContent || '';
                // Description (column 1)
                rowText += ' ' + (cells[1]?.textContent || '');
                // Created date (column 2)
                rowText += ' ' + (cells[2]?.textContent || '');
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
                    <td colspan="4" class="py-20 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="ti ti-search text-3xl opacity-40"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600">Không tìm thấy phòng ban nào</p>
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
