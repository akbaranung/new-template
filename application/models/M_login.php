<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_login extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function ambilPengguna($username, $status)
  {
    $this->db->select('*');
    $this->db->from('users');
    $this->db->where('username', $username);
    $this->db->where('status', $status);
    $query = $this->db->get();

    return $query->num_rows();
  }

  public function password() {}

  public function dataPengguna($username)
  {
    $this->db->select('*');
    $this->db->where('username', $username);
    $query = $this->db->get('users');

    return $query->row();
  }

  public function utility()
  {
    $this->db->select('*');
    $this->db->where('Id', 1);
    $query = $this->db->get('utility');

    return $query->row();
  }
}
