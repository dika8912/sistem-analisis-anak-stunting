<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Validasi dan proteksi route dilakukan di sisi client (JavaScript)
        // melalui localStorage token & role, sehingga kita cukup merender view.
        return view('admin.dashboard');
    }
}
