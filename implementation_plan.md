# Implementation Plan — Fitur Baru JARAK C06 (Versi 2)

## Deskripsi Umum

Penambahan fitur keamanan dan integritas data pada aplikasi Task Management JARAK C06. Fitur-fitur baru mencakup:

1. **Auto-Ownership** — Saat pengguna membuat daftar tugas baru, otomatis menjadi pemilik (owner)
2. **Cascade Delete Atomik** — Saat pengguna menghapus daftar yang dimilikinya, seluruh tugas dan keanggotaan terkait ikut terhapus secara atomik menggunakan database transaction
3. **Authorization Enforcement** — Permintaan dari pengguna yang tidak berwenang ditolak (403 Forbidden)
4. **Input Validation & Prepared Statement** — Semua input pengguna divalidasi dan query menggunakan prepared statement (Eloquent ORM)

---

## Analisis Kondisi Saat Ini

### Struktur Database

| Tabel | Kolom Utama | Foreign Key |
|-------|-------------|-------------|
| `users` | id, name, email, password, role | — |
| `lists` | id, name, user_id, timestamps | `user_id → users.id` (CASCADE DELETE) |
| `list_user` | id, list_id, user_id, role, timestamps | `list_id → lists.id` (CASCADE), `user_id → users.id` (CASCADE) |
| `tasks` | id, list_id, title, description, deadline, priority, is_completed, timestamps | `list_id → lists.id` (CASCADE DELETE) |

### File yang Sudah Ada

| File | Fungsi | Status |
|------|--------|--------|
| [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php) | CRUD daftar tugas | Perlu diperbarui |
| [TaskController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php) | CRUD tugas | Perlu diperbarui |
| [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php) | Kelola kolaborator | Perlu diperbarui |
| [TaskList.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Models/TaskList.php) | Model TaskList | Perlu diperbarui |
| [Task.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Models/Task.php) | Model Task | OK |
| [User.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Models/User.php) | Model User | OK |

### Temuan Penting

> [!NOTE]
> **Cascade delete sudah dikonfigurasi di level database** melalui migration:
> - `lists.user_id` → `cascadeOnDelete()` ke `users`
> - `list_user.list_id` → `cascadeOnDelete()` ke `lists`
> - `tasks.list_id` → `cascadeOnDelete()` ke `lists`
>
> Artinya, saat sebuah record `lists` dihapus, semua `tasks` dan `list_user` terkait **otomatis terhapus di level database**. Namun, kita tetap perlu membungkus operasi di dalam `DB::transaction()` untuk menjamin atomicity jika ada logic tambahan.

> [!IMPORTANT]
> **Auto-ownership sudah sebagian terimplementasi** di `TaskListController@store` — sudah mengassign `user_id => auth()->id()`. Yang perlu ditambahkan adalah juga memasukkan owner ke tabel `list_user` dengan role `owner` secara atomik.

> [!WARNING]
> **Prepared statement sudah otomatis digunakan oleh Laravel Eloquent/Query Builder**. Semua query melalui Eloquent (`create()`, `where()`, `update()`, dll) secara default menggunakan PDO prepared statement. Yang perlu dipastikan adalah TIDAK ada raw query tanpa binding.

---

## Proposed Changes

### Komponen 1: Model — Cascade Delete Events

#### [MODIFY] [TaskList.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Models/TaskList.php)

Tambahkan `boot()` method dengan event `deleting` untuk menghapus relasi secara eksplisit sebelum model dihapus (sebagai safety net di atas database-level cascade):

```php
protected static function boot()
{
    parent::boot();

    static::deleting(function (TaskList $taskList) {
        // Hapus semua tasks terkait
        $taskList->tasks()->delete();
        // Hapus semua keanggotaan (pivot list_user)
        $taskList->collaborators()->detach();
    });
}
```

> [!NOTE]
> Meskipun database cascade sudah menghandle ini, menambahkan logic di level Eloquent memastikan event-event model lain (jika ada) tetap terpicu, dan memberikan transparansi di kode.

---

### Komponen 2: Controller — Transaction, Authorization, Validation

#### [MODIFY] [TaskListController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskListController.php)

**Perubahan pada method `store()`:**
- Bungkus pembuatan list + auto-assign owner ke `list_user` dalam `DB::transaction()`
- Setelah list dibuat, otomatis tambahkan record di `list_user` dengan role `owner`

```php
use Illuminate\Support\Facades\DB;

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
    ]);

    DB::transaction(function () use ($validated) {
        $list = TaskList::create([
            'name' => $validated['name'],
            'user_id' => auth()->id(),
        ]);

        // Auto-assign owner ke tabel pivot list_user
        $list->collaborators()->attach(auth()->id(), ['role' => 'owner']);
    });

    return back()->with('success', 'List created.');
}
```

**Perubahan pada method `destroy()`:**
- Bungkus penghapusan dalam `DB::transaction()`
- Cek otorisasi bahwa hanya pemilik yang boleh menghapus
- Proses hapus secara atomik (tasks + membership + list)

```php
public function destroy(TaskList $list)
{
    if ($list->user_id !== auth()->id()) {
        abort(403, 'Anda tidak berwenang menghapus daftar ini.');
    }

    DB::transaction(function () use ($list) {
        // Hapus semua tasks dalam list
        $list->tasks()->delete();
        // Hapus semua keanggotaan
        $list->collaborators()->detach();
        // Hapus list itu sendiri
        $list->delete();
    });

    return back()->with('success', 'List and all related data deleted.');
}
```

---

#### [MODIFY] [TaskController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php)

**Perubahan pada method `store()`:**
- Bungkus dalam `DB::transaction()`

