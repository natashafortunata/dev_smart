# dev_smart — SPK Metode SMART (Laravel + PostgreSQL)

Sistem Pendukung Keputusan berbasis web menggunakan metode **SMART**
(*Simple Multi Attribute Rating Technique*), dibangun dengan **Laravel 12**,
database **PostgreSQL** (dikelola lewat **pgAdmin 4**), dikembangkan di **VS Code**,
dan versinya disimpan di **GitHub** (repo `dev_smart`).

## Fitur

| Modul | Keterangan |
|---|---|
| Login & Role | `admin` (kelola semua data) dan `pimpinan` (lihat perhitungan & hasil) |
| Kriteria | CRUD kriteria: kode, nama, bobot (1–100), jenis **benefit/cost** |
| Sub Kriteria | Skala nilai per kriteria (mis. Sangat Baik = 100). Kriteria tanpa sub kriteria dinilai dengan angka langsung |
| Alternatif | CRUD alternatif + pencarian |
| Penilaian | Input nilai setiap alternatif terhadap setiap kriteria |
| Perhitungan | Menampilkan seluruh langkah SMART (normalisasi bobot → matriks → utility → nilai akhir → ranking) |
| Hasil & Ranking | Hasil tersimpan di tabel `hasil`, grafik batang, dan **cetak laporan** |
| Pengguna | Manajemen akun admin/pimpinan |

## Rumus SMART yang dipakai

1. **Normalisasi bobot**: `w_j = bobot_j / Σ bobot`
2. **Nilai utility**
   - Benefit: `u_ij = (C_ij − C_min) / (C_max − C_min)`
   - Cost: `u_ij = (C_max − C_ij) / (C_max − C_min)`
   - Jika `C_max = C_min` maka `u_ij = 1`
3. **Nilai akhir**: `V_i = Σ (w_j × u_ij)`
4. **Perankingan**: urutkan `V_i` dari yang terbesar

Logika perhitungan ada di `app/Services/SmartService.php` dan diuji di
`tests/Feature/SmartTest.php` (dicocokkan dengan hitungan manual).

## Struktur Database (PostgreSQL)

```
users        (id, name, email, role[admin|pimpinan], password, ...)
kriteria     (id, kode, nama, bobot, jenis[benefit|cost], keterangan)
sub_kriteria (id, kriteria_id → kriteria, nama, nilai)
alternatif   (id, kode, nama, keterangan)
penilaian    (id, alternatif_id → alternatif, kriteria_id → kriteria,
              sub_kriteria_id → sub_kriteria (nullable), nilai)   UNIQUE(alternatif_id, kriteria_id)
hasil        (id, alternatif_id → alternatif, nilai_akhir, peringkat)
```

---

## Instalasi di Komputer (Windows / macOS / Linux)

### Prasyarat

- PHP 8.2+ dengan ekstensi `pdo_pgsql` dan `pgsql` aktif
  (Windows/XAMPP: buka `php.ini`, hapus `;` di depan `extension=pdo_pgsql` dan `extension=pgsql`)
- Composer
- PostgreSQL 14+ dan **pgAdmin 4**
- Git dan VS Code

### 1. Clone repo

```bash
git clone https://github.com/<username-anda>/dev_smart.git
cd dev_smart
composer install
```

### 2. Buat database di pgAdmin 4

1. Buka pgAdmin 4 → klik kanan **Servers › PostgreSQL › Databases** → **Create › Database…**
2. Nama database: `dev_smart` → **Save**

### 3. Atur file `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Lalu ubah bagian database di `.env` sesuai password PostgreSQL Anda:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dev_smart
DB_USERNAME=postgres
DB_PASSWORD=password_postgres_anda
```

### 4. Buat tabel + data contoh

```bash
php artisan migrate --seed
```

> Alternatif tanpa artisan: di pgAdmin buka database `dev_smart` → **Query Tool** →
> buka file `database/sql/dev_smart.sql` → **Execute (F5)**.

### 5. Jalankan aplikasi

```bash
php artisan serve
```

Buka http://127.0.0.1:8000

| Role | Email | Password |
|---|---|---|
| Admin | `admin@smart.test` | `password` |
| Pimpinan | `pimpinan@smart.test` | `password` |

> Segera ganti password lewat menu **Pengguna** setelah login.

### Data contoh

`database/seeders/ContohDataSeeder.php` berisi **data contoh** (5 kriteria, 5 alternatif)
agar sistem bisa langsung dicoba. Ganti kriteria, bobot, sub kriteria, dan alternatif
sesuai **dokumen analisis sistem** Anda — lewat menu aplikasi, atau edit seeder lalu
jalankan `php artisan migrate:fresh --seed`.

---

## Menghubungkan PostgreSQL ke VS Code

1. Buka folder project di VS Code. VS Code akan menyarankan ekstensi dari
   `.vscode/extensions.json` → klik **Install All** (SQLTools + driver PostgreSQL, dll).
2. Koneksi **"dev_smart (PostgreSQL / pgAdmin 4)"** sudah disiapkan di `.vscode/settings.json`.
   Buka ikon **SQLTools** di sidebar → klik koneksi tersebut → masukkan password PostgreSQL.
3. Tabel `kriteria`, `alternatif`, `penilaian`, `hasil`, dll. dapat dilihat dan di-query
   langsung dari VS Code; perubahan yang sama tampil di pgAdmin 4 karena keduanya
   terhubung ke server PostgreSQL yang sama.

## Menyimpan Setiap Update ke GitHub

Setelah setiap perubahan:

```bash
git add .
git commit -m "Deskripsi perubahan"
git push
```

Di VS Code bisa juga lewat panel **Source Control** (Ctrl+Shift+G): tulis pesan → **Commit** → **Sync Changes**.

Workflow `.github/workflows/tests.yml` akan otomatis menjalankan test dengan PostgreSQL
setiap kali ada push ke branch `main`.

## Testing

```bash
php artisan test
```
