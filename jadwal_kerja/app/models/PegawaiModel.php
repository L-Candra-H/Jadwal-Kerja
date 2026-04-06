<?php

class PegawaiModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 🔍 Ambil seluruh data pegawai lengkap dengan unit & jabatan
  public function getAll()
  {
      $sql = "SELECT p.id,
                     p.nip,
                     p.nama,
                     p.unit_kerja_id,
                     p.jabatan_id,
                     u.nama_unit_kerja AS unit,
                     j.nama_jabatan AS jabatan,
                     p.aktivasi,
                     p.sumber_data
              FROM pegawai p
              LEFT JOIN unit_kerja u ON p.unit_kerja_id = u.id
              LEFT JOIN user_login ul ON p.nip = ul.nip
              LEFT JOIN jabatan j ON ul.jabatan_id = j.id";
      return $this->db->fetchAllRaw($sql);
  }

  // 🔍 Ambil satu pegawai berdasarkan ID
  public function getById($id)
  {
      $sql = "SELECT pegawai.id,
                     pegawai.nip,
                     pegawai.nama,
                     pegawai.unit_kerja_id,
                     pegawai.jabatan_id,
                     unit_kerja.nama_unit_kerja AS unit,
                     jabatan.nama_jabatan AS jabatan,
                     pegawai.aktivasi,
                     pegawai.sumber_data
              FROM pegawai
              LEFT JOIN unit_kerja ON pegawai.unit_kerja_id = unit_kerja.id
              LEFT JOIN jabatan ON pegawai.jabatan_id = jabatan.id
              WHERE pegawai.id = :id LIMIT 1";
      return $this->db->fetchRaw($sql, ['id' => $id]);
  }

  // 🔍 Ambil semua data unit kerja (untuk dropdown atau form)
  public function getUnitKerja()
  {
    return $this->db->fetchAllRaw("SELECT * FROM unit_kerja");
  }

  // 🔍 Ambil semua data jabatan (untuk dropdown atau form)
  public function getJabatan()
  {
    return $this->db->fetchAllRaw("SELECT * FROM jabatan");
  }

  // 🔍 Cek apakah NIP sudah ada
  public function getByNip($nip)
  {
    return $this->db->fetchRaw("SELECT * FROM pegawai WHERE nip = :nip LIMIT 1", [
      'nip' => $nip
    ]);
  }

  // 🔍 Cari berdasarkan nama atau NIP
  public function search($keyword)
  {
      $sql = "SELECT pegawai.id,
                     pegawai.nip,
                     pegawai.nama,
                     pegawai.unit_kerja_id,
                     pegawai.jabatan_id,
                     unit_kerja.nama_unit_kerja AS unit,
                     jabatan.nama_jabatan AS jabatan,
                     pegawai.aktivasi,
                     pegawai.sumber_data
              FROM pegawai
              LEFT JOIN unit_kerja ON pegawai.unit_kerja_id = unit_kerja.id
              LEFT JOIN jabatan ON pegawai.jabatan_id = jabatan.id
              WHERE pegawai.nama LIKE :nama OR pegawai.nip LIKE :nip";

      return $this->db->fetchAllRaw($sql, [
        'nama' => '%' . $keyword . '%',
        'nip'  => '%' . $keyword . '%'
      ]);
  }

  // 💾 Simpan data pegawai baru
  public function insert($data)
  {
    $sql = "INSERT INTO pegawai (nip, nama, unit_kerja_id, jabatan_id, aktivasi, sumber_data)
            VALUES (:nip, :nama, :unit_kerja_id, :jabatan_id, :aktivasi, :sumber_data)";
    $this->db->executeDirect($sql, [
      'nip'           => $data['nip'],
      'nama'          => $data['nama']         ?? NULL,
      'unit_kerja_id' => $data['unit_kerja_id'] ?? NULL,
      'jabatan_id'    => $data['jabatan_id']    ?? NULL,
      'aktivasi'      => $data['aktivasi']      ?? 1,
      'sumber_data'   => $data['sumber_data']   ?? 'petugas'
    ]);
  }

  // ✏️ Update data pegawai
  public function update($data)
  {
    $sql = "UPDATE pegawai SET
              nip = :nip,
              nama = :nama,
              unit_kerja_id = :unit_kerja_id,
              jabatan_id = :jabatan_id,
              aktivasi = :aktivasi
            WHERE id = :id";
    $this->db->executeDirect($sql, [
      'id'            => $data['id'],
      'nip'           => $data['nip'],
      'nama'          => $data['nama'],
      'unit_kerja_id' => $data['unit_kerja_id'],
      'jabatan_id'    => $data['jabatan_id'],
      'aktivasi'      => $data['aktivasi']
    ]);
  }

  // 🔐 Update password pegawai berdasarkan NIP
  public function updatePassword($nip, $passwordBaru)
  {
    $hash = password_hash($passwordBaru, PASSWORD_BCRYPT);
    $sql = "UPDATE pegawai SET password = :pass WHERE nip = :nip";
    $this->db->executeDirect($sql, ['pass' => $hash, 'nip' => $nip]);
  }

  // 🔍 Ambil nama unit kerja berdasarkan ID
  public function getNamaUnitById(int $unit_id): string
  {
    $stmt = $this->db->prepare("SELECT nama_unit_kerja FROM unit_kerja WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $unit_id]);
    $nama = $stmt->fetchColumn();

    return $nama ?: '—';
  }

  // 🔍 Ambil daftar unit kerja (untuk dropdown filter superadmin)
  public function getUnitOptions(): array
  {
    $stmt = $this->db->prepare("SELECT id, nama_unit_kerja FROM unit_kerja ORDER BY nama_unit_kerja ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getUserLoginByNip($nip)
  {
      $sql = "SELECT * FROM user_login WHERE nip = :nip LIMIT 1";
      return $this->db->fetch($sql, ['nip' => $nip]);
  }

}
