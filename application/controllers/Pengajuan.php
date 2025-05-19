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
      $config['enrypt_name'] = TRUE;

      $this->upload->initialize($config);

      // Jika upload error atau gagal
      if (!$this->upload->do_upload('file')) {
        $response = [
          'success' => false,
          'msg' => $this->upload->display_errors()
        ];
      } else {
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

        $this->cb->insert('t_pengajuan', $insert);

        for ($i = 0; $i < count($uraian); $i++) {
          // Menghilangkan karakter
          $detail = [
            'no_pengajuan' => $count,
            'item' => $uraian[$i],
            'qty' => (int)($qty[$i]),
            'price' => $this->_parse_rupiah($price[$i]),
            'total' => $this->_parse_rupiah($sub_total[$i]),
            'cabang' => $cabang
          ];

          // print_r($detail);

          $this->cb->insert('t_pengajuan_detail', $detail);
        }

        // exit;

        $response = [
          'success' => true,
          'msg' => 'Pengajuan berhasil ditambahkan!'
        ];
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

    $data['title'] = 'List Pengajan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/pengajuan/s_pengajuan';
    $data['pages'] = 'pages/pengajuan/v_pengajuan_list';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  private function _parse_rupiah($rupiah)
  {
    // Hilangkan Rp, titik, dan ganti koma dengan titik
    $rupiah = str_replace(['Rp', '.', ' '], '', $rupiah);
    return floatval(str_replace(',', '.', $rupiah));
  }
}
