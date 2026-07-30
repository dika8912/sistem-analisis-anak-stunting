<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Si Anting - Sistem Pakar Stunting</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Script Guard Global (Client-Side Middleware) -->
    <script>
        const token = localStorage.getItem('access_token');
        const role = localStorage.getItem('user_role');
        const currentPath = window.location.pathname;
        const name = localStorage.getItem('user_name') || 'User';

        const publicRoutes = ['/login', '/register', '/', '/about', '/edukasi', '/educations', '/diagnosa', '/detect', '/forgot-password', '/reset-password'];

        const isPublic = publicRoutes.some(route => {
            return currentPath === route || (route !== '/' && currentPath.startsWith(route + '/'));
        });

        if (!token && !isPublic) {
            // Redirect jika tidak ada token dan bukan di rute publik
            window.location.href = '/login';
        }

        if (token && currentPath.startsWith('/admin') && role !== 'admin') {
            // Redirect jika mencoba mengakses rute admin tapi bukan admin
            window.location.href = '/dashboard';
        }

        // Jika admin mengakses dashboard user biasa, arahkan kembali ke admin dashboard
        if (token && role === 'admin' && (currentPath === '/dashboard' || currentPath === '/children')) {
            window.location.href = '/admin/dashboard';
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Update UI Berdasarkan Status Login
            const guestMenu = document.getElementById('nav-guest');
            const authMenu = document.getElementById('nav-auth');
            const adminMenu = document.getElementById('nav-admin');
            const userNameSpan = document.getElementById('nav-user-name');

            if (token) {
                if (guestMenu) {
                    guestMenu.classList.add('hidden');
                    guestMenu.classList.remove('flex');
                }
                if (authMenu) {
                    authMenu.classList.remove('hidden');
                    authMenu.classList.add('flex');
                }
                if (userNameSpan) userNameSpan.innerText = `Halo, ${name}`;

                if (role === 'admin') {
                    if (adminMenu) adminMenu.classList.remove('hidden');
                } else {
                    const userDashMenu = document.getElementById('nav-user-dashboard');
                    if (userDashMenu) userDashMenu.classList.remove('hidden');
                }
            } else {
                if (guestMenu) {
                    guestMenu.classList.remove('hidden');
                    guestMenu.classList.add('flex');
                }
                if (authMenu) {
                    authMenu.classList.add('hidden');
                    authMenu.classList.remove('flex');
                }
                if (adminMenu) adminMenu.classList.add('hidden');
                const userDashMenu = document.getElementById('nav-user-dashboard');
                if (userDashMenu) userDashMenu.classList.add('hidden');
            }
        });

        function handleLogout() {
            localStorage.removeItem('access_token');
            localStorage.removeItem('user_role');
            localStorage.removeItem('user_name');
            window.location.href = '/login';
        }

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <nav class="sticky top-0 bg-white shadow-md p-4 transition-all duration-300" style="z-index: 9999; background-color: #ffffff;">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="/" class="flex items-center space-x-3">
                <img src="{{ asset('Image/SI Anting Logo.png') }}" alt="Logo Si Anting" class="h-16 w-auto hover:scale-105 transition-transform">
                <span class="text-3xl font-bold text-blue-600">Si Anting</span>
            </a>
            <div class="flex items-center space-x-8">
                <ul class="flex space-x-6 font-medium">
                    <li><a href="/" class="hover:text-blue-500 transition-colors">Beranda</a></li>
                    <li><a href="/educations" class="hover:text-blue-500 transition-colors">Edukasi</a></li>
                    <li><a href="/detect" class="hover:text-blue-500 transition-colors">Kalkulator Deteksi</a></li>
                    <!-- Menu Admin (Hidden by default, shown via JS) -->
                    <li id="nav-admin" class="hidden"><a href="/admin/dashboard" class="text-red-600 font-semibold hover:text-red-700 transition-colors">Admin Dashboard</a></li>
                    <!-- Menu User Dashboard (Hidden by default) -->
                    <li id="nav-user-dashboard" class="hidden"><a href="/dashboard" class="text-blue-600 font-semibold hover:text-blue-700 transition-colors">Dashboard Saya</a></li>
                </ul>
                <div class="flex items-center space-x-3 border-l pl-6 border-gray-200">
                    <!-- Guest Menu -->
                    <div id="nav-guest" class="hidden items-center space-x-3">
                        <a href="/login" class="text-blue-600 font-semibold hover:text-blue-700 px-4 py-2 transition-colors">Masuk</a>
                        <a href="/register" class="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 hover:shadow-md transition-all duration-300">Daftar</a>
                    </div>
                    
                    <!-- Auth Menu -->
                    <div id="nav-auth" class="hidden items-center">
                        <span id="nav-user-name" class="text-gray-700 font-medium mr-4">Halo, User</span>
                        <button onclick="handleLogout()" class="bg-red-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-red-700 hover:shadow-md transition-all duration-200">Keluar</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-6xl mx-auto w-full p-6">
        @yield('content')
    </main>

    <footer class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white mt-auto shadow-inner">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-3 text-center md:text-left">
                <div class="bg-white/10 p-2 rounded-xl backdrop-blur-sm">
                    <img src="{{ asset('Image/SI Anting Logo.png') }}" alt="Logo Si Anting" class="h-10 w-auto">
                </div>
                <div>
                    <span class="text-xl font-bold tracking-wide">Si Anting</span>
                    <p class="text-xs text-blue-100">Sistem Pakar Deteksi Dini Stunting & Wasting Berbasis AI</p>
                </div>
            </div>

            <!-- Tombol About Us / Tentang Developer di Bagian Bawah -->
            <div class="flex items-center space-x-4">
                <a href="/about" class="inline-flex items-center space-x-2.5 bg-white/15 hover:bg-white/25 text-white px-6 py-2.5 rounded-full font-semibold text-sm border border-white/25 backdrop-blur-md transition-all duration-300 hover:scale-105 shadow-md">
                    <svg class="w-4 h-4 text-yellow-300 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                    <span>Tentang Pengembang (About Us)</span>
                </a>
            </div>
        </div>
        <div class="border-t border-white/10 py-4 text-center text-xs text-blue-200">
            <p>&copy; 2026 Si Anting - Deteksi Dini Stunting. Dibangun dengan ❤️ untuk Generasi Emas Indonesia.</p>
        </div>
    </footer>

</body>
</html>