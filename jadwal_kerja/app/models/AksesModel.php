<?php

class AksesModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Ambil semua role aktif (admin & user)
  public function getAllRoles()
  {
    return $this->db->fetchAllRaw("SELECT * FROM roles ORDER BY id ASC");
  }

  // Ambil semua menu dari tabel 'menus'
  public function getAllMenus()
  {
    return $this->db->fetchAllRaw("SELECT * FROM menus ORDER BY id ASC");
  }

  // Ambil semua menu_id yang dimiliki oleh suatu role
  public function getMenuByRole($role_id)
  {
    return $this->db->fetchAllRaw(
      "SELECT menu_id FROM role_menu WHERE role_id = :role_id",
      ['role_id' => $role_id]
    );
  }

  // Ambil nama role (misalnya untuk pengecualian superadmin)
  public function getRoleName($role_id)
  {
    $row = $this->db->fetchRaw(
      "SELECT nama_role FROM roles WHERE id = :id",
      ['id' => $role_id]
    );
    return strtolower($row['nama_role'] ?? '');
  }

  // Ambil kode_menu (untuk session dan sidebar)
  public function getKodeMenuByRole($role_id)
  {
    return $this->db->fetchAllRaw(
      "SELECT m.kode_menu
       FROM role_menu rm
       JOIN menus m ON m.id = rm.menu_id
       WHERE rm.role_id = :role_id",
      ['role_id' => $role_id]
    );

    echo '<pre>'; print_r($result); exit;

  }

  // Simpan hak akses baru ke tabel 'role_menu'
  public function updateRoleAccess($role_id, $menu_ids = [])
  {
    if ($this->getRoleName($role_id) === 'superadmin') return;

    $this->db->query("DELETE FROM role_menu WHERE role_id = :role_id")
             ->bind('role_id', $role_id)
             ->execute();

    foreach ($menu_ids as $menu_id) {
      if (trim($menu_id) === '' || !is_numeric($menu_id)) continue;

      $this->db->query(
        "INSERT INTO role_menu (role_id, menu_id) VALUES (:role, :menu)"
      )
      ->bind('role', $role_id)
      ->bind('menu', $menu_id)
      ->execute();
    }
  }

  public function getRoleIdByName($nama_role)
  {
    return $this->db->fetchRaw(
      "SELECT id FROM roles WHERE LOWER(nama_role) = :role",
      ['role' => strtolower($nama_role)]
    );
  }

}
