<?php
require_once __DIR__ . '/../../helpers/QRHelper.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cetak Jadwal Kerja</title>
  <link rel="stylesheet" href="<?= BASEURL ?>/public/assets/css/custom.css">
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .header-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 0;
    }
    .logo-side img {
      width: 80px;
    }
    .institusi-info {
      line-height: 1.3;
    }
    .institusi-info .nama-institusi {
      font-size: 20px;
      font-weight: bold;
    }
    .institusi-info .sub-institusi {
      font-size: 16px;
      font-weight: 600;
    }
    .institusi-info .kontak {
      font-size: 12px;
    }
    .judul-wrapper {
      text-align: center;
      margin: 20px 0;
    }
    .judul-wrapper h1 {
      font-size: 22px;
      margin: 0;
    }
    .judul-wrapper h3 {
      font-size: 14px;
      margin: 4px 0;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid black;
      padding: 4px;
      text-align: center;
    }
    .keterangan-shift {
      font-size: 13px;
      margin-top: 20px;
    }
    .badge {
      padding: 4px 8px;
      border-radius: 4px;
      display: inline-block;
    }
    tbody tr {
      border-bottom: 0.5px solid #ccc;
    }
  </style>
</head>
<body onload="window.print()">

  <!-- 📌 HEADER INSTITUSI -->
  <div class="header-wrap">
    <div class="logo-side">
      <img src="<?= BASEURL ?>/public/storage/gambar/<?= $institusi['logo'] ?>" alt="Logo Institusi">
    </div>
    <div class="institusi-info">
      <div class="nama-institusi"><?= $institusi['nama'] ?></div>
      <div class="sub-institusi"><?= $institusi['sub_nama'] ?></div>
      <div class="kontak"><?= $institusi['alamat'] ?> - <?= $institusi['telepon'] ?></div>
    </div>
  </div>

  <!-- 📢 JUDUL DOKUMEN -->
  <div class="judul-wrapper">
    <h1>JADWAL KERJA KARYAWAN</h1>
    <h3>UNIT <?= getNamaUnit($units, $unit_id ?? '') ?></h3>
    <h3>PERIODE BULAN : <?= ucwords(strtolower($bulan)) ?> <?= $tahun ?></h3>
  </div>

<?php
function getHurufHari($tgl) {
  $map = [
    'Sun' => 'M', 'Mon' => 'S', 'Tue' => 'S',
    'Wed' => 'R', 'Thu' => 'K', 'Fri' => 'J', 'Sat' => 'S'
  ];
  return $map[date('D', strtotime($tgl))] ?? '-';
}
?>

  <!-- 🧾 TABEL JADWAL -->
  <table>
    <thead>
      <tr>
        <th>NIP</th><th>Nama</th>
        <?php foreach ($tanggal_array as $tgl): ?>
          <?php
            $huruf_hari = getHurufHari($tgl);
            $is_minggu  = (date('D', strtotime($tgl)) === 'Sun');
            $warna      = $is_minggu ? 'color:red;' : '';
          ?>
          <th style="<?= $warna ?>">
            <?= date('d', strtotime($tgl)) ?><br><?= $huruf_hari ?>
          </th>
        <?php endforeach; ?>
        <th>Total Jam</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pegawai_list as $peg): ?>
        <tr>
          <td><?= $peg['nip'] ?></td>
          <td><?= $peg['nama'] ?></td>
          <?php
            $total_jam = 0;
            foreach ($tanggal_array as $tgl):
              $kode = $jadwal_tersimpan[$peg['nip']][$tgl] ?? '-';
              $class = 'bg-shift-' . strtolower($kode);
              $jam   = $jam_per_shift[$kode] ?? 0;
              $total_jam += $jam;
          ?>
            <td class="<?= $class ?>"><?= $kode ?></td>
          <?php endforeach; ?>
          <td><?= $total_jam ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- 🧭 KETERANGAN SHIFT -->
  <div class="keterangan-shift mt-4">
    <strong>Keterangan Shift:</strong><br>
    <span class="badge bg-shift-c">C</span> = Cuti &nbsp;
    <span class="badge bg-shift-l">L</span> = Libur &nbsp;
    <span class="badge bg-shift-skt">SKT</span> = Sakit &nbsp;
    <span class="badge bg-shift-tl">TL</span> = Tugas Luar &nbsp;
    <span class="badge bg-shift-p1">P1</span> = Pagi &nbsp;
    <span class="badge bg-shift-pmid">P*</span> = Middle Pagi &nbsp;
    <span class="badge bg-shift-smid">S*</span> = Middle Siang
  </div>

  <!-- 🖋️ PENANDATANGAN -->
  <br><br>
  <?php
  // Urutkan pegawai berdasarkan jabatan_id (semakin kecil = semakin tinggi)
  usort($pegawai_list, function($a, $b) {
      return $a['jabatan_id'] <=> $b['jabatan_id'];
  });
  ?>

  <?php foreach ($pegawai_list as $p): ?>
    <?php if ($p['jabatan'] != '-' && !empty($p['nip'])): ?>
      <?php $qr = ensureQrGenerated($p); ?>
      <div style="width: 300px; float: right; margin: 30px 20px 0 0; line-height: 1.4; text-align: left;">
        <div style="margin-bottom: 6px;">
          Malang, <?= date('d F Y') ?>
        </div>
        <div style="margin-bottom: 6px;">
          <?= $p['jabatan'] ?> <?= getNamaUnit($units, $p['unit_kerja_id'] ?? '') ?>
        </div>
        <div style="margin: 8px 0;">
          <img src="<?= BASEURL ?>/public/qrcode/<?= $qr ?>" alt="QR Code" width="120">
        </div>
        <div style="margin-bottom: 4px;">
          <strong><?= strtoupper($p['nama']) ?></strong>
        </div>
        <div><?= $p['nip'] ?></div>
      </div>
      <?php break; ?>
    <?php endif; ?>
  <?php endforeach; ?>

</body>
</html>
