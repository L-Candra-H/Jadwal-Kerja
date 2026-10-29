-- ---------------------------------------------
-- ✅ SETUP AWAL
-- ---------------------------------------------
CREATE DATABASE IF NOT EXISTS jadwal_kerja;
USE jadwal_kerja;

-- ---------------------------------------------
-- 📍 UNIT KERJA
-- ---------------------------------------------
CREATE TABLE unit_kerja (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_unit_kerja VARCHAR(100) NOT NULL
);

-- ---------------------------------------------
-- 📍 JABATAN
-- ---------------------------------------------
CREATE TABLE jabatan (
  id INT PRIMARY KEY,
  nama_jabatan VARCHAR(50),
  keterangan VARCHAR(100)
);

INSERT INTO jabatan (id, nama_jabatan, keterangan) VALUES
(1, 'Admin', 'Admin'),
(2, 'Ka.Bid', 'Kepala Bidang'),
(3, 'Ka.Ru', 'Kepala Ruang'),
(4, 'Ka.Unit', 'Kepala Unit'),
(5, 'Koord.', 'Koordinator'),
(6, '-', 'Karyawan');

-- ---------------------------------------------
-- 📍 PEGAWAI
-- ---------------------------------------------
CREATE TABLE pegawai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(5) UNIQUE NOT NULL,
  nama VARCHAR(100),
  unit_kerja_id INT,
  jabatan_id INT,
  aktivasi TINYINT DEFAULT 1,
  sumber_data ENUM('akun_sendiri','petugas') DEFAULT 'petugas',
  FOREIGN KEY (unit_kerja_id) REFERENCES unit_kerja(id),
  FOREIGN KEY (jabatan_id) REFERENCES jabatan(id)
);

-- ---------------------------------------------
-- 📍 SHIFT KERJA
-- ---------------------------------------------
CREATE TABLE shift_kerja (
  id INT PRIMARY KEY,
  kode VARCHAR(10),
  keterangan VARCHAR(50),
  durasi DOUBLE NOT NULL,
  catatan TEXT,
  nomor INT
);

INSERT INTO shift_kerja (id, kode, keterangan, durasi, catatan, nomor) VALUES
(1, 'P', 'Pagi', 7, 'Jadwal Normal', 1),
(2, 'P1', 'Pagi1', 8.5, 'Jadwal Khusus 5 Hari Kerja', 2),
(3, 'P*', 'Midle Pagi', 7, 'Jadwal Normal Tengah', 3),
(4, 'S', 'Siang', 7, 'Jadwal Normal', 4),
(5, 'S*', 'Midle Siang', 7, 'Jadwal Normal Tengah', 5),
(6, 'M', 'Malam', 10, 'Jadwal Normal', 6),
(7, 'PS', 'Pagi + Siang', 14, 'Jadwal Normal', 7),
(8, 'SM', 'Siang + Malam', 14, 'Jadwal Normal', 8),
(9, 'MP', 'Malam + Pagi', 14, 'Jadwal Normal', 9),
(10, 'C', 'Cuti', 0, '-', 10),
(11, 'SKT', 'Sakit', 0, '-', 11),
(12, 'TL', 'Tugas Luar', 0, 'Mengikuti Tempat Kegiatan', 12),
(13, 'L', 'Libur', 0, '-', 13);

-- ---------------------------------------------
-- 📍 RINCIAN BULAN KALENDER
-- ---------------------------------------------
CREATE TABLE rincian_bulan_kalender (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_bulan VARCHAR(20),
  mulai DATE,
  selesai DATE
);

-- ---------------------------------------------
-- 📍 BULAN KALENDER KERJA
-- ---------------------------------------------
CREATE TABLE bulan_kalender_kerja (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_bulan VARCHAR(20),
  tanggal_mulai DATE NOT NULL,
  tanggal_akhir DATE NOT NULL,
  tahun INT NOT NULL,
  rincian_bulan_kalender_id INT,
  FOREIGN KEY (rincian_bulan_kalender_id) REFERENCES rincian_bulan_kalender(id)
);

-- ---------------------------------------------
-- 📍 INSTITUSI
-- ---------------------------------------------
CREATE TABLE institusi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_institusi VARCHAR(100) NOT NULL,
  sub_institusi VARCHAR(255) NOT NULL,
  alamat TEXT,
  telepon VARCHAR (15),
  logo VARCHAR(255)
);

