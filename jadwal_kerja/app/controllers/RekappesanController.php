<?php
class RekappesanController {
  public function index() {
    require_once '../app/core/Database.php';
    $db = (new Database())->getPDO();

    // Ambil user dari session
    $user = $_SESSION['user'] ?? [];
    $role = strtoupper($user['role'] ?? 'GUEST');
    $nip = $user['nip'] ?? null;
    $unit_user = $user['unit_kerja_id'] ?? null;

    // Ambil semua unit kerja untuk dropdown
    $stmt = $db->query("SELECT id, nama_unit_kerja FROM unit_kerja");
    $units = $stmt->fetchAll();

    // Filter input
    $isSuper = ($role === 'SUPERADMIN');
    $unit_kerja_id = $_GET['unit_kerja_id'] ?? ($isSuper ? null : $unit_user);
    $tahun = $_GET['tahun'] ?? null;
    $bulan = $_GET['bulan'] ?? null;

    $pesan = [];

    // Ambil rentang periode dari bulan_kalender_kerja
    if (!empty($tahun) && !empty($bulan) && !empty($unit_kerja_id)) {
      $periodeStmt = $db->prepare("
        SELECT tanggal_mulai, tanggal_akhir
        FROM bulan_kalender_kerja
        WHERE nama_bulan = ? AND tahun = ?
        LIMIT 1
      ");
      $periodeStmt->execute([$bulan, $tahun]);
      $periode = $periodeStmt->fetch();

      if ($periode) {
        $tgl_mulai = $periode['tanggal_mulai'];
        $tgl_akhir = $periode['tanggal_akhir'];

        $query = "
          SELECT p.id, p.nip, p.nama, pj.tanggal, pj.status, uk.nama_unit_kerja
          FROM pesan_jadwal pj
          JOIN pegawai p ON pj.nip = p.nip
          JOIN unit_kerja uk ON p.unit_kerja_id = uk.id
          WHERE pj.tanggal BETWEEN ? AND ?
        ";

        $params = [$tgl_mulai, $tgl_akhir];

        // Filter role
        if (!$isSuper) {
          $query .= " AND p.unit_kerja_id = ?";
          $params[] = $unit_kerja_id;
        }

        if ($role === 'USER') {
          $query .= " AND p.nip = ?";
          $params[] = $nip;
        }

        $query .= " ORDER BY pj.tanggal DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $pesan = $stmt->fetchAll();
      }
    }

    // Buat data bisa diakses dari view
    require_once '../app/views/template/header.php';
    require_once '../app/views/laporan/rekap_pesan_jadwal.php';
    require_once '../app/views/template/footer.php';
  }
}
