<?php

class UnitKerjaController extends Controller
{
  public function __construct()
  {
    require_once __DIR__ . '/../helpers/AuthGuard.php';

    // 🔐 Proteksi akses berdasarkan hak akses dinamis
    if (!canAccess('unit_kerja')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }
  }

  public function index()
  {
    $unitKerjaModel = $this->model('UnitKerjaModel');
    $data['unitKerja'] = $unitKerjaModel->getAll();
    $this->view('unit_kerja/index', $data);
  }

  public function simpan()
  {
    if (!isset($_POST['nama_unit_kerja']) || trim($_POST['nama_unit_kerja']) === '') {
      $_SESSION['flash'] = [
        'type' => 'warning',
        'message' => '⚠️ Nama unit kerja tidak boleh kosong.'
      ];
      header('Location: ' . BASEURL . '/UnitKerja/index');
      exit;
    }

    $namaUnit = htmlspecialchars(trim($_POST['nama_unit_kerja']));

    try {
      $unitKerjaModel = $this->model('UnitKerjaModel');
      $unitKerjaModel->insert($namaUnit);

      $_SESSION['flash'] = [
        'type' => 'success',
        'message' => '✅ Unit kerja berhasil ditambahkan.'
      ];
    } catch (Exception $e) {
      $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => '❌ Gagal menambah unit kerja: ' . $e->getMessage()
      ];
    }

    header('Location: ' . BASEURL . '/UnitKerja/index');
    exit;
  }
}
