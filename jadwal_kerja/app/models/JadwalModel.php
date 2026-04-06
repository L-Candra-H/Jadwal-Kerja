<?php
require_once __DIR__ . '/BulanKalenderModel.php';

class JadwalModel
{
  private Database $db;
  private BulanKalenderModel $BulanKalenderModel;

  public function __construct()
  {
    $this->db = new Database();
    $this->BulanKalenderModel = new BulanKalenderModel(); // ✅ sekarang sah!
  }

  /**
   * 🔍 Ambil daftar pegawai dalam satu unit kerja
   */
  public function getPegawaiByUnit(int $unit_kerja_id): array
  {
    $sql = "SELECT p.nip, p.nama, p.id, j.nama_jabatan AS jabatan, p.unit_kerja_id, p.jabatan_id
            FROM pegawai p
            LEFT JOIN jabatan j ON p.jabatan_id = j.id
            WHERE p.unit_kerja_id = :unit";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['unit' => $unit_kerja_id]);

    return $stmt->fetchAll();
  }

  /**
   * 🔁 Ambil jadwal kerja dari unit/bulan/tahun
   */
  public function getJadwal(int $unit_kerja_id, string $bulan, int $tahun): array
  {
    $kalender = $this->BulanKalenderModel->getPeriode($bulan, $tahun);
    if (!$kalender || empty($kalender['tanggal_mulai']) || empty($kalender['tanggal_akhir'])) {
      return []; // fallback jika gagal ambil periode
    }

    $sql = "SELECT jk.nip, jk.tanggal, jk.shift_kode
            FROM jadwal_kerja jk
            JOIN pegawai p ON jk.nip = p.nip
            WHERE p.unit_kerja_id = :unit
              AND jk.tanggal BETWEEN :mulai AND :akhir";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      'unit'  => $unit_kerja_id,
      'mulai' => $kalender['tanggal_mulai'],
      'akhir' => $kalender['tanggal_akhir']
    ]);

    $result = [];
    foreach ($stmt->fetchAll() as $row) {
      $result[$row['nip']][$row['tanggal']] = $row['shift_kode'];
    }

    return $result;
  }

  /**
   * 💾 Simpan ulang jadwal kerja bulanan
   * Tanpa menghapus data lama, hanya tambah jika belum ada
   */
  public function saveJadwal(int $unit_kerja_id, string $bulan, int $tahun, array $data): void
  {
    $pegawai = $this->getPegawaiByUnit($unit_kerja_id);
    $nipList = array_column($pegawai, 'nip');

    $bulan_map = array_flip([
      'Januari','Februari','Maret','April','Mei','Juni','Juli',
      'Agustus','September','Oktober','November','Desember'
    ]);
    $bulan_index = str_pad($bulan_map[$bulan] + 1, 2, '0', STR_PAD_LEFT);
    $awal  = "{$tahun}-{$bulan_index}-01";
    $akhir = date("Y-m-t", strtotime($awal));

    // ❌ Tidak ada DELETE — menjaga jadwal lama tetap utuh

    // ✅ Simpan data baru jika belum ada
    $sql_ins = "INSERT INTO jadwal_kerja (nip, tanggal, shift_kode)
                VALUES (:nip, :tanggal, :shift_kode)";
    $stmt_ins = $this->db->prepare($sql_ins);

    foreach ($data as $nip => $shiftPerTanggal) {
      foreach ($shiftPerTanggal as $tgl => $kode) {
        $kode = strtoupper(trim($kode));
        if (!preg_match('/^[A-Z0-9]+$/', $kode)) continue; // validasi kode sederhana
        if ($this->exists($nip, $tgl)) continue; // 🛡️ Hindari duplikat
        $stmt_ins->execute([
          'nip'        => $nip,
          'tanggal'    => $tgl,
          'shift_kode' => $kode
        ]);
      }
    }
  }

  /**
   * ✅ Insert satu jadwal jika belum ada
   */
  public function insertSingle(string $nip, string $tanggal, string $shift_kode): bool
  {
    if ($this->exists($nip, $tanggal)) {
      return false;
    }

    $sql = "INSERT INTO jadwal_kerja (nip, tanggal, shift_kode)
            VALUES (:nip, :tanggal, :shift_kode)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      'nip'        => $nip,
      'tanggal'    => $tanggal,
      'shift_kode' => strtoupper($shift_kode)
    ]);
  }

  /**
   * 🔍 Cek apakah jadwal sudah terdaftar
   */
  public function exists(string $nip, string $tanggal): bool
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM jadwal_kerja WHERE nip = ? AND tanggal = ?");
    $stmt->execute([$nip, $tanggal]);
    return $stmt->fetchColumn() > 0;
  }

  public function getShiftHariIni(string $nip): string {
    $tglHariIni = date('Y-m-d');
    $stmt = $this->db->prepare("SELECT shift_kode FROM jadwal_kerja WHERE nip = ? AND tanggal = ?");
    $stmt->execute([$nip, $tglHariIni]);
    return $stmt->fetchColumn() ?? 'Belum dijadwalkan';
  }

  public function getShiftBesok(string $nip): string {
    $tglBesok = date('Y-m-d', strtotime('+1 day'));
    $stmt = $this->db->prepare("SELECT shift_kode FROM jadwal_kerja WHERE nip = ? AND tanggal = ?");
    $stmt->execute([$nip, $tglBesok]);
    return $stmt->fetchColumn() ?? 'Belum dijadwalkan';
  }
}
