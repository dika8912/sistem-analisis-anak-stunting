@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden flex items-center justify-between">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold mb-2">Pusat Edukasi Si Anting</h1>
            <p class="text-emerald-100 font-medium max-w-2xl">Kumpulan artikel, panduan gizi, dan informasi penting seputar tumbuh kembang anak untuk mencegah stunting sejak dini.</p>
        </div>
        <div class="relative z-10 hidden md:flex items-center justify-center h-20 w-20 bg-white/20 rounded-full backdrop-blur-sm border border-white/30">
            <i class="ph ph-book-open-text text-4xl text-white"></i>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="flex flex-col items-center justify-center py-16 text-center">
        <i class="ph ph-spinner-gap text-5xl text-emerald-500 animate-spin mb-4"></i>
        <p class="text-gray-500 font-medium">Memuat artikel edukasi terbaru...</p>
    </div>

    <!-- Grid Artikel -->
    <div id="articlesGrid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Disisipkan via JS -->
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const loadingState = document.getElementById('loadingState');
    const articlesGrid = document.getElementById('articlesGrid');

    try {
        const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
        
        let data = [];
        if (typeof window.apiFetch === 'function') {
            data = await window.apiFetch(`/api/educations`);
        } else {
            const response = await fetch(`${API_BASE}/api/educations`);
            if (!response.ok) throw new Error('Gagal memuat artikel');
            data = await response.json();
        }

        renderArticles(data);
    } catch (error) {
        console.warn("Menggunakan dummy data untuk edukasi.", error);
        
        // Fallback Dummy Data
        setTimeout(() => {
            const dummyData = [
                {
                    id: 1,
                    title: "Pentingnya 1000 Hari Pertama Kehidupan",
                    summary: "Masa 1000 Hari Pertama Kehidupan (HPK) merupakan periode emas untuk mencegah stunting. Ketahui asupan gizi yang tepat.",
                    author: "Dr. Siti Rahayu",
                    created_at: "2026-05-10"
                },
                {
                    id: 2,
                    title: "Resep MPASI Kaya Protein Hewani",
                    summary: "Protein hewani sangat penting untuk pertumbuhan linear anak. Berikut 5 resep MPASI mudah dan murah.",
                    author: "Ahli Gizi Budi",
                    created_at: "2026-05-15"
                },
                {
                    id: 3,
                    title: "Mengenal Ciri-Ciri Stunting Pada Balita",
                    summary: "Stunting bukan hanya soal tubuh pendek. Pahami tanda-tanda stunting yang sering terlewat oleh orang tua.",
                    author: "Kemenkes RI",
                    created_at: "2026-05-20"
                }
            ];
            renderArticles(dummyData);
        }, 800);
    }

    function renderArticles(data) {
        loadingState.classList.add('hidden');
        loadingState.classList.remove('flex');
        articlesGrid.classList.remove('hidden');

        if (data.length === 0) {
            articlesGrid.classList.remove('grid', 'grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3');
            articlesGrid.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 text-center w-full bg-white rounded-3xl shadow-sm border border-gray-100">
                    <i class="ph ph-article text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 font-medium">Belum ada artikel edukasi yang dipublikasikan.</p>
                </div>
            `;
            return;
        }

        const html = data.map(article => `
            <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col group h-full transform hover:-translate-y-1">
                <div class="h-48 bg-emerald-50 flex items-center justify-center relative overflow-hidden">
                    <img src="${article.image || '/Image/anakdanibu.jpg'}" alt="${article.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-emerald-700 text-xs font-bold rounded-full shadow-sm">
                            Artikel Gizi
                        </span>
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <div class="text-xs text-gray-500 mb-2 flex items-center">
                        <i class="ph ph-calendar-blank mr-1"></i> ${new Date(article.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}
                        <span class="mx-2">•</span>
                        <i class="ph ph-user mr-1"></i> ${article.author || 'Admin'}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors line-clamp-2">${article.title}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">${article.summary}</p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50">
                        <a href="/educations/${article.id}" class="inline-flex items-center text-emerald-600 font-bold hover:text-emerald-800 transition-colors">
                            Baca Selengkapnya <i class="ph ph-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        `).join('');

        articlesGrid.innerHTML = html;
    }
});
</script>
@endsection
