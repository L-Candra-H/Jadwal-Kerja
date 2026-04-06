<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php $isSuperadmin = ($_SESSION['user']['role'] ?? '') === 'superadmin'; ?>

<div class="card">
  <div class="card-header bg-primary text-white">
    <strong>👤 Profil Saya</strong>
  </div>
  <div class="card-body">
    <dl class="row">
      <dt class="col-sm-3">Nama Lengkap</dt>
      <dd class="col-sm-9"><?= $_SESSION['nama'] ?? ($_SESSION['user']['username'] ?? '-') ?></dd>

      <?php if (!$isSuperadmin): ?>
        <dt class="col-sm-3">NIP</dt>
        <dd class="col-sm-9"><?= $_SESSION['nip'] ?? '-' ?></dd>

        <dt class="col-sm-3">Jabatan</dt>
        <dd class="col-sm-9"><?= $_SESSION['jabatan'] ?? '-' ?></dd>

        <dt class="col-sm-3">Unit Kerja</dt>
        <dd class="col-sm-9"><?= $_SESSION['unit'] ?? '-' ?></dd>
      <?php endif; ?>

      <dt class="col-sm-3">Login Terakhir</dt>
      <dd class="col-sm-9"><?= $_SESSION['last_login'] ?? '-' ?></dd>
    </dl>

    <?php if (!$isSuperadmin): ?>
      <hr>
      <form method="post" action="<?= BASEURL ?>/profil/resetPassword">
        <div class="mb-3">
          <label>Password Baru</label>
          <input type="password" name="password_baru" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">🔐 Ubah Password</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
