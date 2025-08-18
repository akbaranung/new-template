<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_asset extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function asset_get($limit, $start, $cabang, $keyword)
  {
    $this->db->select('a.*, c.keterangan as nama_ruangan, d.nama_jenis')->from('asset_list a')->join('asset_history b', 'a.kode = b.kode', 'left')->join('asset_ruang c', 'c.Id = a.ruangan', 'left')->join('asset_jenis d', 'd.Id = a.jenis_asset', 'left')
      ->group_start()
      ->where('a.cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('a.kode', $keyword, 'both');
      $this->db->or_like('a.spesifikasi', $keyword, 'both');
      $this->db->or_like('a.nama_asset', $keyword, 'both');
      $this->db->or_like('b.remark', $keyword, 'both');
      $this->db->or_like('c.nama_ruangan', $keyword, 'both');
      $this->db->or_like('d.nama_jenis', $keyword, 'both');
      $this->db->group_end();
    }
    $this->db->order_by('a.Id', 'DESC');
    $this->db->group_by('b.kode');
    return $this->db->limit($limit, $start)->get()->result();
  }

  public function asset_count($cabang, $keyword)
  {
    $this->db->select('a.*, c.keterangan as nama_ruangan, d.nama_jenis')->from('asset_list a')->join('asset_history b', 'a.kode = b.kode', 'left')->join('asset_ruang c', 'c.Id = a.ruangan', 'left')->join('asset_jenis d', 'd.Id = a.jenis_asset', 'left')
      ->group_start()
      ->where('a.cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('a.kode', $keyword, 'both');
      $this->db->or_like('a.spesifikasi', $keyword, 'both');
      $this->db->or_like('a.nama_asset', $keyword, 'both');
      $this->db->or_like('b.remark', $keyword, 'both');
      $this->db->group_end();
    }
    $this->db->group_by('b.kode');
    return $this->db->get()->num_rows();
  }

  public function asset_jenis_get($limit, $start, $cabang, $keyword)
  {
    $this->db->select('*')->from('asset_jenis')
      ->group_start()
      ->where('cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('nama_jenis', $keyword, 'both');
      $this->db->group_end();
    }

    $this->db->order_by('Id', 'DESC');
    return $this->db->limit($limit, $start)->get()->result();
  }

  public function jenis_count($cabang, $keyword)
  {
    $this->db->select('*')->from('asset_jenis')
      ->group_start()
      ->where('cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('nama_jenis', $keyword, 'both');
      $this->db->group_end();
    }
    return $this->db->get()->num_rows();
  }

  public function ruangan_get($limit, $start, $cabang, $keyword)
  {
    $this->db->select('*')->from('asset_ruang')
      ->group_start()
      ->where('cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('keterangan', $keyword, 'both');
      $this->db->group_end();
    }

    $this->db->order_by('Id', 'DESC');
    return $this->db->limit($limit, $start)->get()->result();
  }

  public function ruangan_count($cabang, $keyword)
  {
    $this->db->select('*')->from('asset_ruang')
      ->group_start()
      ->where('cabang', $cabang)
      ->group_end();
    if ($keyword) {
      $this->db->group_start();
      $this->db->like('keterangan', $keyword, 'both');
      $this->db->group_end();
    }
    return $this->db->get()->num_rows();
  }

  public function ambil_data_asset($id, $cabang)
  {
    $this->db->select('a.*, c.keterangan as nama_ruangan, d.nama_jenis')->from('asset_list a')->join('asset_history b', 'a.kode = b.kode', 'left')->join('asset_ruang c', 'c.Id = a.ruangan', 'left')->join('asset_jenis d', 'd.Id = a.jenis_asset', 'left')
      ->where('a.Id', $id)
      ->where('a.cabang', $cabang);
    $query = $this->db->get();
    return $query->row();
  }

  public function ambil_data_history($kode, $cabang)
  {
    $this->db->select('a.*, b.keterangan as nama_ruangan')
      ->from('asset_history a')->join('asset_ruang b', 'b.Id = a.ruangan', 'left')
      ->where('kode', $kode)
      ->where('lokasi', $cabang);
    $query = $this->db->get();
    return $query->result();
  }
}
