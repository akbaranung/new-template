<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_pengajuan']);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

    $this->cb = $this->load->database('corebank', TRUE);

    date_default_timezone_set('Asia/Jakarta');
  }

  public function create()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $data['title'] = 'Form Pengajuan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_form';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function insert()
  {
    $tanggal = $this->input->post('tanggal');
    $rekening = $this->input->post('rekening');
    $metode = $this->input->post('metode');
    $catatan = $this->input->post('catatan');

    $uraian = $this->input->post('uraian[]');
    $qty = $this->input->post('qty[]');
    $price = $this->input->post('price[]');
    $sub_total = $this->input->post('subtotal[]');

    $total = $this->input->post('total');

    $this->form_validation->set_rules('rekening', 'No. Rekening', 'required|trim', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('metode', 'Metode Pembayaran', 'required', array('required' => '%s harus dipilih!'));
    $this->form_validation->set_rules('catatan', 'Catatan', 'trim|required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('uraian[]', 'Uraian', 'required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('qty[]', 'Qty', 'required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('price[]', 'Harga', 'required', array('required' => '%s harus diisi!'));

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      // Upload file
      $config['upload_path'] = './uploads/pengajuan';
      $config['allowed_types'] = 'jpg|jpeg|png|pdf';
      $config['encrypt_name'] = TRUE;

      $this->upload->initialize($config);

      // Jika upload error atau gagal
      if (!$this->upload->do_upload('file')) {
        $response = [
          'success' => false,
          'msg' => $this->upload->display_errors()
        ];
      } else {
        $this->db->trans_begin();
        $this->cb->trans_begin();

        $upload = $this->upload->data();
        $cabang = $this->session->userdata('kode_cabang');
        $user = $this->db->select('supervisi')->from('users')->where('nip', $this->session->userdata('nip'))->get()->row();
        $max = $this->cb->select('max(no_pengajuan) as maximal')->from('t_pengajuan')->where('cabang', $cabang)->get()->row();
        $count = $max->maximal + 1;
        $kode = $cabang . '-' . sprintf("%06d", $count);

        $insert = [
          'tanggal' => $tanggal,
          'kode' => $kode,
          'user' => $this->session->userdata('nip'),
          'no_pengajuan' => $count,
          'no_rekening' => $rekening,
          'metode_pembayaran' => $metode,
          'spv' => $user->supervisi,
          'posisi' => 'Diajukan kepada supervisi',
          'bukti_pengajuan' => $upload['file_name'],
          'catatan' => $catatan,
          'total' => $this->_parse_rupiah($total),
          'cabang' => $cabang
        ];

        // Simpan pengajuan
        $pengajuan_id = $this->M_pengajuan->simpan_pengajuan($insert);

        // Simpan detail
        $details = [];
        for ($i = 0; $i < count($uraian); $i++) {
          // Menghilangkan karakter
          $details[] = [
            'no_pengajuan' => $pengajuan_id,
            'item' => $uraian[$i],
            'qty' => (int)($qty[$i]),
            'price' => $this->_parse_rupiah($price[$i]),
            'total' => $this->_parse_rupiah($sub_total[$i]),
            'cabang' => $cabang
          ];
        }

        $this->M_pengajuan->simpan_detail_batch($details);

        if ($this->db->trans_status() === FALSE or $this->cb->trans_status() === false) {
          $this->db->trans_rollback();
          $this->cb->trans_rollback();
        } else {
          $this->db->trans_commit();
          $this->cb->trans_commit();

          $response = [
            'success' => true,
            'msg' => 'Pengajuan berhasil ditambahkan!',
            'reload' => site_url('pengajuan/list')
          ];
        }
      }
    }

    echo json_encode($response);
  }

  public function list()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('pengajuan/list');
    $config['total_rows'] = $this->M_pengajuan->pengajuan_count($this->session->userdata('nip'), $keyword);
    $config['per_page'] = "10";
    $config["uri_segment"] = 3;
    $config["num_links"] = 10;
    $config['enable_query_strings'] = TRUE;
    $config['page_query_string'] = TRUE;
    $config['use_page_numbers'] = TRUE;
    $config['reuse_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';

    // integrate bootstrap pagination
    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = true;
    $config['last_link'] = true;
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = 'Previous';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = 'Next';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    // initialize pagination
    $this->pagination->initialize($config);
    $data['page'] = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    $data['data_pengajuan'] = $this->M_pengajuan->pengajuan_get($config["per_page"], $data['page'], $this->session->userdata('nip'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'List Pengajan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_list';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function detail($kode)
  {
    $data['title'] = 'Detail Pengajuan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_detail';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pengajuan_detail'] = $this->M_pengajuan->pengajuan_get_detail($kode);
    $data['pengajuan'] = $this->M_pengajuan->pengajuan_by_kode($kode);


    if (!$this->uri->segment(4)) {
      if (empty($data['pengajuan']) or ($data['pengajuan']->user != $this->session->userdata('nip') or $data['pengajuan']->cabang != $this->session->userdata('kode_cabang'))) {
        show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
      }
    }

    if ($this->uri->segment(4) == 'spv') {
      if ($data['pengajuan']->spv != $this->session->userdata('nip') or $data['pengajuan']->cabang != $this->session->userdata('kode_cabang')) {
        show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
      }
    }

    if ($this->uri->segment(4) == 'finance') {
      $data['coa'] = $this->cb->get_where('v_coa_all', ['id_cabang' => $this->session->userdata('kode_cabang')])->result();
      $user = $this->db->select('users.Id, bagian.nama as nama_bagian')->from('users')->join('bagian', 'bagian.Id = users.bagian')->where('users.nip', $this->session->userdata('nip'))->where('users.id_cabang', $this->session->userdata('kode_cabang'))->get()->row();
      if ($data['pengajuan']->status < 1 or $data['pengajuan']->cabang != $this->session->userdata('kode_cabang') or $user->nama_bagian != 'Finance') {
        show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
      }
    }

    $this->load->view('index', $data);
  }

  public function ubah($kode)
  {
    $data['title'] = 'Ubah Pengajuan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_ubah';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pengajuan_detail'] = $this->M_pengajuan->pengajuan_get_detail($kode);
    $data['pengajuan'] = $this->M_pengajuan->pengajuan_by_kode($kode);

    if (empty($data['pengajuan']) or $data['pengajuan']->status != 0 or $data['pengajuan']->user != $this->session->userdata('nip')) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $this->load->view('index', $data);
  }

  public function update($kode)
  {
    $tanggal = $this->input->post('tanggal');
    $rekening = $this->input->post('rekening');
    $metode = $this->input->post('metode');
    $catatan = $this->input->post('catatan');

    $uraian = $this->input->post('uraian[]');
    $qty = $this->input->post('qty[]');
    $price = $this->input->post('price[]');
    $sub_total = $this->input->post('subtotal[]');

    $total = $this->input->post('total');

    $this->form_validation->set_rules('rekening', 'No. Rekening', 'required|trim', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('metode', 'Metode Pembayaran', 'required', array('required' => '%s harus dipilih!'));
    $this->form_validation->set_rules('catatan', 'Catatan', 'trim|required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('uraian[]', 'Uraian', 'required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('qty[]', 'Qty', 'required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('price[]', 'Harga', 'required', array('required' => '%s harus diisi!'));

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->db->trans_begin();
      $this->cb->trans_begin();

      $pengajuan = $this->M_pengajuan->pengajuan_by_kode($kode);
      $cabang = $this->session->userdata('kode_cabang');

      if ($_FILES['file']['name']) {
        // Upload File
        $config['upload_path'] = './uploads/pengajuan';
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);

        // Jika upload error atau gagal
        if (!$this->upload->do_upload('file')) {
          $response = [
            'success' => false,
            'msg' => $this->upload->display_errors()
          ];
        } else {
          $upload = $this->upload->data();
          $update = [
            'tanggal' => $tanggal,
            'no_rekening' => $rekening,
            'metode_pembayaran' => $metode,
            'status' => 0,
            'posisi' => 'Diajukan kepada spv',
            'bukti_pengajuan' => $upload['file_name'],
            'catatan' => $catatan,
            'total' => $this->_parse_rupiah($total),
          ];

          // Update Pengajuan
          $this->M_pengajuan->update_pengajuan($update, $kode);
        }
      } else {
        $update = [
          'tanggal' => $tanggal,
          'no_rekening' => $rekening,
          'metode_pembayaran' => $metode,
          'status' => 0,
          'posisi' => 'Diajukan kepada supervisi',
          'catatan' => $catatan,
          'total' => $this->_parse_rupiah($total),
        ];

        // Update Pengajuan
        $this->M_pengajuan->update_pengajuan($update, $kode);
      }

      // Hapus detail jika ada update
      $this->M_pengajuan->delete_detail($pengajuan->Id);

      // Update detail
      $details = [];
      for ($i = 0; $i < count($uraian); $i++) {
        // Menghilangkan karakter
        $details[] = [
          'no_pengajuan' => $pengajuan->Id,
          'item' => $uraian[$i],
          'qty' => (int)($qty[$i]),
          'price' => $this->_parse_rupiah($price[$i]),
          'total' => $this->_parse_rupiah($sub_total[$i]),
          'cabang' => $cabang
        ];
      }

      $this->M_pengajuan->simpan_detail_batch($details);

      if ($this->db->trans_status() === FALSE or $this->cb->trans_status() === false) {
        $this->db->trans_rollback();
        $this->cb->trans_rollback();
      } else {
        $this->db->trans_commit();
        $this->cb->trans_commit();

        $response = [
          'success' => true,
          'msg' => 'Pengajuan ' . $pengajuan->kode . ' berhasil diubah!',
          'reload' => site_url('pengajuan/list')
        ];
      }
    }

    echo json_encode($response);
  }

  public function approval_spv()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('pengajuan/approval_spv');
    $config['total_rows'] = $this->M_pengajuan->pengajuan_count_spv($this->session->userdata('nip'), $keyword);
    $config['per_page'] = "10";
    $config["uri_segment"] = 3;
    $config["num_links"] = 10;
    $config['enable_query_strings'] = TRUE;
    $config['page_query_string'] = TRUE;
    $config['use_page_numbers'] = TRUE;
    $config['reuse_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';

    // integrate bootstrap pagination
    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = true;
    $config['last_link'] = true;
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = 'Previous';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = 'Next';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    // initialize pagination
    $this->pagination->initialize($config);
    $data['page'] = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    $data['data_pengajuan'] = $this->M_pengajuan->pengajuan_get_spv($config["per_page"], $data['page'], $this->session->userdata('nip'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'List Approval Supervisi';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_spv';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $data['belum_proses_spv'] = $this->cb->select('Id')->from('t_pengajuan')->where('posisi', 'Diajukan kepada supervisi')->where('spv', $this->session->userdata('nip'))->where('cabang', $this->session->userdata('kode_cabang'))->get()->num_rows();

    $this->load->view('index', $data);
  }

  public function update_spv($kode)
  {
    $tanggal = $this->input->post('tanggal');
    $status = $this->input->post('status');

    $this->form_validation->set_rules('tanggal', 'Tanggal', 'required', array('required' => '%s harus diisi!'));
    $this->form_validation->set_rules('status', 'Status', 'required|in_list[0,1]', array('required' => '%s harus diisi!'));

    if ($this->form_validation->run() == false) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->cb->trans_begin();

      if ($status == 1) {
        $posisi = 'Diajukan kepada keuangan';
      } else {
        $posisi = 'Ditolak oleh supervisi';
      }

      $update = [
        'status' => $status,
        'date_spv' => $tanggal,
        'posisi' => $posisi
      ];

      $this->cb->where('kode', $kode);
      $this->cb->update('t_pengajuan', $update);

      if ($this->cb->trans_status() === FALSE) {
        $this->cb->trans_rollback();
      } else {
        $this->cb->trans_commit();

        $response = [
          'success' => true,
          'msg' => 'Status pengajuan ' . $kode . ' berhasil diubah!',
          'reload' => site_url('pengajuan/approval_spv')
        ];
      }
    }

    echo json_encode($response);
  }

  public function approval_keuangan()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('pengajuan/approval_spv');
    $config['total_rows'] = $this->M_pengajuan->pengajuan_count_keuangan($this->session->userdata('nip'), $keyword);
    $config['per_page'] = "10";
    $config["uri_segment"] = 3;
    $config["num_links"] = 10;
    $config['enable_query_strings'] = TRUE;
    $config['page_query_string'] = TRUE;
    $config['use_page_numbers'] = TRUE;
    $config['reuse_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';

    // integrate bootstrap pagination
    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = true;
    $config['last_link'] = true;
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = 'Previous';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = 'Next';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    // initialize pagination
    $this->pagination->initialize($config);
    $data['page'] = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    $data['data_pengajuan'] = $this->M_pengajuan->pengajuan_get_keuangan($config["per_page"], $data['page'], $this->session->userdata('nip'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'List Approval Supervisi';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_keuangan';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $data['belum_proses_keuangan'] = $this->cb->select('Id')->from('t_pengajuan')->where('posisi', 'Diajukan kepada keuangan')->where('cabang', $this->session->userdata('kode_cabang'))->get()->num_rows();

    $data['belum_bayar'] = $this->cb->select('Id')->from('t_pengajuan')->where('posisi', 'Diarahkan ke pembayaran')->where('status', 3)->where('cabang', $this->session->userdata('kode_cabang'))->get()->num_rows();

    $data['belum_close'] = $this->cb->select('Id')->from('t_pengajuan')->where('posisi', 'Sudah dibayar')->where('status', 4)->where('cabang', $this->session->userdata('kode_cabang'))->get()->num_rows();

    $this->load->view('index', $data);
  }

  public function update_keuangan($kode)
  {
    $coa_debit = $this->input->post('coa[]');
    $tanggal = $this->input->post('tanggal');
    $status = $this->input->post('status');
    $direksi = $this->input->post('direksi');
    $nama_direksi = $this->input->post('nama_direksi');
    $catatan = $this->input->post('catatan');

    $id_item = $this->input->post('id_item[]');

    $this->form_validation->set_rules('status', 'status pengajuan', 'required');
    $this->form_validation->set_rules('tanggal', 'date', 'required');
    if ($status == 1) {
      $this->form_validation->set_rules('coa[]', 'coa', 'required');
      $this->form_validation->set_rules('direksi', 'Approval direksi', 'required');
    }

    if ($direksi == 1) {
      $this->form_validation->set_rules('nama_direksi', 'direksi', 'required');
    }

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->cb->trans_start();
      if ($status == 1) {
        if ($direksi == 1) {
          $status_pengajuan = 2;
          $posisi = 'Diajukan kepada direksi';
          $nama_direksi = $nama_direksi;
        } else {
          $status_pengajuan = 3;
          $posisi = 'Diarahkan ke pembayaran';
          $nama_direksi = null;
        }

        $update_detail = [];
        for ($i = 0; $i < count($id_item); $i++) {
          $update_detail[] = [
            'Id' => $id_item[$i],
            'debit' => $coa_debit[$i]
          ];
        }

        $this->cb->update_batch('t_pengajuan_detail', $update_detail, 'Id');
      } else {
        $status_pengajuan = 0;
        $posisi = 'Ditolak oleh keuangan';
        $nama_direksi = null;
      }

      $update = [
        'keuangan' => $this->session->userdata('nip'),
        'status' => $status_pengajuan,
        'date_keuangan' => $tanggal,
        'catatan_keuangan' => $catatan,
        'posisi' => $posisi,
        'direksi' => $nama_direksi
      ];

      $this->cb->where('kode', $kode);
      $this->cb->update('t_pengajuan', $update);

      $this->cb->trans_complete();

      if ($this->cb->trans_status() === FALSE) {
        $this->cb->trans_rollback();
      } else {
        $this->cb->trans_commit();

        $response = [
          'success' => true,
          'msg' => 'Status pengajuan ' . $kode . ' berhasil diubah!',
          'reload' => site_url('pengajuan/approval_keuangan')
        ];
      }
    }

    echo json_encode($response);
  }

  public function bayar($kode)
  {
    $data['title'] = 'Proses Bayar Pengajuan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_bayar';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pengajuan_detail'] = $this->M_pengajuan->pengajuan_get_detail($kode);
    $data['pengajuan'] = $this->M_pengajuan->pengajuan_by_kode($kode);

    $data['coa'] = $this->cb->select('no_sbb, nama_perkiraan')->from('v_coa_all')->where('id_cabang', $this->session->userdata('kode_cabang'))->get()->result();

    $user = $this->db->select('users.Id, bagian.nama as nama_bagian')->from('users')->join('bagian', 'bagian.Id = users.bagian')->where('users.nip', $this->session->userdata('nip'))->where('users.id_cabang', $this->session->userdata('kode_cabang'))->get()->row();


    if (empty($data['pengajuan']) or $data['pengajuan']->status != 3 or $data['pengajuan']->cabang != $this->session->userdata('kode_cabang') or $user->nama_bagian != 'Finance') {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $this->load->view('index', $data);
  }

  public function update_bayar($kode)
  {

    $coa_kredit = $this->input->post('coa_credit[]');
    $id_item = $this->input->post('id_item[]');
    $subtotal = $this->input->post('subtotal[]');
    $tgl = $this->input->post('tanggal');

    $this->form_validation->set_rules('coa_credit[]', 'coa', 'required');

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $config['upload_path']          = './uploads/pengajuan';
      $config['allowed_types']        = 'jpg|jpeg|png|pdf';
      $config['encrypt_name']         = TRUE;

      $this->upload->initialize($config);
      if (!$this->upload->do_upload('file')) {
        $response = [
          'success' => false,
          'msg' => $this->upload->display_errors()
        ];
      } else {
        $this->cb->trans_start();
        $upload = $this->upload->data();

        // Update table pengajuan
        $update = [
          'user_bayar' => $this->session->userdata('nip'),
          'status' => 4,
          'posisi' => 'Sudah dibayar',
          'date_bayar' => $tgl,
          'bukti_bayar' => $upload['file_name'],
        ];

        $this->cb->where(['kode' => $kode]);
        $this->cb->update('t_pengajuan', $update);

        $jurnal = [];
        $pengajuan_detail = [];
        for ($i = 0; $i < count($id_item); $i++) {
          $item[] = $this->cb->get_where('t_pengajuan_detail', ['Id' => $id_item[$i]])->row_array();

          if ($item[$i]['debit'] == $coa_kredit[$i]) {
            $response = [
              'success' => false,
              'msg' => 'Coa debit dan kredit tidak boleh sama'
            ];

            $this->cb->trans_rollback();
            echo json_encode($response);
            return;
          }

          // Update coa debit
          $this->update_saldo_coa($item[$i]['debit'], $subtotal[$i], 'debit');

          // Update coa kredit
          $this->update_saldo_coa($coa_kredit[$i], $subtotal[$i], 'kredit');

          // Ambil saldo terbaru dari coa_sbb untuk akun debit
          $saldo_debit = $this->get_saldo_coa($item[$i]['debit']);

          // Ambil saldo terbaru dari coa_sbb untuk akun kredit
          $saldo_kredit = $this->get_saldo_coa($coa_kredit[$i]);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $item[$i]['debit'],
            'jumlah_debit' => $subtotal[$i],
            'akun_kredit' => $coa_kredit[$i],
            'jumlah_kredit' => $subtotal[$i],
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - Pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];

          $pengajuan_detail[] = [
            'Id' => $id_item[$i],
            'kredit' => $coa_kredit[$i]
          ];
        }

        $this->cb->insert_batch('jurnal_neraca', $jurnal);
        $this->cb->update_batch('t_pengajuan_detail', $pengajuan_detail, 'Id');

        $this->cb->trans_complete();

        if ($this->cb->trans_status() === FALSE) {
          $this->cb->trans_rollback();
        } else {
          $this->cb->trans_commit();

          $response = [
            'success' => true,
            'msg' => 'Pengajuan ' . $kode . ' berhasil dibayar!',
            'reload' => site_url('pengajuan/approval_keuangan')
          ];
        }
      }
    }
    echo json_encode($response);
  }

  public function close($kode)
  {
    $data['title'] = 'Proses Close Pengajuan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_close';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pengajuan_detail'] = $this->M_pengajuan->pengajuan_get_detail($kode);
    $data['pengajuan'] = $this->M_pengajuan->pengajuan_by_kode($kode);

    $data['coa'] = $this->cb->select('no_sbb, nama_perkiraan')->from('v_coa_all')->where('id_cabang', $this->session->userdata('kode_cabang'))->get()->result();

    $user = $this->db->select('users.Id, bagian.nama as nama_bagian')->from('users')->join('bagian', 'bagian.Id = users.bagian')->where('users.nip', $this->session->userdata('nip'))->where('users.id_cabang', $this->session->userdata('kode_cabang'))->get()->row();


    if (empty($data['pengajuan']) or $data['pengajuan']->status != 4 or $data['pengajuan']->cabang != $this->session->userdata('kode_cabang') or $user->nama_bagian != 'Finance') {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $this->load->view('index', $data);
  }

  public function update_close($kode)
  {
    $coa_beban = $this->input->post('coa_beban[]');
    $realisasi = $this->input->post('realisasi[]');
    $id_item = $this->input->post('id_item[]');

    $tgl = $this->input->post('tanggal');

    $this->form_validation->set_rules('coa_beban[]', 'COA Beban', 'required');
    $this->form_validation->set_rules('realisasi[]', 'Realisasi', 'required');
    $this->form_validation->set_rules('tanggal', 'date', 'required');

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->cb->trans_start();
      // Update table pengajuan
      $update = [
        'user_bayar' => $this->session->userdata('nip'),
        'status' => 5,
        'posisi' => 'Closed',
        'date_close' => $tgl,
      ];

      $this->cb->where(['kode' => $kode]);
      $this->cb->update('t_pengajuan', $update);

      $jurnal = [];
      $pengajuan_detail = [];
      for ($i = 0; $i < count($id_item); $i++) {
        $item[] = $this->cb->get_where('t_pengajuan_detail', ['Id' => $id_item[$i]])->row_array();

        if ($item[$i]['debit'] == $coa_beban[$i] or $item[$i]['kredit'] == $coa_beban[$i]) {
          $response = [
            'success' => false,
            'msg' => 'Coa debit atau kredit tidak boleh sama dengan coa beban'
          ];

          $this->cb->trans_rollback();
          echo json_encode($response);
          return;
        }

        $selisih[] = $item[$i]['total'] - $realisasi[$i];

        if ($selisih[$i] > 0) {
          // Kredit 
          $this->update_saldo_coa($item[$i]['debit'], $realisasi[$i], 'kredit');

          // Debit
          $this->update_saldo_coa($coa_beban[$i], $realisasi[$i], 'debit');

          // Ambil saldo kredit
          $saldo_kredit = $this->get_saldo_coa($item[$i]['debit']);

          // Ambil saldo debit
          $saldo_debit = $this->get_saldo_coa($coa_beban[$i]);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $coa_beban[$i],
            'jumlah_debit' => $realisasi[$i],
            'akun_kredit' => $item[$i]['debit'],
            'jumlah_kredit' => $realisasi[$i],
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - close pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];

          // Debit
          $this->update_saldo_coa($item[$i]['kredit'], $selisih[$i], 'debit');
          // Kredit
          $this->update_saldo_coa($item[$i]['debit'], $selisih[$i], 'kredit');
          // Ambil saldo debit
          $saldo_debit = $this->get_saldo_coa($item[$i]['kredit']);
          // Ambil saldo kredit
          $saldo_kredit = $this->get_saldo_coa($item[$i]['debit']);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $item[$i]['kredit'],
            'jumlah_debit' => $selisih[$i],
            'akun_kredit' => $item[$i]['debit'],
            'jumlah_kredit' => $selisih[$i],
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - close pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];
        }

        if ($selisih[$i] < 0) {
          // Debit
          $this->update_saldo_coa($coa_beban[$i], $item[$i]['total'], 'debit');
          // Kredit
          $this->update_saldo_coa($item[$i]['debit'], $item[$i]['total'], 'kredit');

          // Ambil saldo debit
          $saldo_debit = $this->get_saldo_coa($coa_beban[$i]);
          // Ambil saldo kredit
          $saldo_kredit = $this->get_saldo_coa($item[$i]['debit']);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $coa_beban[$i],
            'jumlah_debit' => $item[$i]['total'],
            'akun_kredit' => $item[$i]['debit'],
            'jumlah_kredit' => $item[$i]['total'],
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - close pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];

          // Debit
          $this->update_saldo_coa($coa_beban[$i], abs($selisih[$i]), 'debit');
          // Kredit
          $this->update_saldo_coa($item[$i]['kredit'], abs($selisih[$i]), 'kredit');

          // Ambil saldo debit
          $saldo_debit = $this->get_saldo_coa($coa_beban[$i]);
          // Ambil saldo kredit
          $saldo_kredit = $this->get_saldo_coa($item[$i]['kredit']);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $coa_beban[$i],
            'jumlah_debit' => abs($selisih[$i]),
            'akun_kredit' => $item[$i]['kredit'],
            'jumlah_kredit' => abs($selisih[$i]),
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - close pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];
        }

        if ($selisih[$i] == 0) {
          // Debit
          $this->update_saldo_coa($coa_beban[$i], $realisasi[$i], 'debit');
          // Kredit
          $this->update_saldo_coa($item[$i]['debit'], $realisasi[$i], 'kredit');

          // Ambil saldo debit
          $saldo_debit = $this->get_saldo_coa($coa_beban[$i]);
          // Ambil saldo kredit
          $saldo_kredit = $this->get_saldo_coa($item[$i]['debit']);

          // insert jurnal
          $jurnal[] = [
            'tanggal' => $tgl,
            'akun_debit' => $coa_beban[$i],
            'jumlah_debit' => $realisasi[$i],
            'akun_kredit' => $item[$i]['debit'],
            'jumlah_kredit' => $realisasi[$i],
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $item[$i]['item'] . ' - close pengajuan ' . $kode,
            'created_by' => $this->session->userdata('nip'),
            'id_cabang' => $this->session->userdata('kode_cabang')
          ];
        }

        $pengajuan_detail[] = [
          'Id' => $item[$i]['Id'],
          'beban' => $coa_beban[$i],
          'realisasi' => $realisasi[$i]
        ];
      }

      $this->cb->insert_batch('jurnal_neraca', $jurnal);
      $this->cb->update_batch('t_pengajuan_detail', $pengajuan_detail, 'Id');

      $this->cb->trans_complete();

      if ($this->cb->trans_status() === FALSE) {
        $this->cb->trans_rollback();
      } else {
        $this->cb->trans_commit();

        $response = [
          'success' => true,
          'msg' => 'Pengajuan ' . $kode . ' berhasil di closing!',
          'reload' => site_url('pengajuan/approval_keuangan')
        ];
      }
    }
    echo json_encode($response);
  }

  private function update_saldo_coa($akun_no, $jumlah, $tipe)
  {
    $substr_coa = substr($akun_no, 0, 1);
    if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
      $table = "t_coa_sbb";
      $kolom = "no_sbb";
    } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
      $table = "t_coalr_sbb";
      $kolom = "no_lr_sbb";
    }

    $query = $this->cb->query(
      "SELECT posisi, nominal FROM $table WHERE " . $kolom . " = ? AND id_cabang = " . $this->session->userdata('kode_cabang') . " FOR UPDATE",
      [$akun_no]
    );

    $row = $query->row();
    if (!$row) return;

    $posisi = $row->posisi;
    $nominal = $row->nominal;

    if ($posisi == 'AKTIVA') {
      if ($tipe == 'debit') {
        $nominal += $jumlah;
      } else { // kredit
        $nominal -= $jumlah;
      }
    } elseif ($posisi == 'PASIVA') {
      if ($tipe == 'debit') {
        $nominal -= $jumlah;
      } else { // kredit
        $nominal += $jumlah;
      }
    }

    // Update saldo
    $this->cb->where(($table == 't_coa_sbb') ? 'no_sbb' : 'no_lr_sbb', $akun_no);
    $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
    $this->cb->update($table, ['nominal' => $nominal]);
  }

  private function get_saldo_coa($akun_no)
  {
    $substr_coa = substr($akun_no, 0, 1);
    if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
      $table = "t_coa_sbb";
      $kolom = "no_sbb";
    } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
      $table = "t_coalr_sbb";
      $kolom = "no_lr_sbb";
    }

    $row = $this->cb->select('nominal')
      ->where($kolom, $akun_no)
      ->where('id_cabang', $this->session->userdata('kode_cabang'))
      ->get($table)
      ->row();

    return $row->nominal;
  }


  private function _parse_rupiah($rupiah)
  {
    // Hilangkan Rp, titik, dan ganti koma dengan titik
    $rupiah = str_replace(['Rp', '.', ' '], '', $rupiah);
    return floatval(str_replace(',', '.', $rupiah));
  }
}
