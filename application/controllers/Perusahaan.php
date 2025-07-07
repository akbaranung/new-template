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

    $max_users_for_100_percent = 5; // Define your maximum limit
    // Calculate the percentage
    $percentage = ($total_user / $max_users_for_100_percent) * 100;
    $nip = $this->session->userdata('nip');
    $data['total_user'] = $total_user;
    $data['percentage'] = $percentage;
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
        $row[] = '';
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
    $supervisi = $this->db->where('level_jabatan >=', 3)->get()->result();
    $data['supervisi'] = $supervisi;

    $this->cb->from('t_cabang');
    $cabang = $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'))->get()->result();
    $data['cabang'] = $cabang;


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

      $max_users_for_100_percent = 5; // Define your maximum limit

      if ($total_user < $max_users_for_100_percent) {
        $this->load->view('index', $data);
      } else {
        $this->session->set_flashdata('swal_message', [
          'icon' => 'error', // or 'success', 'warning', 'info', 'question'
          'title' => 'Access Denied!',
          'text' => 'You need a premium account to access this feature. Please upgrade your subscription.',
          'confirmButtonText' => 'Upgrade Now',
          'showCancelButton' => true,
          'cancelButtonText' => 'No Thanks',
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
    $raw_slug = $this->input->post('nama_lokasi');
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($raw_slug)));

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
      "nip" => $this->input->post('nip'),
      "level_jabatan" => $this->input->post('level_jabatan'),
      "bagian" => $this->input->post('bagian'),
      "nama_jabatan" => $this->input->post('nama_jabatan'),
      "supervisi" => $this->input->post('supervisi'),
      "tmt" => $this->input->post('tmt'),
      "cuti" => $this->input->post('cuti'),
      "id_cabang" => $this->input->post('cabang'),
    ];
    $this->db->insert('users', $add);

    $nip = $this->input->post('nip');
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

    $this->session->set_flashdata('swal_message', [
      'icon' => 'success', // or 'success', 'warning', 'info', 'question'
      'title' => 'Berhasil!',
      'text' => 'Berhasil Mengubah data!',
      'timer' => 3000, // SweetAlert2 will close after 3 seconds (3000 milliseconds)
      'timerProgressBar' => true, // Shows a progress bar for the timer
    ]);

    redirect('perusahaan/user');
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
      "nip" => $this->input->post('nip'),
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
    $nip = $this->input->post('nip');
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
          'icon' => 'error', // or 'success', 'warning', 'info', 'question'
          'title' => 'Access Denied!',
          'text' => 'You need a premium account to access this feature. Please upgrade your subscription.',
          'confirmButtonText' => 'Upgrade Now',
          'showCancelButton' => true,
          'cancelButtonText' => 'No Thanks',
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
    ];
    $this->cb->insert('t_cabang', $add);
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
    $data['title'] = 'Add Lokasi Presensi';
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
}
