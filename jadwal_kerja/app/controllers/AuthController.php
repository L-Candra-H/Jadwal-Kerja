<?php
if (session_status() === PHP_SESSION_NONE) session_start();

class AuthController extends Controller
{
  protected AuthModel $AuthModel;

  public function __construct()
  {
    $this->AuthModel = $this->loadModel('AuthModel');
  }

  public function index()
  {
    header('Location: ' . BASEURL . '/auth/login');
    exit;
  }

  public function login()
  {
    $this->loadView('auth/login');
  }

  public function prosesLogin()
  {
    $user = $this->AuthModel->cekLogin($_POST['username'], $_POST['password']);

    if (!$user) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Login gagal! Username atau password salah.'];
      header('Location: ' . BASEURL . '/auth/login');
      exit;
    }

    // ✅ Validasi aktivasi akun
    if (isset($user['aktivasi']) && $user['aktivasi'] != 1) {
      $_SESSION['flash'] = ['type' => 'warning', 'message' => '⛔ Akun dinonaktifkan. Silakan hubungi admin.'];
      header('Location: ' . BASEURL . '/auth/login');
      exit;
    }

    $_SESSION['user']        = $user;
    $_SESSION['nip']         = $user['nip'] ?? '';
    $_SESSION['nama']        = $user['nama'] ?? $user['username'];
    $_SESSION['jabatan']     = $user['jabatan'] ?? '';
    $_SESSION['unit']        = $user['unit'] ?? '';
    $_SESSION['last_login']  = date('Y-m-d H:i:s');

    $role     = strtolower($user['role']);
    $roleRow  = $this->model('AksesModel')->getRoleIdByName($role);

    $_SESSION['akses_menu'] = $roleRow
      ? array_column($this->model('AksesModel')->getKodeMenuByRole($roleRow['id']), 'kode_menu')
      : [];

    switch ($user['role']) {
      case 'superadmin':
      case 'admin':
      case 'user':
        header('Location: ' . BASEURL . '/dashboard/index');
        exit;
      default:
        session_destroy();
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Role tidak dikenali'];
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }
  }

  public function logout()
  {
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_destroy();
    header('Location: ' . BASEURL . '/auth/login');
    exit;
  }

  public function register()
  {
    $jabatanModel = $this->model('JabatanModel');
    $pegawaiModel = $this->model('PegawaiModel');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nip      = $_POST['nip']      ?? null;
      $nama     = $_POST['nama']     ?? null;
      $jabatan  = $_POST['jabatan_id'] ?? null;
      $unit     = $_POST['unit_kerja_id'] ?? null;
      $password = $_POST['password'] ?? null;

      if (!preg_match('/^[A-Za-z0-9]{4,5}$/', $nip)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => '⛔ Format NIP tidak valid. 4–5 huruf atau angka saja.'];
        header('Location: ' . BASEURL . '/auth/register');
        exit;
      }

      if ($pegawaiModel->getByNip($nip)) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ NIP sudah dipakai. Gunakan NIP yang unik.'];
        header('Location: ' . BASEURL . '/auth/register');
        exit;
      }

      $pegawaiModel->insert([
        'nip'           => $nip,
        'nama'          => $nama,
        'unit_kerja_id' => $unit,
        'jabatan_id'    => $jabatan,
        'password'      => $password,
        'sumber_data'   => 'registrasi',
        'aktivasi'      => 1
      ]);

      $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Akun berhasil dibuat'];
      header('Location: ' . BASEURL . '/auth/login');
      exit;
    }

    $data['jabatan_list'] = $jabatanModel->getAll();
    $this->view('auth/register', $data);
  }

  public function doRegister()
  {
    $username   = $_POST['username'] ?? '';
    $password   = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $nip        = $_POST['nip'] ?? '';
    $jabatanId  = $_POST['jabatan_id'] ?? '';

    try {
      $jabatanData = $this->model('JabatanModel')->getById($jabatanId);
      $namaJabatan = strtolower($jabatanData['nama_jabatan'] ?? '');

      $role = match ($namaJabatan) {
        'superadmin' => 'superadmin',
        'ka.bid', 'ka.unit', 'ka.ru', 'koord.' => 'admin',
        '-' => 'user',
        default => 'guest'
      };

      $pegawaiModel = $this->model('PegawaiModel');
      if (!$pegawaiModel->getByNip($nip) && $nip !== '') {
        $pegawaiModel->insert([
          'nip'           => $nip,
          'nama'          => null,
          'unit_kerja_id' => null,
          'jabatan_id'    => null,
          'aktivasi'      => 1,
          'sumber_data'   => 'akun_sendiri'
        ]);
      }

      $this->model('UserModel')->insert([
        'username'   => $username,
        'password'   => $password,
        'nip'        => $nip,
        'jabatan_id' => $jabatanId,
        'role'       => $role
      ]);

      $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Akun berhasil dibuat. Silakan login.'];
      header('Location: ' . BASEURL . '/auth/login');
      exit;

    } catch (Exception $e) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Gagal membuat akun: ' . $e->getMessage()];
      header('Location: ' . BASEURL . '/auth/register');
      exit;
    }
  }

  public function lupa()
  {
    $this->view('auth/lupa');
  }

  public function forgot()
  {
    if (!isset($_POST['username'])) {
      $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Username belum dikirim'];
      header('Location: ' . BASEURL . '/auth/lupa');
      exit;
    }

    $user = $this->model('UserModel')->findByUsername($_POST['username']);
    if (!$user) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Username tidak ditemukan'];
      header('Location: ' . BASEURL . '/auth/lupa');
      exit;
    }

    $_SESSION['reset_user'] = $user;
    header('Location: ' . BASEURL . '/auth/reset');
    exit;
  }

  public function reset()
  {
    if (!isset($_SESSION['reset_user'])) {
      header('Location: ' . BASEURL . '/auth/lupa');
      exit;
    }

    $this->view('auth/reset');
  }

  public function doReset()
  {
    if (!isset($_SESSION['reset_user'])) {
      header('Location: ' . BASEURL . '/auth/lupa');
      exit;
    }

    $pw      = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($pw === '' || $confirm === '') {
      $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Data password belum lengkap'];
      header('Location: ' . BASEURL . '/auth/reset');
      exit;
    }

    if ($pw !== $confirm) {
      $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ Password tidak cocok'];
      header('Location: ' . BASEURL . '/auth/reset');
      exit;
    }

    $hashed   = password_hash($pw, PASSWORD_DEFAULT);
    $berhasil = $this->model('UserModel')->updatePassword($_SESSION['reset_user']['id'], $hashed);

    if (!$berhasil) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => '❌ Gagal mengubah password. Silakan coba lagi.'];
      header('Location: ' . BASEURL . '/auth/reset');
      exit;
    }

    unset($_SESSION['reset_user']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Password berhasil diubah'];
    header('Location: ' . BASEURL . '/auth/login');
    exit;
  }
}
