<?php

class Database
{
  private PDO $pdo;
  private ?PDOStatement $stmt = null;

  public function __construct()
  {
    $config = require "../config/config.php";
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";

    try {
      $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
      ]);
    } catch (PDOException $e) {
      die("❌ Koneksi database gagal: " . $e->getMessage());
    }
  }

  // 🔗 Chaining: Siapkan query
  public function query(string $sql): self
  {
    try {
      $this->stmt = $this->pdo->prepare($sql);
      return $this;
    } catch (PDOException $e) {
      throw new Exception("❌ Query gagal: " . $e->getMessage());
    }
  }

  public function bind(string $param, mixed $value, int $type = null): self
  {
    if (is_null($type)) {
      $type = match (true) {
        is_int($value)  => PDO::PARAM_INT,
        is_bool($value) => PDO::PARAM_BOOL,
        is_null($value) => PDO::PARAM_NULL,
        default         => PDO::PARAM_STR,
      };
    }

    if ($this->stmt) {
      $this->stmt->bindValue($param, $value, $type);
    }

    return $this;
  }

  public function execute(): bool
  {
    if (!$this->stmt) {
      throw new Exception("❌ Eksekusi gagal: statement belum disiapkan");
    }

    try {
      return $this->stmt->execute();
    } catch (PDOException $e) {
      throw new Exception("❌ Eksekusi gagal: " . $e->getMessage());
    }
  }

  public function fetch(): array|false
  {
    if (!$this->stmt) {
      throw new Exception("❌ Fetch gagal: statement belum disiapkan");
    }

    try {
      $this->stmt->execute();
      return $this->stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("❌ Fetch gagal: " . $e->getMessage());
    }
  }

  public function fetchAll(): array
  {
    if (!$this->stmt) {
      throw new Exception("❌ FetchAll gagal: statement belum disiapkan");
    }

    try {
      $this->stmt->execute();
      return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("❌ FetchAll gagal: " . $e->getMessage());
    }
  }

  // 🆕 Tambahan untuk chaining ➜ versi single tanpa argumen
  public function singleFromStmt(): array|false
  {
    if (!$this->stmt) {
      throw new Exception("❌ singleFromStmt gagal: statement belum disiapkan");
    }

    try {
      $this->stmt->execute();
      return $this->stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("❌ singleFromStmt gagal: " . $e->getMessage());
    }
  }

  // 🔁 Non-chaining: Langsung ambil satu
  public function fetchRaw(string $query, array $params = []): array|false
  {
    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute($params);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("❌ fetchRaw gagal: " . $e->getMessage());
    }
  }

  public function fetchAllRaw(string $query, array $params = []): array
  {
    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("❌ fetchAllRaw gagal: " . $e->getMessage());
    }
  }

  public function single(string $query, array $params = []): array|false
  {
    return $this->fetchRaw($query, $params);
  }

  public function executeDirect(string $query, array $params = []): bool
  {
    try {
      $stmt = $this->pdo->prepare($query);
      return $stmt->execute($params);
    } catch (PDOException $e) {
      throw new Exception("❌ Eksekusi langsung gagal: " . $e->getMessage());
    }
  }

  public function getPDO(): PDO
  {
    return $this->pdo;
  }

  public function prepare(string $query): PDOStatement
  {
    return $this->pdo->prepare($query);
  }
}
