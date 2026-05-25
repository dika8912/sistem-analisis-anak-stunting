@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="bg-white p-10 rounded-[2rem] shadow-xl border border-gray-100 w-full max-w-lg animate-fade-in-up">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Buat Akun Baru</h1>
            <p class="text-gray-500">Bergabung dengan Si Anting sekarang</p>
        </div>
        
        <form id="registerForm" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Masukkan nama lengkap Anda" required>
            </div>
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Masukkan username unik" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Masukkan email Anda" required>
            </div>
            <div>
                <label for="nomor_kk" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Kartu Keluarga (KK)</label>
                <input type="text" id="nomor_kk" name="nomor_kk" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Masukkan 16 digit Nomor KK" required minlength="16" maxlength="16">
            </div>
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Nomor HP / Telepon</label>
                <input type="text" id="phone" name="phone" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Contoh: 08123456789" required>
            </div>
            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Domisili</label>
                <textarea id="address" name="address" rows="2" class="w-full px-4 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Masukkan alamat lengkap" required></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative group">
                        <input type="password" id="password" name="password" class="w-full pl-4 pr-12 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Minimal 8 karakter" required minlength="8">
                        <button type="button" onclick="togglePassword('password', 'eye-icon-reg-pwd')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors focus:outline-none">
                            <i id="eye-icon-reg-pwd" class="ph ph-eye text-xl"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi</label>
                    <div class="relative group">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pl-4 pr-12 py-3.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Ulangi password" required minlength="8">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-reg-conf')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors focus:outline-none">
                            <i id="eye-icon-reg-conf" class="ph ph-eye text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Sudah punya akun? 
            <a href="/login" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const nomor_kk = document.getElementById('nomor_kk').value;
        const phone = document.getElementById('phone').value;
        const address = document.getElementById('address').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        
        if (password !== password_confirmation) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Konfirmasi password tidak cocok.',
                confirmButtonColor: '#2563EB'
            });
            return;
        }
        
        try {
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses pendaftaran...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kita panggil ke endpoint register API
            const data = await window.apiFetch('/api/auth/register', {
                method: 'POST',
                body: JSON.stringify({ name, username, email, nomor_kk, phone, address, password })
            });
            
            Swal.close();

            // Anggap response memberikan token sama seperti login,
            // atau arahkan pengguna ke login page setelah registrasi sukses
            if (data.access_token) {
                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('user_role', data.user?.role || 'user');
                localStorage.setItem('user_name', data.user?.name || name);
                window.location.href = '/dashboard';
            } else {
                // Jika API tidak auto login, tampilkan pesan dan redirect ke halaman login
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pendaftaran akun berhasil. Silakan masuk.',
                    confirmButtonColor: '#16A34A'
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        } catch (error) {
            let message = 'Pendaftaran gagal. Silakan coba lagi.';
            if (error.data && error.data.message) {
                message = error.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mendaftar',
                text: message,
                confirmButtonColor: '#DC2626'
            });
        }
    });
</script>
@endsection
