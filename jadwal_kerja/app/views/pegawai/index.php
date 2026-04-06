<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<!-- Card Header dengan tombol tambah -->
<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><strong>Data Pegawai</strong></h5>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPegawai">
      ➕ Tambah Pegawai
    </button>
  </div>
</div>

<!-- Modal Form Create/Edit Pegawai -->
<div class="modal fade" id="modalPegawai" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="pegawaiForm" method="post" action="<?= BASEURL ?>/pegawai/simpan">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTitle">Form Pegawai</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="form-id">
          <div class="row">
            <div class="col-md-3">
              <label>NIP</label>
              <input type="text" name="nip" id="form-nip" class="form-control" required minlength="4" maxlength="5">
            </div>
            <div class="col-md-5">
              <label>Nama</label>
              <input type="text" name="nama" id="form-nama" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label>Unit Kerja</label>
              <select name="unit_kerja_id" id="form-unit" class="form-control" required>
                <option value="" selected disabled>--Pilih Unit Kerja--</option>
                <?php foreach ($unitKerja as $unit): ?>
                  <option value="<?= $unit['id'] ?>"><?= $unit['nama_unit_kerja'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mt-3">
              <label>Jabatan</label>
              <select name="jabatan_id" id="form-jabatan" class="form-control" required>
                <option value="" selected disabled>--Pilih Jabatan--</option>
                <?php foreach ($jabatan as $row): ?>
                  <?php if (strtolower($row['nama_jabatan']) !== 'admin'): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nama_jabatan'] ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mt-3">
              <label>Status Keaktifan</label>
              <select name="aktivasi" id="form-aktivasi" class="form-control" required>
                <option value="" selected disabled>--Pilih Status--</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" type="submit" id="form-submit">💾 SIMPAN</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Tabel Daftar Pegawai + Pencarian -->
<div class="card mt-4">
  <div class="card-header bg-info text-white">
    <h6 class="mb-0"><strong>Daftar Pegawai</strong></h6>
  </div>
  <div class="card-body">
    <!-- Form Cari -->
    <form method="get" class="mb-3">
      <div class="input-group">
        <input type="text" id="searchInput" name="q" class="form-control" placeholder="🔎 Cari NIP / Nama..." value="<?= $_GET['q'] ?? '' ?>">
        <button class="btn btn-outline-light" type="submit">Cari</button>
        <?php if (!empty($_GET['q'])): ?>
          <a href="<?= BASEURL ?>/pegawai" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Tabel Data -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0 text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>NIP</th>
            <th>Nama</th>
            <th>Unit Kerja</th>
            <th>Jabatan</th>
            <th>Status</th>
            <th style="width: 10%;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Pagination logic
          $perPage = 8;
          $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
          $total = count($pegawai);
          $totalPages = ceil($total / $perPage);
          $start = ($page - 1) * $perPage;
          $slice = array_slice($pegawai, $start, $perPage);

          foreach ($slice as $p): ?>
            <tr>
              <td><?= $p['nip'] ?? '-' ?></td>
              <td>
                <?= $p['nama'] ?: '-' ?>
                <?php if (($p['sumber_data'] ?? '') === 'akun_sendiri'): ?>
                  <span class="badge bg-warning text-dark">akun baru</span>
                <?php endif; ?>
              </td>
              <td><?= $p['unit'] ?? '-' ?></td>
              <td><?= $p['jabatan'] ?? '-' ?></td>
              <td>
                <span class="badge <?= $p['aktivasi'] ? 'bg-success' : 'bg-secondary' ?>">
                  <?= $p['aktivasi'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td>
                <!-- Tombol Edit memicu modal -->
                <button class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#modalPegawai"
                        data-id="<?= $p['id'] ?>"
                        data-nip="<?= $p['nip'] ?>"
                        data-nama="<?= $p['nama'] ?>"
                        data-unit="<?= $p['unit_kerja_id'] ?? '' ?>"
                        data-jabatan="<?= $p['jabatan_id'] ?? '' ?>"
                        data-aktivasi="<?= $p['aktivasi'] ?>">
                  ✏️ Edit
                </button>

              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <nav class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page-1 ?>">Prev</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Pencarian realtime
document.getElementById("searchInput").addEventListener("keyup", function() {
  let input = this.value.toLowerCase();
  let rows = document.querySelectorAll("tbody tr");
  rows.forEach(row => {
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(input) ? "" : "none";
  });
});

// Isi form modal saat klik Edit / Tambah
const modalPegawai = document.getElementById('modalPegawai');
modalPegawai.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;

  // Jika tombol pemicu tidak punya data-id → mode tambah
  const id = button.getAttribute('data-id') || '';

  const modalTitle = document.getElementById('modalTitle');
  const submitBtn = document.getElementById('form-submit');
  const form = document.getElementById('pegawaiForm');

  if (id) {
    // Mode EDIT: isi semua field dari data-*
    modalTitle.textContent = 'Edit Pegawai';
    submitBtn.textContent = '💾 UPDATE';
    form.action = "<?= BASEURL ?>/pegawai/update";

    document.getElementById('form-id').value = id;
    document.getElementById('form-nip').value = button.getAttribute('data-nip') || '';
    document.getElementById('form-nama').value = button.getAttribute('data-nama') || '';

    // Set select ke ID yang dikirim; jika kosong, tetap di "PILIH"
    const unitId = button.getAttribute('data-unit') || '';
    const jabatanId = button.getAttribute('data-jabatan') || '';
    const aktivasiVal = button.getAttribute('data-aktivasi');

    document.getElementById('form-unit').value = unitId !== '' ? unitId : '';
    document.getElementById('form-jabatan').value = jabatanId !== '' ? jabatanId : '';
    document.getElementById('form-aktivasi').value = (aktivasiVal === '0' || aktivasiVal === '1') ? aktivasiVal : '';
  } else {
    // Mode TAMBAH: kosongkan semua field dan set dropdown ke "PILIH"
    modalTitle.textContent = 'Form Pegawai';
    submitBtn.textContent = '💾 SIMPAN';
    form.action = "<?= BASEURL ?>/pegawai/simpan";

    document.getElementById('form-id').value = '';
    document.getElementById('form-nip').value = '';
    document.getElementById('form-nama').value = '';

    // Set ke placeholder "PILIH"
    document.getElementById('form-unit').value = '';
    document.getElementById('form-jabatan').value = '';
    document.getElementById('form-aktivasi').value = '';
  }
});
</script>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
