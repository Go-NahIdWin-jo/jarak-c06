# JARAK C06 — Aplikasi Manajemen Tugas Kolaboratif

Aplikasi web manajemen daftar tugas (task list) kolaboratif yang dibangun menggunakan Laravel. Pengguna dapat membuat daftar tugas, menambahkan tugas dengan deadline dan prioritas, berkolaborasi dengan pengguna lain, serta memantau progres penyelesaian tugas secara real-time.

## 📋 Daftar Fitur (SRS)

| Kode | Fitur | Status |
|------|-------|--------|
| SRS-1 | User bisa register/login | ✅ |
| SRS-2 | Admin bisa nambah & menghapus pengguna | ✅ |
| SRS-3 | User bisa bikin, edit, hapus **daftar tugas (list)** | ✅ |
| SRS-4 | User bisa bikin, edit, hapus **tugas** di dalam list | ✅ |
| SRS-5 | Tugas punya **deadline & prioritas** | ✅ |
| SRS-6 | User bisa **menandai tugas selesai** | ✅ |
| SRS-7 | Pemilik list bisa **menambahkan user lain** ke listnya (kolaborasi) | ✅ |
| SRS-8 | User bisa **memantau progres** penyelesaian tugas (persen selesai per list) | ✅ |

## 🆕 Fitur Baru — Versi 2

| No | Fitur | Deskripsi |
|----|-------|-----------|
| F1 | **Auto-Ownership** | Pengguna yang membuat daftar tugas baru otomatis menjadi pemilik dan tercatat di tabel keanggotaan (`list_user`) dengan role `owner` |
| F2 | **Cascade Delete Atomik** | Saat pemilik menghapus daftar tugas, seluruh tugas dan data keanggotaan terkait ikut terhapus dalam satu transaksi database yang atomik |
| F3 | **Database Transaction** | Setiap proses yang mengubah data (create, update, delete) dibungkus dalam `DB::transaction()` sehingga jika salah satu operasi gagal, seluruh perubahan otomatis dibatalkan (rollback) |
| F4 | **Authorization Enforcement** | Permintaan dari pengguna yang tidak berwenang ditolak dengan response 403 Forbidden. Hanya pemilik list yang dapat mengedit, menghapus, atau mengelola kolaborator |
| F5 | **Prepared Statement** | Seluruh query menggunakan Eloquent ORM dan Query Builder yang secara otomatis menggunakan PDO prepared statement untuk mencegah SQL injection |

## 🛠️ Tech Stack

- **Framework:** Laravel 12.x
- **Bahasa:** PHP 8.x
- **Database:** MySQL (dengan foreign key cascade)
- **Frontend:** Blade Template + Tailwind CSS
- **Authentication:** Laravel Breeze
- **Build Tool:** Vite

## 🗂️ Struktur Database

```
users
├── id (PK)
├── name
├── email
├── password
├── role (admin | user)
└── timestamps

lists
├── id (PK)
├── name
├── user_id (FK → users.id, CASCADE DELETE)
└── timestamps

list_user (pivot)
├── id (PK)
├── list_id (FK → lists.id, CASCADE DELETE)
├── user_id (FK → users.id, CASCADE DELETE)
├── role (viewer | member | owner)
└── timestamps

tasks
├── id (PK)
├── list_id (FK → lists.id, CASCADE DELETE)
├── title
├── description (nullable)
├── deadline (nullable)
├── priority (low | medium | high)
├── is_completed (boolean)
└── timestamps
```

## 👥 Tim Pengembang

| Programmer | Tanggung Jawab | SRS |
|------------|---------------|-----|
| **Programmer 1** | Admin & User Management (register, login, CRUD user) | SRS-1, SRS-2 |
| **Programmer 2** | Task List & Task CRUD (daftar tugas, tugas, deadline, prioritas, mark complete) | SRS-3, SRS-4, SRS-5, SRS-6 |
| **Programmer 3** | Kolaborasi & Progress Monitoring (invite kolaborator, tracking progres) | SRS-7, SRS-8 |

## 🚀 Instalasi & Setup

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/Go-NahIdWin-jo/jarak-c06.git
cd jarak-c06

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=jarak_c06
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migration & seeder
php artisan migrate --seed

# 8. Build assets
npm run dev

# 9. Jalankan server
php artisan serve
```

### Akses Aplikasi

- **URL:** `http://localhost:8000`
- **Admin default:** (sesuai seeder)
- **User default:** (sesuai seeder)

## 📄 Lisensi

Proyek ini dibuat untuk keperluan Praktikum Pemrograman Komputer (PPK) — Universitas Diponegoro.
