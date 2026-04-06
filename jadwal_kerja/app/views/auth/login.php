<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../template/header-login.php';
?>

<?php
// ✅ SweetAlert2: Flash message dari $_SESSION
if (!empty($_SESSION['flash']['message']) && !empty($_SESSION['flash']['type'])) {
  $type = $_SESSION['flash']['type'];
  $message = htmlspecialchars($_SESSION['flash']['message']);

  echo "
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: '{$type}',
        title: {
          success: '✅ Sukses!',
          danger: '❌ Gagal!',
          warning: '⚠️ Peringatan!',
          info: 'ℹ️ Info'
        }['{$type}'] || '🔔 Notifikasi',
        text: '{$message}',
        timer: 3000,
        showConfirmButton: false,
        backdrop: 'rgba(0,0,0,0.2)'
      });
    });
  </script>
  ";
  unset($_SESSION['flash']);
}

// ⏱️ Notifikasi timeout
if (isset($_GET['timeout'])) {
  echo "
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'info',
        title: '⏱️ Sesi Berakhir',
        text: 'Anda telah logout karena tidak aktif lebih dari 5 menit.',
        timer: 4000,
        showConfirmButton: false,
        backdrop: 'rgba(0,0,0,0.25)'
      });
    });
  </script>
  ";
}
?>

<div class="login-wrapper">
  <form action="<?= BASEURL ?>/auth/prosesLogin" method="POST">
    <img src="<?= BASEURL ?>/img/logo.png" alt="Logo" style="max-width: 64px; margin-bottom: 14px;" class="mx-auto d-block">
    <div class="login-title">Jadwal Kerja</div>

    <div class="form-group mb-3">
      <label for="username">Username</label>
      <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required autofocus>
    </div>

    <div class="form-group mb-4">
      <label for="password">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required minlength="5">
    </div>

    <button type="submit" class="btn btn-primary w-100">MASUK</button>
  </form>

  <p class="text-center mt-3">
    Belum punya akun?
    <a href="<?= BASEURL ?>/auth/register" class="d-inline-block">Buat akun baru</a>
  </p>

  <p class="text-center mt-2">
    <a href="<?= BASEURL ?>/auth/lupa" class="d-block text-warning text-center text-decoration-none">Lupa password?</a>
  </p>
</div>

</body>
</html>
