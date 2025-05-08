<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_task extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function search_user_task($keyword = '', $limit = 10, $offset = 0)
  {
    $this->db->select('nip, nama');
    $this->db->from('users');

    if (!empty($keyword)) {
      $this->db->like('nama', $keyword);
    }
    $this->db->where('nip !=', $this->session->userdata('nip'));
    $this->db->limit($limit, $offset);
    $query = $this->db->get();

    return $query->result();
  }

  public function task_count($nip, $keyword)
  {
    $this->db->select('Id')->from('task')
      ->group_start()
      ->like('member', $nip, 'both')
      ->or_like('pic', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('name', $keyword, 'both');
    }
    return $this->db->get()->num_rows();
  }

  public function task_get($limit, $start, $nip, $keyword)
  {
    $this->db->select('a.Id, a.name, a.read, a.pic, a.activity, a.date_created, b.nama')->from('task a')->join('users b', 'b.nip = a.pic')
      ->group_start()
      ->like('member', $nip, 'both')
      ->or_like('pic', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('name', $keyword, 'both');
    }
    $this->db->order_by('a.activity', 'ASC')->order_by('a.date_created', 'DESC');
    return $this->db->limit($limit, $start)->get()->result();
  }

  public function task_get_detail($id)
  {
    $nip = $this->session->userdata('nip');
    $sql = "SELECT task.read FROM task WHERE task.read NOT LIKE '%$nip%' OR task.id='$id'";
    $query = $this->db->query($sql);
    $result = $query->row();
    $kalimat = $result->read;

    if ($result) {
      $kalimat1 = $kalimat . ' ' . $nip;
      $data_update1    = array(
        'read'    => $kalimat1
      );
      $this->db->where('id', $id);
      $this->db->update('task', $data_update1);
    }

    $sql2 = "SELECT * FROM task where id='$id'";
    $query = $this->db->query($sql2);
    return $query->row();
  }
}
