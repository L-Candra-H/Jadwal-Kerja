<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">

      <!-- Card Shift Kerja (Read Only) -->
      <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0"><strong>Shift Kerja (READ ONLY)</strong></h5>
          <a href="<?= BASEURL ?>/dashboard/index" class="btn btn-secondary btn-sm">
            🔙 Kembali
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0 text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 10%;">Kode</th>
                  <th style="width: 20%;">Nama</th>
                  <th style="width: 15%;">Durasi (Jam)</th>
                  <th style="width: 40%;">Catatan</th>
                  <th style="width: 15%;">Urutan</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Pagination logic
                $perPage = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $total = count($shift);
                $totalPages = ceil($total / $perPage);
                $start = ($page - 1) * $perPage;
                $slice = array_slice($shift, $start, $perPage);

                foreach ($slice as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['kode']); ?></td>
                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                    <td><?= htmlspecialchars($row['durasi']); ?></td>
                    <td class="text-wrap"><?= htmlspecialchars($row['catatan']); ?></td>
                    <td><?= htmlspecialchars($row['nomor']); ?></td>
                  </tr>
                <?php endforeach; ?>
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
