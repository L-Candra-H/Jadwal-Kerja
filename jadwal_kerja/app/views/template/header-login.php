<?php
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: microphone=(), camera=()");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Jadwal Kerja</title>

  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= BASEURL ?>/assets/plugins/fontawesome-free/css/all.min.css" />
  <link rel="icon" type="image/png" href="<?= BASEURL ?>/assets/img/favicon.png">
  <meta name="theme-color" content="#007bff">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="preload" href="<?= BASEURL ?>/assets/plugins/fontawesome-free/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('<?= BASEURL ?>/img/bg-login.png') center/cover no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-wrapper {
      background: rgba(255, 255, 255, 0.94);
      padding: 32px 24px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      text-align: center;
      font-size: 14px;
      margin-bottom: 0;
      margin-left: auto;
      margin-right: auto;
    }

    .login-title {
      font-size: 21px;                /* Lebih besar dari sebelumnya */
      font-weight: 700;               /* Lebih tebal */
      color: #007bff;                 /* Warna biru untuk kontras visual */
      text-align: center;
      text-transform: uppercase;      /* Agar tampak branding */
      letter-spacing: 1px;
      margin-bottom: 30px;
      line-height: 1.2;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-control {
      display: block;
      width: 100%;
      height: 44px;
      font-size: 15px;
      padding: 10px 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      box-sizing: border-box;
    }

    .form-control:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
    }

    .btn-primary {
      width: 100%;
      background-color: #007bff;
      border: none;
      font-weight: 600;
      font-size: 16px;
      padding: 12px;
      border-radius: 6px;
      transition: background-color 0.2s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .footer {
      font-size: 12px;
      color: #ffffff;
      text-align: center;
      margin-top: 24px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body class="login-page">
