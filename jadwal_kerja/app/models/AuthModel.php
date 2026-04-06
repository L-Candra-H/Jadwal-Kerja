<?php

class AuthModel
{
  private Database $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function cekLogin($username, $password)
  {
    $sql = "SELECT u.*, 
                   p.nama, 
                   p.unit_kerja_id,
                   j.nama_jabatan AS jabatan,
                   uk.nama_unit_kerja AS unit,
                   p.aktivasi
            FROM user_login u
            LEFT JOIN pegawai p ON p.nip = u.nip
            LEFT JOIN jabatan j ON j.id = p.jabatan_id
            LEFT JOIN unit_kerja uk ON uk.id = p.unit_kerja_id
            WHERE u.username = :username
            LIMIT 1";

    $user = $this->db->fetchRaw($sql, ['username' => $username]);

    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }

    return false;
  }
}
