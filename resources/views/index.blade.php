<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Quản lý Chấm Công</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-hunonic-ngang.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-hunonic-ngang.png') }}">
    <script src="https://cdn.tailwindcss.com"></script> <!-- Giữ nguyên vite nếu bạn đang dùng Laravel -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-[#FDFDFC]  text-[#1b1b18] min-h-screen flex items-center justify-center p-6 font-sans">
    <div class="w-full max-w-[440px]">
        <!-- Card -->
        <div class="bg-white dark:bg-[#1a1a17] rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 dark:border-white/5 overflow-hidden transition-all">

            <!-- Header -->
            <div class="px-10 pt-12 pb-8 text-center">
                <div class="mx-auto mb-6 flex items-center justify-center">
                    <img src="{{ asset('storage/images/logo-hunonic-ngang.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-[#1b1b18] dark:text-white mb-2">
                    Quản lý Chấm Công
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Chào mừng bạn quay trở lại!
                </p>
            </div>

            <!-- Form -->
            <div class="px-10 pb-10">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email / Mã nhân viên -->
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                            Email hoặc Mã nhân viên
                        </label>
                        <input
                            type="text"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full px-5 py-3.5 rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 text-[#1b1b18] dark:text-white outline-none focus:ring-2 focus:ring-[#b0ffc3] dark:focus:ring-[#b0ffc3]/30 focus:border-[#b0ffc3] transition-all placeholder:text-gray-400"
                            placeholder="example@company.com"
                        >

                        @error('email')
                            <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-[13px] font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">
                            Mật khẩu
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                class="w-full px-5 py-3.5 pr-12 rounded-2xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 text-[#1b1b18] dark:text-white outline-none focus:ring-2 focus:ring-[#b0ffc3] dark:focus:ring-[#b0ffc3]/30 focus:border-[#b0ffc3] transition-all placeholder:text-gray-400"
                                placeholder="••••••••"
                            >
                            <button type="button" id="togglePassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.516-4.1M6.343 6.343A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.885 3.162M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between py-1">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="remember" id="remember"
                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-black focus:ring-black">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors">Ghi nhớ</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white hover:underline transition-all">
                                Quên mật khẩu?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full bg-[#1b1b18] dark:bg-[#b0ffc3] dark:text-black text-white font-bold py-4 rounded-2xl hover:shadow-lg hover:shadow-black/10 dark:hover:shadow-[#b0ffc3]/20 hover:-translate-y-0.5 active:scale-95 transition-all duration-200">
                        Đăng nhập hệ thống
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-6 bg-gray-50/50 dark:bg-white/5 border-t border-gray-100 dark:border-white/5 text-center">
                <p class="text-[11px] uppercase tracking-widest text-gray-400 dark:text-gray-500 font-medium">
                    &copy; 2026 HUNONIC - Hệ thống Quản lý Ra vào Ca
                </p>
            </div>
        </div>

        <!-- Bottom Decor (Optional) -->
        <p class="mt-8 text-center text-sm text-gray-400">
            Bạn gặp sự cố? <a href="#" class="text-black dark:text-[#b0ffc3] font-semibold underline-offset-4 hover:underline">Liên hệ kỹ thuật</a>
        </p>
    </div>
</body>
<script>
    const emailInput    = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberCheck = document.getElementById('remember');

    // Khi load trang: nếu đã lưu thì điền vào ô input
    if (localStorage.getItem('remember_me') === 'true') {
        emailInput.value    = localStorage.getItem('saved_email') || '';
        passwordInput.value = localStorage.getItem('saved_password') || '';
        rememberCheck.checked = true;
    }

    // Khi submit form: lưu hoặc xóa tùy checkbox
    document.querySelector('form').addEventListener('submit', function () {
        if (rememberCheck.checked) {
            localStorage.setItem('remember_me', 'true');
            localStorage.setItem('saved_email', emailInput.value);
            localStorage.setItem('saved_password', passwordInput.value);
        } else {
            localStorage.removeItem('remember_me');
            localStorage.removeItem('saved_email');
            localStorage.removeItem('saved_password');
        }
    });
    document.getElementById('togglePassword').addEventListener('click', function () {
        const eyeOpen   = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        if (passwordInput.type === 'password') {

            passwordInput.type = 'text';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        } else {

            passwordInput.type = 'password';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        }
    });
</script>
</html>
