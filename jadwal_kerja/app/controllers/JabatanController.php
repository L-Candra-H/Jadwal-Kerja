<?php

class JabatanController extends Controller
{
  private JabatanModel $JabatanModel;

  public function __construct()
  {
    // 🛡️ Proteksi akses berdasarkan role
    if (!function_exists('canAccess')) {
      require_once __DIR__ . '/../helpers/AuthGuard.php';
    }

    if (!canAccess('jabatan')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    // 🔁 Inisialisasi model
    $this->JabatanModel = $this->loadModel('JabatanModel');
  }

  public function index()
  {
    $data['jabatan'] = $this->JabatanModel->getAll();
    $this->loadView('jabatan/index', $data);
  }

  // 🧩 Tambahkan metode lain jika perlu (create, edit, delete, dll)
}
