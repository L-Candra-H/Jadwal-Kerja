<?php include __DIR__ . '/../template/header-login.php'; ?>

<div class="login-wrapper">
  <img src="<?= BASEURL ?>/img/logo.png" alt="Logo" class="mx-auto d-block" style="max-width: 64px; margin-bottom: 16px;">
  <div class="login-title">Lupa Password</div>

  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['flash']['message'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form action="<?= BASEURL ?>/auth/forgot" method="POST">
    <div class="form-group">
      <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">LANJUT</button>
    <p class="mt-3 text-center">
      Ingat password? <a href="<?= BASEURL ?>/auth/login">Login di sini</a>
    </p>
  </form>
</div>

<script>
  document.querySelector('input[name="username"]').addEventListener('input', e => {
    e.target.value = e.target.value.trim(); // hapus spasi depan belakang
  });
</script>
