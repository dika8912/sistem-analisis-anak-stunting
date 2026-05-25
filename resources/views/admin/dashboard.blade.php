@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="bg-white p-10 rounded-[1rem] shadow-xl border border-gray-100 w-full max-w-3xl">
        <h1 class="text-2xl font-extrabold mb-4">Admin Dashboard</h1>
        <p class="text-gray-600">Selamat datang, administrator. Halaman ini adalah tempat mengelola aplikasi.</p>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <a href="#" class="block p-4 rounded-lg border border-gray-200 hover:shadow-md">Manage Users</a>
            <a href="#" class="block p-4 rounded-lg border border-gray-200 hover:shadow-md">Site Settings</a>
        </div>
    </div>
</div>
@endsection
