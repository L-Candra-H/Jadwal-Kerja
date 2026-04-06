<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: microphone=(), camera=()");

$timeout = 300; // dalam detik (5 menit)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
  session_unset();
  session_destroy();
  header('Location: ' . BASEURL . '/auth/login');
  exit;
}
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $title ?? 'Dashboard' ?></title>

  <!-- Stylesheet Utama -->
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/adminlte.min.css" />
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/custom.css" />
  <link rel="preload" href="<?= BASEURL ?>/assets/plugins/fontawesome-free/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="icon" href="<?= BASEURL ?>/public/img/favicon.ico" type="image/x-icon">
  <meta name="theme-color" content="#0000FF">
  <meta name="apple-mobile-web-app-capable" content="yes">
</head>

<?php
  $active_page = basename($_SERVER['PHP_SELF'], '.php');
  $body_class = 'hold-transition sidebar-mini layout-fixed';
  if ($active_page === 'dashboard') {
    $body_class .= ' dashboard';
  }
?>
<body class="<?= $body_class ?>">

  <!-- Main Header -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <!-- Tombol burger sidebar -->
      <li class="nav-item">
        <button id="toggleSidebar"
                class="burger-menu btn btn-sm btn-primary d-md-none"
                type="button"
                aria-label="Toggle sidebar menu">
          <i class="fas fa-bars"></i>
        </button>

      </li>
    </ul>
  </nav>
