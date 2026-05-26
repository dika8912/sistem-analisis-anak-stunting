@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
            <h2 class="text-3xl font-extrabold tracking-tight">Tambah Pengukuran</h2>
            <p class="mt-2 text-emerald-50 text-sm font-medium">Rekam data tinggi dan berat badan anak untuk dianalisa.</p>
        </div>

        <form id="createMeasurementForm" class="p-8 space-y-6 bg-gray-50">
            <input type="hidden" id="child_id" value="{{ $childId }}">

            <!-- Tinggi Badan -->
            <div>
                <label for="height" class="block text-sm font-semibold text-gray-700 mb-1">Tinggi Badan (cm)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-ruler text-gray-400 text-lg"></i>
                    </div>
                    <input type="number" step="0.1" id="height" name="height" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none" placeholder="Misal: 85.5" required>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <span class="text-gray-400 font-medium">cm</span>
                    </div>
                </div>
            </div>

            <!-- Berat Badan -->
            <div>
                <label for="weight" class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan (kg)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-scales text-gray-400 text-lg"></i>
                    </div>
                    <input type="number" step="0.1" id="weight" name="weight" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none" placeholder="Misal: 12.3" required>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <span class="text-gray-400 font-medium">kg</span>
                    </div>
                </div>
            </div>

            <!-- Tanggal Pengukuran -->
            <div>
                <label for="measurement_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengukuran</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="ph ph-calendar-check text-gray-400 text-lg"></i>
                    </div>
                    <input type="date" id="measurement_date" name="measurement_date" class="pl-10 block w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none text-gray-700" required>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="/children/{{ $childId }}" class="px-6 py-3 bg-white border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-md text-sm font-bold text-white hover:from-emerald-600 hover:to-teal-700 hover:shadow-lg focus:outline-none transform transition-all hover:-translate-y-0.5">
                    Simpan Pengukuran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Proteksi role admin
        const role = localStorage.getItem('user_role');
        if (role !== 'admin') {
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: 'Hanya admin yang dapat menambahkan pengukuran.',
            }).then(() => {
                const childId = document.getElementById('child_id').value;
                window.location.href = `/children/${childId}`;
            });
            return;
        }

        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('measurement_date').value = today;

        document.getElementById('createMeasurementForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const child_id = document.getElementById('child_id').value;
            const height = document.getElementById('height').value;
            const weight = document.getElementById('weight').value;
            const measurement_date = document.getElementById('measurement_date').value;

            try {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang merekam pengukuran dan memproses analisis stunting.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                await window.apiFetch('/api/measurements', {
                    method: 'POST',
                    body: JSON.stringify({
                        child_id: parseInt(child_id),
                        height: parseFloat(height),
                        weight: parseFloat(weight),
                        measurement_date: measurement_date
                    })
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data pengukuran berhasil disimpan dan hasil deteksi telah diperbarui.',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    window.location.href = `/children/${child_id}`;
                });

            } catch (error) {
                let message = 'Gagal menyimpan pengukuran.';
                if (error.data && error.data.detail) {
                    message = Array.isArray(error.data.detail) ? error.data.detail[0].msg : error.data.detail;
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
