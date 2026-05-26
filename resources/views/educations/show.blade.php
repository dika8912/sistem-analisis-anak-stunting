@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fade-in-up">
    
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="/educations" class="inline-flex items-center text-gray-500 hover:text-emerald-600 font-medium transition-colors">
            <i class="ph ph-arrow-left mr-2"></i> Kembali ke Daftar Edukasi
        </a>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-[2rem] shadow-sm border border-gray-100">
        <i class="ph ph-spinner-gap text-5xl text-emerald-500 animate-spin mb-4"></i>
        <p class="text-gray-500 font-medium">Memuat artikel...</p>
    </div>

    <!-- Artikel Content -->
    <div id="articleContent" class="hidden bg-white rounded-[2rem] shadow-lg border border-gray-100 overflow-hidden">
        
        <!-- Header Image / Hero -->
        <div class="h-64 bg-emerald-50 flex items-center justify-center relative">
            <i class="ph ph-image text-6xl text-emerald-200"></i>
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/30"></div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Meta Info -->
            <div class="flex items-center text-sm text-gray-500 mb-6 gap-4">
                <div class="flex items-center">
                    <i class="ph ph-calendar-blank mr-2 text-emerald-600"></i> 
                    <span id="artDate">Tanggal</span>
                </div>
                <div class="flex items-center">
                    <i class="ph ph-user mr-2 text-emerald-600"></i> 
                    <span id="artAuthor">Penulis</span>
                </div>
            </div>

            <!-- Title -->
            <h1 id="artTitle" class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 leading-tight">Judul Artikel</h1>

            <!-- Body -->
            <div id="artBody" class="prose prose-lg prose-emerald max-w-none text-gray-700 leading-relaxed space-y-6">
                <!-- Content injected here -->
            </div>
            
            <hr class="my-10 border-gray-100">
            
            <!-- Share / Actions -->
            <div class="flex justify-between items-center">
                <div class="text-sm font-semibold text-gray-500">
                    Bagikan artikel ini:
                </div>
                <div class="flex gap-2">
                    <button class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                        <i class="ph ph-facebook-logo text-xl"></i>
                    </button>
                    <button class="h-10 w-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center hover:bg-sky-100 transition-colors">
                        <i class="ph ph-twitter-logo text-xl"></i>
                    </button>
                    <button class="h-10 w-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100 transition-colors">
                        <i class="ph ph-whatsapp-logo text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const articleId = "{{ $id }}";
    const loadingState = document.getElementById('loadingState');
    const articleContent = document.getElementById('articleContent');

    try {
        const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
        
        let data = null;
        if (typeof window.apiFetch === 'function') {
            data = await window.apiFetch(`/api/educations/${articleId}`);
        } else {
            const response = await fetch(`${API_BASE}/api/educations/${articleId}`);
            if (!response.ok) throw new Error('Gagal memuat artikel detail');
            data = await response.json();
        }

        renderArticle(data);
    } catch (error) {
        console.warn("Menggunakan dummy data untuk detail edukasi.", error);
        
        // Fallback Dummy Data
        setTimeout(() => {
            const dummyData = {
                id: articleId,
                title: "Pentingnya 1000 Hari Pertama Kehidupan (HPK) untuk Cegah Stunting",
                content: `
                    <p>Masa <strong>1000 Hari Pertama Kehidupan (HPK)</strong> sering disebut sebagai periode emas <em>(golden period)</em>. Fase ini dimulai sejak anak berada dalam kandungan hingga berusia dua tahun.</p>
                    
                    <h3>Mengapa 1000 HPK Sangat Penting?</h3>
                    <p>Pada periode ini, otak anak berkembang sangat pesat, sistem kekebalan tubuh mulai terbentuk, dan pertumbuhan fisik terjadi dengan sangat cepat. Kegagalan memberikan asupan gizi yang optimal pada fase ini dapat menyebabkan kerusakan yang tidak dapat diperbaiki <em>(irreversible)</em>, salah satunya adalah <strong>stunting</strong>.</p>
                    
                    <h3>Tips Memaksimalkan 1000 HPK:</h3>
                    <ul>
                        <li><strong>Masa Kehamilan:</strong> Ibu hamil wajib mengonsumsi makanan bergizi seimbang, rutin minum tablet tambah darah (TTD), dan memeriksakan kehamilan minimal 6 kali.</li>
                        <li><strong>0-6 Bulan:</strong> Berikan ASI Eksklusif. Bayi tidak membutuhkan makanan atau minuman tambahan selain ASI pada periode ini.</li>
                        <li><strong>6-24 Bulan:</strong> Mulai perkenalkan Makanan Pendamping ASI (MPASI) yang kaya akan <strong>protein hewani</strong> seperti telur, ikan, daging ayam, dan hati sapi. Tetap lanjutkan pemberian ASI hingga anak berusia 2 tahun.</li>
                    </ul>
                    
                    <p>Mari bersama-sama kita jaga tumbuh kembang anak Indonesia. Cegah stunting itu penting!</p>
                `,
                author: "Kemenkes RI",
                created_at: "2026-05-10"
            };
            renderArticle(dummyData);
        }, 800);
    }

    function renderArticle(data) {
        loadingState.classList.add('hidden');
        loadingState.classList.remove('flex');
        articleContent.classList.remove('hidden');

        document.title = `${data.title} - Edukasi Si Anting`;
        document.getElementById('artTitle').innerText = data.title;
        document.getElementById('artAuthor').innerText = data.author || 'Admin';
        document.getElementById('artDate').innerText = new Date(data.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        
        // Jika data dari backend tidak mengandung HTML tag, ubah enter menjadi <br>
        let contentHtml = data.content;
        if (!contentHtml.includes('<p>') && !contentHtml.includes('<br>')) {
            contentHtml = `<p>${contentHtml.replace(/\n/g, '<br>')}</p>`;
        }
        document.getElementById('artBody').innerHTML = contentHtml;
    }
});
</script>
@endsection
