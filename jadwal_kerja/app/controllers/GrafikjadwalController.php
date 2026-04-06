<?php

class GrafikjadwalController {
  public function index() {
    if (!isset($_SESSION['user'])) {
      header("Location: " . BASEURL . "/auth/login");
      exit;
    }

    $user = $_SESSION['user'];
    $role = strtoupper($user['role'] ?? 'GUEST');
    $unit_session = $user['unit_kerja_id'] ?? null;

    $db = new Database();

    $tahun = $_GET['tahun'] ?? date("Y");
    $bulan = $_GET['bulan'] ?? "";
    $unit_kerja_id = $_GET['unit_kerja_id'] ?? ($role === 'SUPERADMIN' ? null : $unit_session);

    // Ambil semua bulan kalender kerja
    $db->query("SELECT * FROM bulan_kalender_kerja WHERE tahun = :tahun ORDER BY tanggal_mulai");
    $db->bind(":tahun", $tahun);
    $bulan_kalender = $db->fetchAll();

    // Ambil unit kerja
    if ($role === 'SUPERADMIN') {
      $units = $db->query("SELECT id, nama_unit_kerja FROM unit_kerja ORDER BY nama_unit_kerja")->fetchAll();
    } else {
      $db->query("SELECT id, nama_unit_kerja FROM unit_kerja WHERE id = :id");
      $db->bind(":id", $unit_session);
      $units = $db->fetchAll();
    }

    $data_bulan     = ['pesan' => 0, 'laporan' => 0];
    $pesan_status   = ['disetujui' => 0, 'ditolak' => 0]; // ✅ Tambahan
    $grafik_shift   = [];
    $is_bulan_valid = false;
    $show_grafik    = false;
    $nama_bulan     = "-";
    $tanggal_awal   = null;
    $tanggal_akhir  = null;

    // Cari tanggal awal dan akhir dari bulan kalender
    foreach ($bulan_kalender as $bk) {
      $kode_bulan = substr($bk['tanggal_mulai'], 5, 2);
      if ($kode_bulan === $bulan) {
        $is_bulan_valid = true;
        $tanggal_awal   = $bk['tanggal_mulai'];
        $tanggal_akhir  = $bk['tanggal_akhir'];
        $nama_bulan     = $bk['nama_bulan'] ?? $kode_bulan;
        break;
      }
    }

    if ($is_bulan_valid && !empty($unit_kerja_id)) {
      // Hitung total laporan jadwal kerja
      $db->query("
        SELECT COUNT(*) as total
        FROM jadwal_kerja jk
        JOIN pegawai p ON jk.nip = p.nip
        WHERE jk.tanggal BETWEEN :awal AND :akhir AND p.unit_kerja_id = :unit
      ");
      $db->bind(":awal", $tanggal_awal);
      $db->bind(":akhir", $tanggal_akhir);
      $db->bind(":unit", $unit_kerja_id);
      $data_bulan['laporan'] = (int)$db->fetch()['total'];

      // Hitung total pesan jadwal
      $db->query("
        SELECT COUNT(*) as total
        FROM pesan_jadwal pj
        JOIN pegawai p ON pj.nip = p.nip
        WHERE pj.tanggal BETWEEN :awal AND :akhir AND p.unit_kerja_id = :unit
      ");
      $db->bind(":awal", $tanggal_awal);
      $db->bind(":akhir", $tanggal_akhir);
      $db->bind(":unit", $unit_kerja_id);
      $data_bulan['pesan'] = (int)$db->fetch()['total'];

      // ✅ Hitung status DISETUJUI dan DITOLAK
      $db->query("
        SELECT pj.status, COUNT(*) as jumlah
        FROM pesan_jadwal pj
        JOIN pegawai p ON pj.nip = p.nip
        WHERE pj.tanggal BETWEEN :awal AND :akhir
          AND p.unit_kerja_id = :unit
          AND pj.status IN ('disetujui', 'ditolak')
        GROUP BY pj.status
      ");
      $db->bind(":awal", $tanggal_awal);
      $db->bind(":akhir", $tanggal_akhir);
      $db->bind(":unit", $unit_kerja_id);
      foreach ($db->fetchAll() as $row) {
        $status = strtolower(trim($row['status']));
        if (isset($pesan_status[$status])) {
          $pesan_status[$status] = (int)$row['jumlah'];
        }
      }

      $show_grafik = ($data_bulan['laporan'] > 0 || $data_bulan['pesan'] > 0);

      // Distribusi shift hanya dari data real
      $db->query("
        SELECT jk.shift_kode, COUNT(*) as jumlah
        FROM jadwal_kerja jk
        JOIN pegawai p ON jk.nip = p.nip
        WHERE jk.tanggal BETWEEN :awal AND :akhir AND p.unit_kerja_id = :unit
        GROUP BY jk.shift_kode
      ");
      $db->bind(":awal", $tanggal_awal);
      $db->bind(":akhir", $tanggal_akhir);
      $db->bind(":unit", $unit_kerja_id);
      foreach ($db->fetchAll() as $row) {
        $kode = strtoupper(trim($row['shift_kode']));
        $grafik_shift[$kode] = (int)$row['jumlah'];
      }
    }

    $data = compact(
      'units', 'unit_kerja_id', 'tahun', 'bulan', 'bulan_kalender',
      'is_bulan_valid', 'show_grafik', 'data_bulan',
      'grafik_shift', 'nama_bulan', 'pesan_status' // ✅ Tambahan
    );
    extract($data);

    require_once '../app/views/laporan/grafik_jadwal.php';
  }
}
