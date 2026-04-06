<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<!-- Container menyatu dengan sidebar -->
<div class="container-fluid px-0">
  <div class="card mt-0 mb-3">
    <div class="card-header bg-primary text-white">
      <strong>Rekap Jadwal Kerja</strong>
    </div>
    <div class="card-body">

      <!-- Form Filter -->
      <form method="GET" action="<?= BASEURL ?>/laporanjadwal/jadwal" class="mb-3">
        <div class="row g-3 align-items-end">

          <!-- Unit Kerja -->
          <div class="col-md-4">
            <label>Unit Kerja</label>
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
              <input type="text" class="form-control" value="<?php
                foreach ($units as $u) {
                  if ($u['id'] == $unit_kerja_id) echo $u['nama_unit_kerja'];
                }
              ?>" readonly>
            <?php endif; ?>
          </div>

          <!-- Tahun -->
          <div class="col-md-2">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-control">
              <option value="">-- Pilih Tahun --</option>
              <?php foreach (range(date('Y') - 2, date('Y') + 2) as $yr): ?>
                <option value="<?= $yr ?>" <?= ($tahun ?? date('Y')) == $yr ? 'selected' : '' ?>><?= $yr ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Bulan -->
          <div class="col-md-3">
            <label hidden class="form-label">Bulan </label>
            <select name="bulan" class="form-control" hidden>
              <option value="">Pilih Bulan</option>
              <?php foreach ($bulan_kalender as $nama => $isi): ?>
                <option value="<?= $nama ?>" <?= ($bulan ?? '') == $nama ? 'selected' : '' ?>><?= $nama ?></option>
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

      <?php
      function getKalenderKerja($tanggal) {
        $tgl = (int)date('d', strtotime($tanggal));
        $bln = (int)date('m', strtotime($tanggal));
        $tahun = (int)date('Y', strtotime($tanggal));
        $map = [1=>'JANUARI',2=>'FEBRUARI',3=>'MARET',4=>'APRIL',5=>'MEI',6=>'JUNI',7=>'JULI',8=>'AGUSTUS',9=>'SEPTEMBER',10=>'OKTOBER',11=>'NOVEMBER',12=>'DESEMBER'];

        if ($tgl >= 16) {
          $bulan = $map[($bln % 12) + 1];
          $tahun_kal = ($bln == 12) ? $tahun + 1 : $tahun;
        } else {
          $bulan = $map[$bln];
          $tahun_kal = $tahun;
        }

        return ['bulan' => $bulan, 'tahun' => $tahun_kal];
      }
      ?>

      <?php if (!empty($riwayat)): ?>
        <hr class="mb-2 mt-1">
        <div class="card-header bg-primary text-white mb-0">
          <strong>Daftar Jadwal Kerja</strong>
        </div>

        <table class="table table-bordered table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Unit Kerja</th>
              <th>Tahun Kalender</th>
              <th>Bulan Kalender</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($riwayat as $row):
              $periode = getKalenderKerja($row['tanggal']);
            ?>
              <tr>
                <td><?= htmlspecialchars($row['unit']) ?></td>
                <td><?= $periode['tahun'] ?></td>
                <td><?= $periode['bulan'] ?></td>
                <td>
                  <a href="<?= BASEURL ?>/cetak/popup&unit_kerja_id=<?= $row['unit_kerja_id'] ?>&tahun=<?= $periode['tahun'] ?>&bulan=<?= urlencode($periode['bulan']) ?>"
                     onclick="window.open(this.href, 'cetakPopUp', 'width=1200,height=700,scrollbars=yes'); return false;"
                     class="btn btn-info btn-sm">🖨️ Cetak</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="alert alert-warning mt-4">
          Silakan pilih <strong>Tahun</strong> untuk menampilkan daftar jadwal kerja.
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
