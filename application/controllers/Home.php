<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
  protected $cb; // Declare the property for the 'corebank' connection

  public function __construct()
  {
    parent::__construct();

    $this->load->model(['M_coa']);
    $this->cb = $this->load->database('corebank', TRUE);

    if ($this->session->userdata('isLogin') == FALSE) {
      $this->session->set_flashdata('error', 'Your session has expired');
      redirect('auth');
    } else if (!$this->session->userdata('nama_perusahaan')) {
      redirect('auth');
    } else if (!$this->session->userdata('is_premium')) {
      $this->db->from('users');
      $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
      $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
      $total_user = $this->db->get()->num_rows(); // Get the number of rows
      if ($total_user < 5) {
        redirect('perusahaan/user');
      }
    }
  }

  public function index()
  {
    $this->db->from('users');
    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('nama_jabatan !=', 'Super Admin');
    $total_user = $this->db->get()->num_rows(); // Get the number of rows

    $max_users_for_100_percent = 4; // Define your maximum limit
    // $max_users_for_100_percent = 5; // Define your maximum limit
    if ($total_user < $max_users_for_100_percent) {
      redirect('perusahaan/user');
    }
    $nip = $this->session->userdata('nip');
    $data['title'] = 'Home';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
    $data['pages'] = 'pages/home/v_home';
    $data['menus'] = $this->M_menu->get_accessible_menus($nip);

    // Ambil data laba rugi modular dari model
    $laba_rugi = $this->getLabaRugiBulanan(5);

    $data['json_categories'] = json_encode($laba_rugi['categories']);
    $data['json_pendapatan'] = json_encode($laba_rugi['pendapatan']);
    $data['json_biaya'] = json_encode($laba_rugi['biaya']);
    $data['json_laba_rugi'] = json_encode($laba_rugi['laba_rugi']);

    $this->db->from('memo');
    $this->db->where('memo.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('MONTH(memo.created_at)', date('m'));
    $this->db->where('YEAR(memo.created_at)', date('Y'));
    $total_memo = $this->db->get()->num_rows(); // Get the number of rows


    $this->cb->from('invoice');
    $this->cb->join('t_cabang', 't_cabang.uid = invoice.id_cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->cb->where('MONTH(invoice.created_at)', date('m'));
    $this->cb->where('YEAR(invoice.created_at)', date('Y'));
    $total_invoice = $this->cb->get()->num_rows(); // Get the number of rows


    $this->cb->from('t_pengajuan');
    $this->cb->join('t_cabang', 't_cabang.uid = t_pengajuan.cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->cb->where('MONTH(t_pengajuan.created_at)', date('m'));
    $this->cb->where('YEAR(t_pengajuan.created_at)', date('Y'));
    $total_pengajuan = $this->cb->get()->num_rows(); // Get the number of rows


    $this->cb->from('t_cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $total_cabang = $this->cb->get()->num_rows(); // Get the number of rows


    $this->db->from('users');
    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->db->where('nama_jabatan !=', 'Super Admin');
    $total_user = $this->db->get()->num_rows(); // Get the number of rows

    $data['total_memo'] = $total_memo;
    $data['total_invoice'] = $total_invoice;
    $data['total_pengajuan'] = $total_pengajuan;
    $data['total_cabang'] = $total_cabang;
    $data['total_user'] = $total_user;


    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaan = $this->db->get()->row(); // Get the number of rows

    $data['perusahaan'] = $perusahaan;



    $this->load->view('index', $data);
  }

  public function getLabaRugiBulanan($bulan_ke_belakang = 5)
  {
    $categories = [];
    $pendapatan = [];
    $biaya = [];
    $laba_rugi = [];

    for ($i = $bulan_ke_belakang; $i >= 0; $i--) {
      $date = strtotime(date('Y-m') . " -$i months");
      $periode = date('Y-m', $date);
      $categories[] = format_indo($periode);

      $row = $this->M_coa->cek_saldo_awal($periode);

      if (!$row || !isset($row['coa']) || empty($row['coa'])) {

        $tanggal_awal = date('Y-m-d', $date); // gunakan tanggal periode
        if ($i == 0) {
          $tanggal_awal = date('Y-m-d'); // gunakan tanggal periode

        }
        $tanggal = new DateTime($tanggal_awal);
        $tanggal->modify('first day of previous month');
        $periode_before = $tanggal->format('Y-m');

        $cek = $this->M_coa->cek_saldo_awal($periode_before);
        $coaLastPeriod = json_decode($cek['coa'] ?? '[]', true);

        $pendapatan_transaksi = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal_awal);
        $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal_awal);

        $gabung = [];

        foreach ($coaLastPeriod as $item) {
          $kode = $item['no_sbb'];
          $gabung[$kode] = [
            'no_sbb' => $kode,
            'saldo_awal' => $item['saldo_awal'],
            'posisi' => $item['posisi'],
            'table_source' => $item['table_source'],
          ];
        }

        $combineTransaksi = array_merge($pendapatan_transaksi, $beban);

        foreach ($combineTransaksi as $transaksi) {
          $kode = $transaksi->no_sbb;
          if (isset($gabung[$kode])) {
            $gabung[$kode]['saldo_awal'] += $transaksi->saldo_awal;
          } else {
            $gabung[$kode] = [
              'no_sbb' => $kode,
              'saldo_awal' => $transaksi->saldo_awal,
              'posisi' => $transaksi->posisi,
              'table_source' => 't_coalr_sbb'
            ];
          }
        }

        $coa_json = json_encode(array_values($gabung));
      } else {
        $coa_json = $row['coa'];
      }

      $coa_array = json_decode($coa_json, true);
      $total_pendapatan = 0;
      $total_biaya = 0;

      foreach ($coa_array as $coa) {
        $kode = $coa['no_sbb'];
        $saldo = $coa['saldo_awal'];

        if (preg_match('/^4/', $kode)) {
          $total_pendapatan += $saldo;
        } elseif (preg_match('/^8/', $kode)) {
          $total_pendapatan += $saldo;
        } elseif (preg_match('/^5/', $kode)) {
          $total_biaya += $saldo;
        } elseif (preg_match('/^6/', $kode)) {
          $total_biaya += $saldo;
        } elseif (preg_match('/^7/', $kode)) {
          $total_biaya += $saldo;
        } elseif (preg_match('/^9/', $kode)) {
          $total_biaya += $saldo;
        }
      }

      $pendapatan[] = $total_pendapatan;
      $biaya[] = $total_biaya;
      $laba_rugi[] = $total_pendapatan - $total_biaya;
    }

    return [
      'categories' => $categories,
      'pendapatan' => $pendapatan,
      'biaya' => $biaya,
      'laba_rugi' => $laba_rugi
    ];
  }
}
