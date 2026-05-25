@extends('layouts.admin')

@section('title', 'Phân Công Ca Mới')

@section('content')
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <a href="{{ route('admin.schedules.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-4">
            <i class="ti ti-arrow-left"></i>
            Quay lại danh sách
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Phân Công Ca Làm Việc Mới</h1>
        <p class="text-gray-500 mt-1">Chọn nhiều nhân viên để phân cùng một ca</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow border border-gray-100 p-8 max-w-3xl">
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf

            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        Chọn Nhân Viên <span class="text-red-500">*</span>
                        <span class="text-xs font-normal text-gray-500">(Có thể chọn nhiều)</span>
                    </label>

                    <div class="flex gap-2">
                        <button type="button" onclick="selectAll()"
                                class="text-sm px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-xl transition">
                            ✓ Chọn tất cả
                        </button>
                        <button type="button" onclick="deselectAll()"
                                class="text-sm px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            ✕ Bỏ chọn tất cả
                        </button>
                    </div>
                </div>

                <div id="no-users-msg" class="hidden text-center py-6 text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    Tất cả nhân viên đã được phân vào ca này trong ngày đã chọn!
                </div>

                <div id="users-container" class="max-h-96 overflow-y-auto border border-gray-200 rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($users as $user)
                    <label data-user-id="{{ $user->id }}" class="user-item flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition">
                        <input type="checkbox"
                               name="user_ids[]"
                               value="{{ $user->id }}"
                               {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-800">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->employee_code }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>

                @error('user_ids')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="shift_id" class="block text-sm font-semibold text-gray-700 mb-2">Chọn Ca <span class="text-red-500">*</span></label>
                <select id="shift_id" name="shift_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500">
                    <option value="">-- Chọn ca làm --</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="location_id" class="block text-sm font-semibold text-gray-700 mb-2">Vị trí làm việc <span class="text-red-500">*</span></label>
                <select id="location_id" name="location_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500">
                    <option value="">-- Chọn vị trí --</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="work_date" class="block text-sm font-semibold text-gray-700 mb-2">Ngày làm việc <span class="text-red-500">*</span></label>
                <input type="date" id="work_date" name="work_date" value="{{ old('work_date', date('Y-m-d')) }}" required
                       class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-8">
                <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú</label>
                <textarea name="note" rows="3"
                          class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:outline-none focus:border-blue-500"
                          placeholder="Ca làm thêm, ca đặc biệt...">{{ old('note') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-semibold flex items-center gap-2 transition">
                    <i class="ti ti-check"></i>
                    Phân Công Cho Nhiều Nhân Viên
                </button>
                <a href="{{ route('admin.schedules.index') }}"
                   class="px-8 py-4 border border-gray-300 rounded-2xl font-medium hover:bg-gray-50 transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectAll() {
        document.querySelectorAll('.user-item:not(.hidden) input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const shiftSelect = document.getElementById('shift_id');
        const workDateInput = document.getElementById('work_date');
        const userItems = document.querySelectorAll('.user-item');
        const usersContainer = document.getElementById('users-container');
        const noUsersMsg = document.getElementById('no-users-msg');

        function filterUsers() {
            const shiftId = shiftSelect.value;
            const workDate = workDateInput.value;

            if (!shiftId || !workDate) {
                userItems.forEach(item => item.classList.remove('hidden'));
                usersContainer.classList.remove('hidden');
                noUsersMsg.classList.add('hidden');
                return;
            }

            const url = `{{ route('admin.schedules.get_assigned_users') }}?shift_id=${shiftId}&work_date=${workDate}`;

            fetch(url)
                .then(response => response.json())
                .then(assignedUserIds => {
                    // Chuyển toàn bộ ID trong mảng trả về thành kiểu Số (Number) để tránh lỗi lệch kiểu dữ liệu chuỗi/số
                    const bậnUserIds = assignedUserIds.map(id => Number(id));
                    let visibleCount = 0;

                    userItems.forEach(item => {
                        const userId = Number(item.getAttribute('data-user-id'));
                        const checkbox = item.querySelector('input[type="checkbox"]');

                        if (bậnUserIds.includes(userId)) {
                            item.classList.add('hidden');
                            checkbox.checked = false; // Bỏ chọn nếu nhân viên bận bị ẩn đi
                        } else {
                            item.classList.remove('hidden');
                            visibleCount++;
                        }
                    });

                    if (visibleCount === 0) {
                        usersContainer.classList.add('hidden');
                        noUsersMsg.classList.remove('hidden');
                    } else {
                        usersContainer.classList.remove('hidden');
                        noUsersMsg.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Lỗi Ajax:', error));
        }

        shiftSelect.addEventListener('change', filterUsers);
        workDateInput.addEventListener('change', filterUsers);

        // Chạy lần đầu tiên phòng trường hợp có dữ liệu cũ cũ lúc load trang
        filterUsers();
    });
</script>
@endpush
