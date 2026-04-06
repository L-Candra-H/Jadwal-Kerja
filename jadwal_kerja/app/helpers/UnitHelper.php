<?php
function getNamaUnit($units, $id): string {
  if (is_array($units)) {
    // Kalau formatnya associative: [1 => 'Produksi']
    if (isset($units[$id]) && is_string($units[$id])) {
      return $units[$id];
    }
    // Kalau formatnya array of array: [['id' => 1, 'nama_unit_kerja' => 'Produksi']]
    foreach ($units as $u) {
      if (is_array($u) && isset($u['id']) && $u['id'] == $id) {
        return $u['nama_unit_kerja'] ?? 'Unit Tidak Dikenali';
      }
    }
  }
  return 'Unit Tidak Dikenali';
}
