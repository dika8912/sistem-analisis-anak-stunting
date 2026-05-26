<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    /**
     * Tampilkan form untuk menambah pengukuran.
     */
    public function create(Request $request)
    {
        $childId = $request->query('child_id');
        return view('measurements.create', compact('childId'));
    }
}
