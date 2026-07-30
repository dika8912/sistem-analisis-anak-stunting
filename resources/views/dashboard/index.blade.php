@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Welcome Section (Profile) -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="h-20 w-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/30">
                <i class="ph ph-user text-4xl text-white"></i>
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-extrabold mb-1" id="profile-name">Memuat profil...</h1>
                <p class="text-blue-100 font-medium flex items-center justify-center md:justify-start">
                    <i class="ph ph-envelope-simple mr-2"></i>
                    <span id="profile-email">...</span>
                </p>
                <div class="mt-4 inline-block bg-white/20 px-4 py-1.5 rounded-full text-sm font-semibold backdrop-blur-sm border border-white/20">
                    Akun Guardian
                </div>
            </div>
        </div>
    </div>

    <!-- Children List Section -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Daftar Anak</h2>
                <p class="text-gray-500 mt-1">Pantau perkembangan dan histori diagnosa anak Anda.</p>
            </div>
            <a href="/children/create" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-md text-sm font-bold text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-none transform transition-all hover:-translate-y-0.5">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Anak
            </a>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Skeleton Card 1 -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 animate-pulse">
                <div class="h-12 w-12 bg-gray-200 rounded-full mb-4"></div>
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                <div class="h-8 bg-gray-200 rounded w-full"></div>
            </div>
            <!-- Skeleton Card 2 -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 animate-pulse hidden md:block">
                <div class="h-12 w-12 bg-gray-200 rounded-full mb-4"></div>
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                <div class="h-8 bg-gray-200 rounded w-full"></div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-12 text-center">
            <div class="h-24 w-24 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-4">
                <i class="ph ph-baby text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Data Anak</h3>
            <p class="text-gray-500 max-w-md mb-6">Data anak Anda belum ditambahkan. Klik tombol di bawah untuk menambahkan profil anak baru dan memulai pemantauan tumbuh kembang.</p>
            <a href="/children/create" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-md text-sm font-bold text-white hover:from-blue-700 hover:to-indigo-700 transition-all">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Data Anak
            </a>
        </div>

        <!-- Children Grid -->
        <div id="children-grid" class="hidden grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Data will be populated here by JS -->
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        // Elements
        const profileName = document.getElementById('profile-name');
        const profileEmail = document.getElementById('profile-email');
        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const childrenGrid = document.getElementById('children-grid');

        try {
            // Memanggil API Endpoint untuk profil user
            // Karena belum ada backend spesifik, kita gunakan asumsikan response standar.
            // Jika apiFetch error (seperti 404), kita bisa handle dengan catch.
            const response = await window.apiFetch('/api/guardians/me', {
                method: 'GET'
            });

            // Update Profile UI
            profileName.innerText = response.name || localStorage.getItem('user_name') || 'Orang Tua / Wali';
            profileEmail.innerText = response.email || '-';

            // Hide Loading
            loadingState.classList.add('hidden');

            const children = response.children || [];

            if (children.length === 0) {
                // Show Empty State
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
            } else {
                // Render Children Cards
                childrenGrid.classList.remove('hidden');
                childrenGrid.classList.add('grid');

                children.forEach(child => {
                    // Map snake_case status dari API -> label Indonesia & warna UI
                    const STATUS_LABEL_MAP = {
                        'normal':               'Normal',
                        'stunted':              'Pendek (Stunted)',
                        'severely_stunted':     'Sangat Pendek',
                        'tall':                 'Tinggi',
                        'wasted':               'Kurus',
                        'severely_wasted':      'Sangat Kurus',
                        'overweight':           'Kelebihan BB',
                        'obese':                'Obesitas',
                        'risk_of_overweight':   'Berisiko Gemuk',
                        'underweight':          'BB Kurang',
                        'severely_underweight': 'BB Sangat Kurang',
                    };

                    const STATUS_THEME = {
                        'normal':               { color: 'bg-green-100 text-green-700',  icon: 'ph-check-circle',   border: 'bg-green-500' },
                        'stunted':              { color: 'bg-orange-100 text-orange-700', icon: 'ph-warning-circle', border: 'bg-orange-500' },
                        'severely_stunted':     { color: 'bg-red-100 text-red-700',      icon: 'ph-warning',        border: 'bg-red-600' },
                        'wasted':               { color: 'bg-orange-100 text-orange-700', icon: 'ph-warning-circle', border: 'bg-orange-500' },
                        'severely_wasted':      { color: 'bg-red-100 text-red-700',      icon: 'ph-warning',        border: 'bg-red-600' },
                        'risk_of_overweight':   { color: 'bg-yellow-100 text-yellow-700', icon: 'ph-shield-warning', border: 'bg-yellow-500' },
                        'overweight':           { color: 'bg-amber-100 text-amber-700',  icon: 'ph-trend-up',       border: 'bg-amber-500' },
                        'obese':                { color: 'bg-red-100 text-red-700',      icon: 'ph-trend-up',       border: 'bg-red-500' },
                        'tall':                 { color: 'bg-blue-100 text-blue-700',    icon: 'ph-arrow-up',       border: 'bg-blue-500' },
                        'underweight':          { color: 'bg-orange-100 text-orange-700', icon: 'ph-warning-circle', border: 'bg-orange-500' },
                        'severely_underweight': { color: 'bg-red-100 text-red-700',      icon: 'ph-warning',        border: 'bg-red-600' },
                    };

                    const statusRaw = child.status || '';
                    const statusLabel = STATUS_LABEL_MAP[statusRaw] || (statusRaw ? statusRaw : 'Belum dicek');
                    const theme = STATUS_THEME[statusRaw] || { color: 'bg-gray-100 text-gray-700', icon: 'ph-info', border: 'bg-gray-300' };

                    let ageMonths = child.age_in_months || child.age_months;
                    if (ageMonths === undefined && (child.date_of_birth || child.birth_date)) {
                        const birth = new Date(child.date_of_birth || child.birth_date);
                        const now = new Date();
                        ageMonths = (now.getFullYear() - birth.getFullYear()) * 12 + (now.getMonth() - birth.getMonth());
                        if (ageMonths < 0) ageMonths = 0;
                    }

                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow relative group overflow-hidden';

                    card.innerHTML = `
                        <div class="absolute left-0 top-0 bottom-0 w-1 ${theme.border}"></div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center">
                                <i class="ph ph-baby text-2xl text-blue-600"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${theme.color}">
                                <i class="ph ${theme.icon} mr-1"></i>
                                ${statusLabel}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 truncate" title="${child.name}">${child.name}</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="ph ph-calendar-blank mr-1"></i> ${ageMonths !== undefined ? ageMonths + ' Bulan' : '-'}
                        </p>
                        
                        <a href="/children/${child.id}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-50 text-blue-600 rounded-xl text-sm font-semibold hover:bg-blue-50 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            Lihat Detail & Histori <i class="ph ph-arrow-right ml-2"></i>
                        </a>
                    `;
                    childrenGrid.appendChild(card);
                });
            }

        } catch (error) {
            console.error("Dashboard API Error:", error);
            // Hide loading
            loadingState.classList.add('hidden');
            
            // Tampilkan state dummy / error sementara karena API mungkin belum ada
            profileName.innerText = localStorage.getItem('user_name') || 'Orang Tua / Wali';
            profileEmail.innerText = 'Gagal memuat profil';
            
            // Tampilkan pesan error di area anak
            emptyState.classList.remove('hidden');
            emptyState.classList.add('flex');
            emptyState.innerHTML = `
                <div class="h-24 w-24 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4">
                    <i class="ph ph-warning-circle text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Gagal Memuat Data</h3>
                <p class="text-gray-500 max-w-md">Koneksi ke server bermasalah atau API belum siap. Silakan coba beberapa saat lagi.</p>
            `;
        }
    });
</script>
@endsection
