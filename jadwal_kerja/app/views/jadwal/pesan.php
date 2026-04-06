<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php if (isset($_SESSION['flash'])): ?>
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
    <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <?= $_SESSION['flash']['message'] ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ✅ Form Pengajuan Pesan Jadwal -->
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><strong>Pengajuan Pesan Jadwal</strong></h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?= BASEURL ?>/PesanJadwal/ajukan">
      <div class="row">
        <div class="col-md-4">
          <label for="nip" class="form-label small">Pegawai</label>
          <?php if ($_SESSION['user']['role'] === 'superadmin'): ?>
            <select name="nip" class="form-control" required>
              <option value="">-- Pilih Pegawai --</option>
              <?php foreach ($pegawai_list as $p): ?>
                <option value="<?= $p['nip'] ?>"><?= $p['nama'] ?> (<?= $p['nip'] ?>)</option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="hidden" name="nip" value="<?= $_SESSION['nip'] ?>">
            <input type="text" class="form-control" value="<?= $_SESSION['nama'] ?>" readonly>
          <?php endif; ?>
        </div>

        <div class="col-md-3">
          <label for="tanggal" class="form-label small">Tanggal Yang Dipesan</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>

        <div class="col-md-3">
          <label for="jenis" class="form-label small">Jenis Permintaan</label>
          <select name="jenis" id="jenis" class="form-control" required>
            <option value="">-- Pilih Jenis --</option>
            <?php foreach ($shiftJenis as $kode => $label): ?>
              <option value="<?= htmlspecialchars($kode) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2 d-flex align-items-end mt-2">
          <button type="submit" class="btn btn-success me-2">✅ Ajukan</button>
          <a href="<?= BASEURL ?>/dashboard/index" class="btn btn-secondary">🔙 Kembali</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($permintaan_pending)): ?>
  <div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><strong>Daftar Pengajuan Pesan Jadwal</strong></h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-sm">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>Status</th>
              <th>Diajukan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($permintaan_pending as $p): ?>
              <tr>
                <td><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
                <td><?= strtoupper($p['jenis']) ?></td>
                <td><span class="badge bg-warning text-dark"><?= ucfirst($p['status']) ?></span></td>
                <td><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
