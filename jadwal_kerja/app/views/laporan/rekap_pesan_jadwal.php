<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<div class="card-header bg-primary text-white">
  <strong>Rekap Pesan Jadwal</strong>
</div>

<div class="card-body">
  <!-- Filter Form -->
  <form method="GET" action="<?= BASEURL ?>/rekappesan/index" class="mb-3">
    <div class="row g-3 align-items-end">
      
      <!-- Unit Kerja -->
      <div class="col-md-4">
        <label class="form-label">Unit Kerja</label>
        <?php if (strtolower($_SESSION['user']['role']) === 'superadmin'): ?>
          <select name="unit_kerja_id" class="form-control" required>
            <option value="">-- Pilih Unit --</option>
            <?php foreach ($units as $unit): ?>
              <option value="<?= $unit['id'] ?>" <?= ($unit_kerja_id == $unit['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($unit['nama_unit_kerja']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input type="hidden" name="unit_kerja_id" value="<?= $unit_kerja_id ?>">
          <input type="text" class="form-control" readonly value="<?php
            foreach ($units as $u) {
              if ($u['id'] == $unit_kerja_id) echo $u['nama_unit_kerja'];
            }
          ?>">
        <?php endif; ?>
      </div>

      <!-- Tahun -->
      <div class="col-md-2">
        <label class="form-label">Tahun</label>
        <select name="tahun" class="form-control" required>
          <option value="">-- Pilih Tahun --</option>
          <?php foreach (range(date('Y') - 2, date('Y') + 2) as $yr): ?>
            <option value="<?= $yr ?>" <?= ($tahun ?? date('Y')) == $yr ? 'selected' : '' ?>>
              <?= $yr ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Bulan -->
      <div class="col-md-3">
        <label class="form-label">Bulan</label>
        <select name="bulan" class="form-control" required>
          <option value="">-- Pilih Bulan --</option>
          <?php foreach (['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'] as $b): ?>
            <option value="<?= $b ?>" <?= ($bulan ?? '') == $b ? 'selected' : '' ?>>
              <?= $b ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tombol -->
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">📄 Tampilkan</button>
        <a href="<?= BASEURL ?>/dashboard" class="btn btn-secondary">🔙 Kembali</a>
      </div>

    </div>
  </form>
</div>

<div class="card-header bg-primary text-white">
  <strong>Daftar Pesan Jadwal</strong>
</div>

<div class="card-body">
  <?php if (empty($tahun) || empty($bulan)): ?>
    <div class="alert alert-info mt-2">Silakan pilih Tahun dan Bulan terlebih dahulu.</div>
  <?php elseif (empty($pesan)): ?>
    <div class="alert alert-warning mt-2">Tidak ada data pesan jadwal untuk periode ini.</div>
  <?php else: ?>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Unit Kerja</th>
          <th>Nama</th>
          <th>NIP</th>
          <th>Tanggal Pesan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pesan as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['nama_unit_kerja']) ?></td>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= $row['nip'] ?></td>
          <td><?= $row['tanggal'] ?></td>
          <?php
          $status = strtolower($row['status']);
          $badgeClass = match ($status) {
            'disetujui' => 'success',
            'pending'   => 'warning',
            'ditolak'   => 'danger',
            default     => 'secondary'
          };
          ?>

          <td>
            <span class="badge bg-<?= $badgeClass ?> text-uppercase">
              <?= strtoupper($row['status']) ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
