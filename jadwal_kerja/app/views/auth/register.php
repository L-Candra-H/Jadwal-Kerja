<?php include __DIR__ . '/../template/header-login.php'; ?>

<div class="login-wrapper">
  <!-- Logo + Judul -->
  <img src="<?= BASEURL ?>/img/logo.png" alt="Logo" style="max-width: 64px; margin-bottom: 14px;" class="mx-auto d-block">
  <div class="login-title">Buat Akun Baru</div>

  <!-- 🔔 Flash Message -->
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?? 'info' ?> text-center" role="alert">
      <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Form Register -->
  <form action="<?= BASEURL ?>/auth/doRegister" method="POST">
    <div class="form-group">
      <input type="text" name="username" class="form-control" placeholder="Username" required minlength="3">
    </div>

    <div class="form-group">
      <input type="password" name="password" class="form-control" placeholder="Password" required minlength="5">
    </div>

    <div class="form-group">
      <input type="text" name="nip" class="form-control" placeholder="NIP Pegawai (4–5 karakter)" pattern="[A-Za-z0-9]{4,5}" required>
    </div>

    <div class="form-group">
      <select name="jabatan_id" class="form-control" required>
        <option value="">-- Pilih Jabatan --</option>
        <?php foreach ($jabatan_list as $jabatan): ?>
          <?php if (strtolower($jabatan['nama_jabatan']) !== 'admin'): ?>
            <option value="<?= htmlspecialchars($jabatan['id']) ?>">
              <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
            </option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary w-100">DAFTAR</button>

    <p class="text-center mt-3">
      Sudah punya akun? <a href="<?= BASEURL ?>/auth/login">Login di sini</a>
    </p>
  </form>
</div>
