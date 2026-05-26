@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight" id="childName">Memuat...</h1>
            <p class="text-gray-500 mt-1">Detail Profil dan Riwayat Pertumbuhan Anak</p>
        </div>
        <div id="adminActionContainer" class="hidden">
            <a href="/measurements/create?child_id={{ $id }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-md text-sm font-bold text-white hover:from-emerald-600 hover:to-teal-700 focus:outline-none transform transition-all hover:-translate-y-0.5">
                <i class="ph ph-plus-circle text-lg mr-2"></i> Tambah Pengukuran
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Info Profil & Status Terakhir -->
        <div class="space-y-8">
            <!-- Kartu Profil -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 bg-blue-50 border-b border-blue-100 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-500 text-white flex items-center justify-center text-2xl font-bold shadow-inner" id="childInitial">
                        -
                    </div>
                    <div>
                        <p class="text-sm font-bold text-blue-600 uppercase tracking-wide">Profil Anak</p>
                        <h3 class="text-xl font-bold text-gray-900" id="childNameCard">-</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Jenis Kelamin</span>
                        <span class="font-semibold text-gray-800" id="childGender">-</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Tanggal Lahir</span>
                        <span class="font-semibold text-gray-800" id="childDob">-</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-gray-500 text-sm">Umur (Bulan)</span>
                        <span class="font-semibold text-gray-800" id="childAge">-</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-500 text-sm">Nama Orang Tua</span>
                        <span class="font-semibold text-gray-800" id="guardianName">-</span>
                    </div>
                </div>
            </div>

            <!-- Kartu Status Gizi Terakhir -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                    <i class="ph ph-heartbeat text-8xl text-emerald-500"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Status Gizi Terakhir</h3>
                    
                    <div class="space-y-6" id="latestMeasurementContainer">
                        <!-- Akan diisi dengan JS -->
                        <div class="animate-pulse flex space-x-4">
                            <div class="flex-1 space-y-4 py-1">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Chart & Histori -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Grafik Pertumbuhan -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Grafik Pertumbuhan</h3>
                    <select id="chartType" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 outline-none">
                        <option value="height">Tinggi Badan (cm)</option>
                        <option value="weight">Berat Badan (kg)</option>
                    </select>
                </div>
                
                <div class="w-full h-80">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <!-- Tabel Histori Pengukuran -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Pengukuran</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Umur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tinggi (cm)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Berat (kg)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Stunting</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data baris tabel di-generate JS -->
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Memuat riwayat pengukuran...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const childId = {{ $id }};
        const role = localStorage.getItem('user_role');
        
        // Hanya tampilkan tombol tambah jika admin
        if (role === 'admin') {
            document.getElementById('adminActionContainer').classList.remove('hidden');
        }

        let childData = null;
        let historyData = [];
        let growthChartObj = null;

        const fetchData = async () => {
            try {
                // Fetch Data Anak
                childData = await window.apiFetch(`/api/children/${childId}`);
                
                // Update UI Profil
                document.getElementById('childName').textContent = childData.name;
                document.getElementById('childNameCard').textContent = childData.name;
                document.getElementById('childInitial').textContent = childData.name.charAt(0).toUpperCase();
                document.getElementById('childGender').textContent = childData.gender === 'L' ? 'Laki-laki' : 'Perempuan';
                document.getElementById('childDob').textContent = new Date(childData.birth_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                
                // Menghitung umur (bulan) secara kasar berdasarkan dob dan hari ini (opsional jika API tidak menyediakan)
                const birth = new Date(childData.birth_date);
                const now = new Date();
                let months = (now.getFullYear() - birth.getFullYear()) * 12;
                months -= birth.getMonth();
                months += now.getMonth();
                document.getElementById('childAge').textContent = months <= 0 ? 0 : months;
                
                if (childData.guardian) {
                    document.getElementById('guardianName').textContent = childData.guardian.name || '-';
                }

                // Fetch History
                historyData = await window.apiFetch(`/api/children/${childId}/history`);
                
                renderLatestMeasurement();
                renderHistoryTable();
                renderChart();

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Gagal memuat data anak', 'error');
            }
        };

        const getStatusColor = (status) => {
            status = (status || '').toLowerCase();
            if (status.includes('severely') || status.includes('sangat pendek')) return 'bg-red-100 text-red-800 border-red-200';
            if (status.includes('stunted') || status.includes('pendek')) return 'bg-orange-100 text-orange-800 border-orange-200';
            if (status.includes('normal')) return 'bg-emerald-100 text-emerald-800 border-emerald-200';
            if (status.includes('risk') || status.includes('berisiko')) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
            if (status.includes('overweight')) return 'bg-purple-100 text-purple-800 border-purple-200';
            if (status.includes('tall') || status.includes('tinggi')) return 'bg-blue-100 text-blue-800 border-blue-200';
            return 'bg-gray-100 text-gray-800 border-gray-200'; // default
        };

        const renderLatestMeasurement = () => {
            const container = document.getElementById('latestMeasurementContainer');
            if (!historyData || historyData.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 italic">Belum ada riwayat pengukuran.</p>';
                return;
            }

            // Urutkan desc berdasarkan tanggal
            const sortedHistory = [...historyData].sort((a, b) => new Date(b.measurement_date) - new Date(a.measurement_date));
            const latest = sortedHistory[0];

            const statusClass = getStatusColor(latest.stunting_status);

            container.innerHTML = `
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status Z-Score (Tinggi/Umur)</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${statusClass}">
                            ${latest.stunting_status || 'Belum dianalisa'}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1">Z-Score</p>
                        <p class="text-xl font-black text-gray-800">${latest.z_score ? latest.z_score.toFixed(2) : '-'}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Tinggi Badan</p>
                            <p class="font-bold text-gray-800">${latest.height} <span class="text-xs text-gray-500 font-normal">cm</span></p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Berat Badan</p>
                            <p class="font-bold text-gray-800">${latest.weight} <span class="text-xs text-gray-500 font-normal">kg</span></p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-500"><i class="ph ph-calendar text-gray-400 mr-1"></i> ${new Date(latest.measurement_date).toLocaleDateString('id-ID')}</span>
                    <span class="text-xs text-gray-500">Umur: ${latest.age_months} bulan</span>
                </div>
            `;
        };

        const renderHistoryTable = () => {
            const tbody = document.getElementById('historyTableBody');
            if (!historyData || historyData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada riwayat pengukuran.</td></tr>';
                return;
            }

            const sortedHistory = [...historyData].sort((a, b) => new Date(b.measurement_date) - new Date(a.measurement_date));
            
            tbody.innerHTML = sortedHistory.map(item => `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        ${new Date(item.measurement_date).toLocaleDateString('id-ID')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        ${item.age_months} bln
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${item.height}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${item.weight}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${getStatusColor(item.stunting_status)}">
                            ${item.stunting_status || '-'}
                        </span>
                    </td>
                </tr>
            `).join('');
        };

        const renderChart = () => {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const type = document.getElementById('chartType').value; // 'height' or 'weight'
            
            if (growthChartObj) {
                growthChartObj.destroy();
            }

            if (!historyData || historyData.length === 0) return;

            // Sort asc for chart (timeline)
            const sortedHistory = [...historyData].sort((a, b) => new Date(a.measurement_date) - new Date(b.measurement_date));

            const labels = sortedHistory.map(h => `${h.age_months} bln`);
            const dataPoints = sortedHistory.map(h => type === 'height' ? h.height : h.weight);
            
            const isHeight = type === 'height';
            const labelText = isHeight ? 'Tinggi Badan (cm)' : 'Berat Badan (kg)';
            const borderColor = isHeight ? '#3B82F6' : '#10B981'; // Blue for height, Emerald for weight
            const bgColor = isHeight ? 'rgba(59, 130, 246, 0.1)' : 'rgba(16, 185, 129, 0.1)';

            growthChartObj = new window.Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: dataPoints,
                        borderColor: borderColor,
                        backgroundColor: bgColor,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: borderColor,
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4 // curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1F2937',
                            padding: 12,
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, family: "'Inter', sans-serif" },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} ${isHeight ? 'cm' : 'kg'}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: { family: "'Inter', sans-serif" },
                                color: '#6B7280'
                            }
                        },
                        y: {
                            grid: {
                                color: '#F3F4F6',
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: "'Inter', sans-serif" },
                                color: '#6B7280',
                                padding: 10
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        };

        // Event listener for chart toggle
        document.getElementById('chartType').addEventListener('change', renderChart);

        fetchData();
    });
</script>
@endsection
