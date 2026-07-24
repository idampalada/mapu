# Dokumentasi API Kendaraan (MAPU)

REST API untuk data kendaraan (tabel `alat_angkutan`) pada sistem Manajemen Aset PU.
Dibangun sebagai **aplikasi CodeIgniter 4 terpisah** di folder `mapu/api-kendaraan/` yang
**berbagi database** dengan aplikasi `mapu`, **tanpa mengubah** kode `mapu` sama sekali.

- **Base URL:** `http://localhost:8081`
- **Format:** JSON
- **Autentikasi:** Bearer token (JWT), berlaku 1 jam

---

## 1. Ringkasan: Apa yang Ditambahkan

| Hal | Keterangan |
|-----|------------|
| Aplikasi API | CI4 mandiri di `mapu/api-kendaraan/`, entry point & container sendiri (port 8081) |
| Database | **Dibagi** dengan `mapu` (container `mapu-db`), tabel `alat_angkutan` & `users` |
| Autentikasi | Login `email/username + password` (akun `users`) → JWT. Verifikasi password kompatibel `Myth\Auth` |
| Operasi | **Baca (GET)** & **Tambah (POST)**. PUT/DELETE sengaja tidak disediakan |
| Dampak ke `mapu` | **Nol** — tidak ada file `mapu` yang diubah/dihapus |

---

## 2. Arsitektur

```
                    ┌──────────────────────────────┐
   :8080  ─────────►│  mapu (aplikasi lama)        │   TIDAK DISENTUH
                    └───────────────┬──────────────┘
                                    │
                    ┌───────────────▼──────────────┐
                    │  mapu-db (PostgreSQL)        │  ← DIBAGI BERSAMA
                    │  tabel: alat_angkutan, users │
                    └───────────────▲──────────────┘
                                    │ network: mapu_mapu-network
                    ┌───────────────┴──────────────┐
   :8081  ─────────►│  mapu/api-kendaraan (BARU)   │   FOLDER BARU
                    └──────────────────────────────┘
```

Alur permintaan di dalam API:

```
(1) POST /api/v1/auth/token   { login, password }
        └─► Auth Controller ─► UserModel (baca users) ─► ApiToken (buat JWT)
        ◄─► { access_token }

(2) GET /api/v1/kendaraan     Authorization: Bearer <token>
        └─► JwtFilter (verifikasi) ─► Kendaraan Controller ─► KendaraanModel
        ◄─► { data }
```

---

## 3. Struktur Folder yang Ditambahkan

```
mapu/api-kendaraan/
├── app/
│   ├── Config/
│   │   ├── Routes.php              # Routing API (diganti)
│   │   └── Filters.php             # + alias 'jwt'
│   ├── Controllers/
│   │   ├── Auth.php                # Endpoint login → token
│   │   └── Kendaraan.php           # Endpoint data kendaraan
│   ├── Filters/
│   │   └── JwtFilter.php           # Verifikasi bearer token
│   ├── Libraries/
│   │   └── ApiToken.php            # Buat & verifikasi JWT (HMAC-SHA256)
│   └── Models/
│       ├── KendaraanModel.php      # Query tabel alat_angkutan
│       └── UserModel.php           # Baca tabel users (login)
├── db-init/
│   └── 01-mapu.sql                 # Dump DB (auto-restore versi portable)
├── docker/nginx/
│   └── default.conf                # Konfigurasi nginx API
├── public/index.php                # Entry point (bawaan CI4)
├── vendor/                         # Dependency (composer, --no-dev)
├── .env                            # Konfigurasi (DB + jwtSecret)
├── Dockerfile                      # Image PHP-FPM API
├── docker-compose.yml              # Menyambung ke DB milik mapu
├── docker-compose.portable.yml     # Self-contained (DB sendiri) untuk laptop lain
├── composer.json / composer.lock   # Daftar dependency
├── DOKUMENTASI-API.md              # File ini
└── CARA-TEST-LAPTOP-LAIN.md        # Panduan portable
```

**File inti buatan sendiri** (di luar kerangka CI4): 6 file PHP di
`app/Controllers`, `app/Filters`, `app/Libraries`, `app/Models`, + `Routes.php`.

---

## 4. Autentikasi (2 Langkah)

### Langkah 1 — Ambil token

Vendor menukar kredensial akun (email/username + password) menjadi bearer token.
Akun berasal dari tabel `users` milik `mapu` (dibuat admin lewat halaman
register/manajemen user yang sudah ada; harus berstatus `active = 1`).

### Langkah 2 — Pakai token

Sertakan token pada setiap request data:
```
Authorization: Bearer <access_token>
```
Token berlaku **1 jam** (`api.jwtTTL`). Jika kedaluwarsa, minta token baru.

> **Kenapa bukan `?api_key=` di URL?** Kredensial di URL akan tercatat plaintext di
> log server, history browser, dan header `Referer`. Bearer token di header
> menghindari semua itu.

---

## 5. Referensi Endpoint

