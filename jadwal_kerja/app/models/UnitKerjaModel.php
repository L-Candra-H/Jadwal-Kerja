<?php

class UnitKerjaModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 📋 Ambil semua unit kerja
  public function getAll(): array
  {
    try {
      return $this->db->fetchAllRaw("SELECT * FROM unit_kerja ORDER BY id ASC");
    } catch (PDOException $e) {
      throw new Exception("❌ Gagal ambil data unit kerja: " . $e->getMessage());
    }
  }

  // 💾 Simpan unit kerja baru
  public function insert(string $nama): void
  {
    if (trim($nama) === '') {
      throw new Exception("Nama unit kerja tidak boleh kosong.");
    }

    try {
      $this->db->executeDirect(
        "INSERT INTO unit_kerja (nama_unit_kerja) VALUES (:nama)",
        ['nama' => trim($nama)]
      );
    } catch (PDOException $e) {
      throw new Exception("❌ Gagal simpan unit kerja: " . $e->getMessage());
    }
  }

  // 🔍 Cari unit kerja by ID
  public function getById(int $id): array|false
  {
    try {
      return $this->db->fetchRaw("SELECT * FROM unit_kerja WHERE id = :id", ['id' => $id]);
    } catch (PDOException $e) {
      throw new Exception("❌ Gagal ambil unit kerja: " . $e->getMessage());
    }
  }

  // 🗑️ Hapus unit kerja
  public function delete(int $id): void
  {
    try {
      $this->db->executeDirect("DELETE FROM unit_kerja WHERE id = :id", ['id' => $id]);
    } catch (PDOException $e) {
      throw new Exception("❌ Gagal hapus unit kerja: " . $e->getMessage());
    }
  }
}
