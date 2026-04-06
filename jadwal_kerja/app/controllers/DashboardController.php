<?php
if (session_status() === PHP_SESSION_NONE) session_start();

class DashboardController extends Controller
{
  public function index()
  {
    // 🔒 Proteksi login
    if (!isset($_SESSION['user'])) {
      header('Location: ' . BASEURL . '/auth/login');
      exit;
    }

    require_once '../app/core/Database.php';
    $db = (new Database())->getPDO();

    // Ambil data user
    $user     = $_SESSION['user'];
    $username = $user['username'] ?? 'Guest';
    $role     = strtoupper($user['role'] ?? 'GUEST');
    $unit_id  = $user['unit_kerja_id'] ?? null;
    $nip      = $user['nip'] ?? null;

    // Jumlah Pegawai
    $jumlahPegawai = 0;
    if ($role === 'SUPERADMIN') {
      $stmt = $db->query("SELECT COUNT(*) FROM pegawai");
      $jumlahPegawai = $stmt->fetchColumn();
    } elseif ($unit_id) {
      $stmt = $db->prepare("SELECT COUNT(*) FROM pegawai WHERE unit_kerja_id = ?");
      $stmt->execute([$unit_id]);
      $jumlahPegawai = $stmt->fetchColumn();
    }

    // Jumlah Shift Hari Ini (ADMIN & USER)
    $jumlahShiftHariIni = 0;
    $today = date('Y-m-d');
    if (in_array($role, ['ADMIN', 'USER']) && $unit_id) {
      $stmt = $db->prepare("
        SELECT COUNT(*) FROM jadwal_kerja jk
        JOIN pegawai p ON jk.nip = p.nip
        WHERE jk.tanggal = ? AND p.unit_kerja_id = ?
      ");
      $stmt->execute([$today, $unit_id]);
      $jumlahShiftHariIni = $stmt->fetchColumn();
    }

    // Pesan Belum Disetujui (ADMIN only)
    $jumlahPesanBelum = 0;
    if ($role === 'ADMIN' && $unit_id) {
      $stmt = $db->prepare("
        SELECT COUNT(*) FROM pesan_jadwal pj
        JOIN pegawai p ON pj.nip = p.nip
        WHERE pj.status = 'pending' AND p.unit_kerja_id = ?
      ");
      $stmt->execute([$unit_id]);
      $jumlahPesanBelum = $stmt->fetchColumn();
    }

    // Nama Unit & Daftar Pegawai
    $unit_nama = '—';
    $pegawai_list = [];
    if ($unit_id) {
      $stmt = $db->prepare("SELECT nama_unit_kerja FROM unit_kerja WHERE id = ?");
      $stmt->execute([$unit_id]);
      $unit_nama = $stmt->fetchColumn() ?? '—';

      $stmt = $db->prepare("SELECT * FROM pegawai WHERE unit_kerja_id = ?");
      $stmt->execute([$unit_id]);
      $pegawai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Shift Pegawai Login Hari Ini → ambil kode
    $kodeShiftHariIni = null;
    if ($nip) {
      $stmt = $db->prepare("SELECT shift_kode FROM jadwal_kerja WHERE nip = ? AND tanggal = ?");
      $stmt->execute([$nip, $today]);
      $kodeShiftHariIni = $stmt->fetchColumn();
    }

    // Mapping shift dari tabel shift_kerja
    $labelShift = [];
    $stmt = $db->query("SELECT kode, keterangan FROM shift_kerja");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      $labelShift[$row['kode']] = $row['keterangan'];
    }

    $namaShiftHariIni = $labelShift[$kodeShiftHariIni] ?? 'Belum dijadwalkan';

    // Shift Pegawai Login Besok → ambil kode
    $kodeShiftBesok = null;
    $besok = date('Y-m-d', strtotime('+1 day'));
    if ($nip) {
      $stmt = $db->prepare("SELECT shift_kode FROM jadwal_kerja WHERE nip = ? AND tanggal = ?");
      $stmt->execute([$nip, $besok]);
      $kodeShiftBesok = $stmt->fetchColumn();
    }

    $namaShiftBesok = $labelShift[$kodeShiftBesok] ?? 'Belum dijadwalkan';

    // Kirim ke view
    $this->view('dashboard/index', [
      'username'           => $username,
      'role'               => $role,
      'jumlahPegawai'      => $jumlahPegawai,
      'jumlahShiftHariIni' => $jumlahShiftHariIni,
      'jumlahPesanBelum'   => $jumlahPesanBelum,
      'unit_nama'          => $unit_nama,
      'pegawai_list'       => $pegawai_list,
      'namaShiftHariIni'   => $namaShiftHariIni,
      'namaShiftBesok'     => $namaShiftBesok 
    ]);
  }
}
