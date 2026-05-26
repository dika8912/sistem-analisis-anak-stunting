<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EducationsController extends Controller
{
    /**
     * Tampilkan daftar edukasi (Publik)
     */
    public function index()
    {
        return view('educations.index');
    }

    /**
     * Tampilkan detail artikel edukasi (Publik)
     */
    public function show($id)
    {
        return view('educations.show', compact('id'));
    }

    /**
     * Tampilkan halaman kelola edukasi (Admin)
     */
    public function adminForm()
    {
        return view('educations.form');
    }
}
