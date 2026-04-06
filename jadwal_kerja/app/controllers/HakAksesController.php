<?php

class HakAksesController extends Controller
{
  private $aksesModel;

  public function __construct()
  {
    $this->aksesModel = $this->model('AksesModel');

    // Akses hanya boleh role tertentu
    if (!canAccess('user_akses')) {
      header('Location: ' . BASEURL . '/dashboard');
      exit;
    }
  }

  // ✅ Tampilkan halaman matrix hak akses
  public function index()
  {
    $data['judul'] = 'Pengaturan Hak Akses';
    $data['roles'] = $this->aksesModel->getAllRoles();
    $data['menus'] = $this->aksesModel->getAllMenus();

    // Ambil akses per role
    $data['akses'] = [];
    foreach ($data['roles'] as $role) {
      $aksesMenu = $this->aksesModel->getMenuByRole($role['id']);
      $data['akses'][$role['id']] = array_column($aksesMenu, 'menu_id');
    }

    $this->loadView('akses/index', $data);
  }

  // ✅ Proses simpan hak akses dari form matrix
  public function simpan()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $akses = $_POST['akses'] ?? [];

      foreach ($akses as $role_id => $menu_ids) {
        $menu_ids = array_filter($menu_ids, fn($id) => trim($id) !== '');
        if ($this->aksesModel->getRoleName($role_id) !== 'superadmin') {
          $this->aksesModel->updateRoleAccess($role_id, $menu_ids);
        }
      }

      $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Hak akses berhasil diperbarui!'
      ];

      header('Location: ' . BASEURL . '/HakAkses');
      exit;
    }

    header('Location: ' . BASEURL . '/dashboard/index');
    exit;
  }
}
