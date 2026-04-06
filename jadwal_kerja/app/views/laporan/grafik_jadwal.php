<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<?php
  $nama_bulan = '-';
  foreach ($bulan_kalender as $bk) {
    if (substr($bk['tanggal_mulai'], 5, 2) === $bulan) {
      $nama_bulan = $bk['nama_bulan'];
      break;
    }
  }
?>

<div class="container-fluid px-0">
  <div class="card mt-0 mb-3">
    <div class="card-header bg-primary text-white">
      <strong>Grafik Jadwal Kerja — Tabel Distribusi Real</strong>
    </div>
    <div class="card-body">
      <!-- 🔍 Filter -->
      <form method="GET" class="form-row align-items-end mb-4">
        <!-- Unit Kerja -->
        <div class="col-md-4">
          <label>Unit Kerja</label>
          <?php if (strtolower($_SESSION['user']['role']) === 'superadmin'): ?>
            <select name="unit_kerja_id" class="form-control" required>
              <option value="">-- Pilih Unit --</option>
              <?php foreach ($units as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($unit_kerja_id == $u['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['nama_unit_kerja']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="hidden" name="unit_kerja_id" value="<?= $unit_kerja_id ?>">
            <input type="text" readonly class="form-control" value="<?php
              foreach ($units as $u) {
                if ($u['id'] == $unit_kerja_id) echo $u['nama_unit_kerja'];
              }
            ?>">
          <?php endif; ?>
        </div>

        <!-- Tahun -->
        <div class="col-md-2">
          <label>Tahun</label>
          <select name="tahun" class="form-control" required>
            <option value="">-- Pilih Tahun --</option>
            <?php foreach (range(date('Y') - 2, date('Y') + 2) as $yr): ?>
              <option value="<?= $yr ?>" <?= ($tahun == $yr) ? 'selected' : '' ?>><?= $yr ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Bulan -->
        <div class="col-md-3">
          <label>Bulan</label>
          <select name="bulan" class="form-control" required>
            <option value="">-- Pilih Bulan --</option>
            <?php foreach ($bulan_kalender as $bk):
              $kode_bulan = substr($bk['tanggal_mulai'], 5, 2); ?>
              <option value="<?= $kode_bulan ?>" <?= ($bulan == $kode_bulan) ? 'selected' : '' ?>>
                <?= htmlspecialchars($bk['nama_bulan']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Tombol Aksi -->
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary">📄 Tampilkan</button>
          <a href="<?= BASEURL ?>/dashboard" class="btn btn-secondary">🔙 Kembali</a>
        </div>
      </form>

      <!-- 💼 Rekap Real -->
      <?php if ($show_grafik): ?>
        <div class="row">
          <!-- 🔁 Rekap Status Persetujuan Pesan Jadwal -->
          <div class="col-md-6 mb-4">
            <div class="card border-info">
              <div class="card-header bg-info text-white fw-bold">
                ✅ <strong>Total Pesan Jadwal — <?= $nama_bulan ?> <?= $tahun ?> </strong>
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between text-success">
                    <span><i class="fas fa-check-circle me-2 text-success"></i> DISETUJUI</span>
                    <strong><?= $pesan_status['disetujui'] ?></strong>
                  </li>
                  <li class="list-group-item d-flex justify-content-between text-danger">
                    <span><i class="fas fa-times-circle me-2 text-danger"></i> DITOLAK</span>
                    <strong><?= $pesan_status['ditolak'] ?></strong>
                  </li>
                  <li class="list-group-item d-flex justify-content-between text-primary">
                    <span>Total Pesan Jadwal</span>
                    <strong><?= $pesan_status['disetujui'] + $pesan_status['ditolak'] ?></strong>
                  </li>
                </ul>
                <!-- Bar Chart Persetujuan -->
                <canvas id="grafikPersetujuanBar" height="240" class="mt-4"></canvas>
              </div>
            </div>
          </div>

          <!-- Distribusi Shift -->
          <div class="col-md-6 mb-4">
            <div class="card border-secondary">
              <div class="card-header bg-secondary text-white fw-bold">
                🍰 <strong> Distribusi Jadwal Kerja — <?= $nama_bulan ?> <?= $tahun ?> </strong>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-bordered table-sm">
                  <thead class="table-light">
                    <tr>
                      <th>Kode Shift</th>
                      <th>Jumlah Jadwal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($grafik_shift as $kode => $jumlah): ?>
                      <tr>
                        <td><strong><?= $kode ?></strong></td>
                        <td><?= $jumlah ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
                <?php if (array_sum($grafik_shift) === 0): ?>
                  <div class="alert alert-warning mt-3">
                    Tidak ada distribusi shift ditemukan untuk bulan <strong><?= $nama_bulan ?></strong>.
                  </div>
                <?php else: ?>
                  <!-- Pie Chart Shift -->
                  <canvas id="grafikShiftPie" height="240" class="mt-4"></canvas>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      <?php elseif ($is_bulan_valid): ?>
        <div class="alert alert-warning">
          Tidak ditemukan data jadwal kerja maupun pesan jadwal pada bulan <strong><?= $nama_bulan ?></strong> <?= $tahun ?>.
        </div>
      <?php else: ?>
        <div class="alert alert-info">
          Pilih <strong>Unit</strong>, <strong>Tahun</strong>, dan <strong>Bulan</strong> untuk melihat rekap 📋
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const grafikPersetujuanBar = new Chart(document.getElementById('grafikPersetujuanBar'), {
    type: 'bar',
    data: {
      labels: ['DISETUJUI', 'DITOLAK'],
      datasets: [{
        label: 'Jumlah',
        data: [<?= $pesan_status['disetujui'] ?>, <?= $pesan_status['ditolak'] ?>],
        backgroundColor: ['#198754', '#dc3545']
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
  });

  const grafikShiftPie = new Chart(document.getElementById('grafikShiftPie'), {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_keys($grafik_shift)) ?>,
      datasets: [{
        data: <?= json_encode(array_values($grafik_shift)) ?>,
        backgroundColor: ['#0d6efd','#ffc107','#198754','#dc3545','#6c757d'],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { enabled: true }
      }
    }
  });
</script>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
