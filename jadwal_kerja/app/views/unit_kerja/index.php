<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">

      <!-- Card Header dengan tombol tambah -->
      <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <strong>Unit Kerja</strong>
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalUnitKerja">
            <i class="fas fa-plus me-1"></i> Tambah Unit Kerja
          </button>
        </div>
      </div>

      <!-- Modal Form Tambah -->
      <div class="modal fade" id="modalUnitKerja" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="<?= BASEURL ?>/UnitKerja/simpan">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Unit Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="nama_unit_kerja">Nama Unit Kerja</label>
                  <input type="text" name="nama_unit_kerja" id="nama_unit_kerja" class="form-control"
                         placeholder="Contoh: Keuangan, Umum, SDM" required>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-success">💾 Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Tabel Daftar dengan Pagination -->
      <div class="card mt-4">
        <div class="card-header bg-primary text-white"><strong>Daftar Unit Kerja</strong></div>
        <div class="card-body">
          <table class="table table-bordered table-sm">
            <thead class="table-light text-center">
              <tr>
                <th style="width: 60px;">No</th>
                <th>Nama Unit Kerja</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Pagination logic
              $perPage = 8;
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $total = count($unitKerja);
              $totalPages = ceil($total / $perPage);
              $start = ($page - 1) * $perPage;
              $slice = array_slice($unitKerja, $start, $perPage);

              $no = $start + 1;
              foreach ($slice as $uk): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><?= htmlspecialchars($uk['nama_unit_kerja']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Pagination Controls -->
          <nav>
            <ul class="pagination justify-content-center">
              <!-- Tombol Prev -->
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?>">Prev</a>
              </li>

              <!-- Nomor Halaman -->
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Tombol Next -->
              <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
              </li>
            </ul>
          </nav>

        </div>
      </div>

    </div>
  </section>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>