```php
use Illuminate\Support\Facades\DB;

public function store(Request $request, TaskList $list)
{
    $this->authorizeListAccess($list);

    $validated = $request->validate([
        'title'    => ['required', 'string', 'max:255'],
        'deadline' => ['nullable', 'date'],
        'priority' => ['required', 'in:low,medium,high'],
    ]);

    DB::transaction(function () use ($list, $validated) {
        $list->tasks()->create($validated);
    });

    return back()->with('success', 'Task added.');
}
```

**Perubahan pada method `update()`:**
- Bungkus dalam `DB::transaction()`

**Perubahan pada method `toggle()`:**
- Bungkus dalam `DB::transaction()`

**Perubahan pada method `destroy()`:**
- Bungkus dalam `DB::transaction()`

---

#### [MODIFY] [CollaborationController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/CollaborationController.php)

**Perubahan pada method `invite()`:**
- Bungkus `attach()` dalam `DB::transaction()`
- Tambahkan validasi untuk mencegah duplikat

**Perubahan pada method `remove()`:**
- Bungkus `detach()` dalam `DB::transaction()`

---

#### [MODIFY] [Admin/UserController.php](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/Admin/UserController.php)

**Perubahan pada method `store()` dan `destroy()`:**
- Bungkus dalam `DB::transaction()` untuk konsistensi

---

### Komponen 3: Verifikasi Prepared Statement

#### Audit Seluruh Query

| File | Query Method | Prepared Statement? |
|------|-------------|---------------------|
| TaskListController | `TaskList::create()`, `$list->update()`, `$list->delete()` | ✅ Ya (Eloquent) |
| TaskController | `$list->tasks()->create()`, `$task->update()`, `$task->delete()` | ✅ Ya (Eloquent) |
| CollaborationController | `TaskList::findOrFail()`, `$list->collaborators()->attach/detach()` | ✅ Ya (Eloquent) |
| Admin/UserController | `User::create()`, `$user->delete()` | ✅ Ya (Eloquent) |
| DashboardController | `Task::whereIn()`, `->where()` | ✅ Ya (Query Builder) |
| ProgressController | `TaskList::where()`, `->whereHas()` | ✅ Ya (Query Builder) |

> [!WARNING]
> **Satu-satunya raw query yang ditemukan** ada di [TaskController.php L27](file:///c:/Users/azkaw/Desktop/UNDIP%20PRAKTIKUM/Ppk/2/jarak-c06/app/Http/Controllers/TaskController.php#L27):
> ```php
> ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
> ```
> Query ini **aman** karena tidak menerima input user — nilainya di-hardcode. Namun, untuk konsistensi dan kompatibilitas lintas database, sebaiknya diganti dengan `orderByRaw()` menggunakan binding atau pendekatan `CASE WHEN`.

**Perubahan yang diusulkan untuk `orderByRaw`:**

```php
->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
```

---

## Ringkasan Perubahan per File

| No | File | Perubahan | SRS Terkait |
|----|------|-----------|-------------|
| 1 | `TaskList.php` | Tambah `boot()` deleting event | SRS-3 |
| 2 | `TaskListController.php` | Transaction di `store()` + auto-ownership pivot, transaction di `destroy()` + cascade manual | SRS-3 |
| 3 | `TaskController.php` | Transaction di `store()`, `update()`, `toggle()`, `destroy()` | SRS-4 |
| 4 | `CollaborationController.php` | Transaction di `invite()`, `remove()` | SRS-7 |
| 5 | `Admin/UserController.php` | Transaction di `store()`, `destroy()` | SRS-2 |
| 6 | `README.md` | Update dokumentasi proyek | — |

---

## Verification Plan

### Automated Tests

Tidak ada unit test framework yang ter-setup saat ini. Verifikasi dilakukan secara manual.

### Manual Verification

1. **Test Auto-Ownership:**
   - Login sebagai user biasa
   - Buat list baru
   - Verifikasi bahwa record muncul di tabel `lists` dengan `user_id` yang benar
   - Verifikasi bahwa record muncul di tabel `list_user` dengan `role = 'owner'`

2. **Test Cascade Delete Atomik:**
   - Buat list dengan beberapa tasks dan kolaborator
   - Hapus list tersebut
   - Verifikasi bahwa semua tasks terhapus dari tabel `tasks`
   - Verifikasi bahwa semua record di `list_user` terkait terhapus
   - Verifikasi bahwa list terhapus dari tabel `lists`

3. **Test Authorization:**
   - Login sebagai user A, buat list
   - Login sebagai user B, coba akses edit/delete list milik user A
   - Verifikasi mendapat response 403 Forbidden

4. **Test Prepared Statement:**
   - Coba input dengan karakter khusus SQL (e.g., `'; DROP TABLE users; --`)
   - Verifikasi bahwa input disimpan sebagai string biasa, bukan dieksekusi sebagai SQL

5. **Test Transaction Rollback:**
   - Simulasikan kegagalan di tengah proses (misal, matikan koneksi DB setelah hapus tasks tapi sebelum hapus list)
   - Verifikasi bahwa semua perubahan di-rollback

---

## Risiko & Rollback Plan

| Risiko | Dampak | Mitigasi |
|--------|--------|---------|
| Transaction deadlock pada concurrent delete | Sedang | MySQL InnoDB default timeout 50 detik, cukup untuk operasi ini |
| Duplikat entry di `list_user` saat auto-ownership | Rendah | Tabel `list_user` sudah punya constraint `UNIQUE(list_id, user_id)` |
| Breaking change pada existing data | Rendah | Tidak ada perubahan schema, hanya logic controller |

**Rollback:** `git revert <commit>` — tidak ada migration baru, jadi rollback cukup revert kode saja.
