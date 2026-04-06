<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php if (!empty($_SESSION['pesan'])): ?>
  <div class="alert alert-info">
    <?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
  </div>
<?php endif; ?>

<!-- Tabel Institusi -->
<div class="card mt-4">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <strong>Institusi Tersimpan</strong>
    <?php if (empty($listInstitusi)): ?>
      <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalInstitusi">
        <i class="fas fa-plus me-1"></i> Tambah Institusi
      </button>
    <?php else: ?>
      <button class="btn btn-success btn-sm" disabled>
        <i class="fas fa-plus me-1"></i> Tambah Institusi
      </button>
    <?php endif; ?>
  </div>

  <div class="card-body">
    <table class="table table-bordered table-sm w-100">
      <thead class="table-light text-center">
        <tr>
          <th>Nama Institusi</th>
          <th>Sub Institusi</th>
          <th>Alamat</th>
          <th>Telepon</th>
          <th>Logo</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php foreach ($listInstitusi ?? [] as $i): ?>
        <tr>
          <td><?= $i['nama_institusi'] ?></td>
          <td><?= $i['sub_institusi'] ?? '-' ?></td>
          <td><?= $i['alamat'] ?></td>
          <td><?= $i['telepon'] ?></td>
          <td>
            <?php if (!empty($i['logo'])): ?>
              <a href="<?= BASEURL ?>/storage/gambar/<?= $i['logo'] ?>" target="_blank">
                <img src="<?= BASEURL ?>/storage/gambar/<?= $i['logo'] ?>" alt="Logo" height="60">
              </a>
            <?php else: ?>
              <em class="text-muted">Tidak Ada</em>
            <?php endif; ?>
          </td>
          <td>
            <button class="btn btn-warning btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalInstitusi"
                    data-id="<?= $i['id'] ?>"
                    data-nama="<?= htmlspecialchars($i['nama_institusi']) ?>"
                    data-sub="<?= htmlspecialchars($i['sub_institusi']) ?>"
                    data-alamat="<?= htmlspecialchars($i['alamat']) ?>"
                    data-telepon="<?= htmlspecialchars($i['telepon']) ?>"
                    data-logo="<?= $i['logo'] ?>">
              <i class="fas fa-edit me-1"></i> Edit
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="modalInstitusi" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" action="<?= BASEURL ?>/setting/simpanInstitusi">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Form Institusi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="form-id">
          <input type="hidden" name="logo_lama" id="form-logo-lama">

          <div class="row">
            <div class="col-md-6">
              <label>Nama Institusi <span class="text-danger">*</span></label>
              <input type="text" name="nama_institusi" id="form-nama" class="form-control" required>

              <label class="mt-2">Sub Institusi</label>
              <input type="text" name="sub_institusi" id="form-sub" class="form-control" placeholder="Fakultas / Unit / Cabang">
            </div>
            <div class="col-md-6">
              <label>Alamat <span class="text-danger">*</span></label>
              <textarea name="alamat" id="form-alamat" class="form-control" required></textarea>
            </div>
            <div class="col-md-6 mt-3">
              <label>Telepon</label>
              <input type="text" name="telepon" id="form-telepon" class="form-control" placeholder="081234567890">
            </div>
            <div class="col-md-6 mt-3">
              <label>Logo (optional)</label>
              <input type="file" name="logo" id="logo" class="form-control">
              <img id="preview" src="#" class="mt-2 rounded border" height="80" style="display:none;">
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

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>

<!-- JS Preview & Fill Form -->
<script>
document.getElementById('logo').addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (file) {
    const preview = document.getElementById('preview');
    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
  }
});

// Isi form saat klik Edit
const modal = document.getElementById('modalInstitusi');
modal.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  if (!button.dataset.id) {
    // create mode: kosongkan form
    document.getElementById('form-id').value = '';
    document.getElementById('form-nama').value = '';
    document.getElementById('form-sub').value = '';
    document.getElementById('form-alamat').value = '';
    document.getElementById('form-telepon').value = '';
    document.getElementById('form-logo-lama').value = '';
    document.getElementById('preview').style.display = 'none';
    return;
  }

  // edit mode: isi form
  document.getElementById('form-id').value = button.dataset.id;
  document.getElementById('form-nama').value = button.dataset.nama;
  document.getElementById('form-sub').value = button.dataset.sub;
  document.getElementById('form-alamat').value = button.dataset.alamat;
  document.getElementById('form-telepon').value = button.dataset.telepon;
  document.getElementById('form-logo-lama').value = button.dataset.logo;
});
</script>
