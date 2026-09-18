# Pembagian Tugas — Fitur Baru JARAK C06 (Versi 2)

## Daftar SRS (Lengkap)

| Kode | Fitur | Status |
|------|-------|--------|
| SRS-1 | User bisa register/login | ✅ Sudah ada |
| SRS-2 | Admin bisa nambah & menghapus pengguna | ✅ Sudah ada |
| SRS-3 | User bisa bikin, edit, hapus **daftar tugas (list)** | 🔄 Perlu diperbarui |
| SRS-4 | User bisa bikin, edit, hapus **tugas** di dalam list | 🔄 Perlu diperbarui |
| SRS-5 | Tugas punya **deadline & prioritas** | ✅ Sudah ada |
| SRS-6 | User bisa **menandai tugas selesai** | ✅ Sudah ada |
| SRS-7 | Pemilik list bisa **menambahkan user lain** ke listnya (kolaborasi) | 🔄 Perlu diperbarui |
| SRS-8 | User bisa **memantau progres** penyelesaian tugas (persen selesai per list) | ✅ Sudah ada |

---

## Deskripsi Fitur Baru (Versi 2)

| No | Fitur Baru | Deskripsi | SRS Terkait |
|----|-----------|-----------|-------------|
| F1 | **Auto-Ownership** | Pengguna yang membuat daftar tugas baru otomatis menjadi pemilik (owner) dan tercatat di tabel pivot `list_user` | SRS-3 |
| F2 | **Cascade Delete Atomik** | Saat pemilik menghapus daftar, seluruh tugas dan keanggotaan terhapus dalam satu transaksi DB | SRS-3 |
| F3 | **Database Transaction** | Setiap proses write (create, update, delete) dibungkus `DB::transaction()` agar atomik — jika salah satu gagal, seluruhnya dibatalkan | SRS-3, SRS-4, SRS-7 |
| F4 | **Authorization Enforcement** | Permintaan dari pengguna yang tidak berwenang ditolak dengan response 403 Forbidden | SRS-2, SRS-3, SRS-4, SRS-7 |
| F5 | **Prepared Statement** | Semua query menggunakan Eloquent ORM / Query Builder yang otomatis menggunakan PDO prepared statement | ALL |

---

## Pembagian Tugas per Programmer

### 👨‍💻 Programmer 1 — Admin & User Management

**Penanggung jawab:** Fitur admin (SRS-1, SRS-2)

| No | Task | File | Detail Perubahan |
|----|------|------|-----------------|
| 1.1 | Tambah `DB::transaction()` pada `store()` | [Admin/UserController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/Admin/UserController.php) | Bungkus `User::create()` dalam transaction |
| 1.2 | Tambah `DB::transaction()` pada `destroy()` | [Admin/UserController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/Admin/UserController.php) | Bungkus `$user->delete()` dalam transaction |
| 1.3 | Verifikasi prepared statement | Semua file terkait | Audit bahwa tidak ada raw query tanpa binding |
| 1.4 | Verifikasi authorization | [AdminMiddleware.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Middleware/AdminMiddleware.php) | Pastikan middleware `admin` sudah benar melindungi route |

**Fitur terkait:** F3, F4, F5

---

### 👨‍💻 Programmer 2 — Task List & Task CRUD

**Penanggung jawab:** Fitur daftar tugas dan tugas (SRS-3, SRS-4, SRS-5, SRS-6)

| No | Task | File | Detail Perubahan |
|----|------|------|-----------------|
| 2.1 | **Auto-Ownership**: Ubah `store()` agar insert ke `list_user` dengan role `owner` | [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php) | Setelah `TaskList::create()`, tambahkan `$list->collaborators()->attach(auth()->id(), ['role' => 'owner'])` |
| 2.2 | **Cascade Delete Atomik**: Ubah `destroy()` agar hapus tasks + membership + list dalam transaction | [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php) | Bungkus dalam `DB::transaction()`, hapus tasks → detach collaborators → delete list |
| 2.3 | Tambah `DB::transaction()` di `store()` TaskListController | [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php) | Bungkus pembuatan list + auto-ownership dalam transaction |
| 2.4 | Tambah `DB::transaction()` di `update()` TaskListController | [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php) | Bungkus `$list->update()` dalam transaction |
| 2.5 | Tambah `DB::transaction()` di semua method TaskController | [TaskController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php) | Bungkus `store()`, `update()`, `toggle()`, `destroy()` dalam transaction |
| 2.6 | Tambah model event `deleting` di TaskList | [TaskList.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Models/TaskList.php) | Tambah `boot()` method dengan event deleting untuk cascade di level Eloquent |
| 2.7 | Ganti `orderByRaw("FIELD(...)")` | [TaskController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php#L27) | Ganti dengan `CASE WHEN` untuk kompatibilitas |
| 2.8 | Verifikasi authorization di semua method | [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php), [TaskController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php) | Pastikan cek `$list->user_id !== auth()->id()` dan `authorizeListAccess()` ada di semua method |

**Fitur terkait:** F1, F2, F3, F4, F5

---

### 👨‍💻 Programmer 3 — Kolaborasi & Progress

**Penanggung jawab:** Fitur kolaborasi dan progress (SRS-7, SRS-8)

| No | Task | File | Detail Perubahan |
|----|------|------|-----------------|
| 3.1 | Tambah `DB::transaction()` di `invite()` | [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php) | Bungkus `$list->collaborators()->attach()` dalam transaction |
| 3.2 | Tambah `DB::transaction()` di `remove()` | [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php) | Bungkus `$list->collaborators()->detach()` dalam transaction |
| 3.3 | Tambah validasi duplikat kolaborator di `invite()` | [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php) | Cek apakah user sudah menjadi kolaborator sebelum attach |
| 3.4 | Verifikasi authorization | [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php) | Pastikan hanya owner yang bisa invite/remove (sudah ada, perlu di-review) |
| 3.5 | Verifikasi prepared statement | [ProgressController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/ProgressController.php) | Audit query di progress dashboard |

**Fitur terkait:** F3, F4, F5

---

## Matriks Tanggung Jawab (RACI)

| Fitur | Programmer 1 | Programmer 2 | Programmer 3 |
|-------|:---:|:---:|:---:|
| F1 - Auto-Ownership | I | **R/A** | I |
| F2 - Cascade Delete Atomik | I | **R/A** | I |
| F3 - DB Transaction | **R/A** (admin) | **R/A** (list/task) | **R/A** (collab) |
| F4 - Authorization | **R/A** (admin) | **R/A** (list/task) | **R/A** (collab) |
| F5 - Prepared Statement | **R/A** (admin) | **R/A** (list/task) | **R/A** (collab) |

> **R** = Responsible (mengerjakan), **A** = Accountable (bertanggung jawab), **I** = Informed

---

## Timeline Estimasi

| Fase | Durasi | Keterangan |
|------|--------|------------|
| Implementasi kode | 1-2 hari | Semua programmer mengerjakan bagiannya masing-masing |
| Testing manual | 1 hari | Masing-masing programmer menguji fiturnya |
| Code review & merge | 1 hari | Review silang antar programmer, merge ke branch `main` |
| **Total** | **3-4 hari** | — |
