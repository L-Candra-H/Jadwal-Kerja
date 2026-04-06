<?php

class ShiftKerjaController extends Controller
{
  protected ShiftKerjaModel $ShiftKerjaModel;

  public function __construct()
  {
    require_once __DIR__ . '/../helpers/AuthGuard.php';

    // 🛡️ Cek hak akses
    if (!canAccess('shift_kerja')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    $this->ShiftKerjaModel = $this->loadModel('ShiftKerjaModel');
  }

  public function index()
  {
    $data['shift'] = $this->ShiftKerjaModel->getAll();
    $this->loadView('shift_kerja/index', $data);
  }

  public function simpan()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->ShiftKerjaModel->insert($_POST);
      $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Data shift berhasil disimpan'];
    }

    header('Location: ' . BASEURL . '/shiftkerja/index');
    exit;
  }
}
