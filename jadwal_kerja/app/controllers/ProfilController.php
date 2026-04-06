<?php
class ProfilController extends Controller
{
  protected PegawaiModel $PegawaiModel;

  public function __construct()
  {
    $this->PegawaiModel = $this->loadModel('PegawaiModel');
  }

  public function index()
  {
    // Bisa ditambah validasi login kalau perlu
    $this->loadView('profil/index');
  }

  public function resetPassword()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $passwordBaru = $_POST['password_baru'] ?? null;
      $nip          = $_SESSION['nip'] ?? null;

      if ($passwordBaru && $nip) {
        $berhasil = $this->model('UserModel')->updatePasswordByNip($nip, $passwordBaru);

        if ($berhasil) {
          $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Password berhasil direset'];
        } else {
          $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Gagal mereset password'];
        }
      } else {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Data tidak lengkap'];
      }

      header('Location: ' . BASEURL . '/profil');
      exit;
    }
  }
}
