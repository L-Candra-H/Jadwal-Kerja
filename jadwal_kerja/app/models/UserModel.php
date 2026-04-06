<?php

class UserModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  // 🔐 Simpan user baru (dipakai saat register)
  public function insert($data)
  {
    try {
      $stmt = $this->db->prepare("
        INSERT INTO user_login (username, password, nip, jabatan_id, role)
        VALUES (:username, :password, :nip, :jabatan_id, :role)
      ");
      $stmt->execute([
        'username'   => $data['username'],
        'password'   => $data['password'],
        'nip'        => $data['nip'] ?? null,
        'jabatan_id' => $data['jabatan_id'],
        'role'       => $data['role']
      ]);
    } catch (PDOException $e) {
      throw new Exception("Gagal simpan user: " . $e->getMessage());
    }
  }

  // 🔎 Cari user berdasarkan username
  public function findByUsername($username)
  {
    $stmt = $this->db->prepare("SELECT * FROM user_login WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // 🔐 Validasi login (username + password)
  public function cekLogin($username, $password)
  {
    $user = $this->findByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }
    return false;
  }

  // 🔁 Update password saat reset
  public function updatePassword($id, $hashedPw): bool
  {
    $stmt = $this->db->prepare("UPDATE user_login SET password = :password WHERE id = :id");
    return $stmt->execute(['password' => $hashedPw, 'id' => $id]);
  }

  public function updatePasswordByNip(string $nip, string $passwordBaru): bool {
    $hashed = password_hash($passwordBaru, PASSWORD_DEFAULT);
    $stmt = $this->db->prepare("UPDATE user_login SET password = :password WHERE nip = :nip");
    return $stmt->execute(['password' => $hashed, 'nip' => $nip]);
  }

  // 📋 Ambil semua user + nama jabatan
  public function getAllWithJabatan()
  {
    $stmt = $this->db->prepare("
      SELECT u.id, u.username, j.nama_jabatan
      FROM user_login u
      LEFT JOIN jabatan j ON u.jabatan_id = j.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // 🧾 Ambil satu user berdasarkan ID
  public function getById($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM user_login WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // 🔧 Update jabatan dan password user
  public function updateUser($id, $jabatan_id, $password = null)
  {
    if ($password) {
      $pass = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $this->db->prepare("
        UPDATE user_login 
        SET jabatan_id = :jabatan_id, password = :password 
        WHERE id = :id
      ");
      $stmt->execute([
        'jabatan_id' => $jabatan_id,
        'password'   => $pass,
        'id'         => $id
      ]);
    } else {
      $stmt = $this->db->prepare("
        UPDATE user_login 
        SET jabatan_id = :jabatan_id 
        WHERE id = :id
      ");
      $stmt->execute([
        'jabatan_id' => $jabatan_id,
        'id'         => $id
      ]);
    }
  }

  // 🔢 Hitung jumlah user
  public function countAll()
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM user_login");
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  // 📄 Ambil data dengan pagination
  public function getPaginated($limit, $offset)
  {
    $stmt = $this->db->prepare("
      SELECT u.*, j.nama_jabatan
      FROM user_login u
      LEFT JOIN jabatan j ON u.jabatan_id = j.id
      ORDER BY u.id ASC
      LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // 🚫 Cek apakah username sudah dipakai
  public function isUsernameTaken($username)
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_login WHERE username = :username");
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
  }
}
