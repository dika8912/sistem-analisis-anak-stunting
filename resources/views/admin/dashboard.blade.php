            @extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Welcome Section (Admin Profile) -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="h-20 w-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/30">
                <i class="ph ph-shield-check text-4xl text-white"></i>
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-extrabold mb-1" id="admin-name">Administrator</h1>
                <p class="text-emerald-100 font-medium flex items-center justify-center md:justify-start">
                    <i class="ph ph-buildings mr-2"></i>
                    Panel Manajemen Sistem Si Anting
                </p>
                <div class="mt-4 inline-block bg-white/20 px-4 py-1.5 rounded-full text-sm font-semibold backdrop-blur-sm border border-white/20">
                    Akses Khusus Admin
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Singkat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-gray-500 font-medium mb-1">Total Anak Terdaftar</p>
                <h3 class="text-4xl font-black text-gray-800" id="stat-total">--</h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                <i class="ph ph-users-three text-3xl text-blue-500"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-gray-500 font-medium mb-1">Perlu Perhatian</p>
                <h3 class="text-4xl font-black text-orange-600" id="stat-stunting">--</h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-orange-50 flex items-center justify-center">
                <i class="ph ph-warning-circle text-3xl text-orange-500"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 flex items-center justify-between hover:shadow-xl transition-shadow">
            <div>
                <p class="text-gray-500 font-medium mb-1">Total Pengukuran</p>
                <h3 class="text-4xl font-black text-emerald-600" id="stat-measurements">--</h3>
            </div>
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i class="ph ph-heartbeat text-3xl text-emerald-500"></i>
            </div>
        </div>
    </div>

    <!-- Menu Utama -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Menu Manajemen</h2>
            <p class="text-gray-500 mt-1">Pilih tindakan yang ingin Anda lakukan sebagai administrator.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tambah Data Anak -->
            <a href="/children/create" class="group flex flex-col items-start p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-blue-50 hover:border-blue-200 transition-all">
                <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="ph ph-user-plus text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-700">Tambah Data Anak Baru</h3>
                <p class="text-gray-500 text-sm">Registrasi profil anak baru ke dalam sistem untuk memulai pemantauan kesehatan gizi.</p>
            </a>

            <!-- Cari & Kelola Data -->
            <a href="/admin/children" class="group flex flex-col items-start p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-emerald-50 hover:border-emerald-200 transition-all">
                <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="ph ph-magnifying-glass text-3xl text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-700">Cari Data Anak</h3>
                <p class="text-gray-500 text-sm">Cari data anak berdasarkan NIK atau nama untuk menambah riwayat pengukuran mereka.</p>
            </a>
            
            <!-- Edukasi / Artikel -->
            <a href="/admin/educations" class="group flex flex-col items-start p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-purple-50 hover:border-purple-200 transition-all">
                <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="ph ph-article text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-700">Kelola Artikel Edukasi</h3>
                <p class="text-gray-500 text-sm">Lihat materi edukasi stunting dan gizi yang tersedia untuk publik (Segera hadir).</p>
            </a>

            <!-- Deteksi Kalkulator -->
            <a href="/detect" class="group flex flex-col items-start p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-orange-50 hover:border-orange-200 transition-all">
                <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="ph ph-calculator text-3xl text-orange-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-700">Kalkulator Cepat</h3>
                <p class="text-gray-500 text-sm">Uji kalkulator diagnosa mandiri tanpa harus menyimpannya ke database pengguna.</p>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        // Tampilkan nama admin
        const nameField = document.getElementById('admin-name');
        nameField.innerText = 'Halo, ' + (localStorage.getItem('user_name') || 'Admin');
        
        // Coba load statistik admin dari endpoint khusus (jika sudah tersedia di FastAPI)
        try {
            const response = await window.apiFetch('/api/admin/stats', {
                method: 'GET'
            });
            if(response) {
                document.getElementById('stat-total').innerText = response.total_children || 0;
                document.getElementById('stat-stunting').innerText = response.stunting_children || 0;
                document.getElementById('stat-measurements').innerText = response.total_measurements || 0;
            }
        } catch (error) {
            console.log("Statistik belum tersedia di API backend, menampilkan data dummy.");
            document.getElementById('stat-total').innerText = '142';
            document.getElementById('stat-stunting').innerText = '18';
            document.getElementById('stat-measurements').innerText = '831';
        }
    });
</script>
@endsection
