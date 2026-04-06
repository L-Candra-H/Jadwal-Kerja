<?php
require_once __DIR__ . '/../core/Database.php';

$db = new Database();
$pdo = $db->getPDO();

class CetakController {
  public function index() {
    $db = new Database();

    $unit_id = $_GET['unit_kerja_id'] ?? null;
    $bulan   = $_GET['bulan'] ?? strtoupper(date('F'));
    $tahun   = $_GET['tahun'] ?? date('Y');

    // 🗓️ Ambil periode aktif dari kalender kerja
    $periode_stmt = $db->prepare("SELECT tanggal_mulai, tanggal_akhir FROM bulan_kalender_kerja WHERE nama_bulan = ? AND tahun = ?");
    $periode_stmt->execute([$bulan, $tahun]);
    $periode = $periode_stmt->fetch();

    $mulai = $periode['tanggal_mulai'] ?? null;
    $akhir = $periode['tanggal_akhir'] ?? null;

    // 👥 Ambil daftar pegawai di unit
    $pegawai_stmt = $db->prepare("
      SELECT p.*, j.nama_jabatan AS jabatan 
      FROM pegawai p 
      LEFT JOIN jabatan j ON p.jabatan_id = j.id 
      WHERE p.unit_kerja_id = ?
    ");
    $pegawai_stmt->execute([$unit_id]);
    $pegawai_list = $pegawai_stmt->fetchAll();

    // 📅 Ambil jadwal kerja sesuai periode dan unit
    $jadwal_stmt = $db->prepare("
      SELECT jk.nip, jk.tanggal, jk.shift_kode
      FROM jadwal_kerja jk 
      JOIN pegawai p ON jk.nip = p.nip 
      WHERE p.unit_kerja_id = ? AND jk.tanggal BETWEEN ? AND ?
    ");
    $jadwal_stmt->execute([$unit_id, $mulai, $akhir]);
    $jadwal_all = $jadwal_stmt->fetchAll();

    // 🧮 Simpan jadwal ke array per pegawai
    $jadwal_tersimpan = [];
    foreach ($jadwal_all as $j) {
      $jadwal_tersimpan[$j['nip']][$j['tanggal']] = $j['shift_kode'];
    }

    // 📆 Susun array tanggal antara mulai dan akhir
    $tanggal_array = [];
    $start = new DateTime($mulai);
    $end = new DateTime($akhir);
    while ($start <= $end) {
      $tanggal_array[] = $start->format('Y-m-d');
      $start->modify('+1 day');
    }

    // 🏢 Ambil semua unit kerja dan bentuk array ID => Nama
    $unit_stmt = $db->prepare("SELECT id, nama_unit_kerja FROM unit_kerja");
    $unit_stmt->execute();
    $units_raw = $unit_stmt->fetchAll();

    $units = [];
    foreach ($units_raw as $u) {
      $units[$u['id']] = $u['nama_unit_kerja'];
    }

    // 🏛️ Ambil data institusi untuk header & logo
    $inst_stmt = $db->prepare("SELECT nama_institusi AS nama, sub_institusi AS sub_nama, alamat, telepon, logo FROM institusi LIMIT 1");
    $inst_stmt->execute();
    $institusi = $inst_stmt->fetch();

    // 🧾 Ambil durasi kerja per shift untuk hitung total jam
    $shift_stmt = $db->prepare("SELECT kode AS kode_shift, durasi FROM shift_kerja");
    $shift_stmt->execute();
    $shift_data = $shift_stmt->fetchAll();

    $jam_per_shift = [];
    foreach ($shift_data as $s) {
      $jam_per_shift[$s['kode_shift']] = (float) $s['durasi'];
    }

    require_once __DIR__ . '/../helpers/QRHelper.php';
    require_once __DIR__ . '/../views/cetak/index.php';
  }
}
