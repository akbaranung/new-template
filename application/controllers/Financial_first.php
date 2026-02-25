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
    // $this->cb->where('no_sbb', '20304');
    // $this->cb->or_where('no_sbb', '20301');
    $this->cb->where_not_in('no_sbb', ['20304', '20301']);
    $this->cb->group_end(); // End the WHERE group
    $cek_coa_cabang = $this->cb->get()->num_rows();

    if ($cek_coa_cabang != 0) {
      redirect('financial/list_coa');
    }

    date_default_timezone_set('Asia/Jakarta');
  }

  public function force_make_coa_sbb()
  {

    $active_tab = $this->input->post('active_tab') ? $this->input->post('active_tab') : $this->session->userdata('active_tab');
    if (!$active_tab) {
      // Default to 'card2' (List COA BB) since it's the default active tab in your HTML
      $active_tab = 'card2';
    }

    // echo $active_tab;
    $this->session->set_userdata('active_tab', $active_tab);

    $keyword_sbb = ($this->input->post('keyword_sbb')) ? trim($this->input->post('keyword_sbb')) : (($this->session->userdata('search_sbb')) ? $this->session->userdata('search_sbb') : '');
    $keyword_bb = ($this->input->post('keyword_bb')) ? trim($this->input->post('keyword_bb')) : (($this->session->userdata('search_bb')) ? $this->session->userdata('search_bb') : '');

    // Reset logic for each keyword
    if ($keyword_sbb !== null) {
      $this->session->set_userdata('search_sbb', $keyword_sbb);
    }
    if ($keyword_bb !== null) {
      $this->session->set_userdata('search_bb', $keyword_bb);
    }

    // Reset logic
    if ($this->input->get('reset_all')) {
      $this->session->unset_userdata('search_sbb');
      $this->session->unset_userdata('search_bb');
      $keyword_sbb = '';
      $keyword_bb = '';
    } else {
      $this->session->set_userdata('search_sbb', $keyword_sbb);
      $this->session->set_userdata('search_bb', $keyword_bb);
    }

    $cabang = $this->input->post('cabang_select') ? $this->input->post('cabang_select') : '';
    if ($cabang === null || $cabang === '') $cabang = $this->session->userdata('kode_cabang');

    $perusahaan = $this->session->userdata('user_perusahaan_id');

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

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang_s = $this->cb->get()->row();

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaansss = $this->db->get()->row();

    $this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
    $this->cb->from('v_coabb_all');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $query = $this->cb->get();
    $all_coa_bb = $query->result_array();


    $this->cb->from('v_coabb_all');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $query = $this->cb->get();
    $cek_coa_bb = $query->num_rows();


    $this->cb->from('v_coa_all');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $query = $this->cb->get();
    $cek_coa_sbb = $query->num_rows();

    $activa = $this->M_coa->get_coa_activa_by_cabang();
    $pasiva = $this->M_coa->get_coa_pasiva_by_cabang();

    $Sumactiva = $this->M_coa->get_sum_coa_activa_by_cabang();
    $sum_activa = $Sumactiva->nominal;
    $Sumpasiva = $this->M_coa->get_sum_coa_pasiva_by_cabang();
    $sum_pasiva = $Sumpasiva->nominal;

    $pendapatan = $this->M_coa->get_sum_coa_pasiva_coalr_by_cabang();
    $beban = $this->M_coa->get_sum_coa_activa_coalr_by_cabang();

    $laba = $pendapatan->nominal - $beban->nominal;

    // --- PAGINATION FOR CARD 1 (v_coa_all) ---

    $per_page = 25;

    // Get total rows for SBB
    $total_sbb_rows = $this->M_coa->count($keyword_sbb, $cabang, 'v_coa_all');

    // Get total rows for BB
    $total_bb_rows = $this->M_coa->count_bb($keyword_bb, $perusahaan, 'v_coabb_all');

    // Prepare the configuration arrays using the new function
    $config_sbb = $this->_pagination_config($total_sbb_rows, $per_page, 'page_sbb');
    $config_bb = $this->_pagination_config($total_bb_rows, $per_page, 'page_bb');

    $page_sbb = ($this->input->get('page_sbb')) ? (($this->input->get('page_sbb') - 1) * $per_page) : 0;
    $coa_sbb = $this->M_coa->list_coa_paginate($per_page, $page_sbb, $keyword_sbb, $cabang);

    $page_bb = ($this->input->get('page_bb')) ? (($this->input->get('page_bb') - 1) * $per_page) : 0;
    $coa_bb = $this->M_coa->list_coa_bb_paginate($per_page, $page_bb, $keyword_bb, $perusahaan);

    $data = [
      'laba' => $laba,
      'activa' => $activa,
      'pasiva' => $pasiva,
      'sum_activa' => $sum_activa,
      'sum_pasiva' => $sum_pasiva,
      'page' => $page_sbb,
      'coa' => $coa_sbb,
      'page_bb' => $page_bb, // Pass the new page variable
      'coa_bb' => $coa_bb,   // Pass the new data for Card 2
      'config_sbb' => $config_sbb, // Pass the SBB config
      'config_bb' => $config_bb, // Pass the BB config

      'cabang_now' => $cabang,
      'cabang' => $cabangs,
      'is_semua_coa' => $cabang_s->ambil_semua_coa,
      'is_sawal' => $cabang_s->generate_sawal,
      'is_semua_coa_bb' => $perusahaansss->ambil_semua_coa_bb,
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'keyword_sbb' => $keyword_sbb,
      'keyword_bb' => $keyword_bb,
      'title' => "List CoA",
      'all_coa_bb' => $all_coa_bb,
      // 'cek_coa_bb' => $cek_coa_bb,
      // 'cek_coalr_bb' => $cek_coalr_bb,
      'active_tab' => $active_tab, // Pass the active tab to the view
      'cek_coa_sbb' => $cek_coa_sbb,
      'cek_coa_bb' => $cek_coa_bb,
    ];


    $data['pages'] = "pages/financial/v_list_coa_force";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial_first';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);
  }

  private function _pagination_config($total_rows, $per_page, $query_string_segment)
  {
    $config = [
      'base_url' => site_url('financial_first/force_make_coa_sbb'),
      'total_rows' => $total_rows,
      'per_page' => $per_page,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => $query_string_segment,
      'full_tag_open' => '<ul class="pagination justify-content-end">',
      'full_tag_close' => '</ul>',
      'first_link' => "<i class='fe fe-chevrons-left'></i>",
      'last_link' => "<i class='fe fe-chevrons-right'></i>",
      'first_tag_open' => '<li class="page-item">',
      'first_tag_close' => '</li>',
      'prev_link' => "<i class='fe fe-chevron-left'></i>",
      'prev_tag_open' => '<li class="page-item">',
      'prev_tag_close' => '</li>',
      'next_link' => "<i class='fe fe-chevron-right'></i>",
      'next_tag_open' => '<li class="page-item">',
      'next_tag_close' => '</li>',
      'last_tag_open' => '<li class="page-item">',
      'last_tag_close' => '</li>',
      'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
      'cur_tag_close' => '</a></li>',
      'num_tag_open' => '<li class="page-item">',
      'num_tag_close' => '</li>',
      'attributes' => ['class' => 'page-link'],
    ];
    return $config;
  }

  public function set_active_tab_session()
  {
    // Check if the request is an AJAX request
    if ($this->input->is_ajax_request()) {
      $active_tab = $this->input->post('active_tab');
      if ($active_tab) {
        $this->session->set_userdata('active_tab', $active_tab);
        echo json_encode(['status' => 'success']);
      }
    }
  }
  public function reset_coa()
  {

    $this->session->unset_userdata('search_sbb');
    $this->session->unset_userdata('search_bb');
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
            'id_company' => $this->session->userdata('user_perusahaan_id'),
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
            'id_company' => $this->session->userdata('user_perusahaan_id'),
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

  // public function tambahCoaBB()
  // {
  //   $no_bb = $this->input->post('no_bb');
  //   $nama_coa = $this->input->post('nama_coa');

  //   $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
  //   $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);

  //   $this->session->set_userdata('active_tab', 'card2');

  //   if ($cek_no_bb) {
  //     if ($cek_nama_coa) {
  //       $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
  //       redirect($_SERVER['HTTP_REFERER']);
  //     } else {

  //       $substr_coa = substr($no_bb, 0, 1);

  //       if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
  //         $posisi = 'AKTIVA';
  //       } else {
  //         $posisi = 'PASIVA';
  //       }

  //       // cek tabel
  //       if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
  //         $tabel = "t_coa_bb";

  //         $data = [
  //           'no_bb' => $no_bb,
  //           'nama_perkiraan' => $nama_coa,
  //           'posisi' => $posisi,
  //           'id_cabang' => $this->session->userdata('kode_cabang'),
  //           'id_company' => $this->session->userdata('user_perusahaan_id'),
  //         ];
  //       } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
  //         $tabel = "t_coalr_bb";
  //         $data = [
  //           'no_lr_bb' => $no_bb,
  //           'nama_perkiraan' => $nama_coa,
  //           'posisi' => $posisi,
  //           'id_cabang' => $this->session->userdata('kode_cabang'),
  //           'id_company' => $this->session->userdata('user_perusahaan_id'),
  //         ];
  //       } else {
  //         $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_bb . ' tidak sesuai.');
  //         redirect($_SERVER['HTTP_REFERER']);
  //       }


  //       $this->cb->trans_begin();

  //       $query = $this->cb->insert($tabel, $data);

  //       if ($query) {
  //         $this->cb->trans_commit();
  //         $this->session->set_flashdata('message_name', 'CoA ' . $no_bb . ' berhasil ditambahkan.');
  //         redirect($_SERVER['HTTP_REFERER']);
  //       } else {
  //         $this->cb->trans_rollback();
  //         $this->session->set_flashdata('message_error', 'CoA ' . $no_bb . ' gagal disimpan. Ket:' . $this->cb->error());
  //         redirect($_SERVER['HTTP_REFERER']);
  //       }
  //     }
  //   } else {
  //     $this->cb->trans_rollback();
  //     // $this->session->set_flashdata('swal_message', [
  //     //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
  //     //   'title' => 'Error!',
  //     //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
  //     //   'confirmButtonText' => 'Mengerti',
  //     // ]);
  //     $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
  //     redirect($_SERVER['HTTP_REFERER']);
  //   }
  // }

  public function tambahCoaBB()
  {
    $no_bb = $this->input->post('no_bb');
    $nama_coa = $this->input->post('nama_coa');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);

    $this->session->set_userdata('active_tab', 'card2');

    if ($cek_no_bb) {
      // $this->cb->trans_rollback();
      // $this->session->set_flashdata('swal_message', [
      //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
      //   'title' => 'Error!',
      //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
      //   'confirmButtonText' => 'Mengerti',
      // ]);
      // $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
      // redirect($_SERVER['HTTP_REFERER']);
      $this->session->set_flashdata('message_error', 'CoA BB dengan Nomor ' . $no_bb . ' sudah ada');
      redirect($_SERVER['HTTP_REFERER']);
    } else {
      if ($cek_nama_coa) {
        $this->session->set_flashdata('message_error', 'CoA BB dengan Nama ' . $nama_coa . ' sudah ada');
        redirect($_SERVER['HTTP_REFERER']);
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
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
          $tabel = "t_coalr_bb";
          $data = [
            'no_lr_bb' => $no_bb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else {
          $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_bb . ' tidak sesuai.');
          redirect($_SERVER['HTTP_REFERER']);
        }


        $this->cb->trans_begin();

        $query = $this->cb->insert($tabel, $data);

        if ($query) {
          $this->cb->trans_commit();
          $this->session->set_flashdata('message_name', 'CoA ' . $no_bb . ' berhasil ditambahkan.');
          redirect($_SERVER['HTTP_REFERER']);
        } else {
          $this->cb->trans_rollback();
          $this->session->set_flashdata('message_error', 'CoA ' . $no_bb . ' gagal disimpan. Ket:' . $this->cb->error());
          redirect($_SERVER['HTTP_REFERER']);
        }
      }
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

    $this->session->set_userdata('active_tab', 'card1');

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
                'id_company' => $this->session->userdata('user_perusahaan_id'),
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
                'id_company' => $this->session->userdata('user_perusahaan_id'),
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
              // redirect($_SERVER['HTTP_REFERER']);
            } else {
              $this->cb->trans_rollback();
              $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
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
      $this->session->set_flashdata('message_name', 'Semua COA berhasil ditambahkan.');
    }
    redirect($_SERVER['HTTP_REFERER']);
  }
  public function ambil_semua_coa_bb()
  {
    $this->load->view('loading');

    $this->session->set_userdata('active_tab', 'card2');

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
              'id_cabang' => $this->session->userdata('kode_cabang'),
              'id_company' => $this->session->userdata('user_perusahaan_id'),
            ];
          } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $tabel = "t_coalr_bb";
            $data = [
              'no_lr_bb' => $no_bb,
              'nama_perkiraan' => $nama_coa,
              'posisi' => $posisi,
              'id_cabang' => $this->session->userdata('kode_cabang'),
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
