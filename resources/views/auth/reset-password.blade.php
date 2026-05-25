@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-10 rounded-[2rem] shadow-xl border border-gray-100 w-full max-w-md animate-fade-in-up">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Reset Password</h1>
            <p class="text-gray-500">Buat password baru untuk akun Anda.</p>
        </div>
        
        <form id="resetPasswordForm" class="space-y-6">
            <!-- Hidden input for token -->
            <input type="hidden" id="token" name="token" value="{{ $token }}">
            
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-lock text-gray-400 group-focus-within:text-blue-500 text-xl transition-colors"></i>
                    </div>
                    <input type="password" id="password" name="password" class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 font-medium" placeholder="Minimal 8 karakter" required minlength="8">
                    <button type="button" onclick="togglePassword('password', 'eye-icon-reset-pwd')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors focus:outline-none">
                        <i id="eye-icon-reset-pwd" class="ph ph-eye text-xl"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-check-circle text-gray-400 group-focus-within:text-blue-500 text-xl transition-colors"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 font-medium" placeholder="Ulangi password baru" required minlength="8">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-reset-conf')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-500 transition-colors focus:outline-none">
                        <i id="eye-icon-reset-conf" class="ph ph-eye text-xl"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('resetPasswordForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const token = document.getElementById('token').value;
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
                text: 'Sedang menyimpan password baru...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const data = await window.apiFetch('/api/auth/reset-password', {
                method: 'POST',
                body: JSON.stringify({ token, password })
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Password berhasil diubah. Silakan masuk dengan password baru Anda.',
                confirmButtonColor: '#16A34A'
            }).then(() => {
                window.location.href = '/login';
            });
        } catch (error) {
            let message = 'Gagal mereset password. Silakan coba lagi atau minta tautan baru.';
            if (error.data && error.data.message) {
                message = error.data.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: message,
                confirmButtonColor: '#DC2626'
            });
        }
    });
</script>
@endsection
