@extends('layouts.app')

@section('content')
<div class="space-y-20 pb-16">
    <!-- Hero Header -->
    <div class="flex flex-col items-center justify-center text-center mt-10 animate-fade-in-up">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 mb-6 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full shadow-sm">
            <span>✨ Tim & Inovasi di Balik Layar</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
            Mengenal Lebih Dekat <br> <span class="text-blue-600">Si Anting & Pengembang</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-3xl leading-relaxed">
            <strong>Si Anting (Sistem Informasi Anak Stunting)</strong> adalah platform sistem pakar ganda yang memadukan akurasi standar antropometri medis <strong>WHO Z-Score (LMS)</strong> dengan kecerdasan buatan <strong>Machine Learning (Random Forest)</strong> untuk mencegah stunting di Indonesia.
        </p>
    </div>

    <!-- Tim Pengembang (Meet the Developers) -->
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Tim Pengembang Si Anting</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Kolaborasi lintas keilmuan antara rekayasa perangkat lunak, sains data artificial intelligence, dan ilmu kesehatan anak.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Developer 1: Lead AI & Backend -->
            <div class="bg-white rounded-3xl p-8 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-blue-200">
                            👨‍💻
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 font-semibold text-xs rounded-full">Lead Architect</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">Dika</h3>
                    <p class="text-sm font-medium text-blue-600 mb-4">Lead AI & Backend Engineer</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Pengembang utama yang merancang arsitektur backend FastAPI, permodelan Machine Learning Random Forest berakurasi tinggi, serta implementasi standar perhitungan WHO LMS Z-Score.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Python</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">FastAPI</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Scikit-Learn</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">MySQL</span>
                </div>
            </div>

            <!-- Developer 2: Frontend -->
            <div class="bg-white rounded-3xl p-8 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-purple-200">
                            🎨
                        </div>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 font-semibold text-xs rounded-full">Frontend Developer</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">Naufal Zanuwar Sudarto</h3>
                    <p class="text-sm font-medium text-purple-600 mb-4">Frontend Developer</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Pengembang yang merancang antarmuka ramah pengguna (Frontend Laravel 12 & Tailwind CSS), animasi interaktif, serta kalkulator gizi balita real-time dan tampilan laporan siap cetak.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Laravel 12</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Tailwind CSS 4</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Blade</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">JavaScript</span>
                </div>
            </div>

            <!-- Developer 3: Medical & Nutrition -->
            <div class="bg-white rounded-3xl p-8 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-emerald-200">
                            🩺
                        </div>
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-full">Medical Contributor</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">Luthfi Satria Wicaksana</h3>
                    <p class="text-sm font-medium text-emerald-600 mb-4">Kontributor Medis & Ahli Gizi</p>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Menyusun basis pengetahuan gizi klinis, kurva pertumbuhan antropometri WHO Z-Score, rekomendasi pangan harian (AI Meal Planner), serta materi edukasi pencegahan stunting.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">WHO LMS</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Kemenkes RI</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Gizi Balita</span>
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg">Posyandu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi & Misi -->
    <div class="max-w-5xl mx-auto bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 md:p-12 text-white shadow-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold tracking-wider uppercase mb-4">Visi Kami</span>
                <h3 class="text-3xl font-extrabold mb-4">Generasi Emas Bebas Stunting 2045</h3>
                <p class="text-blue-100 leading-relaxed">
                    Kami percaya bahwa setiap anak Indonesia berhak tumbuh maksimal dengan nutrisi dan pemantauan kesehatan terbaik. Si Anting hadir menjembatani teknologi tinggi dengan kebutuhan harian orang tua dan kader posyandu.
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 text-gray-900 flex items-center justify-center font-bold text-sm shrink-0 mt-0.5">✓</div>
                    <p class="text-sm"><strong>Deteksi Dini Akurat:</strong> Diagnosis ganda dengan presisi WHO dan Machine Learning.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 text-gray-900 flex items-center justify-center font-bold text-sm shrink-0 mt-0.5">✓</div>
                    <p class="text-sm"><strong>Rekomendasi Nutrisi Praktis:</strong> Panduan gizi personal dan jadwal evaluasi medis.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 text-gray-900 flex items-center justify-center font-bold text-sm shrink-0 mt-0.5">✓</div>
                    <p class="text-sm"><strong>Akses Mudah & Cepat:</strong> Dapat diakses dari rumah kapan pun dan di mana pun.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Box -->
    <div class="max-w-4xl mx-auto text-center bg-white rounded-3xl p-10 shadow-sm border border-gray-100">
        <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-4">Mulai Pantau Pertumbuhan Balita Anda</h3>
        <p class="text-gray-600 mb-8 max-w-xl mx-auto">
            Gunakan kalkulator deteksi gizi Si Anting secara gratis untuk mendapatkan analisis klinis lengkap serta rekomendasi makanan bergizi.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/detect" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                Coba Kalkulator Deteksi
            </a>
            <a href="/edukasi" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-8 py-3.5 rounded-full transition-all duration-200">
                Baca Edukasi Stunting
            </a>
        </div>
    </div>
</div>
@endsection
