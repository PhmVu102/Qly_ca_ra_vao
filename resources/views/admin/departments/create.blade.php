@extends('layouts.admin')

@section('title', 'Thêm phòng ban mới')

@section('content')
<div class="p-6 lg:p-10 max-w-4xl mx-auto min-h-screen bg-[#FDFDFC]">
    <div class="mb-10">
        <a href="{{ route('admin.departments.index') }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-[#1b1b18] mb-6 transition-colors group">
            <i class="ti ti-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="text-sm font-medium">Quay lại danh sách</span>
        </a>

        <h1 class="text-3xl font-bold text-[#1b1b18] tracking-tight">Thêm phòng ban mới</h1>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-gray-100 p-8 md:p-12">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-8">
            @csrf

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-gray-700 ml-1">Tên phòng ban <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all"
                           placeholder="Nhập tên phòng ban" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-gray-700 ml-1">Mô tả</label>
                    <textarea name="description" rows="4"
                              class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#b0ffc3] focus:border-[#b0ffc3] transition-all"
                              placeholder="Mô tả ngắn về phòng ban">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-10 border-t border-gray-100">
                <a href="{{ route('admin.departments.index') }}"
                   class="w-full sm:w-auto px-10 py-4 text-sm font-bold text-gray-500 hover:text-black hover:bg-gray-50 rounded-2xl transition-all text-center">
                    Hủy bỏ
                </a>

                <button type="submit"
                        class="w-full sm:w-auto bg-[#1b1b18] hover:bg-black text-white px-10 py-4 rounded-2xl font-bold text-sm shadow-xl shadow-black/5 hover:-translate-y-0.5 active:scale-95 transition-all">
                    Lưu phòng ban
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
