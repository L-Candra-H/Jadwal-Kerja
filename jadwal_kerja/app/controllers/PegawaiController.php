<?php
class PegawaiController extends Controller
{
  protected PegawaiModel $PegawaiModel;

  public function __construct()
  {
    require_once __DIR__ . '/../helpers/AuthGuard.php';
    
    // 🛡️ Cegah akses jika bukan role yang berwenang
    if (!canAccess('pegawai')) {
      header('Location: ' . BASEURL . '/errors/unauthorized');
      exit;
    }

    $this->PegawaiModel = $this->loadModel('PegawaiModel');
  }

  public function index($id = null)
  {
    $data['unitKerja'] = $this->PegawaiModel->getUnitKerja();
    $data['jabatan']   = $this->PegawaiModel->getJabatan();

    // 🔍 Pencarian
    if (isset($_GET['q']) && trim($_GET['q']) !== '') {
      $keyword = $_GET['q'];
      $data['pegawai'] = $this->PegawaiModel->search($keyword);
    } else {
      $data['pegawai'] = $this->PegawaiModel->getAll();
    }

    // ✏️ Mode Edit
    if ($id) {
      $data['edit_mode'] = true;
      $data['form_data'] = $this->PegawaiModel->getById($id);
    }

    $this->loadView('pegawai/index', $data);
  }

  public function simpan()
  {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $nip = $_POST['nip'] ?? null;

          if (!$nip) {
              $_SESSION['flash'] = ['type' => 'danger', 'message' => '⛔ NIP wajib diisi.'];
              header('Location: ' . BASEURL . '/pegawai');
              exit;
          }

          if (!preg_match('/^[A-Za-z0-9]{4,5}$/', $nip)) {
              $_SESSION['flash'] = ['type' => 'danger', 'message' => '⛔ Format NIP tidak valid. Gunakan 4–5 karakter huruf atau angka.'];
              header('Location: ' . BASEURL . '/pegawai');
              exit;
          }

          if ($this->PegawaiModel->getByNip($nip)) {
              $_SESSION['flash'] = ['type' => 'warning', 'message' => '⚠️ NIP sudah terdaftar. Tidak boleh duplikat.'];
              header('Location: ' . BASEURL . '/pegawai');
              exit;
          }

          // 🔎 Ambil jabatan dari user_login
          $userLogin = $this->PegawaiModel->getUserLoginByNip($nip);
          if ($userLogin) {
              $_POST['jabatan_id'] = $userLogin['jabatan_id'];
          }

          // ✅ Simpan data pegawai
          $this->PegawaiModel->insert($_POST);
          $_SESSION['flash'] = ['type' => 'success', 'message' => '✅ Data pegawai berhasil disimpan'];
          header('Location: ' . BASEURL . '/pegawai');
          exit;
      }
  }

  public function update()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->PegawaiModel->update($_POST);
    }

    header('Location: ' . BASEURL . '/pegawai');
    exit;
  }
}
