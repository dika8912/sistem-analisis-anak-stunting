@extends('layouts.app')

@section('content')
<div class="space-y-20 pb-12">
    <!-- Header Edukasi -->
    <div class="text-center mt-12 animate-fade-in-up">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Mengenal Lebih Dalam Tentang <span class="text-blue-600">Stunting</span></h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">Pahami apa itu stunting, penyebab, dampak, dan cara mencegahnya untuk memastikan anak Anda tumbuh optimal dan sehat.</p>
    </div>

    <!-- Apa itu Stunting -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100 flex flex-col lg:flex-row items-center gap-12">
        <div class="flex-1 space-y-6">
            <h2 class="text-3xl font-bold text-gray-900">Apa Itu Stunting?</h2>
            <p class="text-gray-600 text-lg leading-relaxed">
                Stunting adalah kondisi gagal tumbuh pada anak balita (bayi di bawah 5 tahun) akibat dari kekurangan gizi kronis sehingga anak terlalu pendek untuk usianya. Kekurangan gizi terjadi sejak bayi dalam kandungan pada masa awal setelah bayi lahir. Akan tetapi, kondisi stunting baru nampak setelah bayi berusia 2 tahun.
            </p>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl">
                <p class="text-blue-800 font-medium">Stunting tidak hanya masalah tinggi badan, tetapi juga berkaitan erat dengan perkembangan otak yang kurang maksimal.</p>
            </div>
        </div>
        <div class="flex-1 w-full relative">
            <div class="absolute inset-0 bg-blue-100 rounded-3xl transform rotate-3 scale-105 -z-10 transition-transform hover:rotate-6"></div>
            <img src="{{ asset('Image/ibu anak.jpg') }}" alt="Ibu dan Anak" class="rounded-3xl w-full h-auto object-cover shadow-lg border-4 border-white relative z-0">
        </div>
    </div>

    <!-- Penyebab dan Dampak -->
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Penyebab -->
        <div class="bg-red-50 rounded-3xl p-8 border border-red-100 hover:shadow-lg transition-shadow">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Penyebab Stunting</h3>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">✖</span> <span>Kurang asupan gizi selama hamil.</span></li>
                <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">✖</span> <span>Kebutuhan gizi anak tidak tercukupi.</span></li>
                <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">✖</span> <span>Kurangnya akses air bersih dan sanitasi.</span></li>
                <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">✖</span> <span>Terbatasnya layanan kesehatan dan pembelajaran dini.</span></li>
            </ul>
        </div>
        
        <!-- Dampak -->
        <div class="bg-yellow-50 rounded-3xl p-8 border border-yellow-100 hover:shadow-lg transition-shadow">
             <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Dampak Stunting</h3>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start"><span class="text-yellow-600 mr-2 mt-1">⚠️</span> <span>Gangguan perkembangan otak dan kecerdasan.</span></li>
                <li class="flex items-start"><span class="text-yellow-600 mr-2 mt-1">⚠️</span> <span>Gangguan pertumbuhan fisik (pendek/kerdil).</span></li>
                <li class="flex items-start"><span class="text-yellow-600 mr-2 mt-1">⚠️</span> <span>Rentan terhadap penyakit infeksi.</span></li>
                <li class="flex items-start"><span class="text-yellow-600 mr-2 mt-1">⚠️</span> <span>Risiko penyakit kronis (diabetes, obesitas) di masa dewasa.</span></li>
            </ul>
        </div>
    </div>

    <!-- Cara Pencegahan -->
    <div class="flex flex-col lg:flex-row gap-12 items-start bg-blue-50/50 -mx-6 p-6 md:p-12 rounded-[3rem] border border-blue-100">
        <!-- Sisi Kiri (Teks & Gambar) -->
        <div class="lg:w-1/3 space-y-8 sticky top-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Cara Mencegah Stunting</h2>
                <p class="text-gray-600 text-lg">Pencegahan stunting harus dilakukan sedini mungkin. Berikut adalah langkah-langkah penting yang bisa Anda terapkan.</p>
            </div>
            <div class="relative group">
                <div class="absolute inset-0 bg-blue-200 rounded-3xl transform -rotate-3 scale-105 -z-10 transition-transform group-hover:-rotate-6"></div>
                <img src="{{ asset('Image/Cegah Stunting.png') }}" alt="Ilustrasi Cegah Stunting" class="w-full rounded-3xl shadow-xl border-4 border-white relative z-0 transition-transform duration-300">
            </div>
        </div>
        
        <!-- Sisi Kanan (Kartu) -->
        <div class="lg:w-2/3 grid sm:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">🤰</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Masa Kehamilan</h4>
                <p class="text-gray-600 text-sm">Penuhi kebutuhan gizi ibu hamil, konsumsi tablet tambah darah, dan periksa kehamilan rutin minimal 6 kali.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">🤱</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Bayi 0-6 Bulan</h4>
                <p class="text-gray-600 text-sm">Berikan ASI Eksklusif hingga bayi berusia 6 bulan tanpa tambahan makanan atau minuman lain.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">🥣</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Bayi >6 Bulan</h4>
                <p class="text-gray-600 text-sm">Berikan MPASI yang kaya protein hewani dan terus lanjutkan pemberian ASI hingga anak berusia 2 tahun.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">💧</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Sanitasi Lingkungan</h4>
                <p class="text-gray-600 text-sm">Jaga kebersihan lingkungan, pastikan akses air bersih untuk kebutuhan sehari-hari, dan cuci tangan pakai sabun.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">🏥</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Pantau Pertumbuhan</h4>
                <p class="text-gray-600 text-sm">Rutin bawa anak ke Posyandu setiap bulan untuk memantau berat dan tinggi badannya secara teratur.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="text-4xl mb-4 bg-blue-50 w-16 h-16 rounded-2xl flex items-center justify-center">💉</div>
                <h4 class="font-bold text-lg mb-2 text-gray-900">Imunisasi Lengkap</h4>
                <p class="text-gray-600 text-sm">Pastikan anak mendapatkan imunisasi dasar lengkap agar kekebalan tubuhnya terjaga dari berbagai penyakit.</p>
            </div>
        </div>
    </div>
</div>
@endsection
