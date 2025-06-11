<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    if ($this->session->userdata('isLogin') == FALSE) {
      $this->session->set_flashdata('error', 'Your session has expired');
      redirect('auth');
    }


    $this->load->model(['M_coa']);
    $this->cb = $this->load->database('corebank', TRUE);
  }

  public function index()
  {
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

      if (!$row['coa']) {
        $tanggal_awal = date('Y-m-d', $date); // gunakan tanggal periode
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
        } elseif (preg_match('/^5/', $kode)) {
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
