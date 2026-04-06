</div> <!-- /.wrapper -->

<footer class="main-footer text-sm text-center">
  <strong>&copy; <?= date('Y') ?> Jadwal Kerja Karyawan.</strong>
  All rights reserved.
</footer>

<!-- Script JS -->
<script src="<?= BASEURL ?>/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= BASEURL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASEURL ?>/assets/js/adminlte.min.js"></script>

<!-- SweetAlert2 (jika digunakan) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom JS -->
<script src="<?= BASEURL ?>/assets/js/custom.js"></script>

<!-- ✅ Sidebar Interaktif -->
<script>
  window.onload = function () {
    const sidebar = document.querySelector('.main-sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    let overlay = document.querySelector('.overlay-sidebar');

    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'overlay-sidebar';
      document.body.appendChild(overlay);
    }

    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        console.log('🍔 Burger clicked');

        const isOpen = document.body.classList.contains('sidebar-open');

        if (isOpen) {
          sidebar.classList.remove('show');
          overlay.classList.remove('show');
          document.body.classList.remove('sidebar-open');
          document.body.classList.add('sidebar-hidden');
        } else {
          sidebar.classList.add('show');
          overlay.classList.add('show');
          document.body.classList.add('sidebar-open');
          document.body.classList.remove('sidebar-hidden');
        }
      });
    }

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
      document.body.classList.remove('sidebar-open');
      document.body.classList.add('sidebar-hidden');
    });
  };
</script>
</body>
</html>
