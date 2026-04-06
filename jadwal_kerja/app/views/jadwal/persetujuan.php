<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<!-- ✅ Flash Toast -->
<?php if (isset($_SESSION['flash'])): ?>
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div class="toast show text-white bg-<?= $_SESSION['flash']['type'] ?? 'info' ?>" role="alert">
      <div class="d-flex">
        <div class="toast-body"><?= $_SESSION['flash']['message'] ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ✅ Filter Persetujuan -->
<div class="card mb-3">
  <div class="card-header bg-primary text-white">
    <strong>Persetujuan Pesan Jadwal</strong>
  </div>
  <div class="card-body">
    <form method="get" action="<?= BASEURL ?>/PesanJadwal/persetujuan">
      <div class="row">
        <!-- Unit Kerja -->
        <div class="col-md-4">
          <label class="form-label">Unit Kerja</label>
          <?php if (strtolower($_SESSION['user']['role']) === 'superadmin'): ?>
            <select name="unit_kerja_id" class="form-control" required>
              <option value="">-- Pilih Unit --</option>
              <?php foreach ($unitList as $unit): ?>
                <option value="<?= $unit['id'] ?>" <?= $unitTerpilih == $unit['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($unit['nama_unit_kerja']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="hidden" name="unit_kerja_id" value="<?= $unitTerpilih ?>">
            <input type="text" class="form-control" readonly value="<?= $unitNama ?>">
          <?php endif; ?>
        </div>

        <!-- Tahun -->
        <div class="col-md-2">
          <label class="form-label">Tahun</label>
          <select name="tahun" class="form-control" required>
            <option value="" selected disabled>-- Pilih Tahun --</option>
            <?php 
              $startYear = 2025;
              $endYear   = date('Y') + 3;
              for ($t = $startYear; $t <= $endYear; $t++): 
            ?>
              <option value="<?= $t ?>" <?= ($t == ($tahunTerpilih ?? '')) ? 'selected' : '' ?>><?= $t ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <!-- Bulan -->
        <div class="col-md-3">
          <label class="form-label">Bulan</label>
          <select name="bulan" class="form-control" required>
            <option value="">-- Pilih Bulan --</option>
            <?php foreach ($bulanList as $key => $nama): ?>
              <option value="<?= $key ?>" <?= $key == $bulanTerpilih ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Tombol Aksi -->
        <div class="col-auto align-self-end mt-3">
          <button class="btn btn-primary me-2">📄 Tampilkan</button>
          <a href="<?= BASEURL ?>/dashboard/index" class="btn btn-secondary">🔙 Kembali</a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ✅ Daftar Permintaan -->
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><strong>Persetujuan Pengajuan Jadwal</strong></h5>
  </div>
  <div class="card-body">
    <?php if (empty($permintaan_pending)): ?>
      <div class="alert alert-warning text-center">Tidak ada permintaan yang menunggu persetujuan.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm table-bordered text-center align-middle">
          <thead>
            <tr>
              <th style="width: 50px;">No</th>
              <th>NIP</th>
              <th>Nama</th>
              <th>Tanggal</th>
              <th>Jenis</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($permintaan_pending as $p): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $p['nip'] ?></td>
                <td><?= $p['nama'] ?></td>
                <td><?= date('d-m-Y', strtotime($p['tanggal'])) ?></td>
                <td><?= strtoupper($p['jenis']) ?></td>
                <td>
                  <a href="<?= BASEURL ?>/PesanJadwal/approve?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">✅</a>
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTolak<?= $p['id'] ?>">❌</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ✅ Modal Tolak -->
<?php foreach ($permintaan_pending as $p): ?>
  <div class="modal fade" id="modalTolak<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $p['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white py-2">
          <h6 class="modal-title" id="modalLabel<?= $p['id'] ?>">Tolak Permintaan</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="<?= BASEURL ?>/PesanJadwal/tolak">
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <textarea name="keterangan" rows="3" class="form-control" placeholder="Alasan penolakan..." required></textarea>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger btn-sm">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
