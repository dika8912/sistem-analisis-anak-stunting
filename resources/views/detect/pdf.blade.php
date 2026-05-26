<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Deteksi Si Anting</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563EB;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1E3A8A;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            color: #6B7280;
            font-size: 14px;
        }
        .info-box {
            background-color: #F3F4F6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 40%;
            font-weight: bold;
            color: #4B5563;
        }
        .result-box {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .result-header {
            background-color: #2563EB;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .result-content {
            padding: 20px;
            background-color: #ffffff;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background-color: #EFF6FF;
            color: #1D4ED8;
            font-weight: bold;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 15px;
            border: 1px solid #BFDBFE;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9CA3AF;
            margin-top: 50px;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        .disclaimer {
            font-size: 11px;
            color: #EF4444;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Si Anting - Laporan Deteksi</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>

    <div class="info-box">
        <h3>Data Pengukuran Anak</h3>
        <table class="info-table">
            <tr>
                <td>Umur</td>
                <td>: {{ $age_months }} Bulan</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: {{ $gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Tinggi / Panjang Badan</td>
                <td>: {{ $height }} cm</td>
            </tr>
            <tr>
                <td>Berat Badan</td>
                <td>: {{ $weight }} kg</td>
            </tr>
        </table>
    </div>

    <div class="result-box">
        <div class="result-header">
            Hasil Analisa & Prediksi
        </div>
        <div class="result-content">
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 5px 0; color: #6B7280; font-size: 14px; font-weight: bold;">Status Gizi (Standar WHO)</p>
                <div class="status-badge">
                    {{ $status }}
                </div>
            </div>
            
            <table class="info-table" style="margin-bottom: 20px;">
                <tr>
                    <td>Z-Score (TB/U)</td>
                    <td>: <strong>{{ $z_score }}</strong></td>
                </tr>
                @if(!empty($ml_prediction))
                <tr>
                    <td>Prediksi Machine Learning</td>
                    <td>: <strong>{{ $ml_prediction }}</strong></td>
                </tr>
                @endif
            </table>

            <div class="disclaimer">
                *Catatan: Laporan ini adalah hasil simulasi algoritma (Kalkulator Gizi) dan tidak dapat menggantikan diagnosis medis resmi. Harap konsultasikan dengan dokter atau bidan untuk memastikan kesehatan anak Anda.
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Informasi Anak Stunting (Si Anting)
    </div>

</body>
</html>
