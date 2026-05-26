@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden flex items-center justify-between">
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold mb-2">Manajemen Edukasi</h1>
            <p class="text-gray-300 font-medium max-w-2xl">Kelola artikel edukasi publik. Tambahkan materi baru, perbarui artikel yang ada, atau hapus konten yang sudah tidak relevan.</p>
        </div>
        <div class="relative z-10 hidden md:flex items-center justify-center h-20 w-20 bg-white/10 rounded-full backdrop-blur-sm border border-white/20">
            <i class="ph ph-books text-4xl text-white"></i>
        </div>
    </div>

    <!-- Action Bar & Table -->
    <div class="bg-white rounded-[2rem] shadow-lg border border-gray-100 overflow-hidden">
        
        <div class="p-6 md:p-8 flex justify-between items-center border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xl font-bold text-gray-800">Daftar Artikel</h3>
            <button onclick="openFormModal()" class="flex items-center bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md transition-all duration-300">
                <i class="ph ph-plus-circle mr-2 text-xl"></i> Tambah Artikel
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/2">Judul Artikel</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Penulis</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="eduTableBody" class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="ph ph-spinner-gap text-4xl animate-spin text-blue-500 mb-2"></i>
                            <p>Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Tambah Artikel Baru</h3>
            <button onclick="closeFormModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="ph ph-x-circle text-3xl"></i>
            </button>
        </div>
        
        <form id="eduForm" class="p-6 space-y-6">
            <input type="hidden" id="eduId">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel</label>
                <input type="text" id="eduTitle" required class="block w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" placeholder="Masukkan judul menarik...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ringkasan (Summary)</label>
                <textarea id="eduSummary" required rows="2" class="block w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" placeholder="Tuliskan 1-2 kalimat ringkasan..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi Konten Utama</label>
                <textarea id="eduContent" required rows="6" class="block w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all" placeholder="Isi detail edukasi gizi..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeFormModal()" class="px-6 py-3 rounded-xl text-gray-700 font-bold bg-gray-100 hover:bg-gray-200 transition-colors">Batal</button>
                <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold bg-blue-600 hover:bg-blue-700 transition-colors shadow-md">Simpan Artikel</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentData = [];

document.addEventListener("DOMContentLoaded", () => {
    fetchData();

    document.getElementById('eduForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        saveData();
    });
});

async function fetchData() {
    const tbody = document.getElementById('eduTableBody');
    try {
        const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
        let data = [];
        
        if (typeof window.apiFetch === 'function') {
            data = await window.apiFetch(`/api/educations`);
        } else {
            const response = await fetch(`${API_BASE}/api/educations`);
            if (!response.ok) throw new Error('API Error');
            data = await response.json();
        }
        currentData = data;
        renderTable(data);
    } catch (e) {
        console.warn("Fallback to dummy data", e);
        setTimeout(() => {
            currentData = [
                { id: 1, title: "Pentingnya 1000 HPK", summary: "Periode emas mencegah stunting.", content: "...", author: "Kemenkes", created_at: "2026-05-10" },
                { id: 2, title: "Resep MPASI Kaya Protein", summary: "Protein hewani sangat penting.", content: "...", author: "Ahli Gizi Budi", created_at: "2026-05-15" }
            ];
            renderTable(currentData);
        }, 600);
    }
}

function renderTable(data) {
    const tbody = document.getElementById('eduTableBody');
    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada artikel yang ditambahkan.</td></tr>`;
        return;
    }
    
    tbody.innerHTML = data.map(item => `
        <tr class="hover:bg-gray-50 transition-colors group">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900 line-clamp-1">${item.title}</div>
                <div class="text-xs text-gray-500 line-clamp-1">${item.summary}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">${item.author || 'Admin'}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
            <td class="px-6 py-4 text-right space-x-2">
                <button onclick="editForm(${item.id})" class="inline-flex items-center text-orange-600 bg-orange-50 p-2 rounded-lg hover:bg-orange-100 transition-colors" title="Edit">
                    <i class="ph ph-pencil-simple text-lg"></i>
                </button>
                <button onclick="deleteData(${item.id})" class="inline-flex items-center text-red-600 bg-red-50 p-2 rounded-lg hover:bg-red-100 transition-colors" title="Hapus">
                    <i class="ph ph-trash text-lg"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function openFormModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Artikel Baru';
    document.getElementById('eduForm').reset();
    document.getElementById('eduId').value = '';
    document.getElementById('formModal').classList.remove('hidden');
}

function closeFormModal() {
    document.getElementById('formModal').classList.add('hidden');
}

function editForm(id) {
    const item = currentData.find(d => d.id == id);
    if (item) {
        document.getElementById('modalTitle').innerText = 'Edit Artikel';
        document.getElementById('eduId').value = item.id;
        document.getElementById('eduTitle').value = item.title;
        document.getElementById('eduSummary').value = item.summary;
        document.getElementById('eduContent').value = item.content || '';
        document.getElementById('formModal').classList.remove('hidden');
    }
}

async function saveData() {
    const id = document.getElementById('eduId').value;
    const title = document.getElementById('eduTitle').value;
    const summary = document.getElementById('eduSummary').value;
    const content = document.getElementById('eduContent').value;
    const token = localStorage.getItem('access_token');
    
    const payload = { title, summary, content };
    const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${API_BASE}/api/admin/educations/${id}` : `${API_BASE}/api/admin/educations`;

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) throw new Error('Failed to save');
        
        Swal.fire('Berhasil!', 'Artikel telah disimpan.', 'success');
        closeFormModal();
        fetchData();
    } catch (e) {
        console.warn(e);
        // Dummy Save
        Swal.fire('Berhasil (Dummy)!', 'Simulasi penyimpanan berhasil.', 'success');
        closeFormModal();
    }
}

async function deleteData(id) {
    const result = await Swal.fire({
        title: 'Hapus Artikel?',
        text: "Artikel yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    });

    if (result.isConfirmed) {
        const token = localStorage.getItem('access_token');
        const API_BASE = '{{ env("VITE_API_URL", "http://localhost:5601") }}';
        
        try {
            const response = await fetch(`${API_BASE}/api/admin/educations/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to delete');
            Swal.fire('Terhapus!', 'Artikel berhasil dihapus.', 'success');
            fetchData();
        } catch (e) {
            console.warn(e);
            Swal.fire('Terhapus (Dummy)!', 'Simulasi penghapusan berhasil.', 'success');
        }
    }
}
</script>
@endsection
