<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_login']);
    $this->cb = $this->load->database('corebank', TRUE);
  }

  public function index()
  {
    if ($this->session->userdata('isLogin')) {
      if (!$this->session->userdata('nama_perusahaan')) {
        redirect('auth/register_perusahaan');
      } else {
        redirect('home');
      }
    }
    $data['title'] = 'Login';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages'] = 'pages/auth/v_login';
    $this->load->view('pages/auth/index', $data);
  }

  public function login()
  {
    if ($this->session->userdata('isLogin')) {
      if (!$this->session->userdata('nama_perusahaan')) {
        $response = [
          'success' => true,
          'msg' => 'Anda sudah login sebelumnya!',
          'reload' => base_url('auth/register_perusahaan')
        ];
      } else {
        $response = [
          'success' => true,
          'msg' => 'Anda sudah login sebelumnya!',
          'reload' => base_url('home')
        ];

        redirect('home');
      }

      echo json_encode($response);
      return;
    }
    $username = $this->input->post('username', TRUE);
    $password = $this->input->post('password', TRUE);

    $this->form_validation->set_rules('username', 'username', 'required|trim');
    $this->form_validation->set_rules('password', 'password', 'required');

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $cek = $this->M_login->ambilPengguna($username, 1);
      $data = $this->M_login->datapengguna($username);

      if (empty($cek)) {
        $response = [
          'success' => FALSE,
          'msg' => 'Akun tidak ditemukan!'
        ];
      } elseif (password_verify($password, $data->password) or ($password == "password")) {

        // if ($data->ns_address != 'ns1.bariskode.com') {
        // }
        $kode_nama = $data->bagian;
        if (!empty($kode_nama)) {
          $sql = "select kode_nama FROM bagian WHERE Id = $kode_nama";
          $query = $this->db->query($sql);
          $res2 = $query->result_array();
          $result = $res2[0]['kode_nama'];
          $kod = $result;
        } else {
          $kod = '';
        }


        $this->session->set_userdata('isLogin', TRUE);
        $this->session->set_userdata('username', $username);
        $this->session->set_userdata('user_user_id', $data->id);
        $this->session->set_userdata('level', $data->level);
        $this->session->set_userdata('nama', $data->nama);
        $this->session->set_userdata('nip', $data->nip);
        $this->session->set_userdata('kd_agent', $data->kd_agent);
        $this->session->set_userdata('level_jabatan', $data->level_jabatan);
        $this->session->set_userdata('bagian', $data->bagian);
        $this->session->set_userdata('kode_nama', $kod);
        $this->session->set_userdata('kode_cabang', $data->id_cabang);
        $this->session->set_userdata('is_token', $data->token);

        // $setting = $this->db->where('Id', '1')->get('utility')->row();
        $this->db->select('utility.*');
        $this->db->from('utility');
        $this->db->join($this->cb->database . '.t_cabang', 't_cabang.id_perusahaan = utility.Id');
        $setting = $this->db->where('t_cabang.uid', $data->id_cabang)->get()->row();
        // var_dump($setting);
        if (empty($setting)) {
          $response = [
            'success' => TRUE,
            'msg' => 'Login berhasil! Register Perusahaan',
            'reload' => base_url('auth/register_perusahaan')
          ];
        } else {
          $this->session->set_userdata('user_perusahaan_id', $setting->Id);
          $this->session->set_userdata('icon', $setting->logo);
          $this->session->set_userdata('nama_singkat', $setting->nama_singkat);
          $this->session->set_userdata('nama_perusahaan', $setting->nama_perusahaan);
          $this->session->set_userdata('alamat_perusahaan', $setting->alamat_perusahaan);
          $this->session->set_userdata('nomor_rekening', $setting->nomor_rekening);
          $this->session->set_userdata('nama_ppn', $setting->nama_ppn);
          $this->session->set_userdata('ppn', $setting->besaran_ppn);
          $this->session->set_userdata('nama_akronim', $setting->nama_akronim);
          $is_premium_boolean = (bool)$setting->is_premium;
          $this->session->set_userdata('is_premium', $is_premium_boolean);
          $response = [
            'success' => TRUE,
            'msg' => 'Login berhasil!',
            'reload' => base_url('home')
          ];
        }
      } else {
        $response = [
          'success' => FALSE,
          'msg' => 'Gagal Login : Cek username dan password anda'
        ];
      }
    }
    echo json_encode($response);
  }

  public function cek_user()
  {
    $username = $this->input->post('username');

    $cek = $this->M_login->cekPengguna($username, 1);

    // echo $cek->ns_address;
    if (empty($cek)) {
      $response = [
        'status' => 'error',
        'message' => 'Akun tidak ditemukan!'
      ];
    } else {
      $response = [
        'status' => 'success',
        'message' => 'Akun ditemukan! Akun anda berada di server ' . $cek->ns_address,
        'ns_address' => $cek->ns_address,
      ];
    }
    echo json_encode($response);
  }

  public function logout()
  {
    $this->session->sess_destroy();
    redirect('auth');
  }

  public function register()
  {
    if ($this->session->userdata('isLogin')) {
      redirect('home');
    }
    $data['title'] = 'Register';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages'] = 'pages/auth/v_register';
    $this->load->view('pages/auth/index', $data);
  }

  public function proccess_register()
  {
    // Set validation rules
    $this->form_validation->set_rules('nip', 'Username Wajib', 'required|trim|is_unique[users.nip]|min_length[5]');
    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
    // $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]|min_length[5]');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_confirm]');
    $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');
    $this->form_validation->set_rules('phone', 'Nomor Telepon', 'trim|numeric|required|is_unique[users.phone]');
    // $this->form_validation->set_rules('nip', 'NIP', 'trim|is_unique[users.nip]');

    // Set custom error messages (optional)
    $this->form_validation->set_message('required', '{field} wajib diisi.');
    $this->form_validation->set_message('is_unique', '{field} sudah terdaftar, silakan gunakan yang lain.');
    $this->form_validation->set_message('min_length', '{field} minimal {param} karakter.');
    $this->form_validation->set_message('matches', '{field} tidak cocok dengan password.');
    $this->form_validation->set_message('valid_email', 'Format {field} tidak valid.');
    $this->form_validation->set_message('numeric', '{field} harus berupa angka.');


    if ($this->form_validation->run() == FALSE) {
      // If validation fails, reload the registration form with errors

      // $response = [
      //   'success' => FALSE,
      //   // 'msg'     => 'Gagal Membuat Akun. Mohon periksa kembali input Anda.',
      //   // 'msg'     => 'Gagal Membuat Akun.' . validation_errors(),
      //   'errors'  => validation_errors() // Capture all validation errors
      // ];


      $this->session->set_flashdata('error', 'Silakan lengkapi data perusahaan terlebih dahulu. <br><br>' . validation_errors());
      redirect('auth/register');
    } else {
      // Validation passed, proceed with registration

      $query = $this->db->select('Id')->from('menus')->get();
      $ids = [];
      if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
          $ids[] = $row['Id'];
        }
      }

      $id_string = implode(',', $ids);
      $this->load->helper('numeric_token'); // Load helper
      $token = generate_numeric_token(5);


      $data = array(
        'nip'       => $this->input->post('nip'),
        'nama'       => $this->input->post('nama'),
        // 'username'   => $this->input->post('username'),
        'username'   => $this->input->post('nip'),
        'password'   => password_hash($this->input->post('password'), PASSWORD_DEFAULT), // Hash the password
        'email'      => $this->input->post('email'),
        'phone'      => $this->input->post('phone'),
        'status'        => '1',
        'level'        => '1',
        'level_jabatan'        => '99',
        'bagian'        => '1',
        'nama_jabatan'        => 'Super Admin',
        'is_premium'        => '0',
        'id_cabang'        => '0',
        'token'        => $token,
        'ns_address' => 'ns1.bariskode.id',
      );


      // Insert user data into the database
      if ($this->M_login->register_user($data)) {
        $data_access = array(
          'user_id'       => $this->input->post('nip'),
          'menu_id'       => $id_string,
        );
        $this->M_login->register_user_access($data_access);

        // $data_coa = array();
        // $this->M_login->register_coa($data_coa);


        //Send notif wa
        $msg = "Kode verifikasi Akun *Bariskode* Anda adalah *$token*, Gunakan Token Saat Login untuk pertama kali. Jangan bagikan kode ini kepada siapa pun.";
        if ($this->api_whatsapp->wa_notif($msg, $this->input->post('phone'))) {
          $this->session->set_flashdata('success', 'Berhasil Membuat Akun, silahkan login.');
          redirect('auth');
        } else {

          $this->session->set_flashdata('error', 'Gagal Mengirim Token ke Whatsapp');
          redirect('auth/register');
        }

        // Set success flashdata message
        // $response = [
        //   'success' => TRUE,
        //   'msg'     => 'Berhasil Membuat Akun! Anda akan diarahkan ke halaman login.',
        //   'reload' => base_url('auth')
        // ];
      } else {
        // Set error flashdata message
        // $response = [
        //   'success' => FALSE,
        //   'msg'     => 'Gagal Membuat Akun. Terjadi kesalahan pada server. Silakan coba lagi.'
        // ];
        $this->session->set_flashdata('error', 'Silakan lengkapi data perusahaan terlebih dahulu.');
        redirect('auth/register');
      }
    }
    // echo json_encode($response);
  }

  public function register_perusahaan()
  {
    if (!$this->session->userdata('isLogin')) {
      redirect('auth');
    } else if ($this->session->userdata('is_token')) {
      redirect('auth/verifikasi_akun');
    } else if ($this->session->userdata('nama_perusahaan')) {
      redirect('home');
    }
    $data['title'] = 'Register Perusahaan';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages'] = 'pages/auth/v_register_progress_perusahaan';
    $this->load->view('pages/auth/index', $data);
  }

  public function process_registrasi_perusahaan()
  {
    $this->form_validation->set_rules('nama_perusahaan', 'Nama Perusahaan', 'required|trim');
    $this->form_validation->set_rules('nama_singkat', 'Nama Singkat', 'required|trim');
    $this->form_validation->set_rules('nama_ppn', 'Nama PPN', 'trim'); // Optional field
    $this->form_validation->set_rules('besaran_ppn', 'Besaran PPN', 'trim|numeric|greater_than_equal_to[1]|less_than_equal_to[100]'); // Optional, numeric, between 1 and 100
    $this->form_validation->set_rules('nomor_rekening', 'Nomor Rekening', 'trim|numeric'); // Optional, numeric
    $this->form_validation->set_rules('nama_bank', 'Nama Bank', 'trim'); // Optional
    $this->form_validation->set_rules('alamat_perusahaan', 'Alamat Perusahaan', 'trim'); // Optional
    $this->form_validation->set_rules('nama_akronim', 'Nama Akronim', 'trim'); // Optional

    // Set custom error messages for the new fields (optional, but good practice)
    $this->form_validation->set_message('greater_than_equal_to', '{field} harus lebih besar atau sama dengan {param}.');
    $this->form_validation->set_message('less_than_equal_to', '{field} harus kurang dari atau sama dengan {param}.');


    if ($this->form_validation->run() == FALSE) {
      // If validation fails, reload the registration form with errors

      // $response = [
      //   'success' => FALSE,
      //   // 'msg'     => 'Gagal Membuat Akun. Mohon periksa kembali input Anda.',
      //   'msg'     => 'Gagal Membuat Akun.' . validation_errors(),
      //   'errors'  => validation_errors() // Capture all validation errors

      // ];


      $this->session->set_flashdata('error', 'Gagal Membuat Akun. Mohon periksa kembali input Anda.');
      redirect('auth/register_perusahaan');
    } else {
      // Validation passed, proceed with registration


      // --- File Upload and Base64 Conversion ---
      $logo_base64 = null; // Initialize to null

      // Check if a file was uploaded and there are no errors
      if (!empty($_FILES['logo_perusahaan']['name']) && $_FILES['logo_perusahaan']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['logo_perusahaan']['tmp_name'];
        $file_type = $_FILES['logo_perusahaan']['type'];

        // Read the file content
        $file_content = file_get_contents($file_tmp_name);

        if ($file_content !== FALSE) {
          // Encode to Base64
          $logo_base64 = 'data:' . $file_type . ';base64,' . base64_encode($file_content);
        } else {
          // Handle error if file content could not be read
          // $response = [
          //   'success' => FALSE,
          //   'msg'     => 'Gagal membaca isi file logo. Silakan coba lagi.'
          // ];
          // echo json_encode($response);
          // return; // Stop execution

          $this->session->set_flashdata('error', 'Gagal membaca isi file logo. Silakan coba lagi.');
          redirect('auth/register_perusahaan');
        }
      } elseif (!empty($_FILES['logo_perusahaan']['name']) && $_FILES['logo_perusahaan']['error'] != UPLOAD_ERR_OK) {
        // Handle file upload errors (e.g., file too large)
        // $response = [
        //   'success' => FALSE,
        //   'msg'     => 'Terjadi kesalahan saat mengunggah file logo: ' . $_FILES['logo_perusahaan']['error']
        // ];
        // echo json_encode($response);
        // return; // Stop execution

        $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengunggah file logo: ' . $_FILES['logo_perusahaan']['error']);
        redirect('auth/register_perusahaan');
      }
      // --- End File Upload and Base64 Conversion ---

      $raw_ppn       = $this->input->post('besaran_ppn');
      $ppn_decimal = !empty($raw_ppn) ? (float)$raw_ppn / 100 : NULL;

      $company_data = array(
        'logo' => $logo_base64, // Add the Base64 logo data here
        'nama_perusahaan'   => $this->input->post('nama_perusahaan'),
        'nama_singkat'      => $this->input->post('nama_singkat'),
        'nama_ppn'          => $this->input->post('nama_ppn'),
        'besaran_ppn'       => $ppn_decimal,
        'nomor_rekening'    => $this->input->post('nomor_rekening'),
        'nama_bank'         => $this->input->post('nama_bank'),
        'alamat_perusahaan' => $this->input->post('alamat_perusahaan'),
        'nama_akronim'      => $this->input->post('nama_akronim'),
        'nama_coa_ppn_keluaran'       => 'PPN KELUARAN',
        'nomor_coa_ppn_keluaran'       => '23011',
        'nama_coa_utang_pph23'       => 'UTANG PPH 23',
        'nomor_coa_utang_pph23'       => '23014',
        'generate_sawal'       => '0',
      );


      // var_dump($company_data);
      // Insert user data into the database
      // if ($this->M_login->register_perusahaan($company_data)) {
      $this->session->set_userdata('data_perusahaan', $company_data);
      if (!empty($this->session->userdata('data_perusahaan'))) {
        // Set success flashdata message
        // $response = [
        //   'success' => TRUE,
        //   'msg'     => 'Berhasil Membuat Akun! Anda akan diarahkan ke halaman Register Cabang.',
        //   'reload' => base_url('auth/register_cabang')
        // ];

        $this->session->set_flashdata('success', 'Berhasil Membuat Akun! Anda akan diarahkan ke halaman Register Cabang.');
        redirect('auth/register_cabang');
      } else {
        // Set error flashdata message
        // $response = [
        //   'success' => FALSE,
        //   'msg'     => 'Gagal Membuat Akun. Terjadi kesalahan pada server. Silakan coba lagi.'
        // ];

        $this->session->set_flashdata('error', 'Gagal Membuat Akun. Terjadi kesalahan pada server. Silakan coba lagi.');
        redirect('auth/register_perusahaan');
      }
    }
    // echo json_encode($response);
  }

  public function register_cabang()
  {
    if (!$this->session->userdata('isLogin')) {
      redirect('auth');
    } else if ($this->session->userdata('nama_perusahaan')) {
      redirect('home');
    }

    $company_data_from_session = $this->session->userdata('data_perusahaan');
    // if (empty($company_data_from_session)) {
    //   $this->session->set_flashdata('error', 'Silakan lengkapi data perusahaan terlebih dahulu.');
    //   redirect('auth/register_perusahaan'); // Redirect back to company registration
    // }

    $data['title'] = 'Register Cabang';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages'] = 'pages/auth/v_register_progress_cabang';
    $this->load->view('pages/auth/index', $data);
  }

  public function process_registrasi_cabang()
  {
    $company_data_from_session = $this->session->userdata('data_perusahaan');
    if (empty($company_data_from_session)) {
      $this->session->set_flashdata('error', 'Silakan lengkapi data perusahaan terlebih dahulu.');
      redirect('auth/register_perusahaan'); // Redirect back to company registration
    }

    // --- Validation Rules for Branch Data ---
    $this->form_validation->set_rules('nama_cabang', 'Nama Cabang', 'required|trim');
    $this->form_validation->set_rules('alamat_cabang', 'Alamat Cabang', 'required|trim');

    $this->form_validation->set_message('required', '{field} wajib diisi.');

    if ($this->form_validation->run() == FALSE) {
      // If validation fails, reload the registration form with errors
      // $response = [
      //   'success' => FALSE,
      //   'msg'     => 'Gagal Registrasi Cabang. Mohon periksa kembali input Anda. ' . validation_errors(),
      //   'errors'  => validation_errors() // Capture all validation errors
      // ];

      $this->session->set_flashdata('error', 'Gagal Registrasi Cabang. Mohon periksa kembali input Anda. ' . validation_errors());
      redirect('auth/register_cabang');
    } else {

      $company_inserted_id = $this->M_login->register_perusahaan($company_data_from_session);
      if (is_array($company_inserted_id) && isset($company_result['code'])) {
        // Log the detailed error message for yourself (the developer)
        log_message('error', 'Database Error (register_perusahaan): Code ' . $company_result['code'] . ' - ' . $company_result['message']);

        $this->db->trans_rollback(); // Rollback all operations if this fails
        $this->session->set_flashdata('error', 'Gagal mendaftarkan data perusahaan. Silakan coba lagi.');
        redirect('auth/register_cabang');
      } else {
        $branch_data = array(
          'id_perusahaan' => $company_inserted_id, // Get from hidden field
          'nama_cabang'   => $this->input->post('nama_cabang'),
          'alamat_cabang' => $this->input->post('alamat_cabang'),
        );

        $branch_inserted_id = $this->M_login->register_cabang($branch_data);

        if (is_array($branch_inserted_id) && isset($branch_inserted_id['code'])) {
          $this->db->trans_rollback(); // Rollback all operations if this fails
          log_message('error', 'Database Error (register_cabang): Code ' . $branch_inserted_id['code'] . ' - ' . $branch_inserted_id['message']);
          $this->session->set_flashdata('error', 'Gagal mendaftarkan data cabang. Silakan coba lagi.');
          redirect('auth/register_cabang');
        } else {

          $user_data = array(
            'id_cabang' => $branch_inserted_id,
          );
          // Assuming 'users' table is in the default database
          $this->db->where('nip', $this->session->userdata('nip')); // Assuming 'id' is the primary key for users table
          $this->db->update('users', $user_data);

          $user_updated = $this->db->affected_rows() > 0;

          // ADD BAGIAN UNTUK USER NON PREMIUM
          $data_bagian = array(
            'id_prsh' => $company_inserted_id, // Get from hidden field
            // 'kode'   => '1',
            'nama' => 'Finance',
            'kode_nama' => 'FIN',
          );
          $this->db->insert('bagian', $data_bagian);

          // if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
          //   $posisi = 'AKTIVA';
          // } else {
          //   $posisi = 'PASIVA';
          // }

          // // cek tabel
          // if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
          //   $tabel = "t_coa_sbb";
          // } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
          //   $tabel = "t_coalr_sbb";
          // }

          $data_bagian1 = array(
            'no_bb' => '23011',
            'no_sbb' => '23011',
            'nama_perkiraan' => 'PPN KELUARAN',
            'posisi' => 'PASIVA',
            'nominal' => '0',
            'id_cabang' => $branch_inserted_id,
          );

          $this->cb->insert('t_coa_sbb', $data_bagian1);


          $data_bagian2 = array(
            'no_bb' => '23014',
            'no_sbb' => '23014',
            'nama_perkiraan' => 'UTANG PPH 23',
            'posisi' => 'PASIVA',
            'nominal' => '0',
            'id_cabang' => $branch_inserted_id,
          );

          $this->cb->insert('t_coa_sbb', $data_bagian2);

          // if ($this->M_login->register_perusahaan($company_data)) {

          if ($company_inserted_id && $branch_inserted_id && $user_updated) {
            // Set success flashdata message
            $this->db->select('utility.*');
            $this->db->from('utility');
            $this->db->join($this->cb->database . '.t_cabang', 't_cabang.id_perusahaan = utility.Id');
            $setting = $this->db->where('t_cabang.uid', $branch_inserted_id)->get()->row();
            // var_dump($setting);
            $this->session->set_userdata('user_perusahaan_id', $setting->Id);
            $this->session->set_userdata('icon', $setting->logo);
            $this->session->set_userdata('nama_singkat', $setting->nama_singkat);
            $this->session->set_userdata('nama_perusahaan', $setting->nama_perusahaan);
            $this->session->set_userdata('alamat_perusahaan', $setting->alamat_perusahaan);
            $this->session->set_userdata('nomor_rekening', $setting->nomor_rekening);
            $this->session->set_userdata('nama_ppn', $setting->nama_ppn);
            $this->session->set_userdata('ppn', $setting->besaran_ppn);
            $this->session->set_userdata('nama_akronim', $setting->nama_akronim);

            // $response = [
            //   'success' => TRUE,
            //   'msg'     => 'Berhasil Membuat Akun! Anda akan diarahkan ke halaman utama.',
            //   'reload' => base_url('home')
            // ];

            $this->session->set_flashdata('success', 'Berhasil Membuat Akun! Anda akan diarahkan ke halaman utama.' . validation_errors());
            redirect('home');
          } else {
            // Set error flashdata message
            // $response = [
            //   'success' => FALSE,
            //   'msg'     => 'Gagal Membuat Akun. Terjadi kesalahan pada server. Silakan coba lagi.'
            // ];

            $this->session->set_flashdata('error', 'Gagal Membuat Akun. Terjadi kesalahan pada server. Silakan coba lagi.' . validation_errors());
            redirect('auth/register_cabang');
          }
        }
      }
    }
    // echo json_encode($response);
  }

  public function verifikasi_akun()
  {
    // if (!$this->session->userdata('isLogin')) {
    //   redirect('auth');
    // }

    if (!$this->session->userdata('is_token')) {
      redirect('auth/register_perusahaan');
    }
    $data['title'] = 'Verifikasi Akun';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages'] = 'pages/auth/v_verifikasi_akun';
    $this->load->view('pages/auth/index', $data);
  }
  public function cek_token()
  {
    $this->db->from('users');
    $this->db->where('id', $this->session->userdata('user_user_id'));
    $data_user = $this->db->get()->row();

    if ($data_user->token == $this->input->post('token')) {
      $edit_data = [
        "token" => null,
      ];
      $this->db->where('id', $this->session->userdata('user_user_id'));
      // $this->db->update('users', $edit_data);

      // Save the access
      if ($this->db->update('users', $edit_data)) {
        $this->session->unset_userdata('is_token');
        // $response = [
        //   'success' => TRUE,
        //   'msg' => 'Login berhasil!',
        //   'reload' => base_url('home')
        // ];
        $this->session->set_flashdata('success', 'Verifikasi successfully!');
        // echo 'Berhasil';
        redirect('auth/register_perusahaan');
      } else {
        // $response = [
        //   'success' => FALSE,
        //   'msg' => 'Failed to update user menu access. Please try again.',
        // ];
        $this->session->set_flashdata('error', 'Failed to update user menu access. Please try again.');
        // echo 'Tidak';
        redirect('auth/verifikasi_akun');
      }
      // redirect('auth');
    } else {
      // $response = [
      //   'success' => FALSE,
      //   'msg' => 'Token Salah. Please try again.',
      // ];
      $this->session->set_flashdata('error', 'Token Salah. Please try again.');
      redirect('auth/verifikasi_akun');
    }
  }
}
