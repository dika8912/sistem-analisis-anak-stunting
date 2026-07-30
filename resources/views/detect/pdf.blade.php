<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Deteksi Si Anting</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1F2937;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #1E3A8A;
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            color: #6B7280;
            font-size: 13px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1E3A8A;
            background-color: #EFF6FF;
            padding: 8px 12px;
            border-left: 4px solid #2563EB;
            margin: 20px 0 10px 0;
        }
        .info-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 38%;
            font-weight: bold;
            color: #475569;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 12px;
        }
        .summary-table th {
            background-color: #F1F5F9;
            color: #334155;
            padding: 8px;
            text-align: left;
            border: 1px solid #CBD5E1;
            font-weight: bold;
        }
        .summary-table td {
            padding: 8px;
            border: 1px solid #CBD5E1;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            background-color: #EFF6FF;
            color: #1D4ED8;
            font-weight: bold;
            border-radius: 4px;
            font-size: 15px;
            border: 1px solid #BFDBFE;
            margin-bottom: 10px;
        }
        .recommendation-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
        }
        .recommendation-box ul {
            margin: 8px 0 0 0;
            padding-left: 20px;
            color: #334155;
        }
        .recommendation-box li {
            margin-bottom: 6px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
            margin-top: 35px;
            border-top: 1px solid #E2E8F0;
            padding-top: 15px;
        }
        .disclaimer {
            font-size: 10px;
            color: #EF4444;
            font-style: italic;
            margin-top: 20px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Si Anting - Laporan Deteksi Tumbuh Kembang</h1>
        <p>Sistem Analisa Stunting & Status Gizi Balita berbasis Kecerdasan Buatan (AI)</p>
        <p style="font-size: 11px; margin-top: 4px; color: #64748B;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>

    @php
        $height_m = $height / 100;
        $bmi = ($height_m > 0) ? $weight / ($height_m * $height_m) : 0;

        $whoTB_table = [50.0, 54.7, 58.4, 61.4, 63.9, 66.2, 67.6, 69.2, 70.6, 72.0, 73.3, 74.5, 75.7, 76.9, 78.0, 79.1, 80.2, 81.2, 82.3, 83.2, 84.2, 85.1, 86.0, 86.9, 87.8, 88.7, 89.6, 90.4, 91.2, 92.0, 92.8, 93.6, 94.4, 95.2, 96.0, 96.7, 97.5, 98.2, 98.9, 99.6, 100.3, 101.0, 101.7, 102.4, 103.1, 103.8, 104.5, 105.1, 105.8, 106.4, 107.1, 107.7, 108.4, 109.0, 109.6, 110.2, 110.8, 111.4, 112.0, 112.5, 113.1];
        $age_idx = min(60, max(0, (int)$age_months));
        $normTB = $whoTB_table[$age_idx] ?? 75.0;
        $min2TB = number_format($normTB * 0.93, 1);
        $plus2TB = number_format($normTB * 1.08, 1);

        $normBB = 3.3 + max(0, ($height - 50)) * 0.25;
        $min2BB = number_format($normBB * 0.85, 1);
        $plus2BB = number_format($normBB * 1.18, 1);
    @endphp

    <div class="section-title">1. Data Pengukuran Balita</div>
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td>Nama Balita</td>
                <td>: <strong>{{ !empty($child_name) ? $child_name : 'Anak / Balita' }}</strong></td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>: <strong>{{ $age_months }} Bulan</strong></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <strong>{{ $gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong></td>
            </tr>
            <tr>
                <td>Tinggi / Panjang Badan</td>
                <td>: <strong>{{ $height }} cm</strong></td>
            </tr>
            <tr>
                <td>Berat Badan</td>
                <td>: <strong>{{ $weight }} kg</strong></td>
            </tr>
            <tr>
                <td>Indeks Massa Tubuh (IMT / BMI)</td>
                <td>: <strong>{{ number_format($bmi, 2) }} kg/m²</strong></td>
            </tr>
        </table>
    </div>

    <div class="section-title">2. Klasifikasi & Standar Pertumbuhan WHO (-2 SD s/d +2 SD)</div>
    <div style="margin-bottom: 10px;">
        <span class="status-badge">{{ $status }}</span>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Parameter Medis</th>
                <th>Titik Ukur Anak</th>
                <th>Batas Bawah (-2 SD)</th>
                <th>Rata-Rata (0 SD)</th>
                <th>Batas Atas (+2 SD)</th>
                <th>Prediksi AI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tinggi Badan (TB/U)</strong></td>
                <td style="color: #2563EB; font-weight: bold;">{{ $height }} cm</td>
                <td style="color: #DC2626;">{{ $min2TB }} cm</td>
                <td style="color: #10B981; font-weight: bold;">~ {{ number_format($normTB, 1) }} cm</td>
                <td style="color: #3B82F6;">{{ $plus2TB }} cm</td>
                <td><strong style="color: #1E3A8A;">{{ !empty($stunting_prediction) ? $stunting_prediction : '-' }}</strong></td>
            </tr>
            <tr>
                <td><strong>Berat Badan (BB/TB)</strong></td>
                <td style="color: #9333EA; font-weight: bold;">{{ $weight }} kg</td>
                <td style="color: #DC2626;">{{ $min2BB }} kg</td>
                <td style="color: #10B981; font-weight: bold;">~ {{ number_format($normBB, 1) }} kg</td>
                <td style="color: #F59E0B;">{{ $plus2BB }} kg</td>
                <td><strong style="color: #6B21A8;">{{ !empty($gizi_prediction) ? $gizi_prediction : (!empty($ml_prediction) ? $ml_prediction : '-') }}</strong></td>
            </tr>
    </table>

    <!-- Keterangan Zona Antropometri WHO -->
    <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 10px 12px; margin: 10px 0; font-size: 11px;">
        <strong style="color: #1E3A8A;">Keterangan Zona Antropometri WHO (Standar Medis):</strong>
        <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
            <tr>
                <td style="width: 25%; padding: 4px; vertical-align: top;">
                    <strong style="color: #059669;">● Zona Hijau (-2 SD s/d +2 SD):</strong><br>
                    Normal / Gizi Baik. Tumbuh kembang optimal sesuai standar WHO.
                </td>
                <td style="width: 25%; padding: 4px; vertical-align: top;">
                    <strong style="color: #D97706;">● Zona Kuning (Waspada):</strong><br>
                    Batas atas/bawah (-3 s/d -2 SD atau > +2 SD). Butuh perhatian nutrisi.
                </td>
                <td style="width: 25%; padding: 4px; vertical-align: top;">
                    <strong style="color: #DC2626;">● Zona Merah (< -3 SD | > +3 SD):</strong><br>
                    Kritis (Sangat Pendek / Kurus / Obesitas). Segera rujuk ke DSA/Puskesmas.
                </td>
                <td style="width: 25%; padding: 4px; vertical-align: top;">
                    <strong style="color: #2563EB;">● Zona Biru (> +2 SD Tinggi):</strong><br>
                    Tall (Tinggi di Atas Rata-Rata). Normal karena genetik/nutrisi baik.
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">3. Rekomendasi Gizi & Kunjungan Posyandu</div>
    <div class="recommendation-box">
        <strong style="color: #1E3A8A;">Panduan Nutrisi & Pemantauan Lanjutan:</strong>
        <ul>
            <li><strong>Asupan Nutrisi:</strong> Berikan makanan beraneka ragam dan gizi seimbang dengan penekanan pada sumber protein hewani seperti telur, ikan, susu, dan daging ayam/sapi.</li>
            <li><strong>Pemantauan Rutin:</strong> Lakukan pengukuran tinggi badan dan penimbangan berat badan setiap bulan di Posyandu, Puskesmas, atau klinik terdekat.</li>
            @if(!empty($next_visit))
            <li><strong>Jadwal Kunjungan Selanjutnya:</strong> <span style="color: #2563EB; font-weight: bold;">{{ $next_visit }}</span></li>
            @endif
            <li><strong>Kebersihan & Kesehatan:</strong> Jaga kebersihan lingkungan rumah, sanitasi air bersih, serta melengkapi jadwal imunisasi anak.</li>
        </ul>
    </div>

    <div class="disclaimer">
        *Catatan Penting: Laporan ini adalah hasil komputasi dan analisis algoritma Kecerdasan Buatan (AI) Si Anting berdasarkan kurva referensi standar WHO. Laporan ini merupakan bantuan pemantauan mandiri dan tidak menggantikan pemeriksaan fisik/diagnosis medis resmi. Harap konsultasikan dengan dokter anak atau tenaga kesehatan untuk kepastian penanganan medis.
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Informasi Anak Stunting (Si Anting) - Dicetak secara digital
    </div>

</body>
</html>
