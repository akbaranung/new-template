<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model(['M_asset']);
    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

    $this->cb = $this->load->database('corebank', TRUE);

    date_default_timezone_set('Asia/Jakarta');
  }

  public function index()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('asset');
    $config['total_rows'] = $this->M_asset->asset_count($this->session->userdata('kode_cabang'), $keyword);
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
    $data['data_asset'] = $this->M_asset->asset_get($config["per_page"], $data['page'], $this->session->userdata('kode_cabang'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'Daftar Aset';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/asset/s_asset';
    $data['pages'] = 'pages/asset/v_asset';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function detail($id)
  {
    $data['title'] = 'Daftar Aset';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/asset/s_asset';
    $data['pages'] = 'pages/asset/v_asset_detail';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['data_asset'] = $this->M_asset->ambil_data_asset($id, $this->session->userdata('kode_cabang'));
    $data['asset_history'] = $this->M_asset->ambil_data_history($data['data_asset']->kode, $this->session->userdata('kode_cabang'));
    $data['ruangan'] = $this->db->get_where('asset_ruang', ['cabang' => $this->session->userdata('kode_cabang')])->result();

    $this->load->view('index', $data);
  }

  public function qrcode_view($id)
  {
    $data['data_asset'] = $this->M_asset->ambil_data_asset($id, $this->session->userdata('kode_cabang'));
    $this->load->view('pages/asset/qr-code', $data);
  }

  public function tambah()
  {
    $data['title'] = 'Tambah Aset Baru';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/asset/s_asset';
    $data['pages'] = 'pages/asset/v_form_asset';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['jenis_aset'] = $this->db->get_where('asset_jenis', ['cabang' => $this->session->userdata('kode_cabang')])->result();
    $data['ruangan'] = $this->db->get_where('asset_ruang', ['cabang' => $this->session->userdata('kode_cabang')])->result();
    $data['coa'] = $this->cb->get_where('v_coa_all', ['id_cabang' => $this->session->userdata('kode_cabang')])->result();

    $this->load->view('index', $data);
  }


  public function jenis_aset()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('asset/jenis_aset');
    $config['total_rows'] = $this->M_asset->jenis_count($this->session->userdata('kode_cabang'), $keyword);
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
    $data['data_jenis'] = $this->M_asset->asset_jenis_get($config["per_page"], $data['page'], $this->session->userdata('kode_cabang'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'Jenis Aset';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/asset/s_asset';
    $data['pages'] = 'pages/asset/v_jenis_asset';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function store_jenis()
  {
    $id = $this->input->post('id_jenis');
    $jenis = $this->input->post('jenis_asset');

    $this->form_validation->set_rules('jenis_asset', 'Nama jenis', 'required|trim', ['required' => '%s harus diisi!']);
    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->db->trans_start();
      if ($id) {
        $this->db->where(['Id' => $id, 'cabang' => $this->session->userdata('kode_cabang')]);
        $this->db->update('asset_jenis', ['nama_jenis' => $jenis]);
        $msg = 'Jenis aset berhasil diubah!';
      } else {
        $this->db->insert('asset_jenis', ['nama_jenis' => $jenis, 'cabang' => $this->session->userdata('kode_cabang')]);
        $msg = 'Jenis aset berhasil ditambahkan!';
      }
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
      } else {
        $this->db->trans_commit();

        $response = [
          'success' => true,
          'msg' => $msg,
          'reload' => site_url('asset/jenis_aset')
        ];
      }
    }
    echo json_encode($response);
  }

  public function ruangan()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('asset/ruangan');
    $config['total_rows'] = $this->M_asset->ruangan_count($this->session->userdata('kode_cabang'), $keyword);
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
    $data['ruangan'] = $this->M_asset->ruangan_get($config["per_page"], $data['page'], $this->session->userdata('kode_cabang'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'Ruangan Aset';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/asset/s_asset';
    $data['pages'] = 'pages/asset/v_ruangan_asset';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function store_ruangan()
  {
    $id = $this->input->post('id_ruangan');
    $ruangan = $this->input->post('ruangan');

    $this->form_validation->set_rules('ruangan', 'Nama ruangan', 'required|trim', ['required' => '%s harus diisi!']);
    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->db->trans_start();
      if ($id) {
        $this->db->where(['Id' => $id, 'cabang' => $this->session->userdata('kode_cabang')]);
        $this->db->update('asset_ruang', ['keterangan' => $ruangan]);
        $msg = 'Ruangan aset berhasil diubah!';
      } else {
        $this->db->insert('asset_ruang', ['keterangan' => $ruangan, 'cabang' => $this->session->userdata('kode_cabang')]);
        $msg = 'Ruangan aset berhasil ditambahkan!';
      }
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
      } else {
        $this->db->trans_commit();

        $response = [
          'success' => true,
          'msg' => $msg,
          'reload' => site_url('asset/ruangan')
        ];
      }
    }
    echo json_encode($response);
  }

  public function store_asset()
  {
    $kode = $this->input->post('kode');
    $tgl = $this->input->post('tgl');
    $jenis = $this->input->post('jenis');
    $nama = $this->input->post('nama');
    $spesifikasi = $this->input->post('spesifikasi');
    $ruangan = $this->input->post('ruangan');
    $kondisi = $this->input->post('kondisi');
    $harga = $this->input->post('harga');
    $umur = $this->input->post('umur');
    $coa_aset = $this->input->post('coa_aset');
    $coa_beban = $this->input->post('coa_beban');
    $coa_kas = $this->input->post('coa_kas');
    $coa_penyusutan = $this->input->post('coa_penyusutan');

    $file_name = $_FILES['foto']['name'];

    $this->form_validation->set_rules('kode', 'Kode aset', 'required|trim|alpha_dash|min_length[3]', [
      'required' => '%s harus diisi!',
      'alpha_dash' => '%s hanya boleh berisi huruf, angka, underscore, atau tanda hubung!',
      'min_length' => '%s setidaknya terdiri dari %d karakter!'
    ]);
    $this->form_validation->set_rules('tgl', 'Tanggal perolehan', 'required', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('jenis', 'Jenis aset', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('nama', 'Nama aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('spesifikasi', 'Spesifikasi aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('ruangan', 'Ruangan aset', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('kondisi', 'Kondisi aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('harga', 'Harga perolehan aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('umur', 'Umur aset', 'required|trim|numeric', [
      'required' => '%s harus diisi!',
      'numeric' => '%s hanya boleh berisi angka!'
    ]);
    $this->form_validation->set_rules('coa_aset', 'COA aset', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('coa_beban', 'COA beban', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('coa_kas', 'COA kas', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('coa_penyusutan', 'COA penyusutan', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->db->trans_start();
      $this->cb->trans_start();

      $penyusutanBulan = $this->_parse_rupiah($harga) / $umur;

      if ($this->session->userdata('is_premium')) {
        if ($file_name) {
          $config['upload_path']          = './uploads/asset';
          $config['allowed_types']        = 'jpg|png|jpeg';
          $config['max_size']             = 1024;
          // $config['max_width']            = 1024;
          // $config['max_height']           = 768;
          $config['encrypt_name']          = TRUE;
          $this->upload->initialize($config);

          // Periksa apakah folder sudah ada
          if (!is_dir('./uploads/asset')) {
            // Jika tidak ada, buat folder dengan hak akses 0777
            mkdir('./uploads/asset', 0777, TRUE);
          }

          if (!$this->upload->do_upload('foto')) {
            $response = [
              'success' => FALSE,
              'msg' => $this->upload->display_errors()
            ];

            echo json_encode($response);
            return false;
          } else {
            $gbr = $this->upload->data();
            $foto = $gbr['file_name'];
          }
        } else {
          $foto = null;
        }
      } else {
        $foto = null;
      }

      // insert Asset List
      $insert_assetList = [
        'jenis_asset'  => $jenis,
        'nama_asset'  => $nama,
        'kode'      => $kode,
        'spesifikasi'  => $spesifikasi,
        'ruangan'    => $ruangan,
        'lokasi'    => $this->session->userdata('kode_cabang'),
        'jumlah'    => 1,
        'tgl_perolehan'  => $tgl,
        'kondisi'    => $kondisi,
        'harga'      => $this->_parse_rupiah($harga),
        'umur'      => $umur,
        'last_update'  => date('Y-m-d'),
        'pic' => $foto,
        'sisa_umur' => $umur,
        'coa_asset' => $coa_aset,
        'coa_beban' => $coa_beban,
        'coa_penyusutan' => $coa_penyusutan,
        'penyusutan_bulan' => $penyusutanBulan,
        'nilai_buku' => $this->_parse_rupiah($harga),
        'cabang' => $this->session->userdata('kode_cabang')
      ];
      //Tambah history Asset
      $insert_history = [
        'kode'    => $kode,
        'ruangan'  => $ruangan,
        'lokasi'  => $this->session->userdata('kode_cabang'),
        'tanggal'  => date('Y-m-d'),
        // 'remark'  => $remark,
      ];

      $this->db->insert('asset_list', $insert_assetList);
      $this->db->insert('asset_history', $insert_history);

      // Debit 
      $this->update_saldo_coa($coa_aset, $penyusutanBulan, 'debit');
      // Kredit
      $this->update_saldo_coa($coa_kas, $penyusutanBulan, 'kredit');

      $saldo_debit = $this->get_saldo_coa($coa_aset);
      $saldo_kredit = $this->get_saldo_coa($coa_kas);

      $jurnal = [
        'tanggal' => date('Y-m-d'),
        'akun_debit' => $coa_aset,
        'jumlah_debit' => $penyusutanBulan,
        'akun_kredit' => $coa_kas,
        'jumlah_kredit' => $penyusutanBulan,
        'saldo_debit' => $saldo_debit,
        'saldo_kredit' => $saldo_kredit,
        'created_by' => $this->session->userdata('nip'),
        'keterangan' => 'Nilai penyusutan perbulan asset ' . $nama . ' (' . $kode . ')',
        'id_cabang' => $this->session->userdata('kode_cabang')
      ];

      $this->cb->insert('jurnal_neraca', $jurnal);

      $this->db->trans_complete();
      $this->cb->trans_complete();

      if ($this->db->trans_status() === FALSE or $this->cb->trans_status() === FALSE) {
        $this->cb->trans_rollback();
        $this->db->trans_rollback();
      } else {
        $this->cb->trans_commit();
        $this->db->trans_commit();

        $response = [
          'success' => TRUE,
          'msg' => 'Asset berhasil ditambahkan!',
          'reload' => base_url('asset')
        ];
      }
    }
    echo json_encode($response);
  }

  public function update_asset($id)
  {
    $nama = $this->input->post('nama');
    $spesifikasi = $this->input->post('spesifikasi');
    $ruangan = $this->input->post('ruangan');
    $kondisi = $this->input->post('kondisi');
    $keterangan = $this->input->post('detail');
    $file_name = $_FILES['foto']['name'];

    $this->form_validation->set_rules('nama', 'Nama aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('spesifikasi', 'Spesifikasi aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);
    $this->form_validation->set_rules('ruangan', 'Ruangan aset', 'required|trim', [
      'required' => '%s harus dipilih!'
    ]);
    $this->form_validation->set_rules('kondisi', 'Kondisi aset', 'required|trim', [
      'required' => '%s harus diisi!'
    ]);

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $this->db->trans_start();

      $asset = $this->M_asset->ambil_data_asset($id, $this->session->userdata('kode_cabang'));

      if ($this->session->userdata('is_premium')) {
        if ($file_name) {
          $config['upload_path']          = './uploads/asset';
          $config['allowed_types']        = 'jpg|png|jpeg';
          $config['max_size']             = 1024;
          // $config['max_width']            = 1024;
          // $config['max_height']           = 768;
          $config['encrypt_name']          = TRUE;
          $this->upload->initialize($config);

          if (file_exists('./uploads/asset/' . $asset->pic) and !empty($asset->pic)) {
            // Hapus file lama
            unlink('./uploads/asset/' . $asset->pic);
          }

          if (!$this->upload->do_upload('foto')) {
            $response = [
              'success' => FALSE,
              'msg' => $this->upload->display_errors()
            ];

            echo json_encode($response);
            return false;
          } else {
            $gbr = $this->upload->data();
            $foto = $gbr['file_name'];
          }
        } else {
          $foto = $asset->pic;
        }
      } else {
        $foto = $asset->pic;
      }

      // insert Asset List
      $update = [
        'nama_asset'  => $nama,
        'spesifikasi'  => $spesifikasi,
        'ruangan'    => $ruangan,
        'keterangan'  => $keterangan,
        'kondisi'    => $kondisi,
        'pic' => $foto,
        'cabang' => $this->session->userdata('kode_cabang')
      ];
      //Tambah history Asset
      $insert_history = [
        'kode'    => $asset->kode,
        'ruangan'  => $ruangan,
        'lokasi'  => $this->session->userdata('kode_cabang'),
        'tanggal'  => date('Y-m-d'),
        'remark'  => $keterangan,
      ];

      $this->db->where('Id', $id);
      $this->db->update('asset_list', $update);


      $this->db->insert('asset_history', $insert_history);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
      } else {
        $this->db->trans_commit();

        $response = [
          'success' => TRUE,
          'msg' => 'Asset berhasil diubah!',
          'reload' => base_url('asset')
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
