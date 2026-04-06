<?php

class InstitusiModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 🔍 Ambil satu institusi pertama
  public function get(): array
  {
    $result = $this->db->fetchAllRaw("SELECT * FROM institusi LIMIT 1");
    return $result ? $result[0] : [];
  }

  // 📋 Ambil semua institusi
  public function getAll(): array
  {
    return $this->db->fetchAllRaw("SELECT * FROM institusi");
  }

  // 🔍 Ambil institusi aktif
  public function getActive(): array|false
  {
    return $this->db->fetchRaw("SELECT * FROM institusi LIMIT 1");
  }

  // 🔍 Ambil institusi berdasarkan ID
  public function getById(int $id): array|false
  {
    return $this->db->fetchRaw("SELECT * FROM institusi WHERE id = :id", ['id' => $id]);
  }

  // ✏️ Update institusi + logo jika diunggah
  public function update(array $data, array $file = []): void
  {
    if (empty($data['nama_institusi']) || empty($data['id'])) {
      throw new Exception("⚠️ Data institusi tidak lengkap");
    }

    $logo = $data['logo_lama'] ?? '';

    // Proses logo baru jika ada
    if (!empty($file['logo']['name'])) {
      $uploadDir = dirname(__DIR__, 2) . '/public/storage/gambar/';
      if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        throw new Exception("❌ Tidak bisa membuat folder gambar");
      }

      $oldPath = $uploadDir . $logo;
      if (!empty($logo) && file_exists($oldPath)) unlink($oldPath);

      $safeName = str_replace(' ', '_', $file['logo']['name']);
      $logo = time() . '_' . $safeName;
      $target = $uploadDir . $logo;

      if (!move_uploaded_file($file['logo']['tmp_name'], $target)) {
        throw new Exception("❌ Gagal menyimpan logo ke folder gambar");
      }
    }

    $params = [
      'nama_institusi' => $data['nama_institusi'],
      'sub_institusi'  => $data['sub_institusi'] ?? '',
      'alamat'         => $data['alamat'],
      'telepon'        => $data['telepon'] ?? '',
      'id'             => $data['id']
    ];

    $sql = "UPDATE institusi SET 
              nama_institusi = :nama_institusi,
              sub_institusi  = :sub_institusi,
              alamat         = :alamat,
              telepon        = :telepon";

    if ($logo) {
      $params['logo'] = $logo;
      $sql .= ", logo = :logo";
    }

    $sql .= " WHERE id = :id";

    $this->db->executeDirect($sql, $params);
  }

  // 💾 Simpan institusi baru
  public function simpan(array $data, array $file = []): void
  {
    if (empty($data['nama_institusi'])) {
      throw new Exception("⚠️ Data institusi tidak lengkap");
    }

    $logo = '';

    if (!empty($file['logo']['name'])) {
      $uploadDir = dirname(__DIR__, 2) . '/public/storage/gambar/';
      if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        throw new Exception("❌ Tidak bisa membuat folder gambar");
      }

      $safeName = str_replace(' ', '_', $file['logo']['name']);
      $logo = time() . '_' . $safeName;
      $target = $uploadDir . $logo;

      if (!move_uploaded_file($file['logo']['tmp_name'], $target)) {
        throw new Exception("❌ Gagal menyimpan logo ke folder gambar");
      }
    }

    $sql = "INSERT INTO institusi (
              nama_institusi,
              sub_institusi,
              alamat,
              telepon,
              logo)
            VALUES (
              :nama,
              :sub,
              :alamat,
              :telepon,
              :logo)";

    $this->db->executeDirect($sql, [
      'nama'    => $data['nama_institusi'],
      'sub'     => $data['sub_institusi'] ?? '',
      'alamat'  => $data['alamat'],
      'telepon' => $data['telepon'] ?? '',
      'logo'    => $logo
    ]);
  }
}
