# SiLiar — Sistem Pelaporan Sampah Liar

**Universitas Islam Negeri Siber Syekh Nurjati Cirebon**
Jurusan Informatika | Matakuliah: Pemrograman Web
Dosen: Dr. Saluky, M.Kom | Kelas C | Proyek 2

---

## Deskripsi Proyek

SiLiar adalah aplikasi berbasis web yang memungkinkan warga Kota Cirebon untuk melaporkan keberadaan tumpukan sampah liar atau TPS ilegal di sekitar mereka. Setiap laporan akan ditindaklanjuti oleh petugas kebersihan dan dapat dipantau statusnya secara langsung.

---

## Fitur Aplikasi

- **GPS Lokasi Otomatis** — Koordinat latitude dan longitude terdeteksi otomatis dari browser saat membuat laporan
- **Upload Foto Kondisi** — Pelapor dapat melampirkan foto bukti sampah (JPG/PNG/WEBP, maks. 5MB)
- **Tracking Pekerjaan** — Warga dapat memantau status laporan: Menunggu → Diproses → Selesai
- **Dashboard Admin** — Admin dapat mengelola seluruh laporan, memperbarui status, dan menambahkan catatan tindakan
- **Grafik Pelaporan** — Visualisasi data berupa grafik donut, pie chart, line chart tren bulanan, dan bar chart statistik

---

## Teknologi yang Digunakan

- PHP 8.x (native, tanpa framework)
- MySQL / MariaDB
- HTML5, CSS3, JavaScript (Vanilla)
- Chart.js 4.4 (grafik statistik)
- Google Fonts (Plus Jakarta Sans)
- XAMPP sebagai server lokal

---

## Cara Instalasi

### Langkah 1 — Persiapan
Pastikan XAMPP sudah terinstall dan aktifkan **Apache** serta **MySQL** dari XAMPP Control Panel.

### Langkah 2 — Salin File
Ekstrak folder proyek ke dalam direktori htdocs:
```
C:\xampp\htdocs\sampah_liar\
```

### Langkah 3 — Buat Database
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik **New** di panel kiri
3. Isi nama database: `db_sampah_liar`
4. Klik **Create**

### Langkah 4 — Import Database
1. Pilih database `db_sampah_liar` di panel kiri
2. Klik tab **Import**
3. Klik **Choose File** → pilih file `database.sql`
4. Klik **Go**

### Langkah 5 — Jalankan Aplikasi
Buka browser dan akses:
```
http://localhost/sampah_liar/
```

---

## Akun Admin

| Username | Password |
|----------|----------|
| admin    | admin123 |

Halaman login admin: `http://localhost/sampah_liar/index.php?page=login`

---

## Struktur File

```
sampah_liar/
├── index.php                  ← Router utama (semua halaman diakses lewat sini)
├── database.sql               ← File SQL untuk membuat database dan data awal
├── README.md                  ← Dokumentasi proyek ini
├── uploads/                   ← Folder penyimpanan foto laporan
├── includes/
│   ├── config.php             ← Konfigurasi koneksi database dan konstanta
│   ├── functions.php          ← Fungsi pembantu (upload, flash, auth, format tanggal)
│   ├── header.php             ← Template navbar dan head HTML
│   └── footer.php             ← Template penutup HTML dan JavaScript
├── pages/
│   ├── home.php               ← Beranda (statistik, grafik, laporan terbaru)
│   ├── lapor.php              ← Form membuat laporan baru
│   ├── laporan.php            ← Daftar semua laporan dengan filter dan pencarian
│   ├── detail.php             ← Detail laporan dan tracking status penanganan
│   ├── login.php              ← Halaman login admin
│   ├── dashboard.php          ← Dashboard admin untuk mengelola laporan
│   └── grafik.php             ← Halaman grafik dan statistik visual
└── assets/
    ├── css/style.css          ← Stylesheet utama tampilan aplikasi
    └── js/main.js             ← JavaScript (GPS, preview foto, animasi chart)
```

---

## Daftar URL Halaman

| Halaman | URL |
|---------|-----|
| Beranda | `http://localhost/sampah_liar/` |
| Buat Laporan | `http://localhost/sampah_liar/index.php?page=lapor` |
| Daftar Laporan | `http://localhost/sampah_liar/index.php?page=laporan` |
| Detail Laporan | `http://localhost/sampah_liar/index.php?page=detail&id=1` |
| Login Admin | `http://localhost/sampah_liar/index.php?page=login` |
| Dashboard Admin | `http://localhost/sampah_liar/index.php?page=dashboard` |
| Grafik | `http://localhost/sampah_liar/index.php?page=grafik` |

---

## Konfigurasi Database

Jika username atau password MySQL berbeda, ubah file `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // sesuaikan username MySQL Anda
define('DB_PASS', '');           // isi jika MySQL Anda menggunakan password
define('DB_NAME', 'db_sampah_liar');
```
