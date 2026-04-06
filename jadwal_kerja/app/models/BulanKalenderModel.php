<?php

class BulanKalenderModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database(); // Pastikan class Database aktif & terhubung
  }

  // 💾 Simpan satu record bulan kerja
  public function simpan($data)
  {
    $query = "INSERT INTO bulan_kalender_kerja (nama_bulan, tanggal_mulai, tanggal_akhir, tahun)
              VALUES (:nama, :mulai, :akhir, :tahun)";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([
      'nama'  => $data['nama'],
      'mulai' => $data['mulai'],
      'akhir' => $data['akhir'],
      'tahun' => $data['tahun']
    ]);
  }

  // 📅 Ambil seluruh data bulan kerja berdasarkan tahun
  public function getByTahun($tahun)
  {
    $query = "SELECT 
                nama_bulan AS nama,
                DATE_FORMAT(tanggal_mulai, '%d %M %Y') AS mulai,
                DATE_FORMAT(tanggal_akhir, '%d %M %Y') AS akhir,
                tahun
              FROM bulan_kalender_kerja
              WHERE tahun = :tahun
              ORDER BY id ASC";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute(['tahun' => $tahun]);
    return $stmt->fetchAll();
  }

  // 🔍 Ambil periode kerja berdasarkan nama bulan & tahun
  public function getPeriode($bulan, $tahun)
  {
    $query = "SELECT tanggal_mulai, tanggal_akhir
              FROM bulan_kalender_kerja
              WHERE nama_bulan = :bulan AND tahun = :tahun
              LIMIT 1";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      'bulan' => $bulan,
      'tahun' => $tahun
    ]);
    return $stmt->fetch();
  }
}
