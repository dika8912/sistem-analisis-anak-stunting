@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <h2 class="text-3xl font-extrabold tracking-tight">Tambah Data Anak</h2>
            <p class="mt-2 text-blue-100 text-sm font-medium">Masukkan data profil anak untuk dipantau pertumbuhannya.</p>
        </div>

        <form id="createChildForm" class="p-8 space-y-6 bg-gray-50">
            <!-- NIK Orang Tua / Guardian ID (Hanya muncul jika Admin) -->
            <div id="guardian_id_group" class="hidden">
                <label for="guardian_id" class="block text-sm font-semibold text-gray-700 mb-1">ID Orang Tua (Guardian ID - Khusus Admin)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-identification-card text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" id="guardian_id" name="guardian_id" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Masukkan ID atau NIK Orang Tua (opsional untuk Orang Tua)">
                </div>
            </div>

            <!-- Nama Anak -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Anak</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-user text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" id="name" name="name" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Nama lengkap anak" required>
                </div>
            </div>

            <!-- NIK Anak (Opsional) -->
            <div>
                <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK Anak (16 Digit - Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-cardholder text-gray-400 text-lg"></i>
                    </div>
                    <input type="text" id="nik" name="nik" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Masukkan 16 digit NIK Anak jika ada" maxlength="16">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-calendar text-gray-400 text-lg"></i>
                        </div>
                        <input type="date" id="birth_date" name="birth_date" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-gray-700" required>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="ph ph-gender-intersex text-gray-400 text-lg"></i>
                        </div>
                        <select id="gender" name="gender" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-gray-700 appearance-none" required>
                            <option value="" disabled selected>Pilih jenis kelamin</option>
                            <option value="M">Laki-laki (M)</option>
                            <option value="F">Perempuan (F)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="ph ph-caret-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="javascript:history.back()" class="px-6 py-3 bg-white border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-md text-sm font-bold text-white hover:from-blue-700 hover:to-indigo-700 hover:shadow-lg focus:outline-none transform transition-all hover:-translate-y-0.5">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const role = localStorage.getItem('user_role');
        const guardianGroup = document.getElementById('guardian_id_group');

        // Jika login sebagai Admin, tampilkan field input ID Orang Tua
        if (role === 'admin' && guardianGroup) {
            guardianGroup.classList.remove('hidden');
        }

        document.getElementById('createChildForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const guardian_id = document.getElementById('guardian_id').value;
            const name = document.getElementById('name').value;
            const birth_date = document.getElementById('birth_date').value;
            const gender = document.getElementById('gender').value;
            const nik = document.getElementById('nik').value;

            try {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang menyimpan data anak.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const payload = {
                    name: name,
                    date_of_birth: birth_date,
                    gender: gender
                };
                if (nik && nik.trim() !== '') {
                    payload.nik = nik.trim();
                }

                await window.apiFetch('/api/children', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data anak berhasil ditambahkan.',
                    confirmButtonColor: '#2563EB'
                }).then(() => {
                    window.location.href = role === 'admin' ? '/admin/dashboard' : '/dashboard';
                });

            } catch (error) {
                let message = 'Gagal menyimpan data anak.';
                if (error.data) {
                    if (typeof error.data.detail === 'string') {
                        message = error.data.detail;
                    } else if (Array.isArray(error.data.detail)) {
                        message = error.data.detail.map(e => e.msg || 'Format input tidak valid').join('\n');
                    } else if (error.data.message) {
                        message = error.data.message;
                    }
                } else if (error.message) {
                    message = error.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: message,
                    confirmButtonColor: '#DC2626'
                });
            }
        });
    });
</script>
@endsection
