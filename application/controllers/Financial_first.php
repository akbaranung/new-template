<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Financial_first extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_coa', 'M_Customer', 'M_invoice']);
    $this->load->helper(['number']);
    $this->load->library(['pdfgenerator']);

    $this->cb = $this->load->database('corebank', TRUE);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

    $this->cb->from('v_coa_all');
    $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
    $cek_coa_cabang = $this->cb->get()->num_rows();

    if ($cek_coa_cabang != 0) {
      redirect('home');
    }

    date_default_timezone_set('Asia/Jakarta');
  }

  // public function force_make_coa_sbb() {}
  public function force_make_coa_sbb()
  {

    $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search')) ? $this->session->userdata('search') : '');
    if ($keyword === null) $keyword = $this->session->userdata('search');
    else $this->session->set_userdata('search', $keyword);

    $config = [
      'base_url' => site_url('financial_first/force_make_coa_sbb'),
      'total_rows' => $this->M_coa->count($keyword, 'v_coa_all'),
      'per_page' => 25,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => 'page',
    ];

    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = "<i class='fe fe-chevrons-left'></i>";
    $config['last_link'] = "<i class='fe fe-chevrons-right'></i>";
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = "<i class='fe fe-chevron-left'></i>";
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = "<i class='fe fe-chevron-right'></i>";
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    $this->pagination->initialize($config);

    // $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;
    $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    // $invoices = $this->m_invoice->list_invoice($config["per_page"], $page, $keyword);
    $coa = $this->M_coa->list_coa_paginate_financial_first($config["per_page"], $page, $keyword);

    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $result = $query->row_array()['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $result2 = $query2->row_array()['COUNT(id)'];

    $data = [
      'page' => $page,
      'coa' => $coa,
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'keyword' => $keyword,
      'title' => "List CoA",
    ];

    $data['pages'] = "pages/financial/v_list_coa_force";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial_first';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);
  }
  public function reset_coa()
  {
    $this->session->unset_userdata('search');
    redirect('financial_first/force_make_coa_sbb');
  }
  private function _parse_rupiah($rupiah)
  {
    // Hilangkan Rp, titik, dan ganti koma dengan titik
    $rupiah = str_replace(['Rp', '.', ' '], '', $rupiah);
    return floatval(str_replace(',', '.', $rupiah));
  }
  public function tambahCoa()
  {
    $no_bb = $this->input->post('no_bb');
    $no_sbb = $this->input->post('no_sbb');
    $nama_coa = $this->input->post('nama_coa');
    $saldo_awal = $this->input->post('saldo_awal');

    $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
    $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);

    if ($cek_no_sbb) {
      $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
      redirect($_SERVER['HTTP_REFERER']);
    } else if ($cek_nama_coa) {
      $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
      redirect($_SERVER['HTTP_REFERER']);
    } else {

      $substr_coa = substr($no_sbb, 0, 1);

      if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
        $posisi = 'AKTIVA';
      } else {
        $posisi = 'PASIVA';
      }

      // cek tabel
      if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
        $tabel = "t_coa_sbb";

        $data = [
          'no_bb' => $no_bb,
          'no_sbb' => $no_sbb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'nominal' => $this->_parse_rupiah($saldo_awal),
          'id_cabang' => $this->session->userdata('kode_cabang'),
        ];
      } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
        $tabel = "t_coalr_sbb";
        $data = [
          'no_lr_bb' => $no_bb,
          'no_lr_sbb' => $no_sbb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'nominal' => $this->_parse_rupiah($saldo_awal),
          'id_cabang' => $this->session->userdata('kode_cabang'),
        ];
      } else {
        $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
        redirect($_SERVER['HTTP_REFERER']);
      }


      $this->cb->trans_begin();

      $query = $this->cb->insert($tabel, $data);

      if ($query) {
        $this->cb->trans_commit();
        $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
        redirect($_SERVER['HTTP_REFERER']);
      } else {
        $this->cb->trans_rollback();
        $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
        redirect($_SERVER['HTTP_REFERER']);
      }
    }
  }
}