-- ---------------------------------------------
-- 📍 MENU NAVIGASI
-- ---------------------------------------------
CREATE TABLE menus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_menu VARCHAR(50) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL,
  url VARCHAR(100),
  icon VARCHAR(50)
);

INSERT INTO menus (id, kode_menu, nama, url, icon) VALUES
(1, 'setting_institusi', 'Setting Institusi', '/Setting/institusi', 'far fa-circle'),
(2, 'setting_bulan', 'Setting Bulan Kerja', '/Setting/bulankerja', 'far fa-circle'),
(3, 'user_akses', 'Setting Hak Akses', '/HakAkses', 'far fa-circle'),
(4, 'unit_kerja', 'Unit Kerja', '/UnitKerja/index', 'far fa-circle'),
(5, 'jabatan', 'Jabatan', '/Jabatan/index', 'far fa-circle'),
(6, 'shift_kerja', 'Shift Kerja', '/ShiftKerja/index', 'far fa-circle'),
(7, 'pegawai', 'Pegawai', '/Pegawai/index', 'far fa-circle'),
(8, 'jadwal_kerja', 'Jadwal Kerja', '/Jadwal/index', 'far fa-circle'),
(9, 'pesan_jadwal', 'Pesan Jadwal', '/PesanJadwal/index', 'far fa-circle'),
(10, 'persetujuan_jadwal', 'Persetujuan Jadwal', '/PesanJadwal/persetujuan', 'far fa-circle'),
(11, 'rekap_jadwal', 'Rekapan Jadwal', '/Laporan/jadwal', 'far fa-circle'),
(12, 'rekap_pesan_jadwal', 'Rekapan Pesan Jadwal', '/Laporan/pesan', 'far fa-circle');
(13, 'laporan', 'Laporan Jadwal Kerja', '/rekap/index', 'far fa-circle');

-- ---------------------------------------------
-- 📍 ROLES DAN USER LOGIN
-- ---------------------------------------------
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_role VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO roles (id, nama_role) VALUES
(1, 'admin'),
(2, 'user');

CREATE TABLE role_menu (
  role_id INT,
  menu_id INT,
  PRIMARY KEY (role_id, menu_id),
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (menu_id) REFERENCES menus(id)
);

CREATE TABLE user_login (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  jabatan_id INT,
  role VARCHAR(20),
  nip VARCHAR(5),
  FOREIGN KEY (jabatan_id) REFERENCES jabatan(id),
  FOREIGN KEY (nip) REFERENCES pegawai(nip)
);

INSERT INTO user_login (id, username, password, jabatan_id, role, nip) VALUES
(1, 'admin', '$2y$10$ziViDn0fwdm7UEYLCWfftu1jrKKjhX4reb6LYOPsZsYzm0AKX6/ZG', 1, 'superadmin', NULL);

-- ---------------------------------------------
-- 📍 JADWAL KERJA
-- ---------------------------------------------
CREATE TABLE jadwal_kerja (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(5),
  tanggal DATE,
  shift_kode VARCHAR(5),
  UNIQUE (nip, tanggal),
  FOREIGN KEY (nip) REFERENCES pegawai(nip)
);

-- ---------------------------------------------
-- 📍 PESAN JADWAL (LIBUR/CUTI)
-- ---------------------------------------------
CREATE TABLE pesan_jadwal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(10) NOT NULL,
  tanggal DATE NOT NULL,
  jenis VARCHAR(10) NOT NULL,
  status ENUM('pending','disetujui','ditolak') DEFAULT 'pending',
  keterangan TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (nip) REFERENCES pegawai(nip)
);

-- ---------------------------------------------
-- 📍 QR TANDA TANGAN
-- ---------------------------------------------
CREATE TABLE qr_ttd (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(5),
  jabatan_id INT,
  unit_kerja_id INT,
  file_nama VARCHAR(255),
  isi_qr TEXT,
  waktu_dibuat DATETIME,
  FOREIGN KEY (nip) REFERENCES pegawai(nip),
  FOREIGN KEY (jabatan_id) REFERENCES jabatan(id),
  FOREIGN KEY (unit_kerja_id) REFERENCES unit_kerja(id)
);