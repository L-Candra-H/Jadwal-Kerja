// custom.js — file buatan L.
$(document).ready(function () {
  // ✅ Auto-hide alert sukses
  $(".alert-success").delay(3000).slideUp(300);

  // ✅ Highlight menu sidebar aktif
  const currentUrl = window.location.href;
  $('.nav-sidebar a').each(function () {
    if (this.href === currentUrl) {
      $(this).addClass('active');
      $(this).closest('.has-treeview').addClass('menu-open');
      $(this).closest('.nav-treeview').prev().addClass('active');
    }
  });

  // ✅ Tab event Bootstrap 5
  $('#persetujuanTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    console.log('Tab aktif:', e.target.id);
  });

  // ✅ Modal trigger debug
  $('[data-bs-toggle="modal"]').on('click', function () {
    console.log('Modal dipanggil:', $(this).data('bs-target'));
  });
});
