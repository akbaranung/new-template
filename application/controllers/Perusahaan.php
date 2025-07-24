<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perusahaan extends CI_Controller
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

    $this->load->model(['M_user_access', 'M_perusahaans']);

    $this->cb = $this->load->database('corebank', TRUE);
  }

  public function perusahaan()
  {
    $nip = $this->session->userdata('nip');
    $data['title'] = 'Perusahaan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/perusahaan/v_perusahaan';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function cabang()
  {

    $this->cb->from('t_cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $total_cabang = $this->cb->get()->num_rows(); // Get the number of rows

    $nip = $this->session->userdata('nip');

    $data['total_cabang'] = $total_cabang;
    $data['title'] = 'Cabang';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_cabang';
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/perusahaan/v_cabang';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function user()
  {
    $this->db->from('users');
    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('nama_jabatan !=', 'Super Admin');
    $total_user = $this->db->get()->num_rows(); // Get the number of rows

    $max_users_for_100_percent = 4; // Define your maximum limit


    // $max_users_for_100_percent = 5; // Define your maximum limit

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

    $percentage = ($total_user / $max_users_for_100_percent) * 100;
    $nip = $this->session->userdata('nip');
    $data['user_counts'] = $this->M_perusahaans->get_user_counts_by_role();
    $data['total_user'] = $total_user;
    $data['percentage'] = $percentage;
    $data['cek_coa_cabang'] = $cek_coa_cabang;
    $data['max_users_for_100_percent'] = $max_users_for_100_percent;
    $data['title'] = 'User';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_user';
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/perusahaan/v_user';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    $this->load->view('index', $data);
  }

  public function ajax_user_list()
  {
    $list = $this->M_perusahaans->get_datatables1();
    $data = array();
    $crs = "";
    $no = $_POST['start'];

    foreach ($list as $cat) {

      $no++;
      $row = array();
      $row[] = $no;
      $row[] = $cat->nama;
      $row[] = $cat->username;
      $row[] = $cat->nip;
      $row[] = $cat->nama_jabatan;
      if ($cat->status == '1') {
        $row[] = "Aktif";
      } else {
        $row[] = "Tidak Aktif";
      }

      if ($cat->nama_jabatan == "Super Admin") {
        $row[] = '<a href="' . base_url('perusahaan/edit_user/' . $cat->id) . '" class="btn btn-warning">
        Update
      </a>';
      } else {
        $row[] = '<a href="' . base_url('perusahaan/edit_user/' . $cat->id) . '" class="btn btn-warning">
        Update
      </a> <button onclick="onDelete(' . $cat->id . ')" class="btn btn-danger">
        Delete
      </button>';
      }
      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_perusahaans->count_all1(),
      "recordsFiltered" => $this->M_perusahaans->count_filtered1(),
      "data" => $data,
    );
    echo json_encode($output);
  }

  public function add_user()
  {

    $this->cb = $this->load->database('corebank', TRUE);
    $this->db->from('users')->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('nama_jabatan !=', 'Super Admin');
    $supervisi = $this->db->where('level_jabatan >=', 2)->get()->result();
    $data['supervisi'] = $supervisi;

    $this->cb->from('t_cabang');
    $cabang = $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'))->get()->result();
    $data['cabang'] = $cabang;

    $data['user_counts'] = $this->M_perusahaans->get_user_counts_by_role();


    // --- NEW: Fetch menu access data ---
    // $data['all_menus'] = $this->M_user_access->get_all_menus(); // Get all available menus
    $data['all_menus_hierarchical'] = $this->M_user_access->get_all_menus_hierarchical(); // Get all menus in hierarchy
    // $current_access = $this->M_user_access->get_user_access($user_id); // Get user's current access record

    // $data['user_menu_ids'] = [];
    // if (!empty($current_access) && !empty($current_access->menu_id)) {
    //   // Convert comma-separated string to an array of integers
    //   $data['user_menu_ids'] = array_map('intval', explode(',', $current_access->menu_id));
    // }
    // --- END NEW ---


    $data['title'] = 'Add User';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_user';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/perusahaan/v_user_add';

    // CEK PREMIUM
    if ($this->session->userdata('is_premium')) {
      $this->load->view('index', $data);
    } else {
      $this->db->from('users');
      $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
      $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
      $this->db->where('nama_jabatan !=', 'Super Admin');
      $total_user = $this->db->get()->num_rows(); // Get the number of rows

      $max_users_for_100_percent = 4; // Define your maximum limit
      // $max_users_for_100_percent = 5; // Define your maximum limit

      if ($total_user < $max_users_for_100_percent) {
        $this->load->view('index', $data);
      } else {
        $this->session->set_flashdata('swal_message', [
          'icon' => 'question', // or 'success', 'warning', 'info', 'question'
          'title' => 'Siap Menjadi Raja <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>',
          'text' => 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..',
          'confirmButtonText' => 'Ambil Mahkota Sekarang!',
          'showCancelButton' => true,
          'cancelButtonText' => 'Nanti Saja, Belum Siap Jadi Raja',
          'redirectUrl' => base_url('subscription/upgrade') // URL to redirect if confirmed
        ]);

        redirect('perusahaan/user');
      }
    }
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }
  public function edit_user($id)
  {


    $this->cb = $this->load->database('corebank', TRUE);
    $this->db->from('users')->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('nama_jabatan !=', 'Super Admin');
    $supervisi = $this->db->where('level_jabatan >=', 3)->get()->result();
    $data['supervisi'] = $supervisi;

    $this->cb->from('t_cabang');
    $cabang = $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'))->get()->result();
    $data['cabang'] = $cabang;

    $data['user_counts'] = $this->M_perusahaans->get_user_counts_by_role();
    // --- NEW: Fetch menu access data ---
    // $data['all_menus'] = $this->M_user_access->get_all_menus(); // Get all available menus
    $data['all_menus_hierarchical'] = $this->M_user_access->get_all_menus_hierarchical(); // Get all menus in hierarchy
    $current_access = $this->M_user_access->get_user_access($id); // Get user's current access record

    $data['user_menu_ids'] = [];
    if (!empty($current_access) && !empty($current_access->menu_id)) {
      // Convert comma-separated string to an array of integers
      $data['user_menu_ids'] = array_map('intval', explode(',', $current_access->menu_id));
    }
    // --- END NEW ---

    $data['user'] = $this->M_perusahaans->get_detail_id_user($id);
    $data['title'] = 'Add Lokasi Presensi';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_user';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/perusahaan/v_user_add';

    $this->load->view('index', $data);
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function proccess_add_user()
  {
    // Set validation rules
    $this->form_validation->set_rules('username', 'Username Wajib', 'required|trim|is_unique[users.nip]|min_length[5]');
    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
    // $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]|min_length[5]');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_confirmation]');
    $this->form_validation->set_rules('password_confirmation', 'Konfirmasi Password', 'required|matches[password]');
    $this->form_validation->set_rules('phone', 'Nomor Telepon', 'trim|numeric|required');
    // $this->form_validation->set_rules('nip', 'NIP', 'trim|is_unique[users.nip]');

    // Set custom error messages (optional)
    $this->form_validation->set_message('required', '{field} wajib diisi.');
    $this->form_validation->set_message('is_unique', '{field} sudah terdaftar, silakan gunakan yang lain.');
    $this->form_validation->set_message('min_length', '{field} minimal {param} karakter.');
    $this->form_validation->set_message('matches', '{field} tidak cocok dengan password.');
    $this->form_validation->set_message('valid_email', 'Format {field} tidak valid.');
    $this->form_validation->set_message('numeric', '{field} harus berupa angka.');

    $uri1 = $this->input->post('uri1');
    $uri2 = $this->input->post('uri2');

    if ($this->form_validation->run() == FALSE) {
      // If validation fails, reload the registration form with errors

      // $response = [
      //   'success' => FALSE,
      //   // 'msg'     => 'Gagal Membuat Akun. Mohon periksa kembali input Anda.',
      //   // 'msg'     => 'Gagal Membuat Akun.' . validation_errors(),
      //   'errors'  => validation_errors() // Capture all validation errors
      // ];


      $this->session->set_flashdata('error', 'Silakan lengkapi data perusahaan terlebih dahulu. <br><br>' . validation_errors());
      redirect('perusahaan/add_user/' . $uri1 . '/' . $uri2);
    }

    // $raw_slug = $this->input->post('nama_lokasi');
    // $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($raw_slug)));

    $radius = $this->input->post('radius_lokasi') / 1000;
    $add = [
      "nama" => $this->input->post('nama'),
      "username" => $this->input->post('username'),
      "password" => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      // "level" => $ex_level,
      "status" => $this->input->post('status'),
      "email" => $this->input->post('email'),
      "phone" => $this->input->post('phone'),
      "kd_agent" => $this->input->post('kd_agent'),
      // "nip" => $this->input->post('nip'),
      "nip" => $this->input->post('username'),
      "level_jabatan" => $this->input->post('level_jabatan'),
      "bagian" => $this->input->post('bagian'),
      "nama_jabatan" => $this->input->post('nama_jabatan'),
      "supervisi" => $this->input->post('supervisi'),
      "tmt" => $this->input->post('tmt'),
      "cuti" => $this->input->post('cuti'),
      "id_cabang" => $this->input->post('cabang'),
    ];
    $this->db->insert('users', $add);

    $nip = $this->input->post('username');
    $selected_menu_ids = $this->input->post('menu_ids'); // This will be an array of selected menu IDs

    // var_dump($selected_menu_ids);

    if (empty($selected_menu_ids)) {
      $menu_id_string = ''; // No access
    } else {
      // Ensure unique IDs and convert to comma-separated string
      $menu_id_string = implode(',', array_unique($selected_menu_ids));
    }

    // var_dump($menu_id_string);
    // $query = $this->db->select('Id')->from('menus')->get();
    // $ids = [];
    // if ($query->num_rows() > 0) {
    //   foreach ($query->result_array() as $row) {
    //     $ids[] = $row['Id'];
    //   }
    // }

    // $menu_id_string = implode(',', $ids);

    // Save the access
    if ($this->M_user_access->save_user_access($nip, $menu_id_string)) {
      echo $nip;
      $this->session->set_flashdata('success', 'User menu access updated successfully!');
      echo 'Berhasil';
    } else {
      $this->session->set_flashdata('error', 'Failed to update user menu access. Please try again.');
      echo 'Tidak';
    }

    $this->session->set_flashdata('swal_message', [
      'icon' => 'success', // or 'success', 'warning', 'info', 'question'
      'title' => 'Berhasil!',
      'text' => 'Berhasil Menambah data!',
      'timer' => 3000, // SweetAlert2 will close after 3 seconds (3000 milliseconds)
      'timerProgressBar' => true, // Shows a progress bar for the timer
    ]);

    if ($this->session->userdata('is_premium')) {
      redirect('perusahaan/user');
    } else {
      $this->db->from('users');
      $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
      $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
      $this->db->where('nama_jabatan !=', 'Super Admin');
      $total_user = $this->db->get()->num_rows(); // Get the number of rows

      $max_users_for_100_percent = 4; // Define your maximum limit
      // $max_users_for_100_percent = 5; // Define your maximum limit

      if ($total_user == $max_users_for_100_percent) {
        redirect('financial/list_coa');
      } else {
        redirect('perusahaan/user');
      }
    }
  }

  public function proccess_edit_user($id)
  {
    $edit_data = [
      "nama" => $this->input->post('nama'),
      "username" => $this->input->post('username'),
      "status" => $this->input->post('status'),
      "email" => $this->input->post('email'),
      "phone" => $this->input->post('phone'),
      "kd_agent" => $this->input->post('kd_agent'),
      // "nip" => $this->input->post('nip'),
      "nip" => $this->input->post('username'),
      "level_jabatan" => $this->input->post('level_jabatan'),
      "tmt" => $this->input->post('tmt'),
      "bagian" => $this->input->post('bagian'),
      "nama_jabatan" => $this->input->post('nama_jabatan'),
      "supervisi" => $this->input->post('supervisi'),
      "cuti" => $this->input->post('cuti'),
      "id_lokasi_presensi" => $this->input->post('lokasi_presensi'),
      "jam_masuk" => $this->input->post('jam_masuk'),
      "jam_keluar" => $this->input->post('jam_keluar')
    ];
    $this->db->where('id', $id);
    $this->db->update('users', $edit_data);

    $user_id = $this->input->post('user_id');
    $nip = $this->input->post('username');
    $selected_menu_ids = $this->input->post('menu_ids'); // This will be an array of selected menu IDs

    if (empty($selected_menu_ids)) {
      $menu_id_string = ''; // No access
    } else {
      // Ensure unique IDs and convert to comma-separated string
      $menu_id_string = implode(',', array_unique($selected_menu_ids));
    }

    // Save the access
    if ($this->M_user_access->save_user_access($nip, $menu_id_string)) {
      $this->session->set_flashdata('success', 'User menu access updated successfully!');
      echo 'Berhasil';
    } else {
      $this->session->set_flashdata('error', 'Failed to update user menu access. Please try again.');
      echo 'Tidak';
    }

    redirect('perusahaan/user');
  }

  public function hapus_user()
  {
    $id = $this->input->post('id');
    $this->db->where('id', $id);
    $this->db->delete('users');

    echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

    // redirect('perusahaan/cabang');
  }

  public function ajax_cabang_list()
  {
    $list = $this->M_perusahaans->get_datatables();
    $data = array();
    $crs = "";
    $no = $_POST['start'];

    foreach ($list as $cat) {

      $no++;
      $row = array();
      $row[] = $no;
      $row[] = $cat->nama_cabang;
      $row[] = $cat->alamat_cabang;

      $this->db->from('users');
      $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
      $this->db->where('t_cabang.uid', $cat->uid);
      $total_user = $this->db->get()->num_rows(); // Get the number of rows

      if (!$total_user) {
        $button_delete = '<button onclick="onDelete(' . $cat->uid . ')" class="btn btn-danger">
        Delete
      </button>';
      } else {
        $button_delete = '';
      }
      $row[] = '<a href="' . base_url('perusahaan/edit_cabang/' . $cat->uid) . '" class="btn btn-warning">
        Update
      </a> ' . $button_delete;

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_perusahaans->count_all(),
      "recordsFiltered" => $this->M_perusahaans->count_filtered(),
      "data" => $data,
    );
    echo json_encode($output);
  }

  public function add_cabang()
  {
    $data['title'] = 'Add Cabang';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_cabang';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/perusahaan/v_cabang_add';


    // CEK PREMIUM
    if ($this->session->userdata('is_premium')) {

      $this->load->view('index', $data);
    } else {

      $this->cb->from('t_cabang');
      $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
      $total_cabang = $this->cb->get()->num_rows(); // Get the number of rows


      if ($total_cabang < 1) {
        $this->load->view('index', $data);
      } else {

        $this->session->set_flashdata('swal_message', [
          'icon' => 'question', // or 'success', 'warning', 'info', 'question'
          'title' => 'Siap Menjadi Raja <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>',
          'text' => 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..',
          'confirmButtonText' => 'Ambil Mahkota Sekarang!',
          'showCancelButton' => true,
          'cancelButtonText' => 'Nanti Saja, Belum Siap Jadi Raja',
          'redirectUrl' => base_url('subscription/upgrade') // URL to redirect if confirmed
        ]);

        redirect('perusahaan/cabang');
      }
    }
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function proccess_add_cabang()
  {
    // $raw_slug = $this->input->post('nama_lokasi');
    // $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($raw_slug)));

    // $radius = $this->input->post('radius_lokasi') / 1000;
    $add = [
      "id_perusahaan" => $this->session->userdata('user_perusahaan_id'),
      "nama_cabang" => $this->input->post('nama_cabang'),
      "alamat_cabang" => $this->input->post('alamat_cabang'),
      "nomor_rekening" => $this->input->post('nomor_rekening'),
      "nama_bank" => $this->input->post('nama_bank'),
    ];
    $this->cb->insert('t_cabang', $add);
    $inserted_id = $this->cb->insert_id();

    $data_bagian1 = array(
      'no_bb' => '23011',
      'no_sbb' => '23011',
      'nama_perkiraan' => 'PPN KELUARAN',
      'posisi' => 'PASIVA',
      'nominal' => '0',
      'id_cabang' => $inserted_id,
    );

    $this->cb->insert('t_coa_sbb', $data_bagian1);


    $data_bagian2 = array(
      'no_bb' => '23014',
      'no_sbb' => '23014',
      'nama_perkiraan' => 'UTANG PPH 23',
      'posisi' => 'PASIVA',
      'nominal' => '0',
      'id_cabang' => $inserted_id,
    );

    $this->cb->insert('t_coa_sbb', $data_bagian2);

    $this->session->set_flashdata('swal_message', [
      'icon' => 'success', // or 'success', 'warning', 'info', 'question'
      'title' => 'Berhasil!',
      'text' => 'Berhasil Menambahkan data!',
      'timer' => 3000, // SweetAlert2 will close after 3 seconds (3000 milliseconds)
      'timerProgressBar' => true, // Shows a progress bar for the timer
    ]);
    redirect('perusahaan/cabang');
  }

  public function edit_cabang($id)
  {

    $data['cabang'] = $this->M_perusahaans->get_detail_id_cabang($id);
    $data['title'] = 'Edit Cabang';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/perusahaan/s_cabang';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/perusahaan/v_cabang_add';

    $this->load->view('index', $data);
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function prosses_edit_cabang($id)
  {
    $edit_data = [
      "nama_cabang" => $this->input->post('nama_cabang'),
      "alamat_cabang" => $this->input->post('alamat_cabang'),
      "nomor_rekening" => $this->input->post('nomor_rekening'),
      "nama_bank" => $this->input->post('nama_bank'),
    ];
    $this->cb->where('uid', $id);
    $this->cb->update('t_cabang', $edit_data);

    $this->session->set_flashdata('swal_message', [
      'icon' => 'success', // or 'success', 'warning', 'info', 'question'
      'title' => 'Berhasil!',
      'text' => 'Berhasil Mengubah data!',
      'timer' => 3000, // SweetAlert2 will close after 3 seconds (3000 milliseconds)
      'timerProgressBar' => true, // Shows a progress bar for the timer
    ]);

    redirect('perusahaan/cabang');
  }

  public function hapus_cabang()
  {
    $id = $this->input->post('id');
    $this->cb->where('uid', $id);
    $this->cb->delete('t_cabang');

    echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

    // redirect('perusahaan/cabang');
  }

  public function detail()
  {

    $data['perusahaan'] = $this->M_perusahaans->get_detail_id_perusahaan($this->session->userdata('user_perusahaan_id'));
    $data['title'] = 'Add Lokasi Presensi';
    $data['utility'] = $this->db->get('utility')->row_array();
    // $data['pages_script'] = 'script/perusahaan/s_perusahaan';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/perusahaan/v_perusahaan_detail';

    $this->load->view('index', $data);
    // $this->load->view('pages/absensi/lokasi_presensi_form', $data);
  }

  public function save_new_bagian()
  {

    $this->output->set_content_type('application/json');

    // $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
    $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
    $this->form_validation->set_rules('kode_nama', 'Kode Nama', 'required|trim|is_unique[bagian.kode_nama]');
    $this->form_validation->set_rules('id_prsh', 'ID Perusahaan', 'required|integer');

    if ($this->form_validation->run() == FALSE) {
      echo json_encode([
        'status' => 'error',
        'message' => strip_tags(validation_errors())
      ]);
      return;
    }

    $data = array(
      // 'kode'      => $this->input->post('kode'),
      'nama'      => $this->input->post('nama'),
      'kode_nama' => $this->input->post('kode_nama'),
      'id_prsh'   => $this->input->post('id_prsh'),
    );

    $insert_id = $this->M_perusahaans->insert_bagian($data);

    if ($insert_id) {
      echo json_encode([
        'status'       => 'success',
        'new_id'       => $insert_id,
        'display_text' => $data['kode'] . ' - ' . $data['nama']
      ]);
    } else {
      $db_error = $this->db->error();
      log_message('error', 'Failed to insert new bagian: ' . ($db_error['message'] ?? 'Unknown DB error'));

      echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to save new bagian to database.'
      ]);
    }
  }
}
