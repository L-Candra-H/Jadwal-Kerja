<?php

class JabatanModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 🔍 Ambil semua data jabatan, urut berdasarkan ID
  public function getAll(): array
  {
    $sql = "SELECT * FROM jabatan ORDER BY id";
    return $this->db->fetchAllRaw($sql);
  }

  // 🚫 [Dinonaktifkan] Insert jabatan — tidak digunakan dalam mode read-only
  /*
  public function insert(array $data): void
  {
    $sql = "INSERT INTO jabatan (id, nama_jabatan, keterangan)
            VALUES (:id, :nama, :ket)";
    $this->db->executeDirect($sql, [
      'id'   => $data['id'],
      'nama' => $data['nama_jabatan'],
      'ket'  => $data['keterangan']
    ]);
  }
  */

  // 🔍 Ambil jabatan berdasarkan ID
  public function getById(int $id): array|false
  {
    $sql = "SELECT * FROM jabatan WHERE id = :id";
    return $this->db->fetchRaw($sql, ['id' => $id]);
  }
}
