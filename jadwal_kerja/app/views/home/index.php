<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Jadwal Kerja</title>
  <style>
  body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    background-color: #ddeeff;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .container {
    max-width: 800px;
    width: 100%;
    padding: 20px;
    text-align: center;
  }

  .login-button {
    padding: 10px 20px;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    margin: 8px;
    display: inline-block;
    width: 220px;
  }

  .login-primary {
    background-color: #007bff;
  }

  .login-pdf {
    background-color: #28a745;
  }

  .disclaimer {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    border-left: 5px solid #007bff;
    margin: 30px auto 0;
    width: 100%;
    max-width: 700px;
  }

  .disclaimer span {
    font-size: 18px;
    font-weight: bold;
    color: #ff3300;
    display: block;
    margin-bottom: 40px;
    text-align: center;
  }

  .disclaimer p {
    margin: 8px 0;
    text-align: center;
    line-height: 1.45;
    color: #333;
    font-size: 15px;
    font-weight: bold;
  }

  .pdf-btn-container {
    text-align: center;
    margin-top: 20px;
  }

  /* Modal fullscreen PDF */
  #pdfModal {
    display: none;
    position: fixed;
    z-index: 999;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.8);
  }

  .modal-content {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    padding: 20px;
    box-sizing: border-box;
  }

  .modal-content h3 {
    margin-top: 0;
    color: #007bff;
    font-size: 20px;
  }

  .modal-content iframe {
    width: 100%;
    height: calc(100% - 60px);
    border: none;
  }

  .close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1000;
  }
  </style>
</head>
<body>
  <div class="container">
    <h1>👋 Selamat Datang di <strong>Aplikasi Jadwal Kerja</strong></h1>

    <!-- Tombol Login -->
    <a href="<?= BASEURL ?>/auth/login" class="login-button login-primary">🔐 Klik untuk Login</a>

    <!-- Catatan Penting -->
    <div class="disclaimer">
      <span>⚠️ CATATAN PENTING :</span>
      <p>
        Aplikasi ini dibuat untuk membantu pengguna memenuhi kebutuhan kerja.<br><br>
        Tindakan dan penyebaran informasi di luar aplikasi sepenuhnya tanggung jawab pengguna.<br><br>
        Pembuat tidak bertanggung jawab atas penggunaan di luar ketentuan.
      </p>

      <!-- Tombol PDF di bawah disclaimer -->
      <div class="pdf-btn-container">
        <button onclick="document.getElementById('pdfModal').style.display='block'" class="login-button login-pdf">
          📘 Panduan Aplikasi
        </button>
      </div>
    </div>
  </div>

  <!-- Modal PDF fullscreen -->
  <div id="pdfModal">
    <div class="modal-content">
      <h3>📘 Panduan Penggunaan Aplikasi</h3>
      <iframe src="<?= BASEURL ?>/assets/docs/panduan.pdf"></iframe>
      <button onclick="document.getElementById('pdfModal').style.display='none'" class="close-btn">❌ Tutup</button>
    </div>
  </div>
</body>
</html>
