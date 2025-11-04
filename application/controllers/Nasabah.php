<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nasabah extends CI_Controller
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

    $this->load->model(['M_user_access', 'M_nasabah']);

    $this->cb = $this->load->database('corebank', TRUE);
  }

  public function index()
  {

    $nip = $this->session->userdata('nip');
    $data['title'] = 'User';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/nasabah/s_nasabah';
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/nasabah/v_nasabah';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function ajax_list()
  {
    $list = $this->M_nasabah->get_datatables();
    $data = array();
    $no = $_POST['start'];


    foreach ($list as $cat) {

      $no++;
      $row = array();
      $row[] = $no;
      // $row[] = $cat->no_cib;
      $row[] = $cat->nama;
      $row[] = $cat->alamat;
      $row[] = $cat->no_ktp;
      $row[] = $cat->no_telp;
      $row[] = $cat->ahli_waris;
      $row[] = $cat->kode_pos;
      $row[] = $cat->nama_ibu_kandung;
      $row[] = $cat->pekerjaan;
      $row[] = $cat->kode_ao;
      $row[] = $cat->nama_panggilan;
      $row[] = $cat->tgl_lahir;
      $row[] = $cat->tempat_lahir;
      $row[] = $cat->kota;
      $row[] = $cat->tgl_pendaftaran;
      $row[] = $cat->tipe_nasabah;
      $row[] = $cat->nama_segmen;
      $row[] = $cat->warga_negara;

      $delete_url = base_url('nasabah/delete/' . $cat->no_cib);

      $row[] = '<a class="btn btn-warning m-1" href="' . base_url('nasabah/edit_nasabah/' . $cat->no_cib) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->no_cib . ')">
    Delete
</a>';

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_nasabah->count_all(),
      "recordsFiltered" => $this->M_nasabah->count_filtered(),
      "data" => $data,
    );
    echo json_encode($output);
  }

  public function add_nasabah()
  {

    $this->cb = $this->load->database('corebank', TRUE);
    $data['title'] = 'Add User';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/nasabah/s_nasabah';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/nasabah/v_nasabah_add';
    $data['form_data'] = $this->session->flashdata('form_data');

    $data['segnasabah'] = $this->M_nasabah->get_segnasabah();
    $data['tipe'] = $this->M_nasabah->get_tipe();
    // CEK PREMIUM
    // if ($this->session->userdata('is_premium')) {
    //   $this->db->from('users');
    //   $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    //   $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    //   $total_user_perusahaan = $this->db->get()->num_rows(); // Get the number of rows

    //   $this->db->from('utility');
    //   $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    //   $perusahaan = $this->db->get()->row(); // Get the number of rows

    //   $limit_user = $perusahaan->kuota_user;
    //   if ($total_user_perusahaan >= $limit_user) {
    //     $this->session->set_flashdata('swal_message', [
    //       'icon' => 'info', // Tetap gunakan 'info' atau 'question' untuk kesan informatif
    //       'title' => 'Singgasana Menunggu Anda!', // Judul yang menarik dan bertema
    //       'text' => 'Batas jumlah pelayan (pengguna) dalam kerajaan Anda telah tercapai. Perluas kekuasaan Anda dan tambahkan lebih banyak rakyat dengan menaikan derajat kerajaan Anda!.',
    //       'confirmButtonText' => 'Klaim Takhta Sekarang!', // Kalimat persuasif untuk tombol
    //       'showCancelButton' => true,
    //       'cancelButtonText' => 'Tunda Penobatan', // Opsi yang lucu dan sesuai tema
    //       'redirectUrl' => base_url('subscription/upgrade')
    //     ]);
    //     redirect('perusahaan/user');
    //   }
    $this->load->view('index', $data);
    // } else {
    //   $this->db->from('users');
    //   $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    //   $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    //   $this->db->where('nama_jabatan !=', 'Super Admin');
    //   $total_user = $this->db->get()->num_rows(); // Get the number of rows

    //   $max_users_for_100_percent = 4; // Define your maximum limit
    //   // $max_users_for_100_percent = 5; // Define your maximum limit

    //   if ($total_user < $max_users_for_100_percent) {
    //     $this->load->view('index', $data);
    //   } else {
    //     $this->session->set_flashdata('swal_message', [
    //       'icon' => 'question', // or 'success', 'warning', 'info', 'question'
    //       'title' => 'Siap Menjadi Raja <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>',
    //       'text' => 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..',
    //       'confirmButtonText' => 'Ambil Mahkota Sekarang!',
    //       'showCancelButton' => true,
    //       'cancelButtonText' => 'Nanti Saja, Belum Siap Jadi Raja',
    //       'redirectUrl' => base_url('subscription/upgrade') // URL to redirect if confirmed
    //     ]);
    //     redirect('perusahaan/user');
    //   }
    // }
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }
  public function edit_nasabah($id)
  {


    $this->cb = $this->load->database('corebank', TRUE);

    $data['nasabah'] = $this->M_nasabah->get_nasabah($id);
    $data['title'] = 'Add Lokasi Presensi';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/nasabah/s_nasabah';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/nasabah/v_nasabah_add';


    $data['segnasabah'] = $this->M_nasabah->get_segnasabah();
    $data['tipe'] = $this->M_nasabah->get_tipe();

    $this->load->view('index', $data);
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function proccess_add()
  {
    // Assume input data is captured from POST. We collect this first 
    // to flash it back (old input) if validation fails.
    $data = [
      'nama' => $_POST['nama'] ?? '',
      'alamat' => $_POST['alamat'] ?? '',
      'no_ktp' => $_POST['no_ktp'] ?? '',
      'no_telp' => $_POST['no_telp'] ?? '',
      'ahli_waris' => $_POST['ahli_waris'] ?? '',
      'kode_pos' => $_POST['kode_pos'] ?? '',
      'nama_ibu_kandung' => $_POST['nama_ibu_kandung'] ?? '',
      'pekerjaan' => $_POST['pekerjaan'] ?? '',
      'kode_ao' => $_POST['kode_ao'] ?? '',
      'nama_panggilan' => $_POST['nama_panggilan'] ?? '',
      'tgl_lahir' => $_POST['tgl_lahir'] ?? '',
      'tempat_lahir' => $_POST['tempat_lahir'] ?? '',
      'kota' => $_POST['kota'] ?? '',
      'tgl_pendaftaran' => $_POST['tgl_pendaftaran'] ?? '',
      'tipe_nasabah' => $_POST['tipe_nasabah'] ?? '',
      'segmen_nasabah' => $_POST['segmen_nasabah'] ?? '',
      'warga_negara' => $_POST['warga_negara'] ?? '',
      'id_cabang' => $this->session->userdata('kode_cabang'),
    ];

    // --- SET VALIDATION RULES (Conceptual Framework Syntax) ---
    // In a real framework, you would typically load the validation library first.
    $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
    $this->form_validation->set_rules('alamat', 'Alamat', 'required');
    // $this->form_validation->set_rules('alamat', 'Alamat', 'required');
    $this->form_validation->set_rules('no_ktp', 'Nomor KTP', 'required|numeric|exact_length[16]'); // Assuming 16 digits
    $this->form_validation->set_rules('no_telp', 'No. Telp', 'required|numeric|max_length[15]');
    $this->form_validation->set_rules('ahli_waris', 'Ahli Waris', 'required');
    $this->form_validation->set_rules('nama_ibu_kandung', 'Nama Ibu Kandung', 'required');
    $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahirs', 'required');
    $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required');
    // $this->form_validation->set_rules('kode_ao', 'Kode AO', 'required');
    $this->form_validation->set_rules('tgl_pendaftaran', 'Tgl Pendaftaran', 'required|valid_date');
    // $this->form_validation->set_rules('tipe_nasabah', 'Tipe Nasabah', 'required');
    // $this->form_validation->set_rules('segmen_nasabah', 'Segmen Nasabah', 'required');

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

      // Redirect back to the form view (nasabah/add_nasabah)
      redirect('nasabah/add_nasabah');
      // header('Location: ' . base_url('nasabah/add_nasabah'));
      // exit();
    } else {

      $this->cb->insert('t_nasabah', $data);
      // --- VALIDATION SUCCESS: Process the data ---

      // Example: $this->nasabah_model->save($data);
      $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Tambahkan.');

      // Redirect to a success page
      redirect('nasabah');
      exit();
    }
  }

  public function proccess_edit()
  {
    // Assume input data is captured from POST. We collect this first 
    // to flash it back (old input) if validation fails.
    $edit_data = [
      'nama' => $_POST['nama'],
      'alamat' => $_POST['alamat'],
      'no_ktp' => $_POST['no_ktp'],
      'no_telp' => $_POST['no_telp'],
      'ahli_waris' => $_POST['ahli_waris'],
      'kode_pos' => $_POST['kode_pos'],
      'nama_ibu_kandung' => $_POST['nama_ibu_kandung'],
      'pekerjaan' => $_POST['pekerjaan'],
      'kode_ao' => $_POST['kode_ao'],
      'nama_panggilan' => $_POST['nama_panggilan'],
      'tgl_lahir' => $_POST['tgl_lahir'],
      'tempat_lahir' => $_POST['tempat_lahir'],
      'kota' => $_POST['kota'],
      'tgl_pendaftaran' => $_POST['tgl_pendaftaran'],
      'tipe_nasabah' => $_POST['tipe_nasabah'],
      'segmen_nasabah' => $_POST['segmen_nasabah'],
      'warga_negara' => $_POST['warga_negara'],
    ];


    $this->cb->where('no_cib', $this->input->post('no_cib'));
    if ($this->cb->update('t_nasabah', $edit_data)) {

      $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Ubah.');
      redirect('nasabah');
    } else {

      $this->session->set_flashdata('message_error', 'Nasabah Gagal di Ubah.');
      redirect('nasabah/edit_nasabah/' . $this->input->post('no_cib'));
    }
  }

  public function delete($id)
  {
    $this->cb->where('no_cib', $id);
    if ($this->cb->delete('t_nasabah')) {

      $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Hapus.');
      redirect('nasabah');
    } else {

      $this->session->set_flashdata('message_error', 'Nasabah Gagal di Hapus.');
      redirect('nasabah');
    }

    // echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

    // redirect('perusahaan/cabang');
  }
}
