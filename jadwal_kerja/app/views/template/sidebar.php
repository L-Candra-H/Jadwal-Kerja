<?php
include_once __DIR__ . '/../../helpers/AuthGuard.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function isActive($route)
{
  return strpos($_SERVER['REQUEST_URI'], $route) !== false ? 'active' : '';
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand -->
  <a href="<?= BASEURL ?>/profil" class="brand-link text-white">
    <span class="brand-text fw-bold">👤 <?= $_SESSION['nama'] ?? 'Profil Saya' ?></span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-widget="treeview">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="<?= BASEURL ?>/dashboard/index" class="nav-link">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Pengaturan Dasar -->
        <?php if (canAccess('setting_institusi') || canAccess('setting_bulan') || canAccess('user_akses')): ?>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Pengaturan Dasar <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (canAccess('setting_institusi')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/setting/institusi" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Setting Institusi</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('setting_bulan')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/setting/bulankerja" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Setting Bulan Kerja</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('user_akses')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/HakAkses" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Setting Hak Akses</p>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Pengaturan Lanjutan -->
        <?php if (canAccess('unit_kerja') || canAccess('jabatan') || canAccess('shift_kerja')): ?>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tools"></i>
            <p>Pengaturan Lanjutan <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (canAccess('unit_kerja')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/UnitKerja/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Unit Kerja</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('jabatan')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/jabatan/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Jabatan</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('shift_kerja')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/ShiftKerja/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Shift Kerja</p>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Input Data -->
        <?php if (canAccess('pegawai') || canAccess('jadwal_kerja') || canAccess('pesan_jadwal') || canAccess('persetujuan_jadwal')): ?>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>Input Data <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (canAccess('pegawai')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/pegawai/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pegawai</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('jadwal_kerja')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/jadwal/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Jadwal Kerja</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('pesan_jadwal')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/PesanJadwal/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pesan Jadwal</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('persetujuan_jadwal')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/PesanJadwal/persetujuan" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Persetujuan Jadwal</p>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Riwayat dan Laporan -->
        <?php if (canAccess('rekap_jadwal') || canAccess('rekap_pesan_jadwal') || canAccess('laporan')): ?>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Riwayat & Laporan <i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (canAccess('rekap_jadwal')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/laporanjadwal/jadwal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Rekap Jadwal Kerja</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('rekap_pesan_jadwal')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/rekappesan" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Rekap Pesan Jadwal</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('laporan')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/rekap/index" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Laporan Jadwal Kerja</p>
                </a>
              </li>
            <?php endif; ?>
            <?php if (canAccess('grafik_jadwal')): ?>
              <li class="nav-item">
                <a href="<?= BASEURL ?>/grafikjadwal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Grafik Jadwal Kerja</p>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Logout -->
        <li class="nav-item">
          <a href="<?= BASEURL ?>/auth/logout" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
