<?php

class PesanJadwalModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database(); // Wrapper PDO kamu
  }

  // 🌐 Tambahan agar controller bisa akses koneksi PDO langsung
  public function getDB(): Database
  {
    return $this->db;
  }

  // 💾 Simpan pengajuan baru (hindari duplikat dengan SQL langsung)
  public function insert(array $data): bool
  {
    $sqlCek = "SELECT COUNT(*) FROM pesan_jadwal 
               WHERE nip = :nip AND tanggal = :tanggal 
               AND status IN ('pending', 'disetujui')";
    $stmtCek = $this->db->prepare($sqlCek);
    $stmtCek->execute([
      'nip'     => $data['nip'],
      'tanggal' => $data['tanggal']
    ]);

    if ($stmtCek->fetchColumn() > 0) {
      return false;
    }

    $sql = "INSERT INTO pesan_jadwal (nip, tanggal, jenis, status, created_at)
            VALUES (:nip, :tanggal, :jenis, :status, :created_at)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      'nip'        => $data['nip'],
      'tanggal'    => $data['tanggal'],
      'jenis'      => strtoupper($data['jenis']),
      'status'     => $data['status'] ?? 'pending',
      'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
    ]);

    return true;
  }

  public function getAll(): array
  {
    $sql = "SELECT pj.*, p.nama
            FROM pesan_jadwal pj
            LEFT JOIN pegawai p ON pj.nip = p.nip
            ORDER BY pj.created_at DESC";

    return $this->db->fetchAllRaw($sql);
  }

  public function getPendingWithNama(): array
  {
    $sql = "SELECT pj.*, p.nama
            FROM pesan_jadwal pj
            LEFT JOIN pegawai p ON pj.nip = p.nip
            WHERE pj.status = 'pending'
            ORDER BY pj.tanggal ASC";

    return $this->db->fetchAllRaw($sql);
  }

  public function getPendingByNip(string $nip): array
  {
    return $this->db
      ->query("
        SELECT tanggal, jenis, status, created_at
        FROM pesan_jadwal
        WHERE nip = :nip AND status = 'pending'
        ORDER BY created_at DESC
      ")
      ->bind('nip', $nip)
      ->fetchAll();
  }

  public function getPendingByUnit(int $unit_id): array
  {
    $sql = "SELECT pj.*, p.nama
            FROM pesan_jadwal pj
            JOIN pegawai p ON pj.nip = p.nip
            WHERE p.unit_kerja_id = :unit AND pj.status = 'pending'
            ORDER BY pj.tanggal ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['unit' => $unit_id]);

    return $stmt->fetchAll();
  }

  public function getPendingByUnitAndPeriode(int $unit_id, int $tahun, string $bulan): array
  {
    $sql = "SELECT pj.*, p.nama
            FROM pesan_jadwal pj
            JOIN pegawai p ON pj.nip = p.nip
            WHERE pj.status = 'pending'
              AND p.unit_kerja_id = :unit
              AND YEAR(pj.tanggal) = :tahun
              AND MONTH(pj.tanggal) = :bulan
            ORDER BY pj.tanggal ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      'unit'  => $unit_id,
      'tahun' => $tahun,
      'bulan' => $bulan
    ]);

    return $stmt->fetchAll();
  }

  public function getPendingWithFilter(?int $unit_id, int $tahun, string $bulan): array
  {
    $sql = "SELECT pj.*, p.nama, uk.nama_unit_kerja
            FROM pesan_jadwal pj
            JOIN pegawai p ON pj.nip = p.nip
            JOIN unit_kerja uk ON p.unit_kerja_id = uk.id
            WHERE pj.status = 'pending'
              AND YEAR(pj.tanggal) = :tahun
              AND MONTH(pj.tanggal) = :bulan";

    if ($unit_id) {
      $sql .= " AND p.unit_kerja_id = :unit";
    }

    $sql .= " ORDER BY pj.tanggal ASC";

    $stmt = $this->db->prepare($sql);
    $params = ['tahun' => $tahun, 'bulan' => $bulan];
    if ($unit_id) $params['unit'] = $unit_id;

    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public function getApprovedByUnitAndBulan(int $unit_id, string $bulan, int $tahun): array
  {
    $bulan_map = array_flip([
      'Januari','Februari','Maret','April','Mei','Juni','Juli',
      'Agustus','September','Oktober','November','Desember'
    ]);

    $bulan_index = array_key_exists($bulan, $bulan_map)
      ? str_pad($bulan_map[$bulan] + 1, 2, '0', STR_PAD_LEFT)
      : str_pad((int)$bulan, 2, '0', STR_PAD_LEFT);

    $awal  = "{$tahun}-{$bulan_index}-01";
    $akhir = date("Y-m-t", strtotime($awal));

    $sql = "SELECT pj.nip, pj.tanggal, pj.jenis
            FROM pesan_jadwal pj
            INNER JOIN pegawai p ON pj.nip = p.nip
            WHERE pj.status = 'disetujui'
              AND p.unit_kerja_id = :unit
              AND pj.tanggal BETWEEN :awal AND :akhir";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      'unit' => $unit_id,
      'awal' => $awal,
      'akhir'=> $akhir
    ]);

    $result = [];
    foreach ($stmt->fetchAll() as $row) {
      $result[$row['nip']][$row['tanggal']] = $row['jenis'];
    }

    return $result;
  }

  public function updateStatus(int $id, string $status, ?string $keterangan = null): void
  {
    $sql = "UPDATE pesan_jadwal
            SET status = :status, keterangan = :ket
            WHERE id = :id";

    $updated = $this->db->executeDirect($sql, [
      'status' => $status,
      'ket'    => $keterangan,
      'id'     => $id
    ]);

    if (!$updated) {
      throw new Exception("❌ Gagal update status untuk ID $id");
    }
  }

  public function delete(int $id): void
  {
    $this->db->executeDirect("DELETE FROM pesan_jadwal WHERE id = :id", ['id' => $id]);
  }

  public function findByNipAndTanggal(string $nip, string $tanggal): array|false
  {
    return $this->db
      ->query("
        SELECT id FROM pesan_jadwal
        WHERE nip = :nip AND tanggal = :tanggal AND status IN ('pending', 'disetujui')
        LIMIT 1
      ")
      ->bind('nip', $nip)
      ->bind('tanggal', $tanggal)
      ->singleFromStmt();
  }

  public function findById(int $id): array|false
  {
    return $this->db
      ->query("SELECT * FROM pesan_jadwal WHERE id = :id")
      ->bind('id', $id)
      ->singleFromStmt();
  }

  public function getNamaUnitById(int $id): ?string
  {
    $stmt = $this->db->prepare("SELECT nama_unit_kerja FROM unit_kerja WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn() ?: null;
  }

}
