<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChildController extends Controller
{
    /**
     * Tampilkan form untuk menambah anak (Hanya Admin).
     */
    public function create()
    {
        return view('children.create');
    }

    /**
     * Tampilkan detail anak dan grafik pertumbuhannya.
     *
     * @param int $id ID anak
     */
    public function show($id)
    {
        return view('children.show', compact('id'));
    }
}
