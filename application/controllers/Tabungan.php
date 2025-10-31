<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tabungan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    if ($this->session->userdata('isLogin') == FALSE) {
      $this->session->set_flashdata('error', 'Your session has expired');
      redirect('auth');
    } else if (!$this->session->userdata('nama_perusahaan')) {
      redirect('auth');
    }

    $this->load->model(['M_user_access', 'M_tabungan']);

    $this->cb = $this->load->database('corebank', TRUE);
  }

  public function index()
  {
    $data['nasabah'] = $this->M_tabungan->get_nasabah();

    $nip = $this->session->userdata('nip');
    $data['title'] = 'Tabungan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/tabungan/s_tabungan';
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/tabungan/v_tabungan';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function ajax_list()
  {
    $nasabah = $this->input->post('nasabah');

    $list = $this->M_tabungan->get_datatables($nasabah);
    $data = array();
    $no = $_POST['start'];


    foreach ($list as $cat) {

      $no++;
      $row = array();
      // $row[] = $no;
      $row[] = $cat->no_urut;
      $row[] = $cat->no_tabungan;
      $row[] = $cat->nama;
      $row[] = $cat->nama_tabungan;
      $row[] = $cat->status_tabungan;
      $row[] = $cat->nominal;
      $row[] = $cat->spread_rate;
      $row[] = $cat->nominal_blokir;
      $row[] = $cat->pos_rate;
      $row[] = $cat->nolsp;
      $delete_url = base_url('tabungan/delete/' . $cat->no_tabungan);

      $row[] = '<a class="btn btn-warning m-1" href="' . base_url('tabungan/edit_tabungan/' . $cat->no_tabungan) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->no_tabungan . ')">
    Delete
</a>';

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_tabungan->count_all(),
      "recordsFiltered" => $this->M_tabungan->count_filtered($nasabah),
      "data" => $data,
    );
    echo json_encode($output);
  }

  public function add_tabungan()
  {
    $data['new_tabungan_number'] = $this->M_tabungan->generate_next_no_tabungan();
    $data['new_no_urut'] = $this->M_tabungan->generate_next_no_urut();
    $data['jenis_tabungan'] = $this->M_tabungan->get_jenis_tabungan();
    $data['nasabah'] = $this->M_tabungan->get_nasabah();
    $data['form_data'] = $this->session->flashdata('form_data');

    $nip = $this->session->userdata('nip');
    $data['title'] = 'Add Tabungan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/tabungan/s_tabungan';
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/tabungan/v_tabungan_add';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function edit_tabungan($id)
  {
    $data['tabungan'] = $this->M_tabungan->get_tabungan($id);
    $data['nasabah'] = $this->M_tabungan->get_nasabah();
    $data['jenis_tabungan'] = $this->M_tabungan->get_jenis_tabungan();

    $this->cb = $this->load->database('corebank', TRUE);

    $data['title'] = 'Edit Tabungan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/tabungan/s_tabungan';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/tabungan/v_tabungan_add';

    $this->load->view('index', $data);
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function proccess_add()
  {
    // Assume input data is captured from POST. We collect this first 
    // to flash it back (old input) if validation fails.
    $data = [
      'no_tabungan' => $_POST['no_tabungan'] ?? '',
      'no_cib' => $_POST['no_cib'] ?? '',
      'jenis_tabungan' => $_POST['jenis_tabungan'] ?? '',
      'status_tabungan' => $_POST['status_tabungan'] ?? '',
      'no_urut' => $_POST['no_urut'] ?? '',
      'nominal' => $_POST['nominal'] ?? '',
      'spread_rate' => $_POST['spread_rate'] ?? '',
      'nominal_blokir' => $_POST['nominal_blokir'] ?? '',
      'pos_rate' => $_POST['pos_rate'] ?? '',
      'nolsp' => $_POST['nolsp'] ?? '',
    ];

    // --- SET VALIDATION RULES (Conceptual Framework Syntax) ---
    // In a real framework, you would typically load the validation library first.
    $this->form_validation->set_rules('no_tabungan', 'Nomor Tabungan', 'required|max_length[8]');
    // $this->form_validation->set_rules('alamat', 'Alamat', 'required');
    $this->form_validation->set_rules('no_urut', 'Nomor Urut', 'required|numeric|max_length[5]'); // Assuming 16 digits
    $this->form_validation->set_rules('nominal', 'Nominal', 'numeric');
    $this->form_validation->set_rules('spread_rate', 'Spread Rate', 'required|numeric');
    $this->form_validation->set_rules('nominal_blokir', 'Nominal Blokir', 'required|numeric');
    $this->form_validation->set_rules('pos_rate', 'Pos Rate', 'required|numeric');
    $this->form_validation->set_rules('nolsp', 'Nomor LSP', 'required|numeric');
    // $this->form_validation->set_rules('tgl_pendaftaran', 'Tgl Pendaftaran', 'required|valid_date');
    $this->form_validation->set_rules('no_cib', 'Nasabah', 'required');
    $this->form_validation->set_rules('jenis_tabungan', 'Jenis Tabungan', 'required');

    // Run the validation
    if ($this->form_validation->run() == FALSE) {
      // --- VALIDATION FAILED: Use Flash Data to store errors and old input ---

      // Retrieve all validation errors as an array
      $errors = $this->form_validation->error_array();

      // Store errors in session flash data
      $this->session->set_flashdata('form_errors', $errors);

      // $this->session->set_flashdata('message_error', $errors);


      // Store all submitted data (old input) in session flash data
      $this->session->set_flashdata('form_data', $data);

      // Redirect back to the form view (tabungan/add_tabungan)
      redirect('tabungan/add_tabungan');
      // header('Location: ' . base_url('tabungan/add_tabungan'));
      // exit();
    } else {

      $this->cb->insert('t_tabungan', $data);
      // --- VALIDATION SUCCESS: Process the data ---

      // Example: $this->tabungan_model->save($data);
      $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Tambahkan.');

      // Redirect to a success page
      redirect('tabungan');
      exit();
    }
  }

  public function proccess_edit()
  {
    // Assume input data is captured from POST. We collect this first 
    // to flash it back (old input) if validation fails.
    $edit_data = [
      'no_cib' => $_POST['no_cib'] ?? '',
      'jenis_tabungan' => $_POST['jenis_tabungan'] ?? '',
      'status_tabungan' => $_POST['status_tabungan'] ?? '',
      'no_urut' => $_POST['no_urut'] ?? '',
      'nominal' => $_POST['nominal'] ?? '',
      'spread_rate' => $_POST['spread_rate'] ?? '',
      'nominal_blokir' => $_POST['nominal_blokir'] ?? '',
      'pos_rate' => $_POST['pos_rate'] ?? '',
      'nolsp' => $_POST['nolsp'] ?? '',
    ];


    $this->cb->where('no_tabungan', $this->input->post('no_tabungan'));
    if ($this->cb->update('t_tabungan', $edit_data)) {

      $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Ubah.');
      redirect('tabungan');
    } else {

      $this->session->set_flashdata('message_error', 'Tabungan Gagal di Ubah.');
      redirect('tabungan/edit_tabungan/' . $this->input->post('no_tabungan'));
    }
  }

  public function delete($id)
  {
    $this->cb->where('no_tabungan', $id);
    if ($this->cb->delete('t_tabungan')) {

      $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Hapus.');
      redirect('tabungan');
    } else {

      $this->session->set_flashdata('message_error', 'Tabungan Gagal di Hapus.');
      redirect('tabungan');
    }

    // echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

    // redirect('perusahaan/cabang');
  }
}
