<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<!-- 🟦 Container agar menyatu dengan sidebar -->
<div class="container-fluid px-0">
  <div class="card mt-0 mb-3">
    <div class="card-header bg-primary text-white">
      <strong>Laporan Jadwal Kerja</strong>
    </div>
    <div class="card-body">

      <!-- 🔎 Form Filter -->
      <form method="GET" action="<?= BASEURL ?>/rekap/index" class="mb-3">
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
              <input type="text" class="form-control" readonly value="<?php
                foreach ($units as $u) {
                  if ($u['id'] == $unit_kerja_id) echo $u['nama_unit_kerja'];
                }
              ?>">
            <?php endif; ?>
          </div>

          <!-- Tahun -->
          <div class="col-md-2">
            <label for="tahun">Tahun</label>
            <select name="tahun" id="tahun" class="form-control">
              <option value="">-- Pilih Tahun --</option>
              <?php foreach (range(date('Y') - 2, date('Y') + 2) as $yr): ?>
                <option value="<?= $yr ?>" <?= ($tahun == $yr) ? 'selected' : '' ?>><?= $yr ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Bulan -->
          <div class="col-md-3">
            <label for="bulan">Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
              <option value="">-- Pilih Bulan --</option>
              <?php foreach ($label_bulan as $nama => $label): ?>
                <option value="<?= $nama ?>" <?= ($bulan == $nama) ? 'selected' : '' ?>><?= $label ?></option>
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

      <!-- 📊 Statistik Rekap -->
      <?php if (!empty($pegawai_list) && !empty($tanggal_array)): ?>
      <?php if (!isset($durasi_shift) || !is_array($durasi_shift)) $durasi_shift = []; ?>
        <div class="alert alert-info mt-3">
          <strong>📊 Statistik Rekap:</strong><br>
          <?php
            $unit_nama = '';
            foreach ($units as $u) {
              if ($u['id'] == $unit_kerja_id) {
                $unit_nama = $u['nama_unit_kerja'];
                break;
              }
            }
          ?>
          🏢 Unit Kerja: <strong><?= $unit_nama ?></strong><br>
          📆 Periode: <strong><?= $label_bulan[$bulan] ?? $bulan ?> <?= $tahun ?></strong>
        </div>

        <!-- 🧮 Tabel Rekap -->
        <table class="table table-bordered table-sm mt-2">
          <thead class="table-light text-center">
            <tr>
              <th>NIP</th>
              <th>Nama</th>
              <th>Shift Masuk</th>
              <th>Jam Kerja</th>
              <th>Cuti (C)</th>
              <th>Libur (L)</th>
              <th>Sakit (SKT)</th>
              <th>Tugas Luar (TL)</th>
              <th>% Hadir</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pegawai_list as $pegawai):
              $jumlah_cuti = $jumlah_libur = $jumlah_sakit = $jumlah_tl = $jumlah_masuk = $total_jam = 0;
              foreach ($tanggal_array as $tgl):
                $kode = strtoupper($jadwal_tersimpan[$pegawai['nip']][$tgl] ?? '');
                if ($kode === 'C')       $jumlah_cuti++;
                elseif ($kode === 'L')   $jumlah_libur++;
                elseif ($kode === 'SKT') $jumlah_sakit++;
                elseif ($kode === 'TL')  $jumlah_tl++;
                elseif ($kode !== '')    $jumlah_masuk++;
                $total_jam += $durasi_shift[$kode] ?? 0;
              endforeach;
              $jumlah_total = $jumlah_masuk + $jumlah_cuti + $jumlah_libur + $jumlah_sakit + $jumlah_tl;
              $persen_hadir = $jumlah_total > 0 ? round(($jumlah_masuk / $jumlah_total) * 100, 1) : 0;
            ?>
              <tr>
                <td><?= $pegawai['nip'] ?></td>
                <td><?= $pegawai['nama'] ?></td>
                <td class="text-center"><?= $jumlah_masuk ?></td>
                <td class="text-center"><?= $total_jam ?></td>
                <td class="text-center"><?= $jumlah_cuti ?></td>
                <td class="text-center"><?= $jumlah_libur ?></td>
                <td class="text-center"><?= $jumlah_sakit ?></td>
                <td class="text-center"><?= $jumlah_tl ?></td>
                <td class="text-center"><?= $persen_hadir ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>