Base URL semua endpoint: `http://localhost:8081`

| # | Method | Endpoint | Auth | Fungsi |
|---|--------|----------|------|--------|
| 1 | GET | `/` | — | Health check |
| 2 | POST | `/api/v1/auth/token` | — | Login → bearer token |
| 3 | GET | `/api/v1/kendaraan` | Bearer | Daftar kendaraan (pagination, filter) |
| 4 | GET | `/api/v1/kendaraan/statistik` | Bearer | Statistik per kelompok & kondisi |
| 5 | GET | `/api/v1/kendaraan/{id}` | Bearer | Detail satu kendaraan |
| 6 | POST | `/api/v1/kendaraan` | Bearer | Tambah kendaraan |

---

### 1. Health Check

```
GET /
```
**Respons 200:**
```json
{ "status": "success", "message": "API Kendaraan aktif.", "version": "v1" }
```

---

### 2. Login — Ambil Token

```
POST /api/v1/auth/token
Content-Type: application/json
```
**Body:**
```json
{ "login": "rakamagang", "password": "rahasia" }
```
- `login` — boleh **email** atau **username** (dideteksi otomatis).

**Respons 200:**
```json
{
  "status": "success",
  "token_type": "Bearer",
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9....",
  "expires_in": 3600
}
```

**Kemungkinan gagal:**
| Kode | Kondisi |
|------|---------|
| 400 | `login` / `password` kosong |
| 401 | email/username atau password salah |
| 403 | akun belum aktif (`active = 0`) |
| 500 | `api.jwtSecret` belum dikonfigurasi di server |

---

### 3. Daftar Kendaraan

```
GET /api/v1/kendaraan
Authorization: Bearer <token>
```
**Query parameter (opsional):**
| Param | Default | Keterangan |
|-------|---------|------------|
| `page` | 1 | Halaman |
| `per_page` | 25 | Jumlah per halaman (maks 200) |
| `search` | — | Cari di kode_barang, nama_barang, merk, no_mesin, no_rangka, no_polisi, nup |
| `kelompok` | — | Filter kelompok, mis. `ALAT ANGKUTAN DARAT BERMOTOR` |
| `format` | — | `siman` → format gaya API SIMAN |

**Respons 200 (format default):**
```json
{
  "status": "success",
  "total": 1000,
  "page": 1,
  "per_page": 25,
  "count": 25,
  "data": [
    {
      "id": "350",
      "kode_barang": "3020101001_349",
      "nama_barang": "Sedan",
      "merk": "Nissan",
      "no_polisi": "B 1429 SQO",
      "kondisi": "Baik",
      "kelompok": "ALAT ANGKUTAN DARAT BERMOTOR",
      "...": "..."
    }
  ]
}
```

**Contoh:**
```
GET /api/v1/kendaraan?search=toyota&kelompok=ALAT%20ANGKUTAN%20DARAT%20BERMOTOR&page=1&per_page=10
```

---

### 4. Statistik

```
GET /api/v1/kendaraan/statistik
Authorization: Bearer <token>
```
**Respons 200:**
```json
{
  "status": "success",
  "data": {
    "total": 1000,
    "per_kelompok": [
      { "kelompok": "ALAT ANGKUTAN APUNG BERMOTOR", "jumlah": "10" },
      { "kelompok": "ALAT ANGKUTAN BERMOTOR UDARA", "jumlah": "1" },
      { "kelompok": "ALAT ANGKUTAN DARAT BERMOTOR", "jumlah": "989" }
    ],
    "per_kondisi": [
      { "kondisi": "Baik", "jumlah": "554" },
      { "kondisi": "Rusak Berat", "jumlah": "291" },
      { "kondisi": "Rusak Ringan", "jumlah": "155" }
    ]
  }
}
```

---

### 5. Detail Kendaraan

```
GET /api/v1/kendaraan/{id}
Authorization: Bearer <token>
```
**Respons 200:**
```json
{ "status": "success", "data": { "id": "350", "kode_barang": "...", "...": "..." } }
```
**Respons 404:**
```json
{ "status": "error", "message": "Data kendaraan tidak ditemukan." }
```
Mendukung `?format=siman` (mengembalikan `resource` berisi array 1 item).

---

### 6. Tambah Kendaraan

```
POST /api/v1/kendaraan
Authorization: Bearer <token>
Content-Type: application/json
```
**Body (field wajib: `kode_barang`, `nama_barang`, `kelompok`):**
```json
{
  "kode_barang": "3050104001",
  "nama_barang": "MINI BUS (PENUMPANG 14 ORANG KEBAWAH)",
  "kelompok": "ALAT ANGKUTAN DARAT BERMOTOR",
  "merk": "TOYOTA HIACE",
  "kondisi": "Baik",
  "kuantitas": 1,
  "thn_buat": "2022",
  "no_polisi": "B 1234 XYZ"
}
```
**Respons 201:**
```json
{ "status": "success", "message": "Kendaraan berhasil ditambahkan.", "data": { "id": "1001", "...": "..." } }
```
**Respons 422 (validasi gagal):**
```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": { "kode_barang": "Kode barang harus diisi", "kelompok": "Kelompok harus diisi" }
}
```

