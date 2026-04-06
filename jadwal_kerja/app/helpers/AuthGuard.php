<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * Ambil array kode_menu dari session
 */
function getAksesMenu(): array
{
  return $_SESSION['akses_menu'] ?? [];
}

/**
 * Cek apakah user punya akses ke menu tertentu
 */
function canAccess(string $menu): bool
{
  // Jika role belum tersedia
  if (!isset($_SESSION['user']['role'])) return false;

  // SUPERADMIN boleh akses semua
  if (strtolower($_SESSION['user']['role']) === 'superadmin') return true;

  // Cek apakah menu ada dalam session akses_menu
  return in_array($menu, getAksesMenu());
}
