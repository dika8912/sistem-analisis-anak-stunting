@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 animate-fade-in-up">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-4">Kalkulator Deteksi Stunting</h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto">Gunakan form di bawah ini untuk mengecek status gizi anak secara instan berdasarkan standar WHO. Hasil ini hanya bersifat indikatif dan bukan vonis medis final.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Kolom Kiri: Form Input -->
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <h2 class="text-2xl font-bold">Data Pengukuran Anak</h2>
            </div>
            
            <form id="detectForm" class="p-8 space-y-6">
                <!-- Nama Anak -->
                <div>
                    <label for="child_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Balita / Anak (Opsional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-user text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" id="child_name" name="child_name" class="pl-12 block w-full border border-gray-300 rounded-xl px-4 py-4 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Contoh: Budi Santoso">
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kelamin</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-2xl cursor-pointer hover:bg-blue-50 transition-colors">
                            <input type="radio" name="gender" value="L" class="absolute h-0 w-0 opacity-0 peer" required>
                            <div class="flex flex-col items-center gap-2 peer-checked:text-blue-600 text-gray-400">
                                <i class="ph ph-gender-male text-3xl"></i>
                                <span class="font-bold text-sm">Laki-laki</span>
                            </div>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-blue-600 rounded-2xl pointer-events-none transition-colors"></div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-2xl cursor-pointer hover:pink-50 transition-colors">
                            <input type="radio" name="gender" value="P" class="absolute h-0 w-0 opacity-0 peer" required>
                            <div class="flex flex-col items-center gap-2 peer-checked:text-pink-500 text-gray-400">
                                <i class="ph ph-gender-female text-3xl"></i>
                                <span class="font-bold text-sm">Perempuan</span>
                            </div>
                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-pink-500 rounded-2xl pointer-events-none transition-colors"></div>
                        </label>
                    </div>
                </div>

                <!-- Umur -->
                <div>
                    <label for="age_months" class="block text-sm font-semibold text-gray-700 mb-1">Umur (Bulan)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-calendar text-gray-400 text-lg"></i>
                        </div>
                        <input type="number" id="age_months" name="age_months" min="0" max="60" class="pl-12 block w-full border border-gray-300 rounded-xl px-4 py-4 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Contoh: 24" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-medium">Bulan</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">*Kalkulator ini akurat untuk usia 0 hingga 60 bulan (5 Tahun).</p>
                </div>

                <!-- Tinggi Badan -->
                <div>
                    <label for="height" class="block text-sm font-semibold text-gray-700 mb-1">Tinggi Badan / Panjang Badan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-ruler text-gray-400 text-lg"></i>
                        </div>
                        <input type="number" step="0.1" id="height" name="height" min="30" max="150" class="pl-12 block w-full border border-gray-300 rounded-xl px-4 py-4 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Contoh: 85.5" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-medium">cm</span>
                        </div>
                    </div>
                </div>

                <!-- Berat Badan -->
                <div>
                    <label for="weight" class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-scales text-gray-400 text-lg"></i>
                        </div>
                        <input type="number" step="0.1" id="weight" name="weight" min="1" max="50" class="pl-12 block w-full border border-gray-300 rounded-xl px-4 py-4 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" placeholder="Contoh: 12.3" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-medium">kg</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none transform transition-all hover:-translate-y-1">
                        Cek Status Gizi Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Area Hasil -->
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden relative flex flex-col">
            
            <!-- Default State (Belum ada hasil) -->
            <div id="defaultState" class="flex flex-col items-center justify-center flex-1 p-10 text-center">
                <div class="h-32 w-32 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-6">
                    <i class="ph ph-stethoscope text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Menunggu Input Anda</h3>
                <p class="text-gray-500 max-w-md">Silakan isi formulir di sebelah kiri dan klik tombol cek untuk melihat hasil diagnosa WHO dan saran gizi.</p>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="hidden flex-col items-center justify-center flex-1 p-10 text-center">
                <div class="h-32 w-32 rounded-full flex items-center justify-center mb-6 animate-spin text-blue-600">
                    <i class="ph ph-spinner-gap text-6xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Memproses Data...</h3>
                <p class="text-gray-500">Menganalisa dengan parameter WHO dan Model Prediksi.</p>
            </div>

            <!-- Result State -->
            <div id="resultState" class="hidden flex-col flex-1">
                <!-- Header Status -->
                <!-- Header Status -->
                <div id="resultHeader" class="px-8 py-10 text-center text-white transition-colors duration-500">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm mb-4">
                        <i id="resultIcon" class="ph ph-check-circle text-5xl"></i>
                    </div>
                    <h3 id="resultChildNameDisplay" class="text-sm uppercase tracking-widest font-bold mb-1 opacity-80">Rangkuman Status Kesehatan</h3>
                    <h2 id="resultStatus" class="text-4xl font-black mb-2">Normal</h2>
                    <p id="resultBMIHeader" class="text-lg font-medium opacity-90 mt-2"></p>
                </div>

                <!-- Konten Detail -->
                <div class="p-8 space-y-6 flex-1 bg-gray-50">
                    <!-- Cards Stunting & Gizi (2 Kolom) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Card 1: Stunting -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm text-center flex flex-col justify-between">
                            <div>
                                <span class="text-xs bg-blue-100 text-blue-700 font-extrabold uppercase px-3 py-1 rounded-full inline-block mb-2">
                                    <i class="ph ph-ruler mr-1"></i> AI Stunting (TB/U)
                                </span>
                                <p class="text-xs text-gray-400 mb-1">Tinggi terhadap Umur</p>
                            </div>
                            <p id="resultStunting" class="text-xl font-black text-gray-800 mt-2">Aman</p>
                        </div>

                        <!-- Card 2: Status Gizi -->
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm text-center flex flex-col justify-between">
                            <div>
                                <span class="text-xs bg-purple-100 text-purple-700 font-extrabold uppercase px-3 py-1 rounded-full inline-block mb-2">
                                    <i class="ph ph-scales mr-1"></i> AI Status Gizi (BB/TB)
                                </span>
                                <p class="text-xs text-gray-400 mb-1">Berat terhadap Tinggi</p>
                            </div>
                            <p id="resultGizi" class="text-xl font-black text-gray-800 mt-2">Aman</p>
                        </div>
                    </div>

                    <!-- Highlight Keterangan Khusus Zona yang Sedang Tertera (Aktif) -->
                    <div id="activeZoneHighlightBox" class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm transition-all duration-300">
                        <!-- Diisi otomatis oleh JavaScript berdasarkan zona aktif hasil deteksi anak -->
                    </div>


                    <!-- Rekomendasi Box -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                            <i class="ph ph-lightbulb text-yellow-500 text-xl mr-2"></i>
                            Rekomendasi Tindakan
                        </h4>
                        <ul id="resultRecommendations" class="space-y-3 text-sm text-gray-600">
                            <!-- Injected by JS -->
                        </ul>
                    </div>

                    <!-- Saved to Account Notification Box -->
                    <div id="resultSavedBox" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center justify-between">
                        <div class="flex items-center text-emerald-800">
                            <i class="ph ph-check-circle text-2xl text-emerald-600 mr-3"></i>
                            <div>
                                <h4 class="font-bold text-sm">Data & Pengukuran Tersimpan di Akun Anda</h4>
                                <p id="resultSavedText" class="text-xs text-emerald-700">Data pertumbuhan anak ini telah otomatis ditambahkan ke riwayat.</p>
                            </div>
                        </div>
                        <a id="resultSavedLink" href="/dashboard" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm whitespace-nowrap">
                            Lihat Dashboard
                        </a>
                    </div>

                    <!-- Reminder Box -->
                    <div id="resultReminderBox" class="hidden">
                        <h4 class="font-bold mb-2 flex items-center">
                            <i class="ph ph-calendar-warning text-xl mr-2"></i>
                            Peringatan Kunjungan
                        </h4>
                        <p class="text-sm">Wajib melakukan pengukuran ulang (Posyandu) pada: <strong id="resultNextVisit"></strong></p>
                    </div>

                    <!-- Meal Plan Box -->
                    <div id="resultMealPlanBox" class="bg-blue-50 p-6 rounded-2xl border border-blue-200 shadow-sm hidden">
                        <h4 class="font-bold text-blue-900 mb-3 flex items-center">
                            <i class="ph ph-bowl-food text-xl mr-2"></i>
                            AI Meal Planner (Menu Lokal)
                        </h4>
                        <div class="space-y-2 text-sm text-blue-800">
                            <div class="flex items-start"><span class="font-bold w-16">Pagi:</span> <span id="mpPagi" class="flex-1"></span></div>
                            <div class="flex items-start"><span class="font-bold w-16">Siang:</span> <span id="mpSiang" class="flex-1"></span></div>
                            <div class="flex items-start"><span class="font-bold w-16">Malam:</span> <span id="mpMalam" class="flex-1"></span></div>
                        </div>
                    </div>

                    <!-- Grafik Referensi Stunting & Gizi beserta Titik Anak -->
                    <div id="resultChartBox" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                            <h4 class="font-bold text-gray-900 flex items-center">
                                <i class="ph ph-chart-line-up text-blue-500 text-xl mr-2"></i>
                                Grafik Posisi Anak vs Referensi WHO
                            </h4>
                            <div class="flex rounded-xl bg-gray-100 p-1">
                                <button type="button" id="tabStuntingChart" class="px-3 py-1 text-xs font-bold rounded-lg bg-white shadow-sm text-blue-600 transition-all">Stunting (TB/U)</button>
                                <button type="button" id="tabGiziChart" class="px-3 py-1 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-800 transition-all">Gizi (BB/TB)</button>
                            </div>
                        </div>
                        
                        <div class="w-full h-64 relative">
                            <canvas id="detectionGrowthChart"></canvas>
                        </div>
                        <p id="chartCaption" class="text-xs text-center text-gray-500 mt-3 font-medium">
                            *Titik warna menunjukkan posisi anak Anda dibandingkan kurva referensi normal.
                        </p>

                        <!-- Tabel Ringkasan Titik yang Dicek -->
                        <div class="mt-4 pt-4 border-t border-gray-100 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Parameter</th>
                                        <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Titik Ukur Anak</th>
                                        <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Rentang Normal (-2 SD s/d +2 SD)</th>
                                        <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Rata-Rata (0 SD)</th>
                                        <th class="px-3 py-2 text-left font-bold text-gray-500 uppercase">Status Prediksi AI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-gray-800">Tinggi (TB/U)</td>
                                        <td class="px-3 py-2 font-bold text-blue-600" id="tableTB">-</td>
                                        <td class="px-3 py-2 font-medium text-emerald-700" id="tableRangeTB">-</td>
                                        <td class="px-3 py-2 text-gray-600" id="tableNormalTB">-</td>
                                        <td class="px-3 py-2 font-bold" id="tableStatusTB">-</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-gray-800">Berat (BB/TB)</td>
                                        <td class="px-3 py-2 font-bold text-purple-600" id="tableBB">-</td>
                                        <td class="px-3 py-2 font-medium text-emerald-700" id="tableRangeBB">-</td>
                                        <td class="px-3 py-2 text-gray-600" id="tableNormalBB">-</td>
                                        <td class="px-3 py-2 font-bold" id="tableStatusBB">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Panduan & Keterangan Lengkap Semua Zona WHO (Di Bawah) -->
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <h5 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                <i class="ph ph-info text-blue-600 mr-2 text-base"></i>
                                Keterangan & Arti Semua Zona Antropometri WHO (Standar Medis):
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                                <!-- Zona Hijau -->
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex flex-col justify-between shadow-xs">
                                    <div>
                                        <div class="flex items-center space-x-1.5 mb-1.5 font-bold text-emerald-800">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                            <span>Zona Hijau (-2 SD s/d +2 SD)</span>
                                        </div>
                                        <p class="text-emerald-950 font-bold mb-1">Status: NORMAL / GIZI BAIK</p>
                                        <p class="text-gray-600 leading-relaxed">
                                            Pertumbuhan tinggi dan berat anak sesuai dengan kurva standar optimal anak sehat WHO.
                                        </p>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-emerald-200/60 font-semibold text-emerald-700">
                                        💡 <span class="underline">Saran:</span> Lanjutkan gizi seimbang & rutin pantau bulanan di Posyandu.
                                    </div>
                                </div>

                                <!-- Zona Kuning -->
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex flex-col justify-between shadow-xs">
                                    <div>
                                        <div class="flex items-center space-x-1.5 mb-1.5 font-bold text-amber-800">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                            <span>Zona Kuning (-3 s/d -2 SD | > +2 SD)</span>
                                        </div>
                                        <p class="text-amber-950 font-bold mb-1">Status: WASPADA (Pendek/Kurus/Berlebih)</p>
                                        <p class="text-gray-600 leading-relaxed">
                                            Anak berada di zona batas waspada. Mengindikasikan perlunya perbaikan asupan nutrisi segera.
                                        </p>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-amber-200/60 font-semibold text-amber-700">
                                        💡 <span class="underline">Saran:</span> Perbanyak protein hewani & konsultasi dengan kader/bidan.
                                    </div>
                                </div>

                                <!-- Zona Merah -->
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl flex flex-col justify-between shadow-xs">
                                    <div>
                                        <div class="flex items-center space-x-1.5 mb-1.5 font-bold text-rose-800">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                            <span>Zona Merah (< -3 SD | > +3 SD)</span>
                                        </div>
                                        <p class="text-rose-950 font-bold mb-1">Status: KRITIS (Sangat Pendek/Kurus)</p>
                                        <p class="text-gray-600 leading-relaxed">
                                            Pertumbuhan sangat jauh dari kurva standar. Mengindikasikan malnutrisi berat atau klinis.
                                        </p>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-rose-200/60 font-bold text-rose-700">
                                        🚨 <span class="underline">Saran:</span> Segera periksakan ke Dokter Spesialis Anak / Puskesmas!
                                    </div>
                                </div>

                                <!-- Zona Biru/Ungu -->
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl flex flex-col justify-between shadow-xs">
                                    <div>
                                        <div class="flex items-center space-x-1.5 mb-1.5 font-bold text-blue-800">
                                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                            <span>Zona Biru (> +2 SD Tinggi)</span>
                                        </div>
                                        <p class="text-blue-950 font-bold mb-1">Status: TALL (Tinggi di Atas Rata-Rata)</p>
                                        <p class="text-gray-600 leading-relaxed">
                                            Tinggi anak melebihi rata-rata usianya (normal, dipengaruhi genetik atau nutrisi sangat baik).
                                        </p>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-blue-200/60 font-semibold text-blue-700">
                                        💡 <span class="underline">Saran:</span> Normal, pastikan berat badan seimbang dengan tingginya.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Tersembunyi untuk Download PDF -->
                    <form id="printForm" action="/detect/print" method="POST" class="pt-4 mt-auto">
                        @csrf
                        <input type="hidden" name="child_name" id="pdf_child_name">
                        <input type="hidden" name="age_months" id="pdf_age">
                        <input type="hidden" name="gender" id="pdf_gender">
                        <input type="hidden" name="height" id="pdf_height">
                        <input type="hidden" name="weight" id="pdf_weight">
                        <input type="hidden" name="z_score" id="pdf_zscore">
                        <input type="hidden" name="status" id="pdf_status">
                        <input type="hidden" name="status_raw" id="pdf_status_raw">
                        <input type="hidden" name="ml_prediction" id="pdf_ml">
                        <input type="hidden" name="stunting_prediction" id="pdf_stunting">
                        <input type="hidden" name="gizi_prediction" id="pdf_gizi">
                        <input type="hidden" name="next_visit" id="pdf_next_visit">

                        <button type="submit" class="w-full flex items-center justify-center py-4 px-4 border-2 border-gray-900 rounded-xl text-sm font-bold text-gray-900 bg-white hover:bg-gray-900 hover:text-white focus:outline-none transition-colors">
                            <i class="ph ph-file-pdf mr-2 text-xl"></i>
                            Unduh Laporan PDF
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let detectionChartObj = null;
        let currentInputs = null;
        let currentResultData = null;

        document.getElementById('tabStuntingChart').addEventListener('click', () => renderDetectionChart('stunting'));
        document.getElementById('tabGiziChart').addEventListener('click', () => renderDetectionChart('gizi'));

        function renderDetectionChart(type) {
            const canvas = document.getElementById('detectionGrowthChart');
            if (!canvas || !currentInputs) return;
            const ctx = canvas.getContext('2d');
            if (detectionChartObj) {
                detectionChartObj.destroy();
            }

            const tabStunting = document.getElementById('tabStuntingChart');
            const tabGizi = document.getElementById('tabGiziChart');

            if (type === 'stunting') {
                tabStunting.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-white shadow-sm text-blue-600 transition-all';
                tabGizi.className = 'px-3 py-1 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-800 transition-all';
                document.getElementById('chartCaption').innerText = '*Titik ukur anak Anda (' + currentInputs.height + ' cm, Umur ' + currentInputs.age_months + ' bln) pada kurva referensi TB/U.';

                const refAges = [0, 6, 12, 18, 24, 36, 48, 60];
                const refPlus2TB = [54, 73, 81, 89, 94, 104, 111, 119];
                const refMedianTB = [50, 67, 75, 82, 87, 96, 103, 110];
                const refStuntingTB = [46, 61, 70, 76, 81, 89, 96, 102];

                detectionChartObj = new window.Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: refAges,
                        datasets: [
                            {
                                label: 'Batas Atas (+2 SD)',
                                data: refPlus2TB,
                                borderColor: '#3B82F6',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Rata-rata Normal (0 SD)',
                                data: refMedianTB,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderWidth: 2.5,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Batas Stunting (-2 SD)',
                                data: refStuntingTB,
                                borderColor: '#F97316',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Titik Ukur Anak Anda',
                                data: [{ x: currentInputs.age_months, y: currentInputs.height }],
                                borderColor: '#ffffff',
                                backgroundColor: currentResultData.stunting_status && currentResultData.stunting_status.includes('bahaya') ? '#DC2626' : '#2563EB',
                                borderWidth: 3,
                                pointRadius: 8,
                                pointHoverRadius: 10,
                                showLine: false,
                                type: 'scatter'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'linear',
                                min: 0,
                                max: 60,
                                title: { display: true, text: 'Umur (Bulan)', font: { size: 11, weight: 'bold' } },
                                grid: { color: '#F3F4F6' }
                            },
                            y: {
                                title: { display: true, text: 'Tinggi Badan (cm)', font: { size: 11, weight: 'bold' } },
                                grid: { color: '#F3F4F6' }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { boxWidth: 12, font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${context.parsed.y} cm (${context.parsed.x} bln)`
                                }
                            }
                        }
                    }
                });
            } else {
                tabGizi.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-white shadow-sm text-purple-600 transition-all';
                tabStunting.className = 'px-3 py-1 text-xs font-bold rounded-lg text-gray-500 hover:text-gray-800 transition-all';
                document.getElementById('chartCaption').innerText = '*Titik ukur anak Anda (' + currentInputs.weight + ' kg, Tinggi ' + currentInputs.height + ' cm) pada kurva referensi BB/TB.';

                const refHeights = [50, 60, 70, 80, 90, 100, 110];
                const refPlus2BB = [3.8, 7.0, 10.1, 12.8, 15.5, 18.6, 22.5];
                const refMedianBB = [3.3, 6.0, 8.5, 10.8, 13.0, 15.5, 18.5];
                const refWastingBB = [2.8, 5.1, 7.2, 9.2, 11.1, 13.1, 15.5];

                detectionChartObj = new window.Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: refHeights,
                        datasets: [
                            {
                                label: 'Batas Atas Gemuk (+2 SD)',
                                data: refPlus2BB,
                                borderColor: '#F59E0B',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Rata-rata Normal (0 SD)',
                                data: refMedianBB,
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                borderWidth: 2.5,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Batas Kurus (-2 SD)',
                                data: refWastingBB,
                                borderColor: '#EF4444',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 0
                            },
                            {
                                label: 'Titik Ukur Anak Anda',
                                data: [{ x: currentInputs.height, y: currentInputs.weight }],
                                borderColor: '#ffffff',
                                backgroundColor: currentResultData.gizi_status && currentResultData.gizi_status.includes('bahaya') ? '#DC2626' : '#9333EA',
                                borderWidth: 3,
                                pointRadius: 8,
                                pointHoverRadius: 10,
                                showLine: false,
                                type: 'scatter'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'linear',
                                min: 45,
                                max: 115,
                                title: { display: true, text: 'Tinggi Badan (cm)', font: { size: 11, weight: 'bold' } },
                                grid: { color: '#F3F4F6' }
                            },
                            y: {
                                title: { display: true, text: 'Berat Badan (kg)', font: { size: 11, weight: 'bold' } },
                                grid: { color: '#F3F4F6' }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { boxWidth: 12, font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${context.parsed.y} kg (${context.parsed.x} cm)`
                                }
                            }
                        }
                    }
                });
            }
        }
        const form = document.getElementById('detectForm');
        
        const defaultState = document.getElementById('defaultState');
        const loadingState = document.getElementById('loadingState');
        const resultState = document.getElementById('resultState');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Ambil Nilai Form
            const genderNode = document.querySelector('input[name="gender"]:checked');
            if(!genderNode) {
                Swal.fire('Oops', 'Pilih jenis kelamin terlebih dahulu', 'warning');
                return;
            }

            const gender = genderNode.value;
            const age_months = parseInt(document.getElementById('age_months').value);
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);

            // Validasi Sederhana
            if(age_months < 0 || height <= 0 || weight <= 0) {
                Swal.fire('Data Tidak Valid', 'Pastikan angka yang dimasukkan logis dan tidak minus.', 'error');
                return;
            }

            // Ganti UI ke Loading
            defaultState.classList.add('hidden');
            resultState.classList.add('hidden');
            loadingState.classList.remove('hidden');
            loadingState.classList.add('flex');

            try {
                const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
                
                // Konversi payload sesuai dengan dokumentasi API
                const apiPayload = {
                    gender: gender === 'L' ? 'M' : 'F',
                    age_in_months: age_months,
                    height_cm: height,
                    weight_kg: weight
                };

                // Panggil endpoint calculate (WHO) dan predict (ML) secara bersamaan
                const [calcResponse, predResponse] = await Promise.all([
                    fetch(`${API_BASE}/api/calculate`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(apiPayload)
                    }),
                    fetch(`${API_BASE}/api/predict`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(apiPayload)
                    })
                ]);

                if (!calcResponse.ok || !predResponse.ok) {
                    throw new Error('Gagal memanggil API Deteksi');
                }

                const calcData = await calcResponse.json();
                const predData = await predResponse.json();

                // Gabungkan hasil sesuai format yang diharapkan oleh renderResult
                const result = {
                    stunting_status: predData.ml_prediction.stunting_status_ml,
                    gizi_status: predData.ml_prediction.wasting_status_ml,
                    recommendations: calcData.recommendations,
                    meal_plan: calcData.meal_plan,
                    next_visit_date: calcData.next_visit_date
                };

                // Jika user login, simpan otomatis ke data anak pada user
                const token = localStorage.getItem('access_token');
                if (token) {
                    try {
                        const guardian = await window.apiFetch('/api/guardians/me', { method: 'GET' });
                        const children = guardian.children || [];
                        
                        const inputName = document.getElementById('child_name').value.trim() || ('Anak ' + (children.length + 1));
                        let matchingChild = children.find(c => c.name.toLowerCase() === inputName.toLowerCase());
                        
                        if (!matchingChild) {
                            const today = new Date();
                            const dobDate = new Date(today.setMonth(today.getMonth() - parseInt(age_months)));
                            const birth_date = dobDate.toISOString().split('T')[0];
                            
                            matchingChild = await window.apiFetch('/api/children', {
                                method: 'POST',
                                body: JSON.stringify({
                                    name: inputName,
                                    gender: gender === 'L' ? 'M' : 'F',
                                    date_of_birth: birth_date
                                })
                            });
                        }
                        
                        const todayStr = new Date().toISOString().split('T')[0];
                        await window.apiFetch('/api/measurements', {
                            method: 'POST',
                            body: JSON.stringify({
                                child_id: matchingChild.id,
                                measured_at: todayStr,
                                weight: parseFloat(weight),
                                height: parseFloat(height),
                                measured_by: "Orang Tua / Wali (Kalkulator Mandiri)"
                            })
                        });
                        
                        result.saved_child_id = matchingChild.id;
                        result.saved_child_name = matchingChild.name;

                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan ke Akun Anda!',
                            text: `Data & pengukuran untuk "${matchingChild.name}" berhasil disimpan ke riwayat anak Anda.`,
                            timer: 3500,
                            showConfirmButton: false
                        });
                    } catch (e) {
                        console.warn('Gagal menyimpan otomatis ke akun:', e);
                    }
                }

                // Format & Tampilkan Hasil
                renderResult(result, { age_months, gender, height, weight });

            } catch (error) {
                console.error(error);
                loadingState.classList.remove('flex');
                loadingState.classList.add('hidden');
                defaultState.classList.remove('hidden');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server deteksi. Coba beberapa saat lagi.'
                });
            }
        });

        function renderResult(data, inputs) {
            loadingState.classList.remove('flex');
            loadingState.classList.add('hidden');
            resultState.classList.remove('hidden');
            resultState.classList.add('flex');

            const statusNode = document.getElementById('resultStatus');
            const mlNode = document.getElementById('resultML');
            const headerNode = document.getElementById('resultHeader');
            const iconNode = document.getElementById('resultIcon');
            const recommendationsNode = document.getElementById('resultRecommendations');

            // Map semua label snake_case dari backend -> teks Indonesia yang ramah pengguna
            const STATUS_LABEL_MAP = {
                'zona_aman':            'Zona Aman (Normal / Sehat)',
                'zona_sedang':          'Zona Sedang (Peringatan)',
                'zona_bahaya':          'Zona Bahaya (Kritis)',
                'normal':               'Normal',
                'stunted':              'Pendek (Stunted)',
                'severely_stunted':     'Sangat Pendek (Severely Stunted)',
                'tall':                 'Tinggi (Tall)',
                'wasted':               'Kurus (Wasted)',
                'severely_wasted':      'Sangat Kurus (Severely Wasted)',
                'overweight':           'Kelebihan Berat Badan',
                'obese':                'Obesitas',
                'risk_of_overweight':   'Berisiko Kelebihan Berat Badan',
                'underweight':          'Berat Badan Kurang',
                'severely_underweight': 'Berat Badan Sangat Kurang',
                'model_not_trained':    'Prediksi ML Tidak Tersedia',
            };

            // Set Data
            const stuntingRaw = data.stunting_status || 'normal';
            const giziRaw = data.gizi_status || 'normal';

            const stuntingLabel = STATUS_LABEL_MAP[stuntingRaw] || stuntingRaw;
            const giziLabel = STATUS_LABEL_MAP[giziRaw] || giziRaw;

            // Tentukan status keseluruhan (worst-case)
            let overallRaw = 'normal';
            if (stuntingRaw.includes('bahaya') || giziRaw.includes('bahaya')) {
                overallRaw = 'zona_bahaya';
            } else if (stuntingRaw.includes('sedang') || giziRaw.includes('sedang')) {
                overallRaw = 'zona_sedang';
            } else if (stuntingRaw.includes('aman') && giziRaw.includes('aman')) {
                overallRaw = 'zona_aman';
            } else {
                overallRaw = stuntingRaw;
            }
            const overallLabel = STATUS_LABEL_MAP[overallRaw] || overallRaw;

            // Hitung BMI
            const height_m = inputs.height / 100;
            const bmi = height_m > 0 ? (inputs.weight / (height_m * height_m)).toFixed(2) : '0.00';
            
            statusNode.innerText = overallLabel;
            document.getElementById('resultBMIHeader').innerText = `IMT / BMI: ${bmi} kg/m²`;
            
            const stuntingNode = document.getElementById('resultStunting');
            const giziNode = document.getElementById('resultGizi');
            stuntingNode.innerText = stuntingLabel;
            giziNode.innerText = giziLabel;

            // Warna teks badge per card
            const getTextColorClass = (raw) => {
                if (raw.includes('bahaya')) return 'text-xl font-black text-red-600 mt-2';
                if (raw.includes('sedang')) return 'text-xl font-black text-orange-600 mt-2';
                return 'text-xl font-black text-emerald-600 mt-2';
            };
            stuntingNode.className = getTextColorClass(stuntingRaw);
            giziNode.className = getTextColorClass(giziRaw);

            // Highlight Keterangan Khusus Zona yang Sedang Tertera (Aktif)
            const activeZoneBox = document.getElementById('activeZoneHighlightBox');
            if (activeZoneBox) {
                const getZoneCardHTML = (raw, label, title) => {
                    if (raw.includes('bahaya') || raw.includes('severely') || raw.includes('obese')) {
                        return `
                            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl">
                                <div class="flex items-center space-x-1.5 font-bold text-rose-800 mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                    <span>${title}: ${label}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 leading-snug">
                                    <strong>Zona Kritis (< -3 SD atau > +3 SD).</strong> Mengindikasikan masalah gizi/pertumbuhan berat. Segera periksakan ke Dokter Spesialis Anak / Puskesmas!
                                </p>
                            </div>
                        `;
                    } else if (raw.includes('sedang') || raw.includes('wasted') || raw.includes('stunted') || raw.includes('underweight') || raw.includes('overweight')) {
                        return `
                            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="flex items-center space-x-1.5 font-bold text-amber-800 mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                                    <span>${title}: ${label}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 leading-snug">
                                    <strong>Zona Waspada (-3 s/d -2 SD atau > +2 SD).</strong> Berada di batas waspada. Butuh perbaikan pola makan protein hewani & evaluasi nutrisi.
                                </p>
                            </div>
                        `;
                    } else if (raw.includes('tall') || raw.includes('biru')) {
                        return `
                            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-center space-x-1.5 font-bold text-blue-800 mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                    <span>${title}: ${label}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 leading-snug">
                                    <strong>Tinggi di Atas Rata-Rata (> +2 SD).</strong> Tumbuh kembang tinggi di atas rata-rata (normal faktor genetik atau asupan nutrisi sangat baik).
                                </p>
                            </div>
                        `;
                    } else {
                        return `
                            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                                <div class="flex items-center space-x-1.5 font-bold text-emerald-800 mb-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                    <span>${title}: ${label}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 leading-snug">
                                    <strong>Zona Normal (-2 SD s/d +2 SD).</strong> Tumbuh kembang anak optimal sesuai kurva anak sehat WHO.
                                </p>
                            </div>
                        `;
                    }
                };

                const stuntingHighlightHTML = getZoneCardHTML(stuntingRaw, stuntingLabel, 'AI Stunting (TB/U)');
                const giziHighlightHTML = getZoneCardHTML(giziRaw, giziLabel, 'AI Status Gizi (BB/TB)');

                activeZoneBox.innerHTML = `
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                        <h4 class="text-xs font-bold text-gray-800 flex items-center">
                            <i class="ph ph-info text-blue-600 text-base mr-2"></i>
                            Highlight Keterangan Zona Terdeteksi (Kondisi Anak Anda):
                        </h4>
                        <span class="text-[10px] font-semibold bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full">Fokus Hasil Aktif</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        ${stuntingHighlightHTML}
                        ${giziHighlightHTML}
                    </div>
                `;
            }

            // Tema Warna berdasarkan Status Overall
            let bgColorClass = 'bg-gray-600';
            let iconClass = 'ph-info';

            const STATUS_THEME = {
                'zona_bahaya':          { bg: 'bg-red-600',     icon: 'ph-warning' },
                'zona_sedang':          { bg: 'bg-orange-500',  icon: 'ph-warning-circle' },
                'zona_aman':            { bg: 'bg-emerald-500', icon: 'ph-check-circle' },
                'severely_stunted':     { bg: 'bg-red-600',     icon: 'ph-warning' },
                'severely_wasted':      { bg: 'bg-red-600',     icon: 'ph-warning' },
                'severely_underweight': { bg: 'bg-red-600',     icon: 'ph-warning' },
                'stunted':              { bg: 'bg-orange-500',  icon: 'ph-warning-circle' },
                'wasted':               { bg: 'bg-orange-500',  icon: 'ph-warning-circle' },
                'underweight':          { bg: 'bg-orange-500',  icon: 'ph-warning-circle' },
                'normal':               { bg: 'bg-emerald-500', icon: 'ph-check-circle' },
                'risk_of_overweight':   { bg: 'bg-yellow-500',  icon: 'ph-shield-warning' },
                'overweight':           { bg: 'bg-amber-500',   icon: 'ph-trend-up' },
                'obese':                { bg: 'bg-red-500',     icon: 'ph-trend-up' },
                'tall':                 { bg: 'bg-blue-500',    icon: 'ph-arrow-up' },
            };
            const theme = STATUS_THEME[overallRaw] || STATUS_THEME[stuntingRaw] || { bg: 'bg-gray-600', icon: 'ph-info' };
            bgColorClass = theme.bg;
            iconClass = theme.icon;

            // Ganti warna header
            headerNode.className = `px-8 py-10 text-center text-white transition-colors duration-500 ${bgColorClass}`;
            iconNode.className = `ph ${iconClass} text-5xl`;

            // Rekomendasi (Dummy atau dari API jika ada)
            let recs = data.recommendations || [];
            if (recs.length === 0) {
                if (overallRaw.includes('normal') || overallRaw.includes('aman')) {
                    recs = [
                        "Pertahankan pola makan bergizi seimbang (4 sehat 5 sempurna).",
                        "Rutin mengecek tinggi dan berat badan anak setiap bulan.",
                        "Berikan stimulasi bermain yang cukup untuk perkembangan kognitif."
                    ];
                } else {
                    recs = [
                        "Segera konsultasikan dengan dokter anak atau tenaga kesehatan di Puskesmas.",
                        "Tingkatkan asupan protein hewani (telur, ikan, daging) secara teratur.",
                        "Perhatikan kebersihan sanitasi lingkungan."
                    ];
                }
            }

            recommendationsNode.innerHTML = recs.map(r => `
                <li class="flex items-start">
                    <i class="ph ph-check text-green-500 mt-0.5 mr-2"></i>
                    <span>${r}</span>
                </li>
            `).join('');

            // Meal Plan
            if (data.meal_plan) {
                document.getElementById('resultMealPlanBox').classList.remove('hidden');
                document.getElementById('mpPagi').innerText = data.meal_plan.pagi;
                document.getElementById('mpSiang').innerText = data.meal_plan.siang;
                document.getElementById('mpMalam').innerText = data.meal_plan.malam;
            } else {
                document.getElementById('resultMealPlanBox').classList.add('hidden');
            }

            // Next Visit Date
            if (data.next_visit_date) {
                const reminderBox = document.getElementById('resultReminderBox');
                reminderBox.classList.remove('hidden');
                
                const dateObj = new Date(data.next_visit_date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateStr = dateObj.toLocaleDateString('id-ID', options);
                
                if (overallRaw.includes('bahaya') || overallRaw.includes('sedang')) {
                    reminderBox.className = 'bg-red-50 p-6 rounded-2xl border border-red-200 shadow-sm';
                    reminderBox.innerHTML = `
                        <h4 class="font-bold text-red-800 mb-2 flex items-center">
                            <i class="ph ph-calendar-warning text-xl mr-2"></i>
                            Peringatan Kunjungan Ulang
                        </h4>
                        <p class="text-sm text-red-700">Wajib melakukan pengukuran ulang di Posyandu pada: <strong>${dateStr}</strong></p>
                    `;
                } else {
                    reminderBox.className = 'bg-emerald-50 p-6 rounded-2xl border border-emerald-200 shadow-sm';
                    reminderBox.innerHTML = `
                        <h4 class="font-bold text-emerald-800 mb-2 flex items-center">
                            <i class="ph ph-calendar-check text-xl mr-2"></i>
                            Jadwal Pemantauan Rutin
                        </h4>
                        <p class="text-sm text-emerald-700">Lakukan pemantauan rutin Posyandu selanjutnya pada: <strong>${dateStr}</strong></p>
                    `;
                }
            } else {
                document.getElementById('resultReminderBox').classList.add('hidden');
            }

            const savedBox = document.getElementById('resultSavedBox');
            if (savedBox) {
                if (data.saved_child_id) {
                    savedBox.classList.remove('hidden');
                    const savedText = document.getElementById('resultSavedText');
                    if (savedText) savedText.innerText = `Data & pengukuran untuk "${data.saved_child_name}" telah otomatis disimpan ke akun Anda.`;
                    const savedLink = document.getElementById('resultSavedLink');
                    if (savedLink) savedLink.href = `/children/show/${data.saved_child_id}`;
                } else {
                    savedBox.classList.add('hidden');
                }
            }

            // Simpan state & Tampilkan grafik referensi beserta titik anak
            currentInputs = inputs;
            currentResultData = data;

            const whoTB = [50.0, 54.7, 58.4, 61.4, 63.9, 66.2, 67.6, 69.2, 70.6, 72.0, 73.3, 74.5, 75.7, 76.9, 78.0, 79.1, 80.2, 81.2, 82.3, 83.2, 84.2, 85.1, 86.0, 86.9, 87.8, 88.7, 89.6, 90.4, 91.2, 92.0, 92.8, 93.6, 94.4, 95.2, 96.0, 96.7, 97.5, 98.2, 98.9, 99.6, 100.3, 101.0, 101.7, 102.4, 103.1, 103.8, 104.5, 105.1, 105.8, 106.4, 107.1, 107.7, 108.4, 109.0, 109.6, 110.2, 110.8, 111.4, 112.0, 112.5, 113.1];
            const normTB = whoTB[Math.min(60, Math.max(0, parseInt(inputs.age_months)))] || 75.0;
            const min2TB = (normTB * 0.93).toFixed(1);
            const plus2TB = (normTB * 1.08).toFixed(1);

            const normBB = (3.3 + Math.max(0, (inputs.height - 50)) * 0.25).toFixed(1);
            const min2BB = (normBB * 0.85).toFixed(1);
            const plus2BB = (normBB * 1.18).toFixed(1);

            document.getElementById('tableTB').innerText = `${inputs.height} cm (${inputs.age_months} bln)`;
            document.getElementById('tableRangeTB').innerText = `${min2TB} cm - ${plus2TB} cm`;
            document.getElementById('tableNormalTB').innerText = `~ ${normTB} cm`;
            document.getElementById('tableStatusTB').innerText = stuntingLabel;

            document.getElementById('tableBB').innerText = `${inputs.weight} kg (${inputs.height} cm)`;
            document.getElementById('tableRangeBB').innerText = `${min2BB} kg - ${plus2BB} kg`;
            document.getElementById('tableNormalBB').innerText = `~ ${normBB} kg`;
            document.getElementById('tableStatusBB').innerText = giziLabel;

            renderDetectionChart('stunting');

            // Isi nilai form PDF Tersembunyi & Nama Anak
            const childNameInput = document.getElementById('child_name');
            const nameVal = childNameInput && childNameInput.value ? childNameInput.value.trim() : '';
            if(document.getElementById('resultChildNameDisplay')) {
                document.getElementById('resultChildNameDisplay').innerText = nameVal ? `Status Kesehatan — ${nameVal}` : 'Rangkuman Status Kesehatan';
            }
            if(document.getElementById('pdf_child_name')) document.getElementById('pdf_child_name').value = nameVal || 'Anak / Balita';
            document.getElementById('pdf_age').value = inputs.age_months;
            document.getElementById('pdf_gender').value = inputs.gender;
            document.getElementById('pdf_height').value = inputs.height;
            document.getElementById('pdf_weight').value = inputs.weight;
            if(document.getElementById('pdf_zscore')) document.getElementById('pdf_zscore').value = '';
            if(document.getElementById('pdf_status')) document.getElementById('pdf_status').value = overallLabel;
            if(document.getElementById('pdf_ml')) document.getElementById('pdf_ml').value = giziLabel;
            if(document.getElementById('pdf_stunting')) document.getElementById('pdf_stunting').value = stuntingLabel;
            if(document.getElementById('pdf_gizi')) document.getElementById('pdf_gizi').value = giziLabel;
            if(document.getElementById('pdf_status_raw')) document.getElementById('pdf_status_raw').value = overallRaw;
            if(document.getElementById('pdf_next_visit') && data.next_visit_date) {
                const dateObj = new Date(data.next_visit_date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('pdf_next_visit').value = dateObj.toLocaleDateString('id-ID', options);
            }
        }
    });
</script>
@endsection
