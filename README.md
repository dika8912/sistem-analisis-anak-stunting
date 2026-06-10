# 🩺 Stunting Detection System API
Sistem kalkulasi berat badan sesuai standar dari BMI (Indeks Berat Badan) menggunakan laravel dan python. 

#Dokumentasi Sistem 
<img width="1343" height="922" alt="Screenshot 2026-06-10 092822" src="https://github.com/user-attachments/assets/5ad05b06-b83d-410d-9226-93d394a19cd0" /> Untuk halaman dashboard admin 

<img width="1287" height="920" alt="Screenshot 2026-06-10 092840" src="https://github.com/user-attachments/assets/5b068dd1-74a3-4e8d-b6a2-854949cac8c0" />  Halaman pusat edukasi stunting 

<img width="1253" height="922" alt="Screenshot 2026-06-10 092901" src="https://github.com/user-attachments/assets/d6067e1e-7a3d-43cc-be6d-a8dd56b1936f" /> Halaman tambah data anak 

<img width="1108" height="923" alt="Screenshot 2026-06-10 092952" src="https://github.com/user-attachments/assets/df3ccb0f-c991-462b-b337-9b46f11a87ca" /> Halaman Register

<img width="1478" height="923" alt="Screenshot 2026-06-10 092752" src="https://github.com/user-attachments/assets/d5f13170-f8d2-49d5-b750-b124c1370ae1" />  Halaman login 

<img width="876" height="877" alt="Screenshot 2026-06-10 092712" src="https://github.com/user-attachments/assets/5196130c-31a5-4de1-b662-6b8f2562ee62" />

<img width="881" height="886" alt="Screenshot 2026-06-10 092703" src="https://github.com/user-attachments/assets/fb7fdc68-be7e-414d-ab93-71ccf50392d5" />   Halaman cetak pdf laporan dari deteksi 

<img width="1442" height="920" alt="Screenshot 2026-06-10 092638" src="https://github.com/user-attachments/assets/687ae036-2ee8-43b2-98a7-c602ebd6a31f" />  Halaman Kalkulator deteksi 


Backend API untuk deteksi **Stunting** dan **Wasting** pada anak usia 0–60 bulan berbasis perhitungan **WHO Z-Score (LMS)** dan prediksi **Machine Learning (Random Forest)**. Dilengkapi dengan sistem autentikasi **JWT** dan **Role-Based Access Control (RBAC)**.

---

## 📋 Daftar Isi

