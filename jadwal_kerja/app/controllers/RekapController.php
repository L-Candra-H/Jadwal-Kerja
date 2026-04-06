<?php
require_once '../app/models/BulanKalenderModel.php';

class RekapController {
    public function index() {
        // 🔐 Proteksi login
        if (!isset($_SESSION['user'])) {
            header("Location: " . BASEURL . "/auth/login");
            exit;
        }

        // 🔍 Ambil user & filter
        $user           = $_SESSION['user'];
        $role           = strtoupper($user['role']);
        $unit_session   = $user['unit_kerja_id'] ?? null;

        $unit_kerja_id  = $_GET['unit_kerja_id'] ?? ($role !== 'SUPERADMIN' ? $unit_session : null);
        $tahun          = $_GET['tahun'] ?? date("Y");
        $bulan          = $_GET['bulan'] ?? null;

        $db = new Database();

        // 🔍 Ambil unit kerja sesuai role
        if ($role === 'SUPERADMIN') {
            $units = $db->query("SELECT * FROM unit_kerja ORDER BY nama_unit_kerja")->fetchAll();
        } else {
            $db->query("SELECT * FROM unit_kerja WHERE id = :id");
            $db->bind(":id", $unit_session);
            $db->execute();
            $units = $db->fetchAll();
        }

        // 🔍 Ambil shift kerja
        $db->query("SELECT kode, durasi FROM shift_kerja");
        $db->execute();
        $shift_rows = $db->fetchAll();
        $durasi_shift = [];

        foreach ($shift_rows as $row) {
            $kode = strtoupper($row['kode']);
            $durasi = floatval($row['durasi']);
            $durasi_shift[$kode] = $durasi > 0 ? round($durasi, 2) : 0;
        }

        // 🗓️ Ambil data bulan kerja dari DB
        $db->query("SELECT * FROM bulan_kalender_kerja WHERE tahun = :tahun ORDER BY tanggal_mulai");
        $db->bind(":tahun", $tahun);
        $db->execute();
        $bulan_kalender = $db->fetchAll();

        // 🔤 Buat label bulan kerja untuk dropdown
        $label_bulan = [];
        foreach ($bulan_kalender as $bk) {
            $kode_bulan = substr($bk['tanggal_mulai'], 5, 2);
            $label_bulan[$kode_bulan] = $bk['nama_bulan'];
        }

        // 🧭 Inisialisasi
        $tanggal_array = [];
        $pegawai_list = [];
        $jadwal_tersimpan = [];

        // 🚦 Proses rekap jika filter valid
        if ($unit_kerja_id && $bulan && isset($label_bulan[$bulan])) {
            foreach ($bulan_kalender as $bk) {
                $kode_bulan = substr($bk['tanggal_mulai'], 5, 2);
                if ($kode_bulan === $bulan) {
                    $mulai = new DateTime($bk['tanggal_mulai']);
                    $akhir = new DateTime($bk['tanggal_akhir']);
                    while ($mulai <= $akhir) {
                        $tanggal_array[] = $mulai->format('Y-m-d');
                        $mulai->modify('+1 day');
                    }
                    break;
                }
            }

            // 🔍 Ambil pegawai
            $stmt = $db->prepare("SELECT * FROM pegawai WHERE unit_kerja_id = ?");
            $stmt->execute([$unit_kerja_id]);
            $pegawai_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 🔎 Ambil data jadwal kerja
            $nip_list = array_column($pegawai_list, 'nip');
            if (!empty($nip_list) && !empty($tanggal_array)) {
                $nip_placeholder = implode(',', array_fill(0, count($nip_list), '?'));
                $tgl_placeholder = implode(',', array_fill(0, count($tanggal_array), '?'));

                $stmt = $db->prepare("
                    SELECT * FROM jadwal_kerja
                    WHERE nip IN ($nip_placeholder)
                      AND tanggal IN ($tgl_placeholder)
                ");
                $stmt->execute(array_merge($nip_list, $tanggal_array));
                $jadwal_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($jadwal_rows as $row) {
                    $kode = strtoupper($row['shift_kode']);
                    $jadwal_tersimpan[$row['nip']][$row['tanggal']] = $kode;
                }
            }
        }

        // Kirim ke view
        require_once __DIR__ . '/../views/laporan/index.php';
    }
}
