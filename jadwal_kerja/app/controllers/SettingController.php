<?php
class SettingController extends Controller
{
  protected $InstitusiModel;

  public function __construct()
  {
    require_once __DIR__ . '/../helpers/AuthGuard.php';
    $this->InstitusiModel = $this->model('InstitusiModel');
  }

  // 🔧 Halaman institusi
  public function institusi()
  {
    if (!canAccess('setting_institusi')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    $formData = [];
    $formMode = 'create';

    if (isset($_GET['edit'])) {
      $formData = $this->InstitusiModel->getById($_GET['edit']);
      $formMode = 'edit';
    }

    $listInstitusi = $this->InstitusiModel->getAll();

    $data = [
      'formData'      => $formData,
      'formMode'      => $formMode,
      'listInstitusi' => $listInstitusi
    ];

    $this->view('setting/institusi', $data);
  }

  // 📝 Simpan/update institusi
  public function simpanInstitusi()
  {
    if (!canAccess('setting_institusi')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    if (empty($_POST['nama_institusi']) || empty($_POST['alamat'])) {
      echo "<div style='color:#fff; background:red; padding:10px;'>❌ Nama institusi dan alamat wajib diisi.</div>";
      exit;
    }

    $data = [
      'id'             => $_POST['id'] ?? '',
      'nama_institusi' => $_POST['nama_institusi'] ?? '',
      'sub_institusi'  => $_POST['sub_institusi'] ?? '',
      'alamat'         => $_POST['alamat'] ?? '',
      'telepon'        => $_POST['telepon'] ?? '',
      'logo_lama'      => $_POST['logo_lama'] ?? ''
    ];

    try {
      if (!empty($data['id'])) {
        $this->InstitusiModel->update($data, $_FILES);
        $_SESSION['pesan'] = '✅ Data institusi berhasil diupdate!';
      } else {
        $this->InstitusiModel->simpan($data, $_FILES);
        $_SESSION['pesan'] = '✅ Data institusi berhasil disimpan!';
      }

      header("Location: " . BASEURL . "/setting/institusi");
      exit;
    } catch (Exception $e) {
      $_SESSION['pesan'] = "❌ Gagal memproses data institusi:<br>" . $e->getMessage();
      header("Location: " . BASEURL . "/setting/institusi");
      exit;
    }
  }

  // 📅 Halaman bulan kerja
  public function bulanKerja()
  {
    if (!canAccess('setting_bulan')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    $tahun        = $_GET['tahun'] ?? date("Y");
    $tanggalMulai = 16;
    $tanggalAkhir = 15;

    $bulanKerjaDB = $this->model('BulanKalenderModel')->getByTahun($tahun);

    if (!empty($bulanKerjaDB)) {
      $bulanKerja = $bulanKerjaDB;
      $statusData = 'tersimpan';
    } else {
      $bulanKerja = [];
      $statusData = 'belum_ada';
    }

    $data = [
      'tahun'      => $tahun,
      'bulanKerja' => $bulanKerja,
      'statusData' => $statusData
    ];

    $this->view('setting/bulan_kalender', $data);
  }

  // 💾 Simpan data bulan kerja
  public function simpanBulanKerja()
  {
      if (!canAccess('setting_bulan')) {
          header('Location: ' . BASEURL . '/errors/unauthorized');
          exit;
      }

      $tahun        = $_POST['tahun'] ?? date("Y");
      $tanggalMulai = $_POST['tanggalMulai'] ?? 16;
      $tanggalAkhir = $_POST['tanggalAkhir'] ?? 15;

      // ✅ Cek apakah data tahun ini sudah ada
      $bulanKerjaDB = $this->model('BulanKalenderModel')->getByTahun($tahun);
      if (!empty($bulanKerjaDB)) {
          $_SESSION['pesan'] = "⚠️ Data bulan kerja untuk tahun $tahun sudah ada dan tidak bisa diinput ulang.";
          header("Location: " . BASEURL . "/setting/bulanKerja?tahun=" . $tahun);
          exit;
      }

      // Jika belum ada, baru generate dan simpan
      $bulanAngka = [
          1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
          5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
          9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
      ];

      foreach ($bulanAngka as $num => $namaBulan) {
          $bulanMulai = ($num == 1) ? 12 : $num - 1;
          $tahunMulai = ($num == 1) ? $tahun - 1 : $tahun;

          $tanggalStart = date("Y-m-d", strtotime("$tanggalMulai " . date("F", mktime(0, 0, 0, $bulanMulai, 1)) . " $tahunMulai"));
          $tanggalEnd   = date("Y-m-d", strtotime("$tanggalAkhir " . date("F", mktime(0, 0, 0, $num, 1)) . " $tahun"));

          $this->model('BulanKalenderModel')->simpan([
              'nama'   => $namaBulan,
              'mulai'  => $tanggalStart,
              'akhir'  => $tanggalEnd,
              'tahun'  => $tahun
          ]);
      }

      $_SESSION['pesan'] = "✅ Data bulan kerja untuk tahun $tahun berhasil disimpan.";
      header("Location: " . BASEURL . "/setting/bulanKerja?tahun=" . $tahun);
      exit;
  }
}