- [Tech Stack](#tech-stack)
- [Struktur Direktori](#struktur-direktori)
- [Dataset & Model ML](#dataset--model-ml)
- [Setup & Instalasi](#setup--instalasi)
- [Menjalankan Server](#menjalankan-server)
- [Autentikasi](#autentikasi)
- [Dokumentasi Endpoint](#dokumentasi-endpoint)
  - [Health Check](#health-check)
  - [Auth: Register](#auth-register)
  - [Auth: Login](#auth-login)
  - [Kalkulator Instan WHO (tanpa simpan)](#kalkulator-instan-who-tanpa-simpan)
  - [Prediksi ML Instan (tanpa simpan)](#prediksi-ml-instan-tanpa-simpan)
  - [Simpan Pengukuran](#simpan-pengukuran)
  - [Profil Guardian Saya](#profil-guardian-saya)
  - [Tambah Anak](#tambah-anak)
  - [Detail Anak](#detail-anak)
  - [Histori Pengukuran Anak](#histori-pengukuran-anak)
  - [Pencarian Anak (Admin)](#pencarian-anak-admin)
  - [Manajemen Anak (Admin)](#manajemen-anak-admin)
  - [Modul Edukasi](#modul-edukasi)
- [Menjalankan Unit Test](#menjalankan-unit-test)

---

## Tech Stack

| Komponen         | Teknologi                        |
|------------------|----------------------------------|
| Framework        | FastAPI                          |
| Database         | MySQL (via aiomysql)             |
| ORM              | SQLAlchemy 2.0 (Async)           |
| Autentikasi      | PyJWT + passlib (bcrypt)         |
| ML               | Scikit-Learn, Pandas, Joblib     |
| Migration        | Alembic                          |
| Testing          | pytest + pytest-asyncio + httpx  |
| Server           | Uvicorn (port `5601`)            |

---

## Struktur Direktori

```
stunting_app/
├── api/
│   ├── auth.py              ← Register & Login endpoints
│   ├── deps.py              ← JWT dependencies (RBAC guards)
│   └── endpoints.py         ← Detection, Measurement, Admin endpoints
├── config/
│   └── settings.py          ← Konfigurasi env (.env)
├── core/
│   ├── base.py              ← Base model SQLAlchemy
│   ├── database.py          ← Async DB engine
│   └── security.py          ← JWT & password hashing
├── ml/
│   ├── dataset/
│   │   └── stunting_wasting_dataset.csv   ← Dataset training (4.6 MB)
│   ├── models/
│   │   ├── stunting_classifier.joblib     ← Model stunting terlatih
│   │   └── wasting_classifier.joblib      ← Model wasting terlatih
│   └── train.py             ← Script training model
├── models/                  ← SQLAlchemy ORM models (child.py, education.py, etc)
├── repositories/            ← Async DB repositories (termasuk education_repository.py)
├── seeds/
│   ├── who_seed.py          ← Seed data WHO Standards
│   ├── who_standards.csv    ← CSV WHO LMS parameters
│   └── admin_seed.py        ← Seed akun admin default
├── services/                ← Business logic
├── tests/
│   └── test_endpoints.py    ← 79 unit tests
└── main.py                  ← Entrypoint FastAPI
```

---

## Dataset & Model ML

| File | Lokasi | Keterangan |
|------|--------|------------|
| `stunting_wasting_dataset.csv` | `ml/dataset/` | Dataset training ML (±100.000 baris) |
| `who_standards.csv` | `seeds/` | Tabel parameter LMS WHO (lhfa, wfa, wflh) |
| `stunting_classifier.joblib` | `ml/models/` | Model RandomForest untuk klasifikasi stunting |
| `wasting_classifier.joblib` | `ml/models/` | Model RandomForest untuk klasifikasi wasting |

Untuk melatih ulang model:
```bash
python stunting_app/ml/train.py
```

---

## Setup & Instalasi

### 1. Buat Virtual Environment
```bash
python -m venv .venv
.venv\Scripts\activate        # Windows
source .venv/bin/activate     # Linux/macOS
```

### 2. Install Dependencies
```bash
pip install -r requirements.txt
```

### 3. Konfigurasi `.env`
Buat file `.env` di root project:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASSWORD=yourpassword
DB_NAME=stunting_db
DB_ECHO=False

JWT_SECRET=ganti_dengan_secret_yang_sangat_panjang
JWT_ALGORITHM=HS256
ACCESS_TOKEN_EXPIRE_MINUTES=10080
```

### 4. Jalankan Migrasi Database
```bash
alembic upgrade head
```

### 5. Seed Data WHO Standards
```bash
python stunting_app/seeds/who_seed.py
```

### 6. Seed Akun Admin
```bash
python stunting_app/seeds/admin_seed.py
```
Credentials default admin:
- **Username:** `admin`
- **Password:** `admin123`

> ⚠️ **Segera ganti password admin** setelah pertama kali login di production.

---

## Menjalankan Server

```bash
python stunting_app/main.py
```

Server berjalan di: `http://localhost:5601`

Swagger UI tersedia di: `http://localhost:5601/docs`

ReDoc tersedia di: `http://localhost:5601/redoc`

---

## Autentikasi

API ini menggunakan **JWT Bearer Token**. Setelah login, sertakan token di setiap request yang membutuhkan autentikasi:

```
Authorization: Bearer <access_token>
```

### Role & Hak Akses

| Role    | Deskripsi |
|---------|-----------|
| `user`  | Orang tua / wali anak. Dapat mengakses dan mengelola data anaknya sendiri. |
| `admin` | Administrator. Memiliki akses penuh ke semua data. Dibuat melalui seeder. |

---

## Dokumentasi Endpoint

### Base URL
```
http://localhost:5601
```

---

### Health Check

```http
GET /
```

Mengecek apakah server berjalan dengan normal.

**Auth:** Tidak diperlukan

**Response `200 OK`:**
```json
{
  "status": "ok",
  "message": "Stunting Detection System API is running."
}
```

---

### Auth: Register

```http
POST /api/auth/register
```

Mendaftarkan akun baru sebagai **user** (orang tua). Role otomatis di-set menjadi `user`. Sekaligus membuat profil **Guardian** yang terikat dengan akun tersebut.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/json`

**Request Body:**

| Field       | Tipe     | Wajib | Keterangan |
|-------------|----------|-------|------------|
| `username`  | `string` | ✅    | Min. 3 karakter, harus unik |
| `password`  | `string` | ✅    | Min. 6 karakter |
| `name`      | `string` | ✅    | Nama lengkap orang tua / wali |
| `nomor_kk`  | `string` | ✅    | Nomor Kartu Keluarga (16 digit) |
| `phone`     | `string` | ✅    | Nomor telepon aktif |
| `address`   | `string` | ✅    | Alamat lengkap |
| `email`     | `string` | ❌    | Alamat email (opsional, harus format valid) |

**Contoh Request:**
```json
{
  "username": "budi_santoso",
  "email": "budi@email.com",
  "password": "password123",
  "name": "Budi Santoso",
  "nomor_kk": "3201010101010101",
  "phone": "081234567890",
  "address": "Jl. Merdeka No. 1, Jakarta"
}
```

**Response `200 OK`:**
```json
{
  "id": "aaaaaaaa-0000-0000-0000-000000000001",
  "username": "budi_santoso",
  "email": "budi@email.com",
  "role": "user"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `400` | Username sudah terdaftar |
| `422` | Validasi gagal (format email salah, password < 6 karakter, dll) |

---

### Auth: Login

```http
POST /api/auth/login
```

Melakukan autentikasi dan mendapatkan **JWT Access Token**.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/x-www-form-urlencoded` *(OAuth2 form data)*

**Request Body (Form Data):**

| Field      | Tipe     | Wajib | Keterangan |
|------------|----------|-------|------------|
| `username` | `string` | ✅    | Username terdaftar atau alamat email |
| `password` | `string` | ✅    | Password akun |
| `remember_me` | `boolean` | ❌    | (Opsional) True untuk masa berlaku token 30 hari |

**Contoh Request (curl):**
```bash
curl -X POST "http://localhost:5601/api/auth/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=budi_santoso&password=password123"
```

**Response `200 OK`:**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Username atau password salah |
| `422` | Form data tidak lengkap |

---

### Auth: Forgot Password

```http
POST /api/auth/forgot-password
```

Meminta token untuk melakukan reset password berdasarkan alamat email terdaftar.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/json`

**Request Body:**

| Field   | Tipe     | Wajib | Keterangan |
|---------|----------|-------|------------|
| `email` | `string` | ✅    | Email terdaftar akun pengguna |

**Response `200 OK`:**
```json
{
  "message": "If your email is registered, you will receive a password reset link.",
  "reset_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### Auth: Reset Password

```http
POST /api/auth/reset-password
```

Melakukan update password akun dengan menggunakan token valid dari proses forgot password.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/json`

**Request Body:**

| Field          | Tipe     | Wajib | Keterangan |
|----------------|----------|-------|------------|
| `token`        | `string` | ✅    | Token JWT reset password |
| `new_password` | `string` | ✅    | Password baru (min. 6 karakter) |

**Response `200 OK`:**
```json
{
  "message": "Password successfully reset"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `400` | Token invalid, tipe salah, atau kedaluwarsa (lebih dari 15 menit) |
| `404` | User tidak ditemukan |
| `422` | Password baru kurang dari 6 karakter |

---

### Kalkulator Instan WHO (tanpa simpan)

> **Catatan:** Fitur kalkulator instan dan prediksi instan ini dirancang untuk penggunaan anonim/publik sehingga **datanya tidak disimpan ke dalam histori**. Jika Anda ingin hasil perhitungannya tersimpan ke histori tumbuh kembang anak, gunakan endpoint [Simpan Pengukuran](#simpan-pengukuran) (`POST /api/measurements`).

```http
POST /api/calculate
```

Melakukan **kalkulasi Z-Score WHO** secara instan tanpa menyimpan data ke database. Cocok untuk mode kalkulator kesehatan murni.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/json`

**Request Body:**

| Field           | Tipe      | Wajib | Keterangan |
|-----------------|-----------|-------|------------|
| `gender`        | `string`  | ✅    | `"M"` (Laki-laki) atau `"F"` (Perempuan) |
| `age_in_months` | `integer` | ✅    | Umur anak dalam bulan (0–60) |
| `height_cm`     | `float`   | ✅    | Tinggi badan dalam cm (> 0) |
| `weight_kg`     | `float`   | ✅    | Berat badan dalam kg (> 0) |

**Contoh Request:**
```json
{
  "gender": "M",
  "age_in_months": 24,
  "height_cm": 85.5,
  "weight_kg": 12.1
}
```

**Response `200 OK`:**
```json
{
  "input": {
    "gender": "M",
    "age_in_months": 24,
    "height_cm": 85.5,
    "weight_kg": 12.1
  },
  "who_calculation": {
    "haz_zscore": -1.24,
    "waz_zscore": -0.85,
    "whz_zscore": -0.72,
    "stunting_status_who": "normal",
    "wasting_status_who": "normal",
    "underweight_status_who": "normal"
  },
  "recommendations": [
    "Berikan makanan kaya protein hewani seperti telur, ikan, dan susu.",
    "Lanjutkan pemantauan tumbuh kembang bulanan di Posyandu."
  ]
}
```

---

### Prediksi ML Instan (tanpa simpan)

```http
POST /api/predict
```

Melakukan **prediksi Machine Learning** berbasis model Random Forest tanpa menyimpan data. Digunakan untuk mendapatkan *second opinion* berbasis data historis.

**Auth:** Tidak diperlukan (Publik)

**Content-Type:** `application/json`

**Request Body:** (Sama seperti `/api/calculate`)

**Response `200 OK`:**
```json
{
  "input": {
    "gender": "M",
    "age_in_months": 24,
    "height_cm": 85.5,
    "weight_kg": 12.1
  },
  "ml_prediction": {
    "stunting_status_ml": "normal",
    "stunting_confidence": 0.9400,
    "wasting_status_ml": "normal",
    "wasting_confidence": 0.8800
  }
}
```

**Keterangan Nilai Status:**

| Kategori | Nilai yang Mungkin |
|----------|--------------------|
| `stunting_status_who` | `severely_stunted`, `stunted`, `normal`, `tall` |
| `wasting_status_who` | `severely_wasted`, `wasted`, `normal`, `risk_of_overweight`, `overweight`, `obese` |
| `underweight_status_who` | `severely_underweight`, `underweight`, `normal` |

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `422` | Gender bukan `M`/`F`, umur di luar 0–60, tinggi/berat ≤ 0, tipe data salah |

---

### Simpan Pengukuran

```http
POST /api/measurements
```

Menyimpan data pengukuran fisik anak ke database, yang mana akan **otomatis menghitung Z-Score WHO dan prediksi ML di balik layar**, lalu menyimpan hasilnya untuk membentuk **Histori Pengukuran Anak**.

**Auth:** ✅ Wajib (`user` atau `admin`)

> **Catatan Keamanan:** Jika login sebagai `user`, sistem akan memvalidasi bahwa `child_id` yang dikirim benar-benar milik orang tua yang sedang login. Jika bukan, request akan ditolak dengan `403 Forbidden`.

**Content-Type:** `application/json`

**Headers:**
```
Authorization: Bearer <access_token>
```

**Request Body:**

| Field               | Tipe      | Wajib | Keterangan |
|---------------------|-----------|-------|------------|
| `child_id`          | `string`  | ✅    | UUID anak yang akan diukur |
| `measured_at`       | `date`    | ✅    | Tanggal pengukuran (format: `YYYY-MM-DD`) |
| `weight`            | `float`   | ✅    | Berat badan dalam kg |
| `height`            | `float`   | ✅    | Tinggi badan dalam cm |
| `head_circumference`| `float`   | ❌    | Lingkar kepala dalam cm (opsional) |
| `measured_by`       | `string`  | ❌    | Nama petugas yang mengukur (opsional) |

**Contoh Request:**
```json
{
  "child_id": "cccccccc-0000-0000-0000-000000000001",
  "measured_at": "2026-05-25",
  "weight": 12.1,
  "height": 85.5,
  "head_circumference": 48.0,
  "measured_by": "Bidan Susi"
}
```

**Response `200 OK`:**
```json
{
  "id": "eeeeeeee-0000-0000-0000-000000000001",
  "child_id": "cccccccc-0000-0000-0000-000000000001",
  "measured_at": "2026-05-25",
  "age_in_months": 24,
  "weight": 12.1,
  "height": 85.5,
  "head_circumference": 48.0,
  "measured_by": "Bidan Susi",
  "stunting_result": {
    "haz_zscore": -1.24,
    "waz_zscore": -0.85,
    "whz_zscore": -0.72,
    "stunting_status_who": "normal",
    "wasting_status_who": "normal",
    "underweight_status_who": "normal",
    "stunting_status_ml": "normal",
    "stunting_confidence": 0.9400,
    "wasting_status_ml": "normal",
    "wasting_confidence": 0.8800
  },
  "food_recommendations": [
    "Berikan makanan kaya protein hewani seperti telur, ikan, dan susu."
  ]
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Token tidak ada, sudah expired, atau tidak valid |
| `403` | User mencoba menambahkan pengukuran untuk anak yang bukan miliknya |
| `404` | `child_id` tidak ditemukan di database |
| `422` | Validasi request body gagal |

---

### Profil Guardian Saya

```http
GET /api/guardians/me
```

Menampilkan profil lengkap orang tua / wali yang sedang login berdasarkan JWT token.

**Auth:** ✅ Wajib (`user` atau `admin`)

**Headers:**
```
Authorization: Bearer <access_token>
```

**Response `200 OK`:**
```json
{
  "id": "dddddddd-0000-0000-0000-000000000001",
  "user_id": "aaaaaaaa-0000-0000-0000-000000000001",
  "name": "Budi Santoso",
  "nomor_kk": "3201010101010101",
  "phone": "081234567890",
  "email": "budi@email.com",
  "address": "Jl. Merdeka No. 1, Jakarta"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Token tidak ada atau tidak valid |
| `404` | Profil guardian tidak ditemukan (misalnya akun admin yang tidak memiliki profil guardian) |

---

### Tambah Anak

```http
POST /api/children
```

Menambahkan data anak baru yang dihubungkan otomatis ke profil orang tua / wali yang sedang login.

**Auth:** ✅ Wajib (`user`)

**Content-Type:** `application/json`

**Headers:**
```
Authorization: Bearer <access_token>
```

**Request Body:**

| Field           | Tipe     | Wajib | Keterangan |
|-----------------|----------|-------|------------|
| `name`          | `string` | ✅    | Nama anak (minimal 2 karakter) |
| `gender`        | `string` | ✅    | `"M"` (Laki-laki) atau `"F"` (Perempuan) |
| `date_of_birth` | `date`   | ✅    | Tanggal lahir anak (format: `YYYY-MM-DD`) |

**Contoh Request:**
```json
{
  "name": "Andi Santoso",
  "gender": "M",
  "date_of_birth": "2024-05-25"
}
```

**Response `201 Created`:**
```json
{
  "id": "cccccccc-0000-0000-0000-000000000001",
  "guardian_id": "dddddddd-0000-0000-0000-000000000001",
  "name": "Andi Santoso",
  "gender": "M",
  "date_of_birth": "2024-05-25"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Token tidak ada atau tidak valid |
| `403` | User tidak memiliki profil guardian (misalnya admin mencoba tambah anak ke dirinya) |
| `422` | Validasi gagal (misal gender bukan `M`/`F`, tanggal tidak valid) |

---

### Detail Anak

```http
GET /api/children/{id}
```

Menampilkan detail profil seorang anak berdasarkan ID-nya.

**Auth:** ✅ Wajib (`user` atau `admin`)

> **Catatan Keamanan:** User hanya bisa mengakses data anak yang merupakan miliknya. Admin dapat mengakses data anak siapapun.

**Path Parameter:**

| Parameter | Tipe     | Keterangan |
|-----------|----------|------------|
| `id`      | `string` | UUID anak  |

**Headers:**
```
Authorization: Bearer <access_token>
```

**Contoh Request:**
```
GET /api/children/cccccccc-0000-0000-0000-000000000001
```

**Response `200 OK`:**
```json
{
  "id": "cccccccc-0000-0000-0000-000000000001",
  "guardian_id": "dddddddd-0000-0000-0000-000000000001",
  "name": "Andi Santoso",
  "gender": "M",
  "date_of_birth": "2024-05-25"
}
```

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Token tidak ada atau tidak valid |
| `403` | User mencoba mengakses data anak milik orang tua lain |
| `404` | Anak dengan ID tersebut tidak ditemukan |

---

### Histori Pengukuran Anak

```http
GET /api/children/{id}/history
```

Menampilkan **seluruh riwayat pengukuran fisik** seorang anak beserta hasil deteksi stunting dan wasting dari waktu ke waktu, diurutkan dari terbaru ke terlama.

**Auth:** ✅ Wajib (`user` atau `admin`)

> **Catatan Keamanan:** User hanya bisa mengakses histori anak yang merupakan miliknya. Admin dapat mengakses histori anak siapapun.

**Path Parameter:**

| Parameter | Tipe     | Keterangan |
|-----------|----------|------------|
| `id`      | `string` | UUID anak  |

**Headers:**
```
Authorization: Bearer <access_token>
```

**Contoh Request:**
```
GET /api/children/cccccccc-0000-0000-0000-000000000001/history
```

**Response `200 OK`:**
```json
[
  {
    "measurement": {
      "id": "eeeeeeee-0000-0000-0000-000000000001",
      "child_id": "cccccccc-0000-0000-0000-000000000001",
      "measured_at": "2026-05-25",
      "age_in_months": 24,
      "weight": 12.1,
      "height": 85.5,
      "head_circumference": 48.0,
      "measured_by": "Bidan Susi"
    },
    "result": {
      "id": "ffffffff-0000-0000-0000-000000000001",
      "measurement_id": "eeeeeeee-0000-0000-0000-000000000001",
      "haz_zscore": -1.24,
      "waz_zscore": -0.85,
      "whz_zscore": -0.72,
      "stunting_status_who": "normal",
      "wasting_status_who": "normal",
      "underweight_status_who": "normal",
      "stunting_status_ml": "normal",
      "wasting_status_ml": "normal",
      "ml_stunting_confidence": 0.9400,
      "ml_wasting_confidence": 0.8800,
      "notes": null
    }
  }
]
```

Jika anak belum memiliki riwayat pengukuran, response akan berupa array kosong `[]`.

**Error Responses:**

| Kode | Kondisi |
|------|---------|
| `401` | Token tidak ada atau tidak valid |
| `403` | User mencoba mengakses histori anak milik orang tua lain |
| `404` | Anak dengan ID tersebut tidak ditemukan |

---

### Pencarian Anak (Admin)

```http
GET /api/admin/children/search
```

Endpoint **eksklusif admin** untuk mencari dan menampilkan daftar anak berdasarkan **kategori** (NIK atau Nama). Digunakan untuk keperluan verifikasi dan pengelolaan data dari sisi admin.

**Auth:** ✅ Wajib (`admin` saja, user biasa akan ditolak dengan `403`)

**Headers:**
```
Authorization: Bearer <access_token>
```

**Query Parameters:**

| Parameter   | Tipe     | Wajib | Keterangan |
|-------------|----------|-------|------------|
| `query_val` | `string` | ✅    | Nilai yang dicari (bisa nama atau 16 digit NIK) |
| `category`  | `string` | ✅    | Kategori pencarian (`nik` atau `nama`) |

**Contoh Request:**
```
GET /api/admin/children/search?query_val=1234567890123456&category=nik
```

**Response `200 OK`:**
```json
[
  {
    "id": "cccccccc-0000-0000-0000-000000000001",
    "guardian_id": "dddddddd-0000-0000-0000-000000000001",
    "name": "Andi Santoso",
    "gender": "M",
    "date_of_birth": "2024-05-25",
    "nik": "1234567890123456"
  }
]
```

---

### Manajemen Anak (Admin)

```http
PUT /api/admin/children/{id}
DELETE /api/admin/children/{id}
```

Endpoint untuk melakukan *update* profil anak (seperti merevisi NIK/Nama/Tgl Lahir) atau menghapus data anak sepenuhnya beserta histori pengukurannya (`cascade delete`). Hanya bisa diakses oleh role `admin`.

---

### Modul Edukasi

Menyediakan artikel/informasi edukasi gizi dan penanganan stunting untuk masyarakat umum.

- `GET /api/educations`: Melihat daftar artikel edukasi (Publik / Semua User).
- `GET /api/educations/{id}`: Melihat detail sebuah artikel edukasi.
- `POST /api/admin/educations`: Membuat artikel edukasi baru (Hanya Admin).
- `PUT /api/admin/educations/{id}`: Mengupdate artikel (Hanya Admin).
- `DELETE /api/admin/educations/{id}`: Menghapus artikel (Hanya Admin).

---

## Kode Status HTTP

| Kode | Arti |
|------|------|
| `200` | Request berhasil |
| `400` | Bad Request (misalnya username duplikat) |
| `401` | Unauthorized – Token tidak ada, tidak valid, atau sudah expired |
| `403` | Forbidden – Token valid tapi tidak memiliki hak akses |
| `404` | Data yang dicari tidak ditemukan |
| `422` | Unprocessable Entity – Validasi data request gagal |
| `500` | Internal Server Error |

---

## Menjalankan Unit Test

```bash
.venv\Scripts\pytest stunting_app/tests/test_endpoints.py -v
```

Total: **79 test** yang mencakup:
- Perhitungan Z-Score WHO (boundary, edge case, nilai ekstrem)
- Keamanan JWT (expired, tampered, tanpa `sub`)
- RBAC (user tidak bisa akses endpoint admin)
- Endpoint Auth (register, login, validasi Pydantic)
- Endpoint Deteksi (input aneh, tipe data salah, nilai tidak realistis)
- Skenario aneh (Z-Score infinity, anak 50 kg usia 1 tahun, SQL injection, XSS payload)

---

## Catatan Keamanan

> ⚠️ Ganti nilai `JWT_SECRET` di file `.env` dengan string yang panjang dan acak sebelum deploy ke production. Jangan gunakan nilai default.
>
> ⚠️ Ganti password admin default (`admin123`) setelah seeder pertama kali dijalankan.
