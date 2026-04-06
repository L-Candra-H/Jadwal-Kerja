<?php
require_once __DIR__ . '/../helpers/UnitHelper.php'; // kalau dari controller

class JadwalController extends Controller
{
  protected JadwalModel $JadwalModel;
  protected PegawaiModel $PegawaiModel;
  protected ShiftKerjaModel $ShiftKerjaModel;
  protected BulanKalenderModel $BulanKalenderModel;
  protected PesanJadwalModel $PesanJadwalModel;

  public function __construct()
  {
    $this->JadwalModel         = $this->loadModel('JadwalModel');
    $this->PegawaiModel        = $this->loadModel('PegawaiModel');
    $this->ShiftKerjaModel     = $this->loadModel('ShiftKerjaModel');
    $this->BulanKalenderModel  = $this->loadModel('BulanKalenderModel');
    $this->PesanJadwalModel    = $this->loadModel('PesanJadwalModel');
  }

  public function index()
  {
    // ⏱️ BARIS WAJIB ➜ definisikan dulu
    $role = $_SESSION['user']['role'] ?? 'guest';
    $unit_from_session = $_SESSION['user']['unit_kerja_id'] ?? 'Pilih Unit';

    $unit_kerja_id = ($role === 'superadmin')
      ? ($_GET['unit_kerja_id'] ?? '')
      : $_SESSION['user']['unit_kerja_id'] ?? null;
    $bulan_selected = $_GET['bulan'] ?? null;
    $tahun          = $_GET['tahun'] ?? date('Y');

    $data['units']            = $this->PegawaiModel->getUnitKerja();
    $data['bulan_kalender']   = $this->getBulanKalender();
    $data['unit_kerja_id']    = $unit_kerja_id;
    $data['bulan_selected']   = $bulan_selected;
    $data['tahun']            = $tahun;
    $data['pegawai_list']     = [];
    $data['tanggal_array']    = [];
    $data['jadwal_tersimpan'] = [];
    $data['shift_options']    = $this->ShiftKerjaModel->getAll();
    $data['jadwal_dipesan']   = [];

    if ($unit_kerja_id && !empty($bulan_selected)) {
      $tanggal_array = $this->generateTanggal($bulan_selected, $tahun);
      $pegawai        = $this->JadwalModel->getPegawaiByUnit($unit_kerja_id);
      $jadwal_raw     = $this->JadwalModel->getJadwal($unit_kerja_id, $bulan_selected, $tahun);
      $dipesan_raw    = $this->PesanJadwalModel->getApprovedByUnitAndBulan($unit_kerja_id, $bulan_selected, $tahun);

      // 🔄 Gabungkan jadwal tersimpan dan hasil persetujuan
      $jadwal_map = is_array($jadwal_raw) ? $jadwal_raw : [];
      $dipesan_map = is_array($dipesan_raw) ? $dipesan_raw : [];

      foreach ($dipesan_map as $nip => $tanggal_shift) {
        foreach ($tanggal_shift as $tgl => $kode) {
          // Jika belum ada di jadwal tersimpan, tambahkan dari dipesan
          if (!isset($jadwal_map[$nip][$tgl])) {
            $jadwal_map[$nip][$tgl] = strtoupper($kode);
          }
        }
      }

      $data['tanggal_array']    = $tanggal_array;
      $data['pegawai_list']     = $pegawai;
      $data['jadwal_tersimpan'] = $jadwal_map;
      $data['jadwal_dipesan']   = $dipesan_map;
    }

    $this->loadView('jadwal/index', $data);
  }

  public function save()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $unit_id = $_POST['unit_kerja_id'] ?? null;
      $bulan   = $_POST['bulan'] ?? null;
      $tahun   = $_POST['tahun'] ?? null;
      $jadwal  = $_POST['jadwal'] ?? [];

      if ($unit_id && $bulan && $tahun) {
        $this->JadwalModel->saveJadwal($unit_id, $bulan, $tahun, $jadwal);
        $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Jadwal berhasil disimpan'];
      } else {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Data tidak lengkap'];
      }
    }

    header('Location: ' . BASEURL . '/jadwal?unit_kerja_id=' . $unit_id . '&bulan=' . $bulan . '&tahun=' . $tahun);
    exit;
  }

  // 📅 Generate tanggal berdasarkan pengaturan di bulan_kalender_kerja
  private function generateTanggal(string $nama_bulan, int $tahun): array
  {
    $periode = $this->BulanKalenderModel->getPeriode($nama_bulan, $tahun);

    // 🔁 Fallback otomatis jika data belum ada
    if (!$periode || !isset($periode['tanggal_mulai']) || !isset($periode['tanggal_akhir'])) {
      $bulan_map = array_flip([
        'Januari','Februari','Maret','April','Mei','Juni','Juli',
        'Agustus','September','Oktober','November','Desember'
      ]);
      $bulan_index = str_pad($bulan_map[$nama_bulan] + 1, 2, '0', STR_PAD_LEFT);
      $awal  = "{$tahun}-{$bulan_index}-01";
      $akhir = date("Y-m-t", strtotime($awal));
    } else {
      $awal  = $periode['tanggal_mulai'];
      $akhir = $periode['tanggal_akhir'];
    }

    // 🔄 Hasilkan array tanggal
    $tanggal_mulai = new DateTime($awal);
    $tanggal_akhir = new DateTime($akhir);
    $tanggal_array = [];

    while ($tanggal_mulai <= $tanggal_akhir) {
      $tanggal_array[] = $tanggal_mulai->format('Y-m-d');
      $tanggal_mulai->modify('+1 day');
    }

    return $tanggal_array;
  }

  // 📌 Daftar bulan statik untuk dropdown
  private function getBulanKalender(): array
  {
    return [
      'Januari'   => [],
      'Februari'  => [],
      'Maret'     => [],
      'April'     => [],
      'Mei'       => [],
      'Juni'      => [],
      'Juli'      => [],
      'Agustus'   => [],
      'September' => [],
      'Oktober'   => [],
      'November'  => [],
      'Desember'  => [],
    ];
  }
}
