# SiLiar — Sistem Pelaporan Sampah Liar 🗑️

**Universitas Islam Negeri Siber Syekh Nurjati Cirebon**  
Jurusan Informatika | Matakuliah: Pemrograman Web  
Dosen: Dr. Saluky, M.Kom | Kelas C | **Proyek 2**

---

## Deskripsi
Aplikasi berbasis web untuk melaporkan tumpukan sampah liar atau TPS ilegal kepada Dinas Kebersihan Kota Cirebon. Warga dapat melaporkan kondisi sampah lengkap dengan foto dan lokasi GPS otomatis.

---

## Fitur Aplikasi
| Fitur | Keterangan |
|---|---|
| 📍 GPS Lokasi Otomatis | Koordinat latitude & longitude terdeteksi otomatis dari browser |
| 📸 Foto Kondisi | Upload foto bukti format JPG/PNG/WEBP maks 5MB |
| 🔄 Tracking Pekerjaan | Warga dapat melihat status: Menunggu → Diproses → Selesai |
| 📊 Dashboard Admin | Admin mengelola laporan, update status, tambah catatan |
| 📈 Grafik Pelaporan | Grafik donut, pie, line chart, dan progress bar statistik |

---

## Cara Instalasi

### 1. Persyaratan
- XAMPP (PHP 8.x + MySQL/MariaDB)
- Browser modern (Chrome/Firefox)

### 2. Salin File
Ekstrak folder ke:
```
C:\xampp\htdocs\sampah_liar\
```

### 3. Buat Database
1. Buka **http://localhost/phpmyadmin**
2. Klik **New** → nama database: `db_sampah_liar` → klik **Create**
3. Pilih database `db_sampah_liar` → tab **Import**
4. Pilih file `database.sql` → klik **Go**

### 4. Jalankan Aplikasi
Buka browser:
```
http://localhost/sampah_liar/
```

---

## Akun Admin

| Username | Password |
|---|---|
| `admin` | `admin123` |

Login melalui: `http://localhost/sampah_liar/index.php?page=login`

---

## Struktur File
```
sampah_liar/
├── index.php               ← Router utama (semua halaman lewat sini)
├── database.sql            ← File SQL database
├── README.md               ← Dokumentasi ini
├── uploads/                ← Folder foto laporan (otomatis dibuat)
├── includes/
│   ├── config.php          ← Konfigurasi database & konstanta
│   ├── functions.php       ← Fungsi helper (upload, flash, auth, dll)
│   ├── header.php          ← Template navbar & head HTML
│   └── footer.php          ← Template penutup HTML & script
├── pages/
│   ├── home.php            ← Beranda (statistik + laporan terbaru)
│   ├── lapor.php           ← Form buat laporan baru
│   ├── laporan.php         ← Daftar semua laporan + filter + search
│   ├── detail.php          ← Detail laporan + tracking status
│   ├── login.php           ← Halaman login admin
│   ├── dashboard.php       ← Dashboard admin (kelola laporan)
│   └── grafik.php          ← Grafik & statistik visual
└── assets/
    ├── css/style.css       ← Stylesheet utama
    └── js/main.js          ← JavaScript (GPS, preview foto, bar chart)
```

---

## Cara Akses Halaman

| Halaman | URL |
|---|---|
| Beranda | `localhost/sampah_liar/` |
| Buat Laporan | `localhost/sampah_liar/index.php?page=lapor` |
| Daftar Laporan | `localhost/sampah_liar/index.php?page=laporan` |
| Login Admin | `localhost/sampah_liar/index.php?page=login` |
| Dashboard Admin | `localhost/sampah_liar/index.php?page=dashboard` |
| Grafik | `localhost/sampah_liar/index.php?page=grafik` |
