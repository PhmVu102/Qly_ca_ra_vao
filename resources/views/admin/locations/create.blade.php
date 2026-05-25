@extends('layouts.admin')

@section('title', 'Thêm Vị trí GPS')

@section('content')
<div class="min-h-screen bg-[#FDFDFC] py-8 lg:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <!-- Header -->
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
                    <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Thêm Vị trí GPS</h1>
                    <p class="text-gray-500 mt-1">Thiết lập khu vực chấm công bằng GPS</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">

            <form method="POST" action="{{ route('admin.locations.store') }}" class="p-8 md:p-12 space-y-8">
                @csrf

                <!-- Tên vị trí -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                        <i class="ti ti-building text-gray-400"></i>
                        Tên vị trí <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="VD: Chi nhánh Thái Nguyên - HUNONIC"
                           required
                           class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3]">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tọa độ + Buttons -->
                <div>
                    <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1 mb-3">
                        <i class="ti ti-map-pin text-gray-400"></i>
                        Tọa độ GPS <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Latitude -->
                        <div class="space-y-2">
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                                   placeholder="21.58345678" required
                                   class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] font-mono">
                            @error('latitude')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-2">
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
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
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                            <i class="ti ti-radar text-gray-400"></i>
                            Bán kính cho phép (mét)
                        </label>
                        <div class="relative">
                            <input type="number" name="radius_meter" value="{{ old('radius_meter', 50) }}"
                                   class="w-full pl-6 pr-16 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400">mét</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-[13px] font-bold text-gray-700 ml-1">
                            <i class="ti ti-map text-gray-400"></i>
                            Địa chỉ chi tiết
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl resize-none">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_main" value="1"
                               {{ old('is_main') ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-[#1b1b18] focus:ring-[#b0ffc3]">
                        <span class="text-sm text-gray-700 font-semibold">Đặt làm vị trí chính để chấm công</span>
                    </label>
                    <p class="text-xs text-gray-400 ml-8">Nếu chưa có vị trí chính, hệ thống sẽ tự dùng vị trí này làm mặc định.</p>
                </div>

                <!-- Submit -->
                <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t">
                    <a href="{{ route('admin.locations.index') }}"
                       class="flex-1 sm:flex-none text-center py-4 text-gray-500 hover:bg-gray-100 rounded-2xl font-semibold transition">
                        Hủy
                    </a>
                    <button type="submit"
                            class="flex-1 sm:flex-none bg-[#1b1b18] hover:bg-black text-white py-4 px-4 rounded-2xl font-bold flex items-center justify-center gap-2">
                        Lưu vị trí GPS
                    </button>
                </div>
            </form>
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
