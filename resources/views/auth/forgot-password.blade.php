@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-10 rounded-[2rem] shadow-xl border border-gray-100 w-full max-w-md animate-fade-in-up">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Lupa Password?</h1>
            <p class="text-gray-500">Masukkan email Anda untuk menerima tautan reset password.</p>
        </div>
        
        <form id="forgotPasswordForm" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-envelope text-gray-400 group-focus-within:text-blue-500 text-xl transition-colors"></i>
                    </div>
                    <input type="email" id="email" name="email" class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-gray-800 placeholder-gray-400 font-medium" placeholder="Masukkan email Anda" required>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                Kirim Tautan Reset
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Ingat password Anda? 
            <a href="/login" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        
        try {
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang mengirim instruksi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Endpoint backend FastAPI untuk forgot password
            const data = await window.apiFetch('/api/auth/forgot-password', {
                method: 'POST',
                body: JSON.stringify({ email })
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Tautan reset password telah dikirim ke email Anda.',
                confirmButtonColor: '#16A34A'
            });
        } catch (error) {
            let message = 'Gagal mengirim instruksi reset. Silakan coba lagi.';
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
