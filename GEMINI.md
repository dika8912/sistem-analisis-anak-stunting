# Technical Specification: Si Anting — Frontend Web Application (Laravel 12)

Dokumen ini adalah blueprint teknis untuk pembangunan **frontend** Sistem Deteksi Stunting & Wasting Anak (*Si Anting*). Frontend akan dikembangkan menggunakan framework **Laravel 12** dengan **PHP 8.4.x**, yang akan bertindak sebagai penyedia antarmuka (View) dan terhubung ke backend FastAPI melalui **Native JS `fetch`**.

---

## 1. Overview Proyek

**Si Anting** (Sistem Informasi Anak Stunting) adalah aplikasi web berbasis data untuk:
- Orang tua / wali memantau tumbuh kembang anak secara mandiri.
- Tenaga kesehatan (admin) mengelola dan memverifikasi data anak berdasarkan Nomor KK.
- Memberikan rekomendasi gizi berbasis hasil deteksi WHO Z-Score dan prediksi Machine Learning.

---

## 2. Tech Stack Frontend

| Aspek             | Teknologi                       |
|-------------------|---------------------------------|
| Framework         | **Laravel 12** (PHP 8.4.x)      |
| Templating        | **Blade**                       |
| Styling           | **Tailwind CSS 4.x.x**          |
| HTTP Client       | **Native JS `fetch` API**       |
| Interactivity     | **Vanilla JavaScript**          |
| Chart / Grafik    | **Chart.js**                    |
| Notifikasi        | **SweetAlert2** / Toastify      |
| Icon              | **Phosphor Icons** / FontAwesome|
| Font              | **Inter** (Google Fonts)        |
| Bundler           | **Vite**                        |

---

## 3. Struktur Direktori Frontend (Laravel)

```
si-anting-frontend/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── AuthController.php      # Controller untuk view login/register
│           ├── DashboardController.php # Controller untuk view dashboard user
│           ├── ChildController.php     # Controller untuk view manajemen anak
│           └── AdminController.php     # Controller untuk view admin
├── config/
├── public/
│   └── assets/                         # Gambar statis, logo, dll
├── resources/
│   ├── css/
│   │   └── app.css                     # Entry point Tailwind CSS 4
│   ├── js/
│   │   ├── app.js                      # Entry point Vite JS
│   │   ├── api.js                      # Helper Native Fetch dengan interceptor JWT
│   │   ├── auth.js                     # Logika login, register, dan logout
│   │   └── charts.js                   # Konfigurasi Chart.js untuk grafik pertumbuhan
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php           # Base layout (Tailwind, Font, Script global)
│       │   ├── admin.blade.php         # Layout khusus Admin
│       │   └── navigation.blade.php    # Sidebar / Navbar
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/
│       │   └── index.blade.php         # Dashboard utama user
│       ├── detect/
│       │   └── index.blade.php         # Halaman deteksi kalkulator instan
│       ├── children/
│       │   ├── index.blade.php         # List anak
│       │   ├── show.blade.php          # Detail anak & histori
│       │   └── create.blade.php        # Form tambah anak
│       ├── measurements/
│       │   └── create.blade.php        # Form tambah pengukuran
│       └── admin/
│           ├── dashboard.blade.php     # Dashboard statistik admin
│           └── search.blade.php        # Pencarian by Nama & No. KK
├── routes/
│   └── web.php                         # Routing halaman Blade
├── .env                                # Konfigurasi environment
├── package.json                        # Dependencies Node.js (Tailwind, Vite, dll)
└── vite.config.js                      # Konfigurasi Vite
```

---

## 4. Koneksi ke Backend API (Native Fetch)

Karena kita menggunakan Native JS `fetch` untuk berkomunikasi dengan backend FastAPI, kita akan membuat helper function di `resources/js/api.js` untuk otomatis menyisipkan JWT Token (yang disimpan di `localStorage`) ke dalam header setiap request.

