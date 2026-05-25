@extends('layouts.admin')

@section('title', 'Chỉnh sửa Vị trí GPS')

@section('content')
<div class="min-h-screen bg-[#FDFDFC] py-8 lg:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <!-- Header Section -->
        <div class="mb-10">
            <a href="{{ route('admin.locations.index') }}"
               class="inline-flex items-center gap-2 text-gray-400 hover:text-[#1b1b18] mb-6 transition-colors group">
                <i class="ti ti-arrow-left transition-transform group-hover:-translate-x-1"></i>
                <span class="text-sm font-medium">Quay lại danh sách vị trí</span>
            </a>

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#b0ffc3] rounded-2xl flex items-center justify-center shadow-sm">
                    <i class="ti ti-map-pin text-2xl text-[#1b1b18]"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Chỉnh sửa Vị trí GPS</h1>
                    <p class="text-gray-500 mt-1">Cập nhật tọa độ khu vực cho phép nhân viên chấm công</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">

            <form method="POST" action="{{ route('admin.locations.update', $location->id) }}" class="p-8 md:p-12 space-y-8">
                @csrf
                @method('PUT')

                <!-- Tên vị trí -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                        <i class="ti ti-building text-gray-400"></i>
                        Tên vị trí <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}"
                           placeholder="VD: Chi nhánh Thái Nguyên - HUNONIC"
                           required
                           class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all text-[#1b1b18] placeholder:text-gray-400">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tọa độ -->
                <div>
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1 mb-3">
                        <i class="ti ti-map-pin text-gray-400"></i>
                        Tọa độ GPS <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Latitude -->
                        <div class="space-y-2">
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}"
                                   placeholder="21.58345678" required
                                   class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] font-mono">
                            @error('latitude')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-2">
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}"
                                   placeholder="105.81234567" required
                                   class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] font-mono">
                            @error('longitude')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 mt-4">
                        <button type="button" onclick="getCurrentLocation()"
                                class="flex-1 md:flex-none px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-semibold flex items-center justify-center gap-2 transition">
                            <i class="ti ti-gps"></i>
                            Lấy vị trí hiện tại
                        </button>

                        <button type="button" onclick="openMapModal()"
                                class="flex-1 md:flex-none px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-semibold flex items-center justify-center gap-2 transition">
                            <i class="ti ti-map"></i>
                            Chọn trên bản đồ
                        </button>
                    </div>
                </div>

                <!-- Bán kính & Địa chỉ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Bán kính -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                            <i class="ti ti-radar text-gray-400"></i>
                            Bán kính cho phép
                        </label>
                        <div class="relative">
                            <input type="number" name="radius_meter" value="{{ old('radius_meter', $location->radius_meter ?? 50) }}"
                                   class="w-full pl-6 pr-16 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all text-[#1b1b18] font-semibold">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-6 pointer-events-none">
                                <span class="text-gray-400 font-medium text-sm">mét</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1 ml-1 font-medium">Khuyến nghị: 30 - 100 mét</p>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                            <i class="ti ti-map text-gray-400"></i>
                            Địa chỉ chi tiết
                        </label>
                        <textarea name="address" rows="2"
                                  placeholder="Số 123, Đường ABC, TP. Thái Nguyên..."
                                  class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all text-[#1b1b18] resize-none">{{ old('address', $location->address) }}</textarea>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_main" value="1"
                               {{ old('is_main', $location->is_main) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-[#1b1b18] focus:ring-[#b0ffc3]">
                        <span class="text-sm text-gray-700 font-medium">Vị trí chính dùng để chấm công</span>
                    </label>

                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                        <i class="ti ti-toggle-right text-gray-400"></i>
                        Trạng thái
                    </label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="status" value="1"
                                   {{ old('status', $location->status) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-[#1b1b18] focus:ring-[#b0ffc3]">
                            <span class="text-sm text-gray-700 font-medium">Hoạt động</span>
                        </label>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-10 border-t border-gray-100">
                    <a href="{{ route('admin.locations.index') }}"
                       class="w-full sm:w-auto px-10 py-4 text-sm font-bold text-gray-500 hover:text-black hover:bg-gray-50 rounded-2xl transition-all text-center">
                        Hủy bỏ
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto bg-[#1b1b18] hover:bg-black text-white px-10 py-4 rounded-2xl font-bold text-sm shadow-xl shadow-black/5 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy text-lg"></i>
                        Xác nhận cập nhật
                    </button>
                </div>
            </form>
        </div>

        <div class="flex items-center justify-center gap-2 mt-8 text-gray-400">
            <i class="ti ti-info-circle"></i>
            <p class="text-xs font-medium">Hệ thống sẽ sử dụng Google Maps để đối chiếu sai số thiết bị</p>
        </div>
    </div>
</div>

<!-- Map Modal -->
<div id="mapModal" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl w-full max-w-4xl mx-4 overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-bold text-lg">Chọn vị trí trên bản đồ</h3>
            <button onclick="closeMapModal()" class="text-2xl leading-none hover:text-red-500">×</button>
        </div>
        <div id="map" style="height: 500px; width: 100%;"></div>
        <div class="p-4 bg-gray-50 flex justify-end gap-3">
            <button onclick="closeMapModal()"
                    class="px-6 py-3 text-gray-600 hover:bg-gray-100 rounded-2xl">Hủy</button>
            <button onclick="useSelectedLocation()"
                    class="px-6 py-3 bg-blue-600 text-white rounded-2xl">Sử dụng vị trí này</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
let map;
let marker = null;
let selectedLat = null;
let selectedLng = null;

// Lấy vị trí hiện tại
function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert("Trình duyệt của bạn không hỗ trợ Geolocation!");
        return;
    }

    if (confirm("Cho phép lấy vị trí hiện tại của bạn?")) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                alert("✅ Đã lấy vị trí hiện tại thành công!");
            },
            (error) => {
                alert("Không thể lấy vị trí. Vui lòng kiểm tra quyền truy cập GPS.");
            }
        );
    }
}

// Mở modal bản đồ
function openMapModal() {
    document.getElementById('mapModal').classList.remove('hidden');

    setTimeout(() => {
        if (!map) {
            map = L.map('map').setView([21.5833, 105.8125], 13); // Trung tâm Thái Nguyên

            L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=vi', {
                attribution: '&copy; Google Maps',
                maxZoom: 20
            }).addTo(map);

            map.on('click', function(e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;

                if (marker) marker.remove();
                marker = L.marker([selectedLat, selectedLng]).addTo(map);
            });
        }
    }, 300);
}

function closeMapModal() {
    document.getElementById('mapModal').classList.add('hidden');
}

function useSelectedLocation() {
    if (selectedLat && selectedLng) {
        document.getElementById('latitude').value = selectedLat.toFixed(8);
        document.getElementById('longitude').value = selectedLng.toFixed(8);
        alert("✅ Đã chọn vị trí trên bản đồ!");
        closeMapModal();
    } else {
        alert("Vui lòng click vào bản đồ để chọn vị trí!");
    }
}
</script>
@endpush
