<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">

      <!-- Card Jabatan (Read Only) -->
      <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0"><strong>Jabatan (READ ONLY)</strong></h5>
          <a href="<?= BASEURL ?>/dashboard/index" class="btn btn-secondary btn-sm">
            🔙 Kembali
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0 text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 40%;">Nama Jabatan</th>
                  <th style="width: 60%;">Keterangan</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Pagination logic
                $perPage = 8;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $total = count($jabatan);
                $totalPages = ceil($total / $perPage);
                $start = ($page - 1) * $perPage;
                $slice = array_slice($jabatan, $start, $perPage);

                foreach ($slice as $row):
                  if ($row['nama_jabatan'] !== 'Admin'): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['nama_jabatan']); ?></td>
                      <td class="text-wrap"><?= htmlspecialchars($row['keterangan']); ?></td>
                    </tr>
                  <?php endif;
                endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination Controls -->
          <nav class="mt-3">
            <ul class="pagination justify-content-center">
              <!-- Prev -->
              <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?>">Prev</a>
              </li>

              <!-- Nomor Halaman -->
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <!-- Next -->
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
