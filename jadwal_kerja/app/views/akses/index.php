<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mx-3 mt-3" role="alert">
    <?= $_SESSION['flash']['message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="post" action="<?= BASEURL ?>/HakAkses/simpan">
  <div class="card-header bg-primary text-white">
    <h5 class="card-title d-inline"><strong>Pengaturan Hak Akses</h5></strong>
    <div class="float-right">
      <button class="btn btn-success px-4 me-2" type="submit">💾 Simpan</button>
    </div>
  </div>

  <?php include __DIR__ . '/akses-matrix.php'; ?>
</form>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
