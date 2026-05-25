# Si Anting (Sistem Informasi Anak Stunting) - Frontend

Repositori ini berisi kode *frontend* untuk aplikasi **Si Anting**, sebuah Sistem Deteksi Stunting & Wasting Anak. Aplikasi dibangun menggunakan **Laravel 12** sebagai penyedia tampilan (*Blade templates*) dan **Tailwind CSS 4** untuk desain antarmuka, yang terhubung ke backend FastAPI melalui *Native JS `fetch`*.

## Fitur yang Telah Diselesaikan (Fase 1 - 3)

1. **Setup & UI Framework**: 
   - Konfigurasi Laravel 12 dengan Vite dan Tailwind CSS v4.
   - Migrasi aset eksternal (CDN) ke paket lokal via `pnpm` (Phosphor Icons, Chart.js, SweetAlert2, FontSource Inter).
   - *Layouting* global (`app.blade.php`) dengan desain UI yang modern (kartu membulat, gradien halus).

2. **Sistem Autentikasi (Client-side)**:
   - Halaman **Login** (`/login`) dengan fitur *Show/Hide Password* dan pengiriman *payload* berbasis `application/x-www-form-urlencoded` yang disesuaikan dengan standar FastAPI `OAuth2PasswordRequestForm`.
   - Halaman **Register** (`/register`) dengan validasi kolom tambahan wajib dari API (`username`, `nomor_kk`, `phone`, `address`).
   - Halaman **Lupa Password & Reset Password** dengan UI yang konsisten dan interaktif.
   - *Middleware/Guard Client-side* untuk memproteksi halaman berbasis `localStorage` token.

3. **Dashboard User (Orang Tua / Wali)**:
   - Halaman **Dashboard** (`/dashboard`) yang terintegrasi penuh untuk menampilkan sapaan nama pengguna, dan menyusun *grid* "Kartu Anak" secara asinkron dari endpoint `/api/guardians/me`.
   - *State handling* yang ramah (menampilkan animasi *skeleton loading* saat menunggu API, dan UI *Empty State* yang interaktif bila data kosong).
   - *Unit test* integrasi rute dasar menggunakan PHPUnit/Pest.

## 🐛 Known Bug (Isu Saat Ini)

Saat ini sedang terjadi sebuah anomali (*bug*) pada saat proses login, yaitu:
- **Token Leakage / Logic Error**: Jika backend gagal memverifikasi kredensial (misalnya mengirim status code gagal) namun tidak tertangkap dengan benar oleh blok `catch` di `api.js` (atau API mengembalikan `200 OK` dengan body error), skrip *frontend* akan keliru menyimpan token `undefined` atau data kosong ke `localStorage` dan tetap memaksa (*redirect*) pengguna masuk ke halaman `/dashboard`. 
- **Rencana Tindak Lanjut**: Hal ini akan diteliti dan diselesaikan besok melalui serangkaian *Unit Testing* JavaScript / alur *try-catch* untuk mencegah pengalihan (*redirect*) jika otorisasi benar-benar gagal.

---

*Dibangun dengan ❤️ menggunakan Agentic Development (Antigravity).*
