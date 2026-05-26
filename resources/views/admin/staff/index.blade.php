@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản')

@section('content')
<div class="p-4 lg:p-8 max-w-screen-2xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">Quản lý Tài khoản</h1>
            <p class="text-gray-500 mt-1 text-lg">
                Tổng <span class="font-semibold text-gray-700">{{ $staff->total() }}</span> tài khoản
            </p>
        </div>

        <a href="{{ route('admin.staff.create') }}"
           class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 px-6 py-3.5 rounded-3xl flex items-center gap-3 font-semibold shadow-sm hover:shadow transition-all active:scale-95">
            <i class="ti ti-plus text-2xl"></i>
            <span>Thêm tài khoản mới</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100/80 border border-gray-100 overflow-hidden">

        <!-- Search -->
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="relative max-w-md">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="ti ti-search text-xl"></i>
                </div>
                <input type="text" id="searchInput"
                       placeholder="Tìm theo tên, mã, email, số điện thoại..."
                       class="w-full pl-12 pr-5 py-4 bg-white border border-gray-200 rounded-3xl focus:outline-none focus:border-blue-500 focus:ring-1 text-base">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] table-fixed">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-28 px-4 py-5 text-left text-sm font-semibold text-gray-500">Mã số</th>
                        <th class="w-52 px-4 py-5 text-left text-sm font-semibold text-gray-500">Họ và tên</th>
                        <th class="w-36 px-4 py-5 text-left text-sm font-semibold text-gray-500">Vai trò</th>
                        <th class="w-40 px-4 py-5 text-left text-sm font-semibold text-gray-500">Phòng ban</th>
                        <th class="px-4 py-5 py-5 text-left text-sm font-semibold text-gray-500">Email</th>
                        <th class="w-32 px-4 py-5 text-left text-sm font-semibold text-gray-500">SĐT</th>
                        <th class="w-32 px-4 py-5 text-left text-sm font-semibold text-gray-500">Ngày vào làm</th>
                        <!-- <th class="w-36 px-4 py-5 text-center text-sm font-semibold text-gray-500">Trạng thái</th> -->
                        <th class="w-28 px-4 py-5 text-center text-sm font-semibold text-gray-500">Hành động</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($staff as $user)
                    <tr class="hover:bg-blue-50/70 transition-colors group">
                        <td class="px-4 py-5 font-mono text-gray-700 font-medium">
                            {{ $user->employee_code ?? '—' }}
                        </td>

                        <td class="px-4 py-5">
                            <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ $user->role->name ?? 'Không xác định' }}
                            </div>
                        </td>

                        <td class="px-4 py-5">
                            @if($user->role_id == 1)
                                <span class="inline-block px-4 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-2xl">
                                    Quản trị viên
                                </span>
                            @else
                                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-2xl">
                                    Nhân viên
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-5">
                            @if($user->department)
                                <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-2xl">
                                    {{ $user->department->name }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">Chưa phân ban</span>
                            @endif
                        </td>

                        <td class="px-4 py-5 text-gray-600 truncate max-w-[300px]">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-5 text-gray-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-4 py-5 text-gray-600">
                            {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d/m/Y') : '—' }}
                        </td>

                        <!-- <td class="px-4 py-5 text-center">
                            @if($user->status ?? 1)
                                <span class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-3xl text-xs font-medium">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    Đang làm
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-500 px-4 py-1.5 rounded-3xl text-xs font-medium">
                                    Nghỉ việc
                                </span>
                            @endif
                        </td> -->

                        <td class="px-4 py-5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.staff.edit', $user->id) }}"
                                   class="w-9 h-9 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-2xl transition hover:scale-110"
                                   title="Chỉnh sửa">
                                    <i class="ti ti-edit text-xl"></i>
                                </a>

                                @if($user->is_locked ?? 0)
                                    <form action="{{ route('admin.staff.toggle-lock', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Mở khóa tài khoản này?')"
                                                class="w-9 h-9 flex items-center justify-center text-green-600 hover:bg-green-100 rounded-2xl transition hover:scale-110"
                                                title="Mở khóa">
                                            <i class="ti ti-lock-open text-xl"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.staff.toggle-lock', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Khóa tài khoản này?')"
                                                class="w-9 h-9 flex items-center justify-center text-amber-600 hover:bg-amber-100 rounded-2xl transition hover:scale-110"
                                                title="Khóa">
                                            <i class="ti ti-lock text-xl"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.staff.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Xóa tài khoản này?')"
                                            class="w-9 h-9 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-2xl transition hover:scale-110"
                                            title="Xóa">
                                        <i class="ti ti-trash text-xl"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-24 text-center">
                            <i class="ti ti-users text-8xl text-gray-200 mb-4"></i>
                            <p class="text-xl font-medium text-gray-400">Chưa có tài khoản nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-7 py-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Hiển thị từ <span class="font-semibold text-gray-800">{{ $staff->firstItem() ?? 0 }}</span>
                đến <span class="font-semibold text-gray-800">{{ $staff->lastItem() ?? 0 }}</span>
                trong tổng số <span class="font-semibold text-gray-800">{{ $staff->total() }}</span> nhân sự
            </div>

            <div class="flex items-center gap-2">
                {{-- Nút Trang Trước --}}
                @if ($staff->onFirstPage())
                    <span class="h-10 px-4 bg-gray-100 text-gray-400 font-medium rounded-xl flex items-center gap-1 cursor-not-allowed text-sm border border-gray-200">
                        <i class="ti ti-chevron-left"></i> Trang trước
                    </span>
                @else
                    <a href="{{ $staff->previousPageUrl() }}" class="h-10 px-4 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl flex items-center gap-1 text-sm border border-gray-200 shadow-sm transition">
                        <i class="ti ti-chevron-left"></i> Trang trước
                    </a>
                @endif

                {{-- Hiển thị Trang hiện tại / Tổng số trang --}}
                <span class="text-sm font-medium text-gray-600 px-2">
                    Trang <span class="text-gray-900 font-bold">{{ $staff->currentPage() }}</span> / {{ $staff->lastPage() }}
                </span>

                {{-- Nút Trang Sau --}}
                @if ($staff->hasMorePages())
                    <a href="{{ $staff->nextPageUrl() }}" class="h-10 px-4 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl flex items-center gap-1 text-sm border border-gray-200 shadow-sm transition">
                        Trang sau <i class="ti ti-chevron-right"></i>
                    </a>
                @else
                    <span class="h-10 px-4 bg-gray-100 text-gray-400 font-medium rounded-xl flex items-center gap-1 cursor-not-allowed text-sm border border-gray-200">
                        Trang sau <i class="ti ti-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Search function
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (row.querySelector('td[colspan]')) return; // Bỏ qua dòng empty
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endsection
