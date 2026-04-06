<?php

class Controller
{
  // 🔧 Load model dan simpan sebagai properti dinamis
  public function loadModel($model)
  {
    $file = "../app/models/" . $model . ".php";

    if (file_exists($file)) {
      require_once $file;

      if (class_exists($model)) {
        $modelInstance = new $model;
        return $modelInstance;

      } else {
        die("❌ Class '$model' tidak ditemukan di file: $file");
      }
    } else {
      die("❌ File model '$model.php' tidak ditemukan di folder: /app/models/");
    }
  }

  // 🔁 Load model dan langsung return instance
  public function model($model)
  {
    $file = "../app/models/" . $model . ".php";

    if (file_exists($file)) {
      require_once $file;

      if (class_exists($model)) {
        return new $model;
      } else {
        die("❌ Class '$model' tidak ditemukan di file: $file");
      }
    } else {
      die("❌ File model '$model.php' tidak ditemukan di folder: /app/models/");
    }
  }

  // 📄 Load view (versi lama)
  public function loadView($view, $data = [])
  {
    extract($data);
    require_once "../app/views/" . $view . ".php";
  }

  // 📄 Load view (versi utama)
  public function view($view, $data = [])
  {
    extract($data);
    require_once "../app/views/" . $view . ".php";
  }
}
