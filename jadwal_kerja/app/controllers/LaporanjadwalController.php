<?php
// app/controllers/LaporanjadwalController.php

class LaporanjadwalController {
  public function jadwal() {
    require_once '../app/core/Database.php';
    $db = (new Database())->getPDO();

    // Ambil user dari session
    $user = $_SESSION['user'] ?? null;
    $role = strtoupper($user['role'] ?? 'GUEST');
    $unit_user = $user['unit_kerja_id'] ?? null;

    // Ambil semua unit kerja
    $stmt = $db->query("SELECT id, nama_unit_kerja FROM unit_kerja");
    $units = $stmt->fetchAll();

    // Tentukan unit kerja aktif
    $isSuper = ($role === 'SUPERADMIN');
    $unit_kerja_id = $_GET['unit_kerja_id'] ?? ($isSuper ? null : $unit_user);
    $tahun = $_GET['tahun'] ?? null;
    $bulan = $_GET['bulan'] ?? null;

    // Kalender kerja berdasarkan tahun
    $bulan_kalender = [
      'JANUARI'   => ['mulai'=>"16 December ".($tahun-1), 'akhir'=>"15 January ".$tahun],
      'FEBRUARI'  => ['mulai'=>"16 January ".$tahun, 'akhir'=>"15 February ".$tahun],
      'MARET'     => ['mulai'=>"16 February ".$tahun, 'akhir'=>"15 March ".$tahun],
      'APRIL'     => ['mulai'=>"16 March ".$tahun, 'akhir'=>"15 April ".$tahun],
      'MEI'       => ['mulai'=>"16 April ".$tahun, 'akhir'=>"15 May ".$tahun],
      'JUNI'      => ['mulai'=>"16 May ".$tahun, 'akhir'=>"15 June ".$tahun],
      'JULI'      => ['mulai'=>"16 June ".$tahun, 'akhir'=>"15 July ".$tahun],
      'AGUSTUS'   => ['mulai'=>"16 July ".$tahun, 'akhir'=>"15 August ".$tahun],
      'SEPTEMBER' => ['mulai'=>"16 August ".$tahun, 'akhir'=>"15 September ".$tahun],
      'OKTOBER'   => ['mulai'=>"16 September ".$tahun, 'akhir'=>"15 October ".$tahun],
      'NOVEMBER'  => ['mulai'=>"16 October ".$tahun, 'akhir'=>"15 November ".$tahun],
      'DESEMBER'  => ['mulai'=>"16 November ".$tahun, 'akhir'=>"15 December ".$tahun],
    ];

    $riwayat = [];
    if (!empty($unit_kerja_id) && !empty($tahun)) {
       // eksekusi loop foreach dan isi riwayat_unik
    }

    // Tampilkan riwayat hanya jika semua filter terisi
    if (!empty($unit_kerja_id) && !empty($tahun)) {
      $riwayat_unik = [];

      foreach ($bulan_kalender as $nama_bulan => $range) {
        $mulai = date("Y-m-d", strtotime($range['mulai']));
        $akhir = date("Y-m-d", strtotime($range['akhir']));

        $stmt2 = $db->prepare("
          SELECT j.tanggal, uk.nama_unit_kerja as unit, uk.id as unit_kerja_id
          FROM jadwal_kerja j
          JOIN pegawai p ON j.nip = p.nip
          JOIN unit_kerja uk ON p.unit_kerja_id = uk.id
          WHERE p.unit_kerja_id = ? AND j.tanggal BETWEEN ? AND ?
          LIMIT 1
        ");
        $stmt2->execute([$unit_kerja_id, $mulai, $akhir]);
        $data = $stmt2->fetch();

        if ($data) {
          $riwayat_unik[] = [
            'unit' => $data['unit'],
            'unit_kerja_id' => $data['unit_kerja_id'],
            'bulan' => $nama_bulan,
            'tahun' => $tahun,
            'tanggal' => $data['tanggal']
          ];
        }
      }

      $riwayat = $riwayat_unik;
    }

    require_once '../app/views/template/header.php';
    require_once '../app/views/laporan/jadwal.php';
    require_once '../app/views/template/footer.php';
  }
}
