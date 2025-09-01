<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Financial_first extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_coa', 'M_customer', 'M_invoice']);
    $this->load->helper(['number']);
    $this->load->library(['pdfgenerator']);

    $this->cb = $this->load->database('corebank', TRUE);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

    // $this->cb->from('v_coa_all');
    // $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
    // $cek_coa_cabang = $this->cb->get()->num_rows();

    $this->cb->from('v_coa_all');
    $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
    // Add the OR conditions
    $this->cb->group_start(); // Start a WHERE group for the OR conditions
    // $this->cb->where('no_sbb', '23014');
    // $this->cb->or_where('no_sbb', '23011');
    $this->cb->where_not_in('no_sbb', ['23014', '23011']);
    $this->cb->group_end(); // End the WHERE group
    $cek_coa_cabang = $this->cb->get()->num_rows();

    if ($cek_coa_cabang != 0) {
      redirect('financial/list_coa');
    }

    date_default_timezone_set('Asia/Jakarta');
  }

  public function force_make_coa_sbb()
  {
    $this->cb->from('t_coa_bb');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $cek_coa_bb = $this->cb->get()->num_rows();

    $this->cb->from('t_coalr_bb');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $cek_coalr_bb = $this->cb->get()->num_rows();

    $keyword_sbb = ($this->input->post('keyword_sbb')) ? trim($this->input->post('keyword_sbb')) : (($this->session->userdata('search_sbb')) ? $this->session->userdata('search_sbb') : '');
    $keyword_bb = ($this->input->post('keyword_bb')) ? trim($this->input->post('keyword_bb')) : (($this->session->userdata('search_bb')) ? $this->session->userdata('search_bb') : '');

    // Reset logic for each keyword
    if ($keyword_sbb !== null) {
      $this->session->set_userdata('search_sbb', $keyword_sbb);
    }
    if ($keyword_bb !== null) {
      $this->session->set_userdata('search_bb', $keyword_bb);
    }

    if ($this->input->get('reset_all')) {
      $this->session->unset_userdata('search_sbb');
      $this->session->unset_userdata('search_bb');
      $keyword_sbb = '';
      $keyword_bb = '';
    }

    $cabang = $this->input->post('cabang_select') ? $this->input->post('cabang_select') : '';
    if ($cabang === null || $cabang === '') $cabang = $this->session->userdata('kode_cabang');

    $perusahaan = $this->session->userdata('user_perusahaan_id');

    // --- PAGINATION FOR CARD 1 (v_coa_all) ---
    $config = [
      'base_url' => site_url('financial_first/force_make_coa_sbb'),
      'total_rows' => $this->M_coa->count($keyword_sbb, $cabang, 'v_coa_all'),
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

    $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    $coa = $this->M_coa->list_coa_paginate($config["per_page"], $page, $keyword_sbb, $cabang);

    // --- PAGINATION FOR CARD 2 (t_coa_bb) ---
    $config_bb = [
      'base_url' => site_url('financial_first/force_make_coa_sbb'),
      'total_rows' => $this->M_coa->count_bb($keyword_bb, $perusahaan, 'v_coabb_all'), // You'll need to create this M_coa function
      'per_page' => 25,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => 'page_bb', // Use a different query string segment
    ];

    // Reuse the same pagination styling as above
    $config_bb['full_tag_open'] = $config['full_tag_open'];
    $config_bb['full_tag_close'] = $config['full_tag_close'];
    $config_bb['first_link'] = $config['first_link'];
    $config_bb['last_link'] = $config['last_link'];
    $config_bb['first_tag_open'] = $config['first_tag_open'];
    $config_bb['first_tag_close'] = $config['first_tag_close'];
    $config_bb['prev_link'] = $config['prev_link'];
    $config_bb['prev_tag_open'] = $config['prev_tag_open'];
    $config_bb['prev_tag_close'] = $config['prev_tag_close'];
    $config_bb['next_link'] = $config['next_link'];
    $config_bb['next_tag_open'] = $config['next_tag_open'];
    $config_bb['next_tag_close'] = $config['next_tag_close'];
    $config_bb['last_tag_open'] = $config['last_tag_open'];
    $config_bb['last_tag_close'] = $config['last_tag_close'];
    $config_bb['cur_tag_open'] = $config['cur_tag_open'];
    $config_bb['cur_tag_close'] = $config['cur_tag_close'];
    $config_bb['num_tag_open'] = $config['num_tag_open'];
    $config_bb['num_tag_close'] = $config['num_tag_close'];
    $config_bb['attributes'] = $config['attributes'];

    $this->pagination->initialize($config_bb);

    $page_bb = ($this->input->get('page_bb')) ? (($this->input->get('page_bb') - 1) * $config_bb['per_page']) : 0;
    $coa_bb = $this->M_coa->list_coa_bb_paginate($config_bb["per_page"], $page_bb, $keyword_bb, $perusahaan); // You'll need to create this M_coa function

    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $result = $query->row_array()['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $result2 = $query2->row_array()['COUNT(id)'];

    $this->cb->from('t_cabang');
    $this->cb->where('id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $cabangs = $this->cb->get()->result();
    $show_card2 = ($cek_coa_bb == 0 && $cek_coalr_bb == 0);

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang_s = $this->cb->get()->row();

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaansss = $this->db->get()->row();

    $data = [
      'page' => $page,
      'coa' => $coa,
      'page_bb' => $page_bb, // Pass the new page variable
      'coa_bb' => $coa_bb,   // Pass the new data for Card 2
      'cabang_now' => $cabang,
      'cabang' => $cabangs,
      'is_semua_coa' => $cabang_s->ambil_semua_coa,
      'is_semua_coa_bb' => $perusahaansss->ambil_semua_coa_bb,
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'keyword_sbb' => $keyword_sbb,
      'keyword_bb' => $keyword_bb,
      'title' => "List CoA",
      'show_card2' => $show_card2,
      // 'cek_coa_bb' => $cek_coa_bb,
      // 'cek_coalr_bb' => $cek_coalr_bb,

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

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
    $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);

    if ($cek_no_bb) {
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
    } else {
      $this->cb->trans_rollback();
      // $this->session->set_flashdata('swal_message', [
      //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
      //   'title' => 'Error!',
      //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
      //   'confirmButtonText' => 'Mengerti',
      // ]);
      $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
      redirect($_SERVER['HTTP_REFERER']);
    }
  }
  public function ajax_template_coa_list()
  {
    $list = $this->M_coa->get_datatables1();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $cat) {
      $no++;
      $row = array();
      $row[] = $no;

      // Store data in data attributes for easy retrieval with JavaScript
      $row[] = '<span data-no_bb="' . $cat->no_bb . '">' . $cat->no_bb . '</span>';
      $row[] = '<span data-no_sbb="' . $cat->no_sbb . '">' . $cat->no_sbb . '</span>';
      $row[] = '<span data-nama_coa="' . $cat->nama_perkiraan . '">' . $cat->nama_perkiraan . '</span>';

      // Input field for saldo_awal
      $row[] = '<input type="text" name="saldo_awal" class="form-control uang saldo-awal-input" value="0">';

      // Action button with a specific class for event delegation
      $row[] = '<button class="btn btn-primary submit-coa-btn" type="button">Buat</button>'; // type="button" to prevent default form submission if any parent form exists

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_coa->count_all1(),
      "recordsFiltered" => $this->M_coa->count_filtered1(),
      "data" => $data,
    );
    echo json_encode($output);
  }
  public function ajax_template_coa_bb_list()
  {
    $list = $this->M_coa->get_datatables1_bb();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $cat) {
      $no++;
      $row = array();
      $row[] = $no;

      // Store data in data attributes for easy retrieval with JavaScript
      $row[] = '<span data-no_bb="' . $cat->no_bb . '">' . $cat->no_bb . '</span>';
      $row[] = '<span data-nama_coa="' . $cat->nama_perkiraan . '">' . $cat->nama_perkiraan . '</span>';

      // Action button with a specific class for event delegation
      $row[] = '<button class="btn btn-primary submit-coa-bb-btn" type="button">Buat</button>'; // type="button" to prevent default form submission if any parent form exists

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_coa->count_all1_bb(),
      "recordsFiltered" => $this->M_coa->count_filtered1_bb(),
      "data" => $data,
    );
    echo json_encode($output);
  }
  public function tambahCoaAjax()
  {
    $no_bb = $this->input->post('no_bb');
    $no_sbb = $this->input->post('no_sbb');
    $nama_coa = $this->input->post('nama_coa');
    $saldo_awal = $this->input->post('saldo_awal');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
    $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);

    if ($cek_no_bb) {
      if ($cek_no_sbb) {
        // $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'No. ' . $no_sbb . ' sudah ada',
          'reload' => base_url('financial_first/force_make_coa_sbb')
        ];
      } else if ($cek_nama_coa) {
        // $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
        // redirect($_SERVER['HTTP_REFERER']);

        $response = [
          'status' => "error",
          'msg' => 'CoA ' . $nama_coa . ' sudah ada',
          'reload' => base_url('financial_first/force_make_coa_sbb')
        ];
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
          // $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "error",
            'msg' => 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.',
            'reload' => base_url('financial_first/force_make_coa_sbb')
          ];
        }


        $this->cb->trans_begin();

        $query = $this->cb->insert($tabel, $data);

        if ($query) {
          $this->cb->trans_commit();
          // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "success",
            'msg' => 'CoA ' . $no_sbb . ' berhasil ditambahkan.',
            'reload' => base_url('financial_first/force_make_coa_sbb')
          ];
        } else {
          $this->cb->trans_rollback();
          // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "error",
            'msg' => 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error(),
            'reload' => base_url('financial_first/force_make_coa_sbb')
          ];
        }
      }
    } else {
      $this->cb->trans_rollback();
      $response = [
        'status' => "error",
        'msg' => 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu',
        'reload' => base_url('financial_first/force_make_coa_sbb')
      ];
    }
    echo json_encode($response);
  }
  public function tambahCoaBBAjax()
  {
    $no_bb = $this->input->post('no_bb');
    $nama_coa = $this->input->post('nama_coa');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);

    if ($cek_no_bb) {
      // $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
      // redirect($_SERVER['HTTP_REFERER']);
      $response = [
        'status' => "error",
        'msg' => 'No. ' . $no_bb . ' sudah ada',
        'reload' => base_url('financial_first/force_make_coa_sbb')
      ];
    } else if ($cek_nama_coa) {
      // $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
      // redirect($_SERVER['HTTP_REFERER']);

      $response = [
        'status' => "error",
        'msg' => 'CoA ' . $nama_coa . ' sudah ada',
        'reload' => base_url('financial_first/force_make_coa_sbb')
      ];
    } else {

      $substr_coa = substr($no_bb, 0, 1);

      if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
        $posisi = 'AKTIVA';
      } else {
        $posisi = 'PASIVA';
      }

      // cek tabel
      if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
        $tabel = "t_coa_bb";

        $data = [
          'no_bb' => $no_bb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'id_company' => $this->session->userdata('user_perusahaan_id'),
        ];
      } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
        $tabel = "t_coalr_bb";
        $data = [
          'no_lr_bb' => $no_bb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'id_company' => $this->session->userdata('user_perusahaan_id'),
        ];
      } else {
        // $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'Format nomor CoA ' . $no_bb . ' tidak sesuai.',
          'reload' => base_url('financial_first/force_make_coa_sbb')
        ];
      }


      $this->cb->trans_begin();

      $query = $this->cb->insert($tabel, $data);

      if ($query) {
        $this->cb->trans_commit();
        // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "success",
          'msg' => 'CoA ' . $no_bb . ' berhasil ditambahkan.',
          'reload' => base_url('financial_first/force_make_coa_sbb')
        ];
      } else {
        $this->cb->trans_rollback();
        // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'CoA ' . $no_bb . ' gagal disimpan. Ket:' . $this->cb->error(),
          'reload' => base_url('financial_first/force_make_coa_sbb')
        ];
      }
    }
    echo json_encode($response);
  }
  public function ambil_semua_coa()
  {
    $this->load->view('loading');

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang = $this->cb->get()->row();

    if ($cabang->ambil_semua_coa == 0) {
      $this->cb->select('no_bb, no_sbb, nama_perkiraan');
      $this->cb->from('t_coa_sbb_gabungan');
      $this->cb->group_by('no_bb, no_sbb'); // Group by the columns that define uniqueness

      $all_coa = $this->cb->get()->result();

      foreach ($all_coa as $coas) {

        $no_bb = $coas->no_bb;
        $no_sbb = $coas->no_sbb;
        // $nama_bb = $coas->nama_bb;
        $nama_coa = $coas->nama_perkiraan;
        $saldo_awal = 0;
        $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
        $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);
        $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
        if ($cek_no_bb) {
          if ($cek_no_sbb) {
            continue;
            // } else if ($cek_nama_coa) {
            //   continue;
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
              // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
              // redirect($_SERVER['HTTP_REFERER']);
            } else {
              $this->cb->trans_rollback();
              // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
            }
          }
        } else {
          $this->cb->trans_rollback();
          // $this->session->set_flashdata('swal_message', [
          //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
          //   'title' => 'Error!',
          //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
          //   'confirmButtonText' => 'Mengerti',
          // ]);
          $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
          redirect($_SERVER['HTTP_REFERER']);
        }
      }

      $cabang_data = array(
        'ambil_semua_coa' => 1,
      );
      // Assuming 'users' table is in the default database
      $this->cb->where('uid', $this->session->userdata('kode_cabang')); // Assuming 'id' is the primary key for users table
      $this->cb->update('t_cabang', $cabang_data);
    }
    redirect($_SERVER['HTTP_REFERER']);
  }
  public function ambil_semua_coa_bb()
  {
    $this->load->view('loading');

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $cabang = $this->db->get()->row();

    if ($cabang->ambil_semua_coa_bb == 0) {

      $this->cb->select('no_bb, nama_perkiraan');
      $this->cb->from('t_coa_bb_gabungan');
      $this->cb->group_by('no_bb'); // Group by the columns that define uniqueness

      $all_coa = $this->cb->get()->result();

      foreach ($all_coa as $coas) {

        $no_bb = $coas->no_bb;
        // $nama_bb = $coas->nama_bb;
        $nama_coa = $coas->nama_perkiraan;
        $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
        $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);
        if ($cek_no_bb) {
          continue;
          // } else if ($cek_nama_coa) {
          //   continue;
        } else {

          $substr_coa = substr($no_bb, 0, 1);

          if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
            $posisi = 'AKTIVA';
          } else {
            $posisi = 'PASIVA';
          }

          // cek tabel
          if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $tabel = "t_coa_bb";

            $data = [
              'no_bb' => $no_bb,
              'nama_perkiraan' => $nama_coa,
              'posisi' => $posisi,
              'id_company' => $this->session->userdata('user_perusahaan_id'),
            ];
          } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $tabel = "t_coalr_bb";
            $data = [
              'no_lr_bb' => $no_bb,
              'nama_perkiraan' => $nama_coa,
              'posisi' => $posisi,
              'id_company' => $this->session->userdata('user_perusahaan_id'),
            ];
          } else {
            $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_bb . ' tidak sesuai.');
            redirect($_SERVER['HTTP_REFERER']);
          }

          var_dump($substr_coa);


          $this->cb->trans_begin();

          $query = $this->cb->insert($tabel, $data);

          if ($query) {
            $this->cb->trans_commit();
            // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
            // redirect($_SERVER['HTTP_REFERER']);
          } else {
            $this->cb->trans_rollback();
            // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
          }
        }
      }
      $company_data = array(
        'ambil_semua_coa_bb' => 1,
      );
      // Assuming 'users' table is in the default database
      $this->db->where('Id', $this->session->userdata('user_perusahaan_id')); // Assuming 'id' is the primary key for users table
      $this->db->update('utility', $company_data);
    }
    redirect($_SERVER['HTTP_REFERER']);
  }
}
