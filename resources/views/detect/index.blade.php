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
                <div id="resultHeader" class="px-8 py-10 text-center text-white transition-colors duration-500">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm mb-4">
                        <i id="resultIcon" class="ph ph-check-circle text-5xl"></i>
                    </div>
                    <h3 class="text-sm uppercase tracking-widest font-bold mb-1 opacity-80">Status Gizi</h3>
                    <h2 id="resultStatus" class="text-4xl font-black mb-2">Normal</h2>
                </div>

                <!-- Konten Detail -->
                <div class="p-8 space-y-6 flex-1 bg-gray-50">
                    <!-- Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm text-center">
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Z-Score (TB/U)</p>
                            <p id="resultZScore" class="text-2xl font-black text-gray-800">0.00</p>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm text-center">
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Prediksi Lanjut (ML)</p>
                            <p id="resultML" class="text-xl font-bold text-gray-800">Aman</p>
                        </div>
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

                    <!-- Form Tersembunyi untuk Download PDF -->
                    <form id="printForm" action="/detect/print" method="POST" class="pt-4 mt-auto">
                        @csrf
                        <input type="hidden" name="age_months" id="pdf_age">
                        <input type="hidden" name="gender" id="pdf_gender">
                        <input type="hidden" name="height" id="pdf_height">
                        <input type="hidden" name="weight" id="pdf_weight">
                        <input type="hidden" name="z_score" id="pdf_zscore">
                        <input type="hidden" name="status" id="pdf_status">
                        <input type="hidden" name="ml_prediction" id="pdf_ml">

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

<script>
    document.addEventListener("DOMContentLoaded", () => {
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
                    z_score: calcData.who_calculation.haz_zscore,
                    status: calcData.who_calculation.stunting_status_who,
                    ml_prediction: predData.ml_prediction.stunting_status_ml,
                    recommendations: calcData.recommendations
                };

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
            const zScoreNode = document.getElementById('resultZScore');
            const mlNode = document.getElementById('resultML');
            const headerNode = document.getElementById('resultHeader');
            const iconNode = document.getElementById('resultIcon');
            const recommendationsNode = document.getElementById('resultRecommendations');

            // Set Data
            const zScore = (data.z_score !== undefined && data.z_score !== null) ? parseFloat(data.z_score).toFixed(2) : '0.00';
            const status = data.status || 'Tidak Diketahui';
            let mlPrediction = data.ml_prediction || 'Tidak Ada Data ML';

            // Rapikan teks 'model_not_trained' dari backend
            if (mlPrediction === 'model_not_trained') {
                mlPrediction = 'Model Belum Dilatih';
            } else if (mlPrediction === 'normal') {
                mlPrediction = 'Normal (Aman)';
            }

            zScoreNode.innerText = zScore;
            statusNode.innerText = status;
            mlNode.innerText = mlPrediction;

            // Tema Warna berdasarkan Status
            let bgColorClass = 'bg-gray-600';
            let iconClass = 'ph-info';

            const statusLower = status.toLowerCase();
            if (statusLower.includes('severely') || statusLower.includes('sangat pendek')) {
                bgColorClass = 'bg-red-600';
                iconClass = 'ph-warning';
            } else if (statusLower.includes('stunted') || statusLower.includes('pendek')) {
                bgColorClass = 'bg-orange-500';
                iconClass = 'ph-warning-circle';
            } else if (statusLower.includes('normal')) {
                bgColorClass = 'bg-emerald-500';
                iconClass = 'ph-check-circle';
            } else if (statusLower.includes('risk') || statusLower.includes('berisiko')) {
                bgColorClass = 'bg-yellow-500';
                iconClass = 'ph-shield-warning';
            } else if (statusLower.includes('tall') || statusLower.includes('tinggi')) {
                bgColorClass = 'bg-blue-500';
                iconClass = 'ph-arrow-up';
            }

            // Ganti warna header
            headerNode.className = `px-8 py-10 text-center text-white transition-colors duration-500 ${bgColorClass}`;
            iconNode.className = `ph ${iconClass} text-5xl`;

            // Rekomendasi (Dummy atau dari API jika ada)
            let recs = data.recommendations || [];
            if (recs.length === 0) {
                if (statusLower.includes('normal')) {
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

            // Isi nilai form PDF Tersembunyi
            document.getElementById('pdf_age').value = inputs.age_months;
            document.getElementById('pdf_gender').value = inputs.gender;
            document.getElementById('pdf_height').value = inputs.height;
            document.getElementById('pdf_weight').value = inputs.weight;
            document.getElementById('pdf_zscore').value = zScore;
            document.getElementById('pdf_status').value = status;
            document.getElementById('pdf_ml').value = mlPrediction;
        }
    });
</script>
@endsection
