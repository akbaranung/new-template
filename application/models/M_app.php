<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_app extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function search_user_memo($keyword = '', $limit = 10, $offset = 0)
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

  public function memo_count($nip, $keyword)
  {
    $this->db->select('Id')->from('memo')
      ->group_start()
      ->like('nip_kpd', $nip, 'both')
      ->or_like('nip_cc', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('judul', $keyword, 'both');
    }
    return $this->db->get()->num_rows();
  }

  public function memo_get($limit, $start, $nip, $keyword)
  {
    $this->db->select('a.Id, a.nomor_memo, a.nip_kpd, a.judul, a.tanggal, a.read, a.nip_dari, b.nama')->from('memo a')->join('users b', 'a.nip_dari = b.nip', 'left')
      ->group_start()
      ->like('nip_kpd', $nip, 'both')
      ->or_like('nip_cc', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('judul', $keyword, 'both');
    }
    $this->db->order_by('a.tanggal', 'DESC');
    return $this->db->limit($limit, $start)->get()->result();
  }

  public function memo_get_detail($id)
  {
    $nip = $this->session->userdata('nip');
    $result = $this->db->select('read')->from('memo')->where('Id', $id)->get()->row();
    $kalimat = $result->read;

    if ($result) {
      $kalimat1 = $kalimat . ' ' . $nip;
      $data_update1    = array(
        'read'    => $kalimat1
      );
      $this->db->where('Id', $id);
      $this->db->update('memo', $data_update1);
    }

    $nip = $this->session->userdata('nip');

    $query = $this->db->select('a.*,b.nama_jabatan,b.nama,b.supervisi,c.kode_nama,b.level_jabatan')->from('memo a')->join('users b', 'b.nip = a.nip_dari', 'LEFT')->join('bagian c', 'b.bagian = c.kode')->where('a.Id', $id)->group_start()->like('a.nip_dari', $nip, 'both')->or_like('a.nip_kpd', $nip, 'both')->or_like('a.nip_cc', $nip, 'both')->group_end()->get();
    return $query->row();
  }

  public function memo_count_outbox($nip, $keyword)
  {
    $this->db->select('Id')->from('memo')
      ->group_start()
      ->like('nip_dari', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('judul', $keyword, 'both');
    }
    return $this->db->get()->num_rows();
  }

  public function memo_get_outbox($limit, $start, $nip, $keyword)
  {
    $this->db->select('a.Id, a.nomor_memo, a.nip_kpd, a.judul, a.tanggal, a.read, a.nip_dari, b.nama')->from('memo a')->join('users b', 'a.nip_dari = b.nip', 'left')
      ->group_start()
      ->like('nip_dari', $nip, 'both')
      ->group_end();
    if ($keyword) {
      $this->db->like('judul', $keyword, 'both');
    }
    $this->db->order_by('a.tanggal', 'DESC');
    return $this->db->limit($limit, $start)->get()->result();
  }
}
