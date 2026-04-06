<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/sidebar.php';
?>

<!-- Mulai konten utama -->
<div class="content-wrapper" 
     style="background: url('/jadwal_kerja/public/img/bg-login.png') no-repeat center center fixed;
            background-size: cover;">
  <section class="content pt-4">
    <div class="container-fluid">
      <div class="card mb-4 shadow-sm border-start border-success" style="background-color: #e9fce9;">
        <div class="card-body">
           <h4 class="card-title text-success">
             Selamat Datang, <?= htmlspecialchars($data['username']) ?>! 🎉
           </h4>
           <p class="card-text">
             Anda login sebagai <strong><?= htmlspecialchars($data['role']) ?></strong>.
           </p>
        </div>
      </div>

      <div class="row">
        <!-- SUPERADMIN: Jumlah Total Pegawai -->
        <?php if ($data['role'] === 'SUPERADMIN'): ?>
          <div class="col-md-4">
            <div class="card bg-primary text-white mb-3">
              <div class="card-body">
                <h5 class="card-title">Jumlah Total Pegawai : </h5>
                <p class="card-text" style="font-weight: bold;"><?= $data['jumlahPegawai'] ?> orang</p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- ADMIN & USER: Jumlah Pegawai Unit -->
        <?php if (in_array($data['role'], ['ADMIN', 'USER'])): ?>
          <div class="col-md-4">
            <div class="card bg-info text-white mb-3">
              <div class="card-body">
                <h5 class="card-title">Jumlah Pegawai</h5>
                <p class="card-text mb-1">UNIT: <strong><?= htmlspecialchars($unit_nama ?? '—') ?></strong></p>
               <p class="card-text" style="font-weight: bold;"><?= count($pegawai_list ?? []) ?> orang</p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- ADMIN & USER: Shift Hari Ini -->
        <?php if (in_array($data['role'], ['ADMIN', 'USER'])): ?>
          <div class="col-md-4">
            <div class="card bg-warning text-dark mb-3">
              <div class="card-body">
                <h5 class="card-title">Shift Hari Ini</h5>
                <p class="card-text mb-1" style="font-weight: bold; font-size: 16px;">
                  <?= (new DateTime())->format('l, d F Y') ?>
                </p>
                <p class="card-text" style="font-weight: bold;">
                  <?= htmlspecialchars($data['namaShiftHariIni']) ?>
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card bg-info text-dark mb-3">
              <div class="card-body">
                <h5 class="card-title">Shift Besok</h5>
                <p class="card-text mb-1" style="font-weight: bold; font-size: 16px;">
                  <?= (new DateTime('+1 day'))->format('l, d F Y') ?>
                </p>
                <p class="card-text" style="font-weight: bold;">
                  <?= htmlspecialchars($data['namaShiftBesok'] ?? 'Belum dijadwalkan') ?>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- ADMIN: Jumlah Pesan Belum Disetujui -->
        <?php if ($data['role'] === 'ADMIN'): ?>
          <div class="col-md-4">
            <div class="card bg-danger text-white text-dark mb-3">
              <div class="card-body">
                <h5 class="card-title">Pesan Jadwal Belum Disetujui : </h5>
                <p class="card-text" style="font-weight: bold;"><?= $data['jumlahPesanBelum'] ?> orang</p>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>
<!-- Akhir konten -->

<script>
  setInterval(() => {
    location.reload();
  }, 1800000); // 30 menit = 1800000 ms
</script>

<?php include __DIR__ . '/../template/footer.php'; ?>
