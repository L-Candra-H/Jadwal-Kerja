<?php
// Hindari definisi ulang BASEURL
if (!defined('BASEURL')) {
  define('BASEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/jadwal_kerja');
}

date_default_timezone_set('Asia/Jakarta'); // atau 'Asia/Makassar' sesuai lokasi kamu

// Konfigurasi database dalam bentuk array
return [
  'host'     => 'localhost',
  'database' => 'jadwal_kerja',
  'user'     => 'root',
  'password' => ''

];


