-- ============================================================
-- SISTEM PELAPORAN SAMPAH LIAR (SiLiar)
-- Universitas Islam Negeri Siber Syekh Nurjati Cirebon
-- Jurusan Informatika | Matakuliah: Pemrograman Web
-- Dosen: Dr. Saluky, M.Kom | Kelas C | Proyek 2
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_sampah_liar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_sampah_liar;

-- Tabel admin
CREATE TABLE IF NOT EXISTS admin (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(50) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    nama       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel laporan
CREATE TABLE IF NOT EXISTS laporan (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    nama_pelapor   VARCHAR(100) NOT NULL,
    telepon        VARCHAR(20) NOT NULL,
    alamat         TEXT NOT NULL,
    jenis          ENUM('Tumpukan Sampah','TPS Ilegal') NOT NULL,
    deskripsi      TEXT NOT NULL,
    foto           VARCHAR(255) DEFAULT NULL,
    latitude       VARCHAR(30) DEFAULT NULL,
    longitude      VARCHAR(30) DEFAULT NULL,
    status         ENUM('Menunggu','Diproses','Selesai') DEFAULT 'Menunggu',
    catatan_admin  TEXT DEFAULT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DATA AWAL
-- ============================================================

-- Admin: username=admin | password=admin123 (MD5)
INSERT INTO admin (username, password, nama) VALUES
('admin', '0192023a7bbd73250516f069df18b500', 'Administrator');

-- Data contoh laporan
INSERT INTO laporan (nama_pelapor, telepon, alamat, jenis, deskripsi, latitude, longitude, status, catatan_admin, created_at) VALUES
('Budi Santoso',  '08123456789', 'Jl. Siliwangi No. 12, Cirebon',   'TPS Ilegal',       'Tumpukan sampah besar di pinggir jalan, menimbulkan bau tidak sedap dan mengundang lalat.',  '-6.7320', '108.5523', 'Menunggu', NULL,                                                  '2025-01-10 08:30:00'),
('Siti Rahayu',   '08567890123', 'Jl. Kesambi No. 5, Cirebon',      'Tumpukan Sampah',  'Sampah berserakan di depan sekolah sejak 3 hari lalu, mengganggu aktivitas belajar.',         '-6.7280', '108.5601', 'Diproses', 'Petugas kebersihan sudah dikirim ke lokasi.',          '2025-01-11 10:15:00'),
('Ahmad Fauzi',   '08234567891', 'Jl. Panjunan No. 8, Cirebon',     'TPS Ilegal',       'TPS ilegal di lahan kosong, warga sering membuang sampah sembarangan di sini.',               '-6.7350', '108.5480', 'Selesai',  'Sampah sudah dibersihkan, dipasang papan larangan.',   '2025-01-08 14:20:00'),
('Dewi Lestari',  '08991234567', 'Jl. Ciremai Raya No. 3, Cirebon', 'Tumpukan Sampah',  'Sampah menumpuk di dekat got, air jadi mampet dan berbau.',                                   '-6.7410', '108.5550', 'Menunggu', NULL,                                                  '2025-01-14 07:45:00'),
('Rudi Hartono',  '08765432109', 'Jl. Dr. Cipto No. 20, Cirebon',   'TPS Ilegal',       'Ada oknum yang membuang sampah industri di lahan kosong secara diam-diam malam hari.',        '-6.7295', '108.5612', 'Diproses', 'Koordinasi dengan Satpol PP untuk penindakan.',        '2025-01-15 09:00:00');