---

## 6. Format Respons SIMAN (opsional)

Tambahkan `?format=siman` pada endpoint list/detail untuk meniru gaya API SIMAN
(`apigw.pu.go.id`):
```json
{ "resource": [ { "id": "350", "kode_barang": "...", "...": "..." } ] }
```
Autentikasi tetap memakai header Bearer (SIMAN memakai `?api_key=`, yang **tidak**
ditiru karena alasan keamanan). Pagination tetap berlaku.

---

## 7. Kode Status HTTP

| Kode | Arti |
|------|------|
| 200 | Berhasil (GET) |
| 201 | Berhasil dibuat (POST) |
| 400 | Request tidak valid (body kosong) |
| 401 | Tidak terautentikasi (token salah/kedaluwarsa/tidak ada) |
| 403 | Akun belum aktif |
| 404 | Data / route tidak ditemukan |
| 422 | Validasi data gagal |
| 500 | Konfigurasi server bermasalah |

---

## 8. Field Tabel `alat_angkutan`

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| `id` | int | auto | Primary key |
| `kode_barang` | string(100) | ✓ | Kode BMN |
| `nama_barang` | string(255) | ✓ | Nama kendaraan |
| `kelompok` | string(100) | ✓ | Kategori angkutan |
| `sub_kelompok`, `nup`, `merk` | string | | Identitas barang |
| `kondisi` | string | | Baik / Rusak Ringan / Rusak Berat |
| `kuantitas` | int | | Jumlah |
| `status_penggunaan`, `thn_buat` | string | | Info umum |
| `no_mesin`, `no_rangka`, `no_polisi` | string | | Identitas kendaraan |
| `daya_mesin`, `bhn_bakar` | string | | Spesifikasi |
| `nilai_perolehan`, `nilai_penyusutan`, `nilai_buku` | decimal | | Nilai aset |
| `tgl_tarik`, `tanggal_perolehan` | date | | Tanggal |
| `nama_kl`, `nama_kpknl`, `nama_satker` | string | | Info instansi |
| `created_at`, `updated_at` | datetime | auto | Timestamp |

---

## 9. Menjalankan API

### Mode terhubung ke `mapu` (di mesin ini)

Pastikan database `mapu` jalan, lalu:
```bash
cd mapu
docker-compose up -d db          # database
cd api-kendaraan
docker-compose up -d --build     # API di port 8081
```

### Mode portable (laptop lain, DB sendiri)

Lihat [CARA-TEST-LAPTOP-LAIN.md](CARA-TEST-LAPTOP-LAIN.md):
```bash
cd api-kendaraan
docker-compose -f docker-compose.portable.yml up -d --build
```

Hentikan:
```bash
docker-compose down
```

---

## 10. Konfigurasi (`.env`)

| Kunci | Contoh | Keterangan |
|-------|--------|------------|
| `database.default.hostname` | `db` | `db` (Docker) atau `localhost` (host) |
| `database.default.database` | `mapu` | Nama database |
| `database.default.charset` | `utf8` | **Wajib** (default CI4 `utf8mb4` ditolak PostgreSQL) |
| `api.jwtSecret` | *(acak)* | Kunci tanda tangan token — **rahasia**, jangan dibagikan |
| `api.jwtTTL` | `3600` | Umur token (detik) |

---

## 11. Keamanan & Batasan

- **Hanya baca + tambah.** Tidak ada endpoint ubah/hapus → data tidak bisa
  dimodifikasi atau dihapus lewat API.
- **Token pendek (1 jam)** — mengurangi risiko bila token bocor.
- **Cabut akses vendor** → non-aktifkan/hapus akunnya di tabel `users`.
- **Batalkan semua token** → ganti `api.jwtSecret` di `.env`.
- **Verifikasi password** kompatibel `Myth\Auth`
  (`password_verify(base64_encode(hash('sha384', $pw, true)), $hash)`), sehingga
  akun `users` dipakai tanpa mengubah kode `mapu`.

---

## 12. Catatan Data

Isi tabel `alat_angkutan` saat ini masih **data dummy** (contoh angka: 1000 baris).
Data asli menyusul. API bersifat **data-agnostic** — saat data diganti, tidak ada
kode yang perlu diubah.

---

## Lampiran — Uji Cepat (curl)

```bash
# 1. Ambil token
TOKEN=$(curl -s -X POST http://localhost:8081/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"login":"USERNAME","password":"PASSWORD"}' \
  | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

# 2. Ambil data
curl -s -H "Authorization: Bearer $TOKEN" \
  "http://localhost:8081/api/v1/kendaraan?per_page=5"
```

Untuk pengujian via Postman, lihat panduan di
`../../tahapan/08-postman.md` dan koleksi
`../../tahapan/postman/MAPU-API-Kendaraan.postman_collection.json`.
