<?php include __DIR__ . '/../template/header.php'; ?>
<?php include __DIR__ . '/../template/sidebar.php'; ?>
<?php include __DIR__ . '/../template/content-start.php'; ?>

<!-- Filter Jadwal -->
<div class="card">
  <div class="card-header bg-primary text-white">
    <strong>Form Jadwal Kerja</strong>
  </div>
  <div class="card-body">
    <form method="get" action="<?= BASEURL ?>/jadwal/index">
      <div class="row">
        <div class="col-md-4">
          <label>Unit Kerja</label>
          <?php if ($_SESSION['user']['role'] === 'superadmin'): ?>
            <select name="unit_kerja_id" class="form-control" required>
              <option value="">-- Pilih Unit --</option>
              <?php foreach ($units as $unit): ?>
                <option value="<?= $unit['id'] ?>" <?= ($unit_kerja_id == $unit['id']) ? 'selected' : '' ?>>
                  <?= $unit['nama_unit_kerja'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="hidden" name="unit_kerja_id" value="<?= $unit_kerja_id ?>">
            <input type="text" class="form-control" value="<?= getNamaUnit($units, $unit_kerja_id) ?>" readonly>
          <?php endif; ?>
        </div>
        <div class="col-md-2">
          <label>Tahun</label>
          <select name="tahun" class="form-control" required>
            <option value="" selected disabled>-- Pilih Tahun --</option>
            <?php 
              $startYear = 2025;
              $endYear   = date('Y') + 3;
              for ($yr = $startYear; $yr <= $endYear; $yr++): 
            ?>
              <option value="<?= $yr ?>" <?= ($tahun ?? '') == $yr ? 'selected' : '' ?>><?= $yr ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label>Bulan</label>
          <select name="bulan" class="form-control" required>
            <option value="">-- Pilih Bulan -- </option>
            <?php foreach ($bulan_kalender as $nama => $isi): ?>
              <option value="<?= $nama ?>" <?= ($bulan_selected ?? '') == $nama ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto align-self-end mt-3">
          <button class="btn btn-primary me-2">📄 Tampilkan</button>
          <a href="<?= BASEURL ?>/dashboard/index" class="btn btn-secondary">🔙 Kembali</a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tabel Jadwal -->
<?php if (!empty($pegawai_list) && !empty($tanggal_array)): ?>
<form method="post" action="<?= BASEURL ?>/jadwal/save">
  <input type="hidden" name="unit_kerja_id" value="<?= $unit_kerja_id ?>">
  <input type="hidden" name="bulan" value="<?= $bulan_selected ?>">
  <input type="hidden" name="tahun" value="<?= $tahun ?>">

  <div class="card mt-4">
    <div class="card-header bg-info text-white clearfix">
      <h5 class="card-title d-inline"><strong>Jadwal Pegawai</h5></strong>
      <div class="float-right">
        <button class="btn btn-success btn px-4 me-2" type="submit">💾 Simpan</button>
        <a href="<?= BASEURL ?>/cetak/popup?unit_kerja_id=<?= $unit_kerja_id ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan_selected ?>"
           onclick="window.open(this.href, 'popup', 'width=1200,height=700'); return false;"
           class="btn btn-light btn px-4">
          🖨️ Cetak
        </a>
      </div>
    </div>
  </div>

  <div class="card-body table-container">
    <table class="table table-bordered table-sm text-center align-middle table-shift">
      <thead>
        <tr>
          <th class="sticky-column nip" rowspan="2">NIP</th>
          <th class="sticky-column nama" rowspan="2">Nama</th>
          <th colspan="<?= count($tanggal_array) ?>">Tanggal</th>
          <th rowspan="2">Total Jam</th>
        </tr>
        <tr>
          <?php foreach ($tanggal_array as $tgl): ?>
            <?php $isMinggu = date('w', strtotime($tgl)) == 0; ?>
            <th<?= $isMinggu ? ' class="minggu"' : '' ?>>
              <?= date('d', strtotime($tgl)) ?><br>
              <small><?= date('D', strtotime($tgl)) ?></small>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pegawai_list as $peg): ?>
          <tr>
            <td class="sticky-column nip"><?= $peg['nip'] ?></td>
            <td class="sticky-column nama"><?= $peg['nama'] ?></td>
            <?php foreach ($tanggal_array as $tgl): ?>
              <?php
                $terjadwal  = $jadwal_tersimpan[$peg['nip']][$tgl] ?? '';
                $dipesan    = $jadwal_dipesan[$peg['nip']][$tgl] ?? '';
                $terpilih   = $terjadwal ?: $dipesan;
                $is_dipesan = (!$terjadwal && $dipesan);
              ?>
              <td>
                <select name="jadwal[<?= $peg['nip'] ?>][<?= $tgl ?>]"
                        class="form-select form-select-sm shift-select"
                        data-nip="<?= $peg['nip'] ?>"
                        data-tgl="<?= $tgl ?>"
                        <?= $is_dipesan ? 'title="Dari permintaan jadwal yang telah disetujui"' : '' ?>>
                  <option value="">--</option>
                  <?php foreach ($shift_options as $s): ?>
                    <option value="<?= $s['kode'] ?>"
                            data-durasi="<?= $s['durasi'] ?>"
                            <?= $terpilih == $s['kode'] ? 'selected' : '' ?>>
                      <?= $s['kode'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
            <?php endforeach; ?>
            <td class="total-jam" data-nip="<?= $peg['nip'] ?>">0</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="keterangan-shift mt-4">
      <strong>Keterangan:</strong><br>
      <span class="badge bg-shift-c">C</span> = Cuti,
      <span class="badge bg-shift-l">L</span> = Libur,
      <span class="badge bg-shift-skt">SKT</span> = Sakit,
      <span class="badge bg-shift-tl">TL</span> = Tugas Luar,
      <span class="badge bg-shift-p1">P1</span> = Pagi,
      <span class="badge bg-shift-pmid">P*</span> = Middle Pagi,
      <span class="badge bg-shift-smid">S*</span> = Middle Siang
    </div>
  </div>
</div>

<script>
  const warnaShift = {
    'C':  '#FFFF00',
    'L':  '#FF0000',
    'SKT':'#964B00',
    'TL': '#00FF00',
    'P1': '#d1e7ff',
    'P*': '#ffe5b4',
    'S*': '#c9f5d9'
  };

  function warnaiSelect(el) {
    const kode = el.value;
    el.style.backgroundColor = warnaShift[kode] || '';
    el.style.color = (kode === 'L' || kode === 'SKT') ? '#fff' : '#000';
  }

  document.querySelectorAll('select.shift-select').forEach(el => {
    warnaiSelect(el);
    el.addEventListener('change', () => warnaiSelect(el));
  });
</script>

</form>
<?php endif; ?>

<?php include __DIR__ . '/../template/content-end.php'; ?>
<?php include __DIR__ . '/../template/footer.php'; ?>

<!-- ✅ Total Jam Kerja Otomatis -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const shiftSelects = document.querySelectorAll(".shift-select");
  const skipKode = ['C', 'L', 'SKT', 'TL'];

  function hitungTotalJam(nip) {
    let total = 0;
    const shifts = document.querySelectorAll(`select[data-nip="${nip}"]`);
    
    shifts.forEach(select => {
      const selected = select.options[select.selectedIndex];
      const kode = selected?.value;
      const durasi = selected?.getAttribute("data-durasi");

      if (!skipKode.includes(kode) && durasi && !isNaN(durasi)) {
        total += parseFloat(durasi);
      }
    });

    const cell = document.querySelector(`.total-jam[data-nip="${nip}"]`);
    if (cell) cell.textContent = total;
  }

  // Hitung semua NIP saat halaman pertama dibuka
  const semuaNIP = new Set();
  shiftSelects.forEach(select => semuaNIP.add(select.getAttribute("data-nip")));
  semuaNIP.forEach(nip => hitungTotalJam(nip));

  // Hitung ulang saat ada perubahan
  shiftSelects.forEach(select => {
    select.addEventListener("change", function () {
      const nip = this.getAttribute("data-nip");
      hitungTotalJam(nip);
    });
  });
});
</script>
});
</script>
