<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Customer extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function apply_cabang_filter()
    {
        $kode_cabang = $this->session->userdata('kode_cabang');
        return $this->cb->where('id_cabang', $kode_cabang);
    }

    public function customer()
    {
        return $this->apply_cabang_filter()->order_by('nama_customer', 'ASC')->get('customer')->result();
    }

    public function list_customer($status = NULL)
    {
        $this->apply_cabang_filter();
        if ($status) {
            $this->cb->where('status_customer', $status);
        }
        return $this->cb->order_by('nama_customer', 'ASC')->get('customer')->result();
    }

    public function insert($data)
    {
        return $this->cb->insert('customer', $data);
    }

    public function update($data, $old_slug)
    {
        $this->cb->where('slug', $old_slug);
        return $this->cb->update('customer', $data);
    }

    public function show($id)
    {
        return $this->cb->where('slug', $id)->get('customer')->row_array();
    }

    public function is_available($id)
    {
        return $this->cb->where('slug', $id)->get('customer')->num_rows();
    }
}
