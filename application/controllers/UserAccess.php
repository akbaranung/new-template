<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once('PHPExcel.php');

class UserAccess extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function check_access()
  {
    $nip = $this->session->userdata('nip');
    $url = $this->uri->segment(1); // sesuaikan dengan struktur URL kamu
    $has_access = $this->M_menu->has_access($nip, $url);

    if (!$has_access) {
      // show_error('Unauthorized access. You do not have permission to access this page.', 403, 'Access Denied');
      // atau redirect ke view khusus:
      redirect('errors/unauthorized');
    }
  }
}
