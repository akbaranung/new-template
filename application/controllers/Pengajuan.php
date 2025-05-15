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
    $rekening = $this->input->post('rekening');
    $metode = $this->input->post('metode');
    $catatan = $this->input->post('catatan');

    $uraian = $this->input->post('uraian[]');
    $qty = $this->input->post('qty[]');
    $price = $this->input->post('price[]');
    $sub_total = $this->input->post('subtotal[]');

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
    }

    echo json_encode($response);
  }
}
