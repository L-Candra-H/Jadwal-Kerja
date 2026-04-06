<?php include __DIR__ . '/../template/header-login.php'; ?>

<div class="login-wrapper">
  <img src="<?= BASEURL ?>/img/logo.png" alt="Logo" class="mx-auto d-block" style="max-width: 64px; margin-bottom: 16px;">
  <div class="login-title">Reset Password</div>

  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['flash']['message'] ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- ✅ Perbaikan: action mengarah ke /auth/doReset -->
  <form action="<?= BASEURL ?>/auth/doReset" method="POST">
    <div class="form-group">
      <input type="password" name="new_password" id="new_password" class="form-control mb-2" placeholder="Password Baru" required minlength="5">
      <small id="pwStrength" class="text-muted"></small>
    </div>

    <div class="form-group">
      <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi Password" required minlength="5">
    </div>

    <button type="submit" class="btn btn-primary w-100">Simpan Password</button>

    <p class="mt-3 text-center">
      Sudah punya akun? <a href="<?= BASEURL ?>/auth/login">Login di sini</a>
    </p>
  </form>
</div>

<script>
  const pwField = document.getElementById('new_password');
  const pwStrength = document.getElementById('pwStrength');

  pwField.addEventListener('input', () => {
    const val = pwField.value;
    const strongRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{5,}$/;

    if (!val) {
      pwStrength.textContent = '';
    } else if (strongRegex.test(val)) {
      pwStrength.textContent = 'Password kuat ✅';
      pwStrength.classList.remove('text-danger');
      pwStrength.classList.add('text-success');
    } else {
      pwStrength.textContent = 'Gunakan minimal 5 karakter dengan huruf besar, angka, dan simbol';
      pwStrength.classList.remove('text-success');
      pwStrength.classList.add('text-danger');
    }
  });
</script>
