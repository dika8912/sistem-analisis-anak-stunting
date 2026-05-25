@extends('layouts.app')

@section('content')
<div class="space-y-24 pb-12">
    <!-- Hero Section -->
    <div class="flex flex-col items-center justify-center text-center mt-12 animate-fade-in-up">
        <div class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full">
            Sistem Pakar Stunting Berbasis AI
        </div>
        <h1 class="text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
            Pantau Tumbuh Kembang <br> <span class="text-blue-600">Anak Anda</span>
        </h1>
        <p class="text-xl text-gray-600 mb-10 max-w-2xl">
            Sistem pakar diagnosa dini stunting menggunakan kecerdasan buatan.
            Ketahui status gizi balita Anda hanya dalam beberapa langkah mudah.
        </p>

        <div class="flex space-x-4">
            <a href="/diagnosa"
                class="bg-blue-600 text-white px-8 py-3 rounded-full font-semibold shadow-lg shadow-blue-200 hover:bg-blue-800 hover:scale-105 transition-all duration-300">
                Mulai Diagnosa
            </a>
            <a href="/edukasi"
                class="bg-white text-blue-600 border border-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-blue-50 hover:scale-105 transition-all duration-300">
                Pelajari Stunting
            </a>
        </div>
    </div>

    <!-- Features S -->
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Kenapa Memilih Si Anting?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Diagnosa Cerdas</h3>
                <p class="text-gray-600 leading-relaxed">Menggunakan algoritma sistem pakar untuk menganalisa status gizi anak secara akurat dan real-time.</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Edukasi Terpercaya</h3>
                <p class="text-gray-600 leading-relaxed">Dapatkan informasi lengkap dan tips kesehatan seputar pencegahan dan penanganan stunting pada anak.</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Mudah Digunakan</h3>
                <p class="text-gray-600 leading-relaxed">Akses layanan kapan saja dan di mana saja langsung dari smartphone atau komputer Anda tanpa repot.</p>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 -mx-6 p-12 rounded-[3rem] mt-12 border border-blue-100">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-16">Cara Kerja Si Anting</h2>
        <div class="flex flex-col md:flex-row justify-center items-start md:items-center space-y-12 md:space-y-0 md:space-x-8 max-w-4xl mx-auto relative">
            
            <!-- penghubung background -->
            <div class="hidden md:block absolute top-1/4 left-1/2 -translate-x-1/2 w-2/3 border-t-2 border-dashed border-blue-300 -z-0"></div>

            <div class="text-center flex-1 relative z-10 bg-white md:bg-transparent p-6 md:p-0 rounded-2xl md:rounded-none w-full shadow-sm md:shadow-none border md:border-none border-blue-100">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-xl shadow-blue-200">1</div>
                <h4 class="font-bold text-xl mb-3 text-gray-900">Isi Data Balita</h4>
                <p class="text-base text-gray-600">Masukkan data umur, berat badan, dan tinggi badan anak Anda.</p>
            </div>
            
            <div class="text-center flex-1 relative z-10 bg-white md:bg-transparent p-6 md:p-0 rounded-2xl md:rounded-none w-full shadow-sm md:shadow-none border md:border-none border-blue-100">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-xl shadow-blue-200">2</div>
                <h4 class="font-bold text-xl mb-3 text-gray-900">Sistem Menganalisa</h4>
                <p class="text-base text-gray-600">Kecerdasan buatan akan memproses data sesuai standar gizi.</p>
            </div>
            
            <div class="text-center flex-1 relative z-10 bg-white md:bg-transparent p-6 md:p-0 rounded-2xl md:rounded-none w-full shadow-sm md:shadow-none border md:border-none border-blue-100">
                <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-xl shadow-blue-200">3</div>
                <h4 class="font-bold text-xl mb-3 text-gray-900">Terima Hasil</h4>
                <p class="text-base text-gray-600">Dapatkan status gizi anak dan rekomendasi penanganan instan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