### 4.1. Helper Fetch (`resources/js/api.js`)
```javascript
// Base URL FastAPI Backend
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:5601';

/**
 * Custom Fetch API Wrapper dengan JWT Interceptor
 */
export async function apiFetch(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const token = localStorage.getItem('access_token');

    // Setup headers
    const headers = {
        'Content-Type': 'application/json',
        ...options.headers,
    };

    // Inject JWT token jika ada
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    // Eksekusi fetch
    const response = await fetch(url, {
        ...options,
        headers,
    });

    // Handle 401 Unauthorized (Token Expired / Invalid)
    if (response.status === 401) {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');
        window.location.href = '/login';
        throw new Error('Unauthorized');
    }

    // Coba parse JSON (walaupun error)
    const contentType = response.headers.get('content-type');
    let data = null;
    if (contentType && contentType.includes('application/json')) {
        data = await response.json();
    }

    if (!response.ok) {
        throw { status: response.status, data: data, message: response.statusText };
    }

    return data;
}
```

---

## 5. Mapping Halaman (Laravel) ↔ Endpoint (FastAPI)

| Halaman Laravel (View) | Method JS | Endpoint FastAPI (Backend) | Auth |
|------------------------|-----------|----------------------------|------|
| `/login`               | `POST`    | `/api/auth/login`          | -    |
| `/register`            | `POST`    | `/api/auth/register`       | -    |
| `/forgot-password`     | `POST`    | `/api/auth/forgot-password`| -    |
| `/reset-password`      | `POST`    | `/api/auth/reset-password` | -    |
| `/dashboard`           | `GET`     | `/api/guardians/me`        | user |
| `/detect`              | `POST`    | `/api/detect`              | -    |
| `/children`            | `GET`     | `/api/guardians/me`        | user |
| `/children/create`     | `POST`    | `/api/children`            | user |
| `/children/{id}`       | `GET`     | `/api/children/{id}`       | user |
| `/children/{id}`       | `GET`     | `/api/children/{id}/history`| user |
| `/measurements/add`    | `POST`    | `/api/measurements`        | user |
| `/admin/children`      | `GET`     | `/api/admin/children`      | admin|

---

## 6. Alur Autentikasi & Proteksi Route

Karena arsitektur ini memisahkan view (Laravel) dan data (FastAPI via JS Fetch), proteksi route akan dilakukan di **sisi Client (JavaScript)** pada setiap halaman Blade yang dirender.

```mermaid
flowchart TD
    A[User Buka Halaman Blade] --> B{JS Cek localStorage('access_token')}
    B -- Tidak Ada --> C[Redirect ke /login via window.location]
    B -- Ada Token --> D{Fetch Data ke FastAPI}
    D -- 200 OK --> E[Render Data ke DOM]
    D -- 401 Unauthorized --> F[Hapus Token & Redirect ke /login]
    D -- 403 Forbidden --> G[Redirect ke /dashboard]
```

### Script Guard Global (`resources/views/layouts/app.blade.php`)
```html
<script>
    // Contoh implementasi middleware di sisi client
    const token = localStorage.getItem('access_token');
    const role = localStorage.getItem('user_role');
    const currentPath = window.location.pathname;

    const publicRoutes = ['/login', '/register', '/'];

    if (!token && !publicRoutes.includes(currentPath)) {
        window.location.href = '/login';
    }

    if (token && currentPath.startsWith('/admin') && role !== 'admin') {
        window.location.href = '/dashboard';
    }
</script>
```

---

## 7. Desain UI / UX (Tailwind CSS 4)

### 7.1. Konfigurasi Tailwind CSS 4
Di Tailwind v4, konfigurasi dilakukan langsung melalui file CSS (`resources/css/app.css`):
```css
@import "tailwindcss";

/* Definisi Color Palette Status */
@theme {
    --color-status-severely: #DC2626; /* Merah Tua */
    --color-status-stunted: #F97316;  /* Oranye */
    --color-status-normal: #16A34A;   /* Hijau */
    --color-status-risk: #CA8A04;     /* Kuning */
    --color-status-overweight: #9333EA; /* Ungu */
    --color-status-tall: #2563EB;     /* Biru */
}
```

