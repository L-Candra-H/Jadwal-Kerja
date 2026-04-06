<?php

class ShiftKerjaModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 📋 Ambil semua data shift kerja urut berdasarkan nomor
  public function getAll(): array
  {
    return $this->db->fetchAllRaw("SELECT * FROM shift_kerja ORDER BY nomor ASC");
  }

  // 💾 Simpan shift kerja baru
  public function insert(array $data): void
  {
    $sql = "INSERT INTO shift_kerja (id, kode, keterangan, durasi, catatan, nomor) 
            VALUES (:id, :kode, :ket, :durasi, :catatan, :nomor)";

    $this->db->executeDirect($sql, [
      'id'      => $data['id'],
      'kode'    => $data['kode'],
      'ket'     => $data['keterangan'],
      'durasi'  => $data['durasi'],
      'catatan' => $data['catatan'],
      'nomor'   => $data['nomor']
    ]);
  }
}
