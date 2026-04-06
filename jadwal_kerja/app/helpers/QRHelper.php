<?php
require_once __DIR__ . '/../../vendor/phpqrcode/qrlib.php';
require_once __DIR__ . '/../core/Database.php'; // pastikan sesuai path Database class

/**
 * Buat nama file QR unik
 */
function generateQrFileName($unit_id, $jabatan_id, $nip) {
    return "qr_{$unit_id}_{$jabatan_id}_{$nip}.png";
}

/**
 * Pastikan QR sudah ada, kalau belum generate + simpan metadata ke DB
 */
function ensureQrGenerated($pegawai): string {
    $filename = generateQrFileName(
        $pegawai['unit_kerja_id'],
        $pegawai['jabatan_id'],
        $pegawai['nip']
    );

    $path = __DIR__ . '/../../public/qrcode/' . $filename;
    $isiQR = "Ditandatangani Oleh: {$pegawai['nama']}\nJabatan: {$pegawai['jabatan']}";

    // 🔧 Generate file QR jika belum ada
    if (!file_exists($path)) {
        QRcode::png($isiQR, $path, QR_ECLEVEL_L, 4);
    }

    // 🔧 Simpan metadata ke DB jika belum ada
    try {
        $db = new Database();
        $cek = $db->fetch("SELECT id FROM qr_ttd WHERE nip = :nip LIMIT 1", [
            'nip' => $pegawai['nip']
        ]);

        if (!$cek) {
            $db->executeDirect(
                "INSERT INTO qr_ttd (nip, jabatan_id, unit_kerja_id, file_nama, isi_qr, waktu_dibuat)
                 VALUES (:nip, :jabatan_id, :unit_kerja_id, :file_nama, :isi_qr, NOW())",
                [
                    'nip' => $pegawai['nip'],
                    'jabatan_id' => $pegawai['jabatan_id'],
                    'unit_kerja_id' => $pegawai['unit_kerja_id'],
                    'file_nama' => $filename,
                    'isi_qr' => $isiQR
                ]
            );
        }
    } catch (Exception $e) {
        // Jangan ganggu proses cetak kalau DB gagal
        error_log("QR DB insert error: " . $e->getMessage());
    }

    return $filename;
}
