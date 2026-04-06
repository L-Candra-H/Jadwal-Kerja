<?php
class HomeController extends Controller
{
  public function index()
  {
    $data['judul'] = 'Landing Page'; // opsional, bisa dipakai di view
    $this->loadView('home/index', $data);
  }

  public function loadView($view, $data = [])
  {

    require_once "../app/views/" . $view . ".php";
  }

 
}