### 7.2. Halaman Utama (User)

1. **`/login`** — Form login minimalis dengan background gradien. Terintegrasi dengan fitur "Remember Me". Interaksi submit via JS `fetch`, simpan token ke `localStorage`, redirect.
2. **`/register`** — Form pendaftaran Guardian dan User.
3. **`/forgot-password` & `/reset-password`** — Halaman dan form untuk melakukan pemulihan akses (lupa password).
4. **`/dashboard`** — Menampilkan ringkasan data orang tua dan kartu list anak. Data diambil asinkronus dengan Fetch API dan dirender ke DOM.
4. **`/detect`** — Mode kalkulator tanpa login. Mengirim request ke `/api/detect`, hasil langsung muncul di bawah form tanpa *page reload*.
5. **`/children/{id}`** — Detail anak dan *timeline* histori pengukuran.
6. **`/measurements/create`** — Form tambah pengukuran baru.

### 7.3. Halaman Admin

7. **`/admin/dashboard`** — Panel admin dengan statistik.
8. **`/admin/children`** — Form pencarian `Nama Orang Tua` + `Nomor KK`. Menampilkan tabel hasil pencarian dari FastAPI secara dinamis.

### 7.4. Grafik Pertumbuhan (Chart.js)
Tampilkan grafik garis menggunakan **Chart.js** yang diinisialisasi melalui Vanilla JS:
- **Sumbu X**: Umur anak (bulan)
- **Sumbu Y**: Tinggi badan (cm) atau Berat badan (kg)
- **Garis WHO**: Tambahkan dataset tambahan berupa garis referensi batas normalitas (+2SD dan -2SD).

---

## 8. Environment Variables (Laravel `.env`)

Konfigurasikan backend URL di `.env` Laravel. Variabel dengan prefix `VITE_` akan terekspos ke sisi klien (JavaScript).

```env
APP_NAME="Si Anting"
APP_ENV=local
APP_URL=http://localhost:8000

# URL Backend FastAPI (Digunakan oleh JS Fetch API)
VITE_API_URL=https://api-sianting.bilikku.my.id
```

---

## 9. Rencana Implementasi Fase Frontend

### Fase 1: Setup Framework & Assets
- Instalasi Laravel 12 & setup Vite dengan Tailwind CSS 4.x.x.
- Konfigurasi `api.js` helper untuk Native Fetch dan penanganan token.
- Pembuatan layout global (`app.blade.php`).

### Fase 2: Autentikasi (Client-Side State)
- Pembuatan tampilan `/login` dan `/register`.
- Penulisan skrip Vanilla JS untuk menangani form submission (Fetch POST), penyimpanan JWT di `localStorage`, dan client-side guard.

### Fase 3: Dashboard & Profil
- Desain Sidebar dan Header (Tailwind).
- Pembuatan halaman `/dashboard` yang melakukan `fetch` ke `/api/guardians/me` dan merender data ke layar.

### Fase 4: Manajemen Anak & Pengukuran
- Pembuatan halaman detail anak (`/children/{id}`).
- Integrasi Chart.js untuk menggambar grafik histori pengukuran dari endpoint `/api/children/{id}/history`.
- Form pengukuran fisik anak (`/measurements/create`).

### Fase 5: Fitur Kalkulator Deteksi
- Halaman publik `/detect`.
- Form dengan validasi native JS, memanggil endpoint `/api/detect`.
- Render UI hasil kalkulasi (WHO dan Prediksi ML) dan list rekomendasi gizi secara dinamis.

### Fase 6: Fitur Admin
- Layout khusus admin (`admin.blade.php`).
- Proteksi route khusus role `admin`.
- Pembuatan halaman pencarian data anak untuk verifikasi kesehatan oleh nakes.
