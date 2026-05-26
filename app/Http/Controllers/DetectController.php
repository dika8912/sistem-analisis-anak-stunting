<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DetectController extends Controller
{
    /**
     * Tampilkan halaman kalkulator publik.
     */
    public function index()
    {
        return view('detect.index');
    }

    /**
     * Cetak hasil deteksi ke format PDF.
     */
    public function printPdf(Request $request)
    {
        $data = $request->validate([
            'age_months' => 'required|numeric',
            'gender' => 'required|in:L,P',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'z_score' => 'required|numeric',
            'status' => 'required|string',
            'ml_prediction' => 'nullable|string',
        ]);

        $pdf = Pdf::loadView('detect.pdf', $data);
        
        // Atur ukuran kertas
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('hasil-deteksi-sianting.pdf');
    }
}
