<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php if (!empty($_SESSION['pesan'])): ?>
  <div class="alert alert-info">
    <?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
  </div>
<?php endif; ?>

<!-- Card Header dengan tombol tambah -->
<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <strong>Pengaturan Bulan Kerja</strong>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalBulanKerja">
      <i class="fas fa-plus me-1"></i> Tambah Bulan Kerja
    </button>
  </div>
</div>

<!-- Modal Form Bulan Kerja -->
<div class="modal fade" id="modalBulanKerja" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="<?= BASEURL ?>/setting/simpanBulanKerja">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Form Bulan Kerja</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
              <input type="number" name="tanggalMulai" id="tanggalMulai" class="form-control"
                value="16" min="1" max="31" required disabled>
            </div>
            <div class="col-md-4">
              <label for="tanggalAkhir" class="form-label">Tanggal Akhir</label>
              <input type="number" name="tanggalAkhir" id="tanggalAkhir" class="form-control"
                value="15" min="1" max="31" required disabled>
            </div>
            <div class="col-md-4">
              <label for="tahun" class="form-label">Tahun Kalender</label>
              <select name="tahun" id="tahun" class="form-control" required>
                <option value="" selected disabled>-- Pilih Tahun --</option>
                <?php 
                  $startYear = 2025; 
                  $endYear   = date("Y") + 3; 
                  for ($y = $startYear; $y <= $endYear; $y++): 
                ?>
                  <option value="<?= $y ?>" <?= ($y == ($tahun ?? '')) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
              </select>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" type="submit">
            <i class="fas fa-save me-1"></i> Simpan
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TABEL BULAN KERJA -->
<?php if (!empty($bulanKerja)): ?>
<div class="card mt-4">
  <div class="card-header bg-primary text-white">
    <div>
      <strong>Daftar Bulan Kerja Tahun <?= $tahun ?></strong>
      <?php if ($statusData === 'preview'): ?>
        <span class="badge bg-warning text-dark ms-2">Preview</span>
      <?php else: ?>
        <span class="badge bg-success ms-2">Tersimpan</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-sm">
      <thead class="table-light text-center">
        <tr>
          <th>Nama Bulan</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Akhir</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php foreach($bulanKerja as $b): ?>
          <tr>
            <td><?= $b['nama'] ?></td>
            <td><?= $b['mulai'] ?></td>
            <td><?= $b['akhir'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php if ($statusData === 'preview'): ?>
      <div class="alert alert-warning mt-3">
        ⚠️ Data di atas masih <strong>preview</strong> dan belum tersimpan di database.<br>
        Klik tombol <strong>Simpan Ke Database</strong> untuk menyimpan.
      </div>
    <?php endif; ?>
  </div>
</div>
<?php else: ?>
<div class="alert alert-warning mt-4">
  ⚠️ Belum ada data bulan kerja untuk tahun <strong><?= $tahun ?></strong>.<br>
  Silakan klik tombol <strong>Tambah Bulan Kerja</strong> untuk mengisi secara otomatis.
</div>
<?php endif; ?>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
