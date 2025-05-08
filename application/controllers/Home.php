<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    if ($this->session->userdata('isLogin') == FALSE) {
      $this->session->set_flashdata('error', 'Your session has expired');
      redirect('auth');
    }
  }

  public function index()
  {
    $nip = $this->session->userdata('nip');
    $data['title'] = 'Home';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['user'] = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
    $data['pages'] = 'pages/home/v_home';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);
    $this->load->view('index', $data);
  }
}
