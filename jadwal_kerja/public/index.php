<?php
// 🚀 Start Session — WAJIB sebelum output apapun
if (session_status() === PHP_SESSION_NONE) session_start();

// 🔍 Aktifkan Error untuk Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 📂 Load Helper dan Konfigurasi
require_once '../app/helpers/AuthGuard.php';
require_once '../app/helpers/UnitHelper.php';
require_once '../config/config.php';

// ⚙️ Load Core System
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Database.php';

// 🚦 Start the App
$app = new App();
