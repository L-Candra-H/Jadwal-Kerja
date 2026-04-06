<?php

class PesanJadwalController extends Controller
{
  protected PegawaiModel $PegawaiModel;
  protected PesanJadwalModel $PesanJadwalModel;

  public function __construct()
  {
    require_once __DIR__ . '/../helpers/AuthGuard.php';
    $this->PegawaiModel     = $this->loadModel('PegawaiModel');
    $this->PesanJadwalModel = $this->loadModel('PesanJadwalModel');
  }

  private function getJenisPermintaan(): array
  {
    $stmt = $this->PesanJadwalModel->getDB()->prepare(
      "SELECT kode, keterangan FROM shift_kerja WHERE kode NOT IN ('SKT','TL')"
    );
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $row) {
      $result[$row['kode']] = $row['keterangan'];
    }

    return $result;
  }

  public function index()
  {
    if (!canAccess('pesan_jadwal')) {
      header('Location: ' . BASEURL . '/errors/unauthorized'); exit;
    }

    $role = $_SESSION['user']['role'] ?? 'guest';

    if ($role === 'user') {
      $nip = $_SESSION['nip'] ?? null;
      $data['pegawai_list'] = [$this->PegawaiModel->getByNip($nip)];
      $data['permintaan_pending'] = $this->PesanJadwalModel->getPendingByNip($nip);
    } elseif ($role === 'superadmin') {
      $data['pegawai_list'] = $this->PegawaiModel->getAll();
      $data['permintaan_pending'] = $this->PesanJadwalModel->getPendingWithNama();
    } else {
      $unit_id_raw = $_GET['unit_kerja_id'] ?? ($_SESSION['unit'] ?? null);
      $unit_id = is_numeric($unit_id_raw) ? (int) $unit_id_raw : 0;
      $data['pegawai_list'] = $this->PegawaiModel->getByUnit($unit_id);
      $data['permintaan_pending'] = $this->PesanJadwalModel->getPendingByUnit($unit_id);
    }

    $data['shiftJenis'] = $this->getJenisPermintaan();
    $this->loadView('jadwal/pesan', $data);
  }

  public function ajukan()
  {
    if (!canAccess('pesan_jadwal')) {
      header('Location: ' . BASEURL . '/errors/unauthorized'); exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nip     = $_POST['nip']     ?? null;
      $tanggal = $_POST['tanggal'] ?? null;
      $jenis   = $_POST['jenis']   ?? null;
      $role    = $_SESSION['user']['role'] ?? 'guest';

      if ($role === 'user' && $nip !== ($_SESSION['nip'] ?? null)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Tidak bisa mengajukan atas nama pegawai lain.'];
        header('Location: ' . BASEURL . '/PesanJadwal'); exit;
      }

      if (!preg_match('/^[A-Za-z0-9]{4,5}$/', $nip)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⛔ Format NIP tidak valid.'];
        header('Location: ' . BASEURL . '/PesanJadwal'); exit;
      }

      $cekDuplikat = $this->PesanJadwalModel->findByNipAndTanggal($nip, $tanggal);
      if ($cekDuplikat) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Sudah ada pengajuan di tanggal tersebut.'];
        header('Location: ' . BASEURL . '/PesanJadwal'); exit;
      }

      $validJenis = array_keys($this->getJenisPermintaan());
      if (!in_array(strtoupper($jenis), $validJenis)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⛔ Jenis permintaan tidak valid.'];
        header('Location: ' . BASEURL . '/PesanJadwal'); exit;
      }

      if ($nip && $tanggal && $jenis) {
        $this->PesanJadwalModel->insert([
          'nip'        => strtoupper($nip),
          'tanggal'    => $tanggal,
          'jenis'      => strtoupper($jenis),
          'status'     => 'pending',
          'created_at' => date('Y-m-d H:i:s')
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Permintaan berhasil diajukan.'];
      } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⚠️ Data tidak lengkap.'];
      }

      header('Location: ' . BASEURL . '/PesanJadwal'); exit;
    }

    header('Location: ' . BASEURL . '/PesanJadwal'); exit;
  }

  public function persetujuan()
  {
    if (!canAccess('persetujuan_jadwal')) {
      header('Location: ' . BASEURL . '/errors/unauthorized'); exit;
    }

    $role = strtolower($_SESSION['user']['role'] ?? 'guest');
    $unit_id_raw = $_GET['unit_kerja_id'] ?? ($_SESSION['user']['unit_kerja_id'] ?? null);
    $unit_id = is_numeric($unit_id_raw) ? (int) $unit_id_raw : 0;
    $unitNama = $this->PesanJadwalModel->getNamaUnitById($unit_id);
    $tahun       = $_GET['tahun'] ?? date('Y');
    $bulan = $_GET['bulan'] ?? '';

    $unitList = $this->PegawaiModel->getUnitOptions(); // kirim untuk semua role

    if ($role === 'superadmin') {
      $permintaan_pending = $this->PesanJadwalModel->getPendingWithFilter($unit_id, $tahun, $bulan);
    } else {
      $permintaan_pending = $this->PesanJadwalModel->getPendingByUnitAndPeriode($unit_id, $tahun, $bulan);
    }

    $bulanIndonesia = [
     'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
     'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
    ];

    $tahunList = range(date('Y'), date('Y') + 2);
    $unitNama = $this->PesanJadwalModel->getNamaUnitById($unit_id);

    $this->loadView('jadwal/persetujuan', [
      'permintaan_pending' => $permintaan_pending,
      'role'               => $role,
      'unitList'           => $unitList,
      'unitNama'           => $unitNama,
      'unitTerpilih'       => $unit_id,
      'tahunList'          => $tahunList,
      'bulanList'          => $bulanIndonesia,
      'tahunTerpilih'      => $tahun,
      'bulanTerpilih'      => $bulan
    ]);
  }

  // ✂️ LANJUTKAN KE BAGIAN 2: approve() & tolak()

  public function approve()
  {
    if (!canAccess('persetujuan_jadwal')) {
      header('Location: ' . BASEURL . '/errors/unauthorized'); exit;
    }

    $id = $_GET['id'] ?? null;

    if ($id) {
      $permintaan = $this->PesanJadwalModel->findById($id);

      if ($permintaan && $permintaan['nip'] && $permintaan['tanggal'] && $permintaan['jenis']) {
        $this->PesanJadwalModel->updateStatus($id, 'disetujui');

        $jadwalKerja = $this->loadModel('JadwalModel');
        $kode_shift  = strtoupper($permintaan['jenis']);

        if ($jadwalKerja->insertSingle($permintaan['nip'], $permintaan['tanggal'], $kode_shift)) {
          $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Jadwal berhasil disimpan.'];
        } else {
          $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Jadwal sudah ada — tidak ditambahkan ulang.'];
        }
      } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⚠️ Data permintaan tidak valid.'];
      }
    } else {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ ID permintaan tidak ditemukan.'];
    }

    header('Location: ' . BASEURL . '/PesanJadwal/persetujuan'); exit;
  }

  public function tolak()
  {
    if (!canAccess('persetujuan_jadwal')) {
      header('Location: ' . BASEURL . '/errors/unauthorized'); exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id         = $_POST['id'] ?? null;
      $keterangan = $_POST['keterangan'] ?? '';

      $permintaan = $this->PesanJadwalModel->findById($id);

      if ($permintaan && $keterangan) {
        $this->PesanJadwalModel->updateStatus($id, 'ditolak', $keterangan);
        $_SESSION['flash'] = ['type' => 'warning', 'message' => '❌ Permintaan ditolak.'];
      } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⚠️ Data penolakan tidak valid atau alasan kosong.'];
      }
    }

    header('Location: ' . BASEURL . '/PesanJadwal/persetujuan'); exit;
  }
}
