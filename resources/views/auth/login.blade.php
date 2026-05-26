@extends('layouts.app')

@section('content')
<!-- Container -->
<div class="flex items-center justify-center min-h-[80vh] py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl w-full bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row transform transition-all hover:shadow-3xl border border-gray-100">
        
        <!-- Left Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-white">
            <div class="mb-10">
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">Selamat Datang</h2>
                <p class="text-gray-500 text-lg">Silakan masuk ke akun Si Anting Anda.</p>
            </div>

            <form id="loginForm" class="space-y-6">
                <!-- Username / Email Input -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-user text-gray-400 group-focus-within:text-blue-500 text-xl transition-colors"></i>
                    </div>
                    <input type="text" id="username" name="username" class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 font-medium" placeholder="Email atau Username Anda" required>
                </div>

                <!-- Password Input -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-400 group-focus-within:text-blue-500 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" class="w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 font-medium" placeholder="Password" required>
                    <button type="button" onclick="togglePassword('password', 'eye-icon-login')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors focus:outline-none">
                        <i id="eye-icon-login" class="ph ph-eye text-xl"></i>
                    </button>
                </div>


                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer transition-colors">
                        <label for="remember" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">Ingat Saya</label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-semibold text-blue-600 hover:text-blue-800 transition-colors">Lupa Password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all hover:-translate-y-1">
                        Masuk Sekarang
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-base text-gray-600">
                    Belum memiliki akun? 
                    <a href="/register" class="font-bold text-blue-600 hover:text-blue-800 transition-colors">Daftar di sini</a>
                </p>
            </div>
            
            <!-- Separator -->
            <div class="mt-10 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">Atau masuk dengan</span>
                </div>
            </div>

            <!-- Social Login -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <button class="w-full flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:shadow-md transition-all">
                    <i class="ph ph-google-logo mr-2 text-lg text-red-500"></i>
                    Google
                </button>
                <button class="w-full flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:shadow-md transition-all">
                    <i class="ph ph-facebook-logo mr-2 text-lg text-blue-600"></i>
                    Facebook
                </button>
            </div>
        </div>

        <!-- Right Side: Carousel Panel -->
        <div class="w-full md:w-1/2 relative hidden md:block overflow-hidden rounded-r-[2.5rem]">
            <!-- Carousel Track -->
            <div id="carousel-track" class="flex transition-transform duration-700 ease-in-out h-full" style="width: 300%;">
                
                <!-- Slide 1: Logo -->
                <div class="w-1/3 relative h-full bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-800 flex flex-col justify-center items-center text-white p-12 text-center">
                    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <img src="{{ asset('Image/SI Anting Logo.png') }}" alt="Logo Si Anting" class="h-40 w-auto mb-8 drop-shadow-2xl hover:scale-105 transition-transform">
                        <h3 class="text-4xl font-extrabold mb-4 leading-tight">Selamat Datang di Si Anting</h3>
                        <p class="text-blue-50 text-lg leading-relaxed font-medium">Sistem Pakar Stunting untuk masa depan anak yang lebih baik.</p>
                    </div>
                </div>

                <!-- Slide 2: Dokter Anak -->
                <div class="w-1/3 relative h-full">
                    <img src="{{ asset('Image/dokteranak.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Dokter Anak">
                    <div class="absolute inset-0 bg-indigo-900/60 mix-blend-multiply"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/40 to-transparent"></div>
                    <div class="relative z-10 h-full flex flex-col justify-end items-center text-white p-12 text-center pb-24">
                        <h3 class="text-4xl font-extrabold mb-4 leading-tight drop-shadow-lg">Pantau Tumbuh Kembang</h3>
                        <p class="text-blue-50 text-lg leading-relaxed font-medium drop-shadow-md">Konsultasi dan deteksi dini bersama pakar kesehatan anak.</p>
                    </div>
                </div>

                <!-- Slide 3: Ibu & Anak -->
                <div class="w-1/3 relative h-full">
                    <img src="{{ asset('Image/anakdanibu.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Ibu dan Anak">
                    <div class="absolute inset-0 bg-indigo-900/60 mix-blend-multiply"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/40 to-transparent"></div>
                    <div class="relative z-10 h-full flex flex-col justify-end items-center text-white p-12 text-center pb-24">
                        <h3 class="text-4xl font-extrabold mb-4 leading-tight drop-shadow-lg">Kesehatan Ibu & Anak</h3>
                        <p class="text-blue-50 text-lg leading-relaxed font-medium drop-shadow-md">Pastikan asupan gizi yang tepat untuk mencegah stunting sejak dini.</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Dots -->
            <div class="absolute bottom-12 left-0 right-0 flex justify-center space-x-3 z-20">
                <button type="button" onclick="goToSlide(0)" id="dot-0" class="w-3 h-3 rounded-full transition-all duration-300 bg-white shadow-lg scale-125 focus:outline-none"></button>
                <button type="button" onclick="goToSlide(1)" id="dot-1" class="w-3 h-3 rounded-full transition-all duration-300 bg-white/40 hover:bg-white/70 focus:outline-none"></button>
                <button type="button" onclick="goToSlide(2)" id="dot-2" class="w-3 h-3 rounded-full transition-all duration-300 bg-white/40 hover:bg-white/70 focus:outline-none"></button>
            </div>
        </div>

        <script>
            let currentSlide = 0;
            const track = document.getElementById('carousel-track');
            const totalSlides = 3;

            function updateDots() {
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.getElementById(`dot-${i}`);
                    if (i === currentSlide) {
                        dot.classList.remove('bg-white/40', 'hover:bg-white/70');
                        dot.classList.add('bg-white', 'shadow-lg', 'scale-125');
                    } else {
                        dot.classList.remove('bg-white', 'shadow-lg', 'scale-125');
                        dot.classList.add('bg-white/40', 'hover:bg-white/70');
                    }
                }
            }

            function goToSlide(index) {
                currentSlide = index;
                track.style.transform = `translateX(-${(currentSlide * 100) / totalSlides}%)`;
                updateDots();
            }

            // Auto-advance
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                goToSlide(currentSlide);
            }, 5000);
        </script>
        
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const remember = document.getElementById('remember').checked;
        
        try {
            // Tampilkan loading state
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses login...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // FastAPI OAuth2PasswordRequestForm expects application/x-www-form-urlencoded
            const params = new URLSearchParams();
            params.append('username', username); // FastAPI uses 'username' field for login
            params.append('password', password);
            params.append('remember_me', remember); // Teruskan nilai remember_me ke backend

            const data = await window.apiFetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params
            });
            
            // Simpan token ke localStorage
            localStorage.setItem('access_token', data.access_token);
            
            // Fungsi untuk men-decode JWT
            function parseJwt(token) {
                try {
                    const base64Url = token.split('.')[1];
                    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                    const jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                    }).join(''));
                    return JSON.parse(jsonPayload);
                } catch (e) {
                    return null;
                }
            }

            const decodedToken = parseJwt(data.access_token);
            
            // Ambil role dari response (jika ada), atau dari JWT token payload (fallback standard)
            const tokenRole = decodedToken ? (decodedToken.role || decodedToken.user_role) : null;
            const tokenName = decodedToken ? (decodedToken.name || decodedToken.sub) : null;
            
            // Jika token tidak memiliki parameter role, kita cek dari username
            const isUsernameAdmin = (username.toLowerCase() === 'admin') || (tokenName && tokenName.toLowerCase() === 'admin');
            const fallbackRole = isUsernameAdmin ? 'admin' : 'user';

            const userRole = (data.user && data.user.role) ? data.user.role : (tokenRole || fallbackRole);
            const userName = (data.user && data.user.name) ? data.user.name : username;
            
            localStorage.setItem('user_role', userRole);
            localStorage.setItem('user_name', userName);
            
            Swal.close();

            // Redirect sesuai role
            if (userRole === 'admin') {
                window.location.href = '/admin/dashboard';
            } else {
                window.location.href = '/dashboard';
            }
        } catch (error) {
            let message = 'Login gagal. Silakan periksa kembali email dan password Anda.';
            if (error.data && error.data.message) {
                message = error.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: message,
                confirmButtonColor: '#2563EB'
            });
        }
    });
</script>
@endsection
