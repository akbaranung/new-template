<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Financial extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_coa', 'M_customer', 'M_invoice', 'M_login']);
    $this->load->helper(['number']);
    $this->load->library(['pdfgenerator']);

    $this->cb = $this->load->database('corebank', TRUE);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

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

    if ($cek_coa_cabang == 0) {
      redirect('financial_first/force_make_coa_sbb');
    }

    date_default_timezone_set('Asia/Jakarta');
  }

  public function reportByDate()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $button_sbm = $this->input->post('button_sbm');
    $nip = $this->session->userdata('nip');

    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};

    $per_tanggal = ($this->input->post('per_tanggal') ? $this->input->post('per_tanggal') : date('Y-m-d'));

    $data = [
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'per_tanggal' => $per_tanggal
    ];

    $jenis_laporan = $this->input->post('jenis_laporan');

    if ($jenis_laporan) {
      if ($jenis_laporan == "neraca") {
        $this->prepareNeracaReportByDate($data, $per_tanggal, $button_sbm);
      } else if ($jenis_laporan == "laba_rugi") {
        $this->prepareLabaRugiReportByDate($data, $per_tanggal, $button_sbm);
      } else if ($jenis_laporan == "laba_rugi_tanpa_sawal") {
        $this->prepareLabaRugiReportByDateNoSawal($data, $per_tanggal, $button_sbm);
      } else if ($jenis_laporan == "neraca_bb") {
        $this->prepareNeracaBbReportByDate($data, $per_tanggal, $button_sbm);
      } else if ($jenis_laporan == "lr_bb") {
        $this->prepareLrBbReportByDate($data, $per_tanggal, $button_sbm);
      }
    } else {
      $this->prepareNeracaReportByDate($data, $per_tanggal);
    }
  }

  private function prepareNeracaReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);

    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    $cek = $this->M_coa->cek_saldo_awal($periode);

    if ($cek) {
      $coaLastPeriod = json_decode($cek['coa']);
      $filteredCoaAktiva = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coa_sbb';
      });

      $activa = $this->M_coa->getNeracaByDate('t_coa_sbb', 'AKTIVA', $tanggal, $periode);
      $pasiva = $this->M_coa->getNeracaByDate('t_coa_sbb', 'PASIVA', $tanggal, $periode);
      $pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
      $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

      // Part Aktiva
      $combinedActiva = [];

      foreach ($activa as $item) {
        if (!isset($combinedActiva[$item->no_sbb])) {
          $combinedActiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      foreach ($filteredCoaAktiva as $item) {
        if (!isset($combinedActiva[$item->no_sbb])) {
          $combinedActiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      usort($combinedActiva, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_activa = array_sum(array_column($combinedActiva, 'saldo_awal'));

      // Part Pasiva
      $filteredCoaPasiva = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coa_sbb';
      });

      $combinedPasiva = [];

      foreach ($pasiva as $item) {
        if (!isset($combinedPasiva[$item->no_sbb])) {
          $combinedPasiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPasiva as $item) {
        if (!isset($combinedPasiva[$item->no_sbb])) {
          $combinedPasiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      usort($combinedPasiva, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_pasiva = array_sum(array_column($combinedPasiva, 'saldo_awal'));

      // Part Pendapatan
      $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
      });
      $combinedPendapatan = [];

      foreach ($pendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

      // Part Beban
      $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
      });

      $combinedBeban = [];

      foreach ($beban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaBeban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

      $laba = $total_pendapatan - $total_beban;
      $sum_pasiva = $total_pasiva + $laba;

      $data['activa'] = $combinedActiva;
      $data['sum_activa'] = $total_activa;
      $data['pasiva'] = $combinedPasiva;
      $data['laba'] = $laba;
      $data['sum_pasiva'] = $sum_pasiva;
      $data['neraca'] = $sum_pasiva - $total_activa;
    } else {
      $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
    }

    $data['title'] = 'Neraca per tanggal ' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['pages'] = 'pages/financial/v_neraca_by_date';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Neraca SBB")
        ->setSubject("Neraca SBB")
        ->setDescription("Neraca SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Neraca SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Neraca SBB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'AKTIVA');
      $sheet->setCellValue('E2', 'PASIVA');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_activa ?? 0);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $sum_pasiva ?? 0);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;

      foreach ($combinedActiva as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
          $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
          $numrowActiva++;
        endif;
      }


      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($combinedPasiva as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
          $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
          $numrowPasiva++;
        endif;
      }

      $sheet->setCellValue('E' . $numrowPasiva, '3103001');
      $sheet->setCellValue('F' . $numrowPasiva, 'LABA TAHUN BERJALAN');
      $sheet->setCellValue('G' . $numrowPasiva, $laba);

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Neraca per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  private function prepareLabaRugiReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);

    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    $cek = $this->M_coa->cek_saldo_awal($periode);

    $data['total_pendapatan'] = 0;
    $data['sum_biaya'] = 0;
    $data['sum_pendapatan'] = 0;
    $data['biaya'] = [];
    $data['pendapatan'] = [];
    if ($cek) {
      $coaLastPeriod = json_decode($cek['coa']);

      $pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
      $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

      // Part Pendapatan
      $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
      });
      $combinedPendapatan = [];

      foreach ($pendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

      // Part Beban
      $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
      });

      $combinedBeban = [];

      foreach ($beban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaBeban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

      $data['biaya'] = $combinedBeban;
      $data['pendapatan'] = $combinedPendapatan;
      $data['sum_biaya'] = $total_beban;
      $data['sum_pendapatan'] = $total_pendapatan;
      $data['total_pendapatan'] = $total_pendapatan - $total_beban;
    } else {
      $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
    }

    // print_r($data['total_pendapatan']);
    // exit;
    $data['title'] = 'Laba rugi per tanggal ' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['pages'] = 'pages/financial/v_laba_rugi_by_date';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Laba rugi SBB")
        ->setSubject("Laba rugi SBB")
        ->setDescription("Laba rugi SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Laba rugi SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Laba rugi SBB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'BEBAN');
      $sheet->setCellValue('E2', 'PENDAPATAN');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_beban);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $total_pendapatan);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;
      foreach ($combinedBeban as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
          $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
          $numrowActiva++;
        endif;
      }

      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($combinedPendapatan as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
          $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
          $numrowPasiva++;
        endif;
      }

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Laba rugi per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  private function prepareLabaRugiReportByDateNoSawal($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);

    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    // $cek = $this->M_coa->cek_saldo_awal($periode);

    $data['total_pendapatan'] = 0;
    $data['sum_biaya'] = 0;
    $data['sum_pendapatan'] = 0;
    $data['biaya'] = [];
    $data['pendapatan'] = [];
    // if ($cek) {
    // $coaLastPeriod = json_decode($cek['coa']);

    $pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
    $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

    // Part Pendapatan
    // $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
    //   return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
    // });
    $combinedPendapatan = [];

    foreach ($pendapatan as $item) {
      if (!isset($combinedPendapatan[$item->no_sbb])) {
        $combinedPendapatan[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
      }
    }
    // foreach ($filteredCoaPendapatan as $item) {
    //   if (!isset($combinedPendapatan[$item->no_sbb])) {
    //     $combinedPendapatan[$item->no_sbb] = (object) [
    //       'no_sbb' => $item->no_sbb,
    //       'saldo_awal' => $item->saldo_awal,
    //     ];
    //   } else {
    //     $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
    //   }
    // }
    $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

    // Part Beban
    // $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
    //   return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
    // });

    $combinedBeban = [];

    foreach ($beban as $item) {
      if (!isset($combinedBeban[$item->no_sbb])) {
        $combinedBeban[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
      }
    }
    // foreach ($filteredCoaBeban as $item) {
    //   if (!isset($combinedBeban[$item->no_sbb])) {
    //     $combinedBeban[$item->no_sbb] = (object) [
    //       'no_sbb' => $item->no_sbb,
    //       'saldo_awal' => $item->saldo_awal,
    //     ];
    //   } else {
    //     $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
    //   }
    // }
    $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

    $data['biaya'] = $combinedBeban;
    $data['pendapatan'] = $combinedPendapatan;
    $data['sum_biaya'] = $total_beban;
    $data['sum_pendapatan'] = $total_pendapatan;
    $data['total_pendapatan'] = $total_pendapatan - $total_beban;
    // } else {
    //   $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
    // }

    // print_r($data['total_pendapatan']);
    // exit;
    $data['title'] = 'Laba rugi per tanggal ' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $data['pages'] = 'pages/financial/v_laba_rugi_by_date';

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Laba rugi SBB")
        ->setSubject("Laba rugi SBB")
        ->setDescription("Laba rugi SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Laba rugi SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Laba rugi SBB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'BEBAN');
      $sheet->setCellValue('E2', 'PENDAPATAN');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_beban);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $total_pendapatan);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;
      foreach ($combinedBeban as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
          $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
          $numrowActiva++;
        endif;
      }

      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($combinedPendapatan as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
          $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
          $numrowPasiva++;
        endif;
      }

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Laba rugi per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  private function prepareNeracaBbReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);

    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    $cek = $this->M_coa->cek_saldo_awal($periode);

    if ($cek) {
      $coaLastPeriod = json_decode($cek['coa']);
      $filteredCoaAktiva = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coa_sbb';
      });

      $activa = $this->M_coa->getNeracaByDate('t_coa_sbb', 'AKTIVA', $tanggal, $periode);
      $pasiva = $this->M_coa->getNeracaByDate('t_coa_sbb', 'PASIVA', $tanggal, $periode);
      $pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
      $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

      // Part Aktiva
      $combinedActiva = [];

      foreach ($activa as $item) {
        if (!isset($combinedActiva[$item->no_sbb])) {
          $combinedActiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      foreach ($filteredCoaAktiva as $item) {
        if (!isset($combinedActiva[$item->no_sbb])) {
          $combinedActiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      usort($combinedActiva, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_activa = array_sum(array_column($combinedActiva, 'saldo_awal'));

      // Part Pasiva
      $filteredCoaPasiva = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coa_sbb';
      });

      $combinedPasiva = [];

      foreach ($pasiva as $item) {
        if (!isset($combinedPasiva[$item->no_sbb])) {
          $combinedPasiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPasiva as $item) {
        if (!isset($combinedPasiva[$item->no_sbb])) {
          $combinedPasiva[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      usort($combinedPasiva, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_pasiva = array_sum(array_column($combinedPasiva, 'saldo_awal'));

      // Part Pendapatan
      $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
      });
      $combinedPendapatan = [];

      foreach ($pendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

      // Part Beban
      $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
      });

      $combinedBeban = [];

      foreach ($beban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaBeban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));


      // Proses pengelompokan, penjumlahan, dan group-ing no_bb Aktiva
      $bbActiva = [];
      foreach ($combinedActiva as $item) {
        $key = substr($item->no_sbb, 0, 3);
        $bbActiva[$key] = ($bbActiva[$key] ?? 0) + $item->saldo_awal;
      }

      // Membentuk groupedActiva dan menghitung total saldo aktiva
      $groupedActiva = [];

      foreach ($bbActiva as $key => $saldo) {
        $groupedActiva[] = (object) ['no_bb' => $key, 'saldo_aktiva' => $saldo];
      }

      // Proses pengelompokan, penjumlahan, dan group-ing no_bb pasiva
      $bbPasiva = [];
      foreach ($combinedPasiva as $item) {
        $key = substr($item->no_sbb, 0, 3);
        $bbPasiva[$key] = ($bbPasiva[$key] ?? 0) + $item->saldo_awal;
      }

      // Membentuk groupedPasiva dan menghitung total saldo pasiva
      $groupedPasiva = [];

      foreach ($bbPasiva as $key => $saldo) {
        $groupedPasiva[] = (object) ['no_bb' => $key, 'saldo_pasiva' => $saldo];
      }



      $laba = $total_pendapatan - $total_beban;
      $sum_pasiva = $total_pasiva + $laba;
      $data['activa'] = $groupedActiva;
      $data['sum_activa'] = $total_activa;
      $data['pasiva'] = $groupedPasiva;
      $data['laba'] = $laba;
      $data['sum_pasiva'] = $sum_pasiva;
      $data['neraca'] = $sum_pasiva - $total_activa;
    } else {
      // $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
      $this->session->set_flashdata('message_error', 'Saldo Awal bulan ' . format_indo($periode) . ' belum terbentuk');
    }
    $data['pages'] = 'pages/financial/v_neraca_bb_by_date';
    $data['title'] = 'Neraca Buku Besar' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Neraca BB")
        ->setSubject("Neraca BB")
        ->setDescription("Neraca BB per tanggal " . format_indo($tanggal))
        ->setKeywords("Neraca BB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Neraca BB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'AKTIVA');
      $sheet->setCellValue('E2', 'PASIVA');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_activa);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $sum_pasiva);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;
      foreach ($groupedActiva as $t) {
        $coa = $this->M_coa->getCoaBB($t->no_bb);

        $sheet->setCellValue('A' . $numrowActiva, $t->no_bb);
        $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
        $sheet->setCellValue('C' . $numrowActiva, $t->saldo_aktiva);

        $numrowActiva++;
      }

      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($groupedPasiva as $t) {
        $coa = $this->M_coa->getCoaBB($t->no_bb);

        $sheet->setCellValue('E' . $numrowPasiva, $t->no_bb);
        $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
        $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_pasiva);

        $numrowPasiva++;
      }
      $sheet->setCellValue('E' . $numrowPasiva, '3103');
      $sheet->setCellValue('F' . $numrowPasiva, 'LABA TAHUN BERJALAN');
      $sheet->setCellValue('G' . $numrowPasiva, $laba);

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Neraca BB per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  private function prepareLrBbReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);

    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    $cek = $this->M_coa->cek_saldo_awal($periode);

    if ($cek) {
      $coaLastPeriod = json_decode($cek['coa']);

      $pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
      $beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

      // Part Pendapatan
      $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
      });
      $combinedPendapatan = [];

      foreach ($pendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaPendapatan as $item) {
        if (!isset($combinedPendapatan[$item->no_sbb])) {
          $combinedPendapatan[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }

      usort($combinedPendapatan, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

      // Part Beban
      $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
        return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
      });

      $combinedBeban = [];

      foreach ($beban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      foreach ($filteredCoaBeban as $item) {
        if (!isset($combinedBeban[$item->no_sbb])) {
          $combinedBeban[$item->no_sbb] = (object) [
            'no_sbb' => $item->no_sbb,
            'saldo_awal' => $item->saldo_awal,
          ];
        } else {
          $combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
        }
      }
      usort($combinedBeban, function ($a, $b) {
        return strcmp($a->no_sbb, $b->no_sbb);
      });
      $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

      // Proses pengelompokan, penjumlahan, dan group-ing no_bb Aktiva
      $bbActiva = [];
      foreach ($combinedBeban as $item) {
        $key = substr($item->no_sbb, 0, 3);
        $bbActiva[$key] = ($bbActiva[$key] ?? 0) + $item->saldo_awal;
      }

      // Membentuk groupedActiva dan menghitung total saldo aktiva
      $groupedActiva = [];

      foreach ($bbActiva as $key => $saldo) {
        $groupedActiva[] = (object) ['no_bb' => $key, 'saldo_aktiva' => $saldo];
      }

      // Proses pengelompokan, penjumlahan, dan group-ing no_bb pasiva
      $bbPasiva = [];
      foreach ($combinedPendapatan as $item) {
        $key = substr($item->no_sbb, 0, 3);
        $bbPasiva[$key] = ($bbPasiva[$key] ?? 0) + $item->saldo_awal;
      }

      // Membentuk groupedPasiva dan menghitung total saldo pasiva
      $groupedPasiva = [];

      foreach ($bbPasiva as $key => $saldo) {
        $groupedPasiva[] = (object) ['no_bb' => $key, 'saldo_pasiva' => $saldo];
      }

      $data['biaya'] = $groupedActiva;
      $data['pendapatan'] = $groupedPasiva;
      $data['sum_biaya'] = $total_beban;
      $data['sum_pendapatan'] = $total_pendapatan;
      $data['total_pendapatan'] = $total_pendapatan - $total_beban;
    } else {
      $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
    }

    $data['title'] = 'Laba rugi BB per tanggal ' . format_indo($tanggal);
    $data['pages'] = 'pages/financial/v_labarugi_bb_by_date';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Neraca SBB")
        ->setSubject("Neraca SBB")
        ->setDescription("Neraca SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Neraca SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Laba rugi per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'BEBAN');
      $sheet->setCellValue('E2', 'PENDAPATAN');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_beban);
      $sheet->setCellValue('F2', 'Total: ');
      $sheet->setCellValue('G3', $total_pendapatan);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;
      foreach ($groupedActiva as $t) {
        $coa = $this->M_coa->getCoaBB($t->no_bb);

        $sheet->setCellValue('A' . $numrowActiva, $t->no_bb);
        $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
        $sheet->setCellValue('C' . $numrowActiva, $t->saldo_aktiva);

        $numrowActiva++;
      }

      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($groupedPasiva as $t) {
        $coa = $this->M_coa->getCoaBB($t->no_bb);

        $sheet->setCellValue('E' . $numrowPasiva, $t->no_bb);
        $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
        $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_pasiva);

        $numrowPasiva++;
      }

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Laba rugi BB per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  public function financial_entry($jenis = NULL)
  {

    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $res2 = $query->result_array();
    $result = $res2[0]['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $res2 = $query2->result_array();
    $result2 = $res2[0]['COUNT(id)'];

    $data = [
      'coa' => $this->M_coa->list_coa(),
      'count_inbox' => $result,
      'count_inbox2' => $result2,
    ];

    $data['title'] = 'Financial Entry';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));



    if ($this->session->userdata('is_premium')) {
      if ($jenis == "debit") {
        $data['pages'] = 'pages/financial/v_financial_entry_debit';
      } else if ($jenis == "kredit") {
        $data['pages'] = 'pages/financial/v_financial_entry_kredit';
      } else {
        $data['pages'] = 'pages/financial/v_financial_entry';
      }
    } else {
      $data['pages'] = 'pages/financial/v_financial_entry';
      if ($jenis) {
        $this->session->set_flashdata('swal_message', [
          'icon' => 'question', // or 'success', 'warning', 'info', 'question'
          'title' => 'Siap Menjadi Raja <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>',
          'text' => 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..',
          'confirmButtonText' => 'Ambil Mahkota Sekarang!',
          'showCancelButton' => true,
          'cancelButtonText' => 'Nanti Saja, Belum Siap Jadi Raja',
          'redirectUrl' => base_url('subscription/upgrade') // URL to redirect if confirmed
        ]);
      }
    }

    $this->load->view('index', $data);
  }

  public function process_financial_entry($jenis = null)
  {

    $keterangan = trim($this->input->post('input_keterangan'));
    $tanggal_transaksi = $this->input->post('tanggal');

    $base64_data = null; // Initialize the variable to hold the Base64 string
    $file_name = null;   // <--- New variable to hold the file name
    $file_input_name = 'file'; // The name of your <input type="file">

    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] != UPLOAD_ERR_NO_FILE) {

      $file = $_FILES[$file_input_name];

      // --- File WAS submitted, proceed with custom checks and conversion ---

      // Define your allowed file extensions and maximum size (for custom check)
      $allowed_types = ['gif', 'jpg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf'];
      $max_size_kb = 2048; // 2MB

      // Get file extension and size for manual checking
      $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      $file_size_kb = round($file['size'] / 1024);

      // **A. Manual Type and Size Checks**
      if (!in_array(strtolower($file_ext), $allowed_types) || $file_size_kb > $max_size_kb) {

        // File failed manual check (Type or Size)
        // $error_msg = "The file is not permitted (allowed types: " . implode(', ', $allowed_types) . ") or exceeds the maximum size ({$max_size_kb} KB).";
        // $error = array('upload_error' => $error_msg);

        // Re-load your form view with the error message
        // $this->load->view('upload_form', $error);
        $this->session->set_flashdata('message_error', "The file is not permitted (allowed types: " . implode(', ', $allowed_types) . ") or exceeds the maximum size ({$max_size_kb} KB).");

        redirect('financial/financial_entry');

        return; // Stop execution
      }

      // **B. Convert the file content to Base64**
      $file_name = $file['name'];

      // 1. Read the file contents from the temporary location

      $file_content = file_get_contents($file['tmp_name']);

      if ($file_content === FALSE) {
        // Handle read error
        // $error = array('upload_error' => 'Error reading file content during conversion.');
        $this->session->set_flashdata('message_error', 'Error reading file content during conversion.');

        // $this->load->view('financial_entry');
        redirect('financial/financial_entry');

        return;
      }

      // 2. Encode the content to Base64
      $encoded_content = base64_encode($file_content);

      // 3. Create the full Data URI string (MIME type is crucial here)
      $base64_data = 'data:' . $file['type'] . ';base64,' . $encoded_content;

      // echo "File Base64 :" . $base64_data;
      // echo "File Name :" . $file_name;
      // exit();
    }
    // else {
    //   echo "Gak Masuk";
    //   exit();
    // }


    $this->cb->trans_start(); // Mulai transaksi
    $id_invoice = NULL;

    if ($jenis == "multi_kredit") {
      $coa_debit  = $this->input->post('neraca_debit');
      $coa_kredit = $this->input->post('accounts');
      $nominal    = $this->input->post('nominals');

      if (is_array($coa_kredit) && is_array($nominal)) {
        foreach ($coa_kredit as $i => $kredit) {
          $this->posting($coa_debit, $kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice, $base64_data, $file_name);
        }
      }
    } elseif ($jenis == "multi_debit") {
      $coa_debit  = $this->input->post('accounts');
      $coa_kredit = $this->input->post('neraca_kredit');
      $nominal    = $this->input->post('nominals');

      if (is_array($coa_debit) && is_array($nominal)) {
        foreach ($coa_debit as $i => $debit) {
          $this->posting($debit, $coa_kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice, $base64_data, $file_name);
        }
      }
    } else {
      $coa_debit  = $this->input->post('neraca_debit');
      $coa_kredit = $this->input->post('neraca_kredit');

      if ($coa_debit == $coa_kredit) {
        $this->session->set_flashdata('message_error', 'CoA Debit dan Kredit tidak boleh sama');
        redirect('financial/financial_entry');
      }

      // $nominal = preg_replace('/[^a-zA-Z0-9\']/', '', $this->input->post('input_nominal'));
      $nominal = $this->_parse_rupiah($this->input->post('input_nominal'));
      $this->posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal_transaksi, $id_invoice, $base64_data, $file_name);
    }

    $this->cb->trans_complete(); // Selesaikan transaksi

    if ($this->cb->trans_status() === FALSE) {
      $this->cb->trans_rollback();
      $this->session->set_flashdata('message_error', 'Transaksi gagal, silakan coba lagi.');
    } else {
      $this->cb->trans_commit();
      $this->session->set_flashdata('message_name', 'Transaksi berhasil.');
    }

    redirect('financial/financial_entry');
  }

  public function upload_financial_entry()
  {
    $this->load->library('upload');
    require APPPATH . 'third_party/autoload.php';

    // Include PhpSpreadsheet from third_party
    require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';


    // Configure upload settings
    $config['upload_path'] = FCPATH . 'upload/financial_entry';
    $config['allowed_types'] = 'xls|xlsx|csv'; // Allowed file types
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('format_data')) {
      // If the upload fails, show the error
      $error = $this->upload->display_errors();
      echo json_encode(['status' => false, 'message' => $error, 'upload_path' => $config['upload_path']]);
      return;
    }

    // File upload success
    $file_data = $this->upload->data();
    $file_path = $file_data['full_path'];

    try {
      // Load the spreadsheet using PhpSpreadsheet
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
      $worksheet = $spreadsheet->getActiveSheet();

      // Get total rows
      $totalRows = iterator_count($worksheet->getRowIterator());
      $totalRows -= 2; // Adjust for headers
      $insertedRows = 0;

      // --- Initialize counters ---  
      $no_debit_rows = [];
      $no_kredit_rows = [];
      $success_count = 0;

      // Process rows
      foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
        // Skip header rows
        if ($rowIndex < 3) continue;

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cellIterator as $cell) {
          $data[] = $cell->getValue();
        }

        // Extract and process row data
        $coa_debit = isset($data[0]) ? (string)$data[0] : null;
        $coa_kredit = isset($data[1]) ? (string)$data[1] : null;
        $nominal = isset($data[2]) ? (string)$data[2] : null;
        $tanggal = isset($data[3]) ? $this->processDate($data[3]) : null;
        $keterangan = isset($data[4]) ? $data[4] : null;

        $posting = $this->posting(
          $coa_debit,
          $coa_kredit,
          $keterangan,
          $nominal,
          $tanggal,
          $jenis_fe = 'single'
        );

        // --- Store row index if an error occurs ---
        if ($posting == "No Debit") {
          $no_debit_rows[] = $rowIndex;
        } else if ($posting == "No Kredit") {
          $no_kredit_rows[] = $rowIndex;
        } else {
          $success_count++;
        }

        $insertedRows++;
        $progress = round(($insertedRows / $totalRows) * 100);
        echo "data: " . json_encode(['progress' => $progress, 'currentRow' => $insertedRows, 'totalRows' => $totalRows]) . "\n\n";
        ob_flush();
        flush();
      }

      // Commit transaction
      if ($this->cb->trans_status() === FALSE) {
        $this->cb->trans_rollback();
        echo json_encode(['status' => false, 'message' => 'Database error']);
      } else {
        $this->cb->trans_commit();
        // echo json_encode(['status' => true, 'message' => 'File processed successfully']);
        echo json_encode([
          'status' => true,
          'message' => 'File processed successfully',
          'success_count' => $success_count,
          'no_debit_rows' => $no_debit_rows,
          'no_kredit_rows' => $no_kredit_rows
        ]);
      }
    } catch (Exception $e) {
      // Handle exceptions
      echo json_encode(['status' => false, 'message' => $e->getMessage()]);
    } finally {
      // Cleanup uploaded file
      if (file_exists($file_path)) unlink($file_path);
    }
  }

  public function closing($slug = NULL)
  {
    $has_access = $this->M_menu->has_access();

    if (!$slug) {
      if (!$has_access) {
        show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
      }
    } else {
      $segment1 = $this->uri->segment(1); // 'financial'
      $segment2 = $this->uri->segment(2); // 'closing'
      $route = $segment1 . '/' . $segment2; // hasil: 'financial/closing'

      $nip = $this->session->userdata('nip');
      $allowed_routes = $this->M_menu->get_allowed_routes($nip);

      if (!in_array($route, $allowed_routes)) {
        show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
      }
    }

    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($slug) {
      $data['title'] = "Detail saldo";
      $data['saldo'] = $this->M_coa->get_saldo_awal($slug);
      $data['coa'] = json_decode($data['saldo']['coa']);
      $data['pages'] = 'pages/financial/v_saldo_view';
      // $this->load->view('saldo_view', $data);
    } else if ($this->input->post('periode')) {
      $data['title'] = "Detail saldo";
      $data['saldo'] = $this->M_coa->get_saldo_awal($this->input->post('periode'));
      $data['coa'] = json_decode($data['saldo']['coa']);
      $data['pages'] = 'pages/financial/v_saldo_view';
      // $this->load->view('saldo_view', $data);
    } else {
      $data['title'] = "Saldo awal";
      $data['saldo'] = $this->M_coa->list_saldo();
      $data['pages'] = 'pages/financial/v_saldo_awal';
      // $this->load->view('saldo_awal', $data);
    }

    $this->load->view('index', $data);
  }

  public function save_saldo_awal()
  {
    $periode = $this->input->post('periode');
    $password = $this->input->post('password');
    $this->form_validation->set_rules('periode', 'periode', 'required');
    $this->form_validation->set_rules('password', 'password', 'required');

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $data = $this->M_login->datapengguna($this->session->userdata('username'));
      if (password_verify($password, $data->password)) {
        $cek = $this->M_coa->cek_saldo_awal($periode);

        $date = new DateTime($periode);

        $bulan = $date->format('m');
        $tahun = $date->format('Y');

        $last_periode = new DateTime($periode);
        $last_periode = $last_periode->modify('-1 month');
        $last_periode = $last_periode->format('Y-m');

        $getLastPeriod = $this->M_coa->cek_saldo_awal($last_periode);

        if (empty($getLastPeriod)) {
          $updated_saldo_awal = $this->M_coa->calculate_saldo_awal($bulan, $tahun);
        } else {
          $coaLastPeriod = json_decode($getLastPeriod['coa']);
          $saldo_bulan_ini = $this->M_coa->calculate_saldo_awal($bulan, $tahun);

          $saldo_awal_map = [];
          foreach ($coaLastPeriod as $saldo_awal) {
            $saldo_awal_map[$saldo_awal->no_sbb] = $saldo_awal;
          }

          foreach ($saldo_bulan_ini as $saldo_baru) {
            if (isset($saldo_awal_map[$saldo_baru->no_sbb])) {
              $saldo_awal_map[$saldo_baru->no_sbb]->saldo_awal += (float) $saldo_baru->saldo_awal;
            } else {
              $saldo_awal_map[$saldo_baru->no_sbb] = (object) [
                'no_sbb' => $saldo_baru->no_sbb,
                'saldo_awal' => (float) $saldo_baru->saldo_awal,
                'posisi' => $saldo_baru->posisi,
                'table_source' => $saldo_baru->table_source,
              ];
            }
          }
          $updated_saldo_awal = array_values($saldo_awal_map);
        }

        $nextMonth = ($date->modify('+1 month'));
        $nextMonth = $date->format('Y-m');

        $data = [
          'periode' => $periode,
          'created_by' => $this->session->userdata('nip'),
          'created_at' => date('Y-m-d H:i:s'),
          'slug' => 'saldo-awal-' . $nextMonth,
          'coa' => json_encode($updated_saldo_awal),
          'keterangan' => 'Saldo awal ' . format_indo($nextMonth),
          'id_cabang' => $this->session->userdata('kode_cabang'),
          'id_company' => $this->session->userdata('user_perusahaan_id')
        ];

        if (!$cek) {
          $this->M_coa->insert_saldo_awal($data);
          $response = [
            'success' => true,
            'msg' => 'Closing bulan ' . format_indo($periode) . 'Saldo awal periode ' . format_indo($nextMonth) . ' berhasil ditetapkan',
            'reload' => site_url('financial/closing')
          ];
        } else {
          $this->M_coa->update_saldo_awal($periode, $data);
          $response = [
            'success' => true,
            'msg' => 'Closing bulan ' . format_indo($periode) . ' sudah diperbarui',
            'reload' => site_url('financial/closing')
          ];
        }
      } else {
        $response = [
          'success' => false,
          'msg' => 'Password salah!'
        ];
      }
    }

    echo json_encode($response);
  }

  public function coa_report()
  {

    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $nip = $this->session->userdata('nip');
    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};
    $data = [
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'coas' => $this->M_coa->list_coa(),
    ];

    $no_coa = $this->input->post('no_coa');

    if ($no_coa) {
      $this->prepareCoaReport($data, $no_coa);
    } else {
      $data['title'] = "Report CoA";
      $data['pages'] = "pages/financial/v_report_per_coa";
      $data['utility'] = $this->db->get('utility')->row_array();
      $data['pages_script'] = 'script/financial/s_financial';
      $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

      $this->load->view('index', $data);
    }
  }

  public function invoice()
  {

    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $customer_id = $this->input->post('customer_id');
    $keyword = trim($this->input->post('keyword', true) ?? '');

    $config = [
      'base_url' => site_url('financial/invoice'),
      'total_rows' => $this->M_invoice->invoice_count($keyword, $customer_id),
      'per_page' => 20,
      'uri_segment' => 3,
      'num_links' => 10,
      'full_tag_open' => '<ul class="pagination" style="margin: 0 0">',
      'full_tag_close' => '</ul>',
      'first_link' => false,
      'last_link' => false,
      'first_tag_open' => '<li>',
      'first_tag_close' => '</li>',
      'prev_link' => '«',
      'prev_tag_open' => '<li class="prev">',
      'prev_tag_close' => '</li>',
      'next_link' => '»',
      'next_tag_open' => '<li>',
      'next_tag_close' => '</li>',
      'last_tag_open' => '<li>',
      'last_tag_close' => '</li>',
      'cur_tag_open' => '<li class="active"><a href="#">',
      'cur_tag_close' => '</a></li>',
      'num_tag_open' => '<li>',
      'num_tag_close' => '</li>'
    ];

    $this->pagination->initialize($config);

    $page = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
    $invoices = $this->M_invoice->list_invoice($config["per_page"], $page, $keyword, $customer_id);

    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $result = $query->row_array()['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $result2 = $query2->row_array()['COUNT(id)'];

    $data = [
      'page' => $page,
      'invoices' => $invoices,
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'coa' => $this->M_coa->list_coa(),
      // 'coa_kas' => $this->M_coa->getCoaByCode('1201'),
      'coa_kas' => $this->M_coa->getCoaByCode('1'),
      'coa_pendapatan' => $this->M_coa->getCoaByCode('410'),
      'keyword' => $keyword,
      'title' => "Invoice",
      'customers' => $this->M_customer->list_customer(''),
    ];

    $data['title'] = "Daftar Invoice";
    $data['pages'] = "pages/financial/v_invoice";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    // echo '<pre>';
    // print_r($data['invoices']);
    // echo '</pre>';
    // exit;


    $this->cb->from('invoice');
    $this->cb->join('t_cabang', 't_cabang.uid = invoice.id_cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->cb->where('MONTH(invoice.created_at)', date('m'));
    $this->cb->where('YEAR(invoice.created_at)', date('Y'));
    $total_invoice = $this->cb->get()->num_rows(); // Get the number of rows

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaan = $this->db->get()->row(); // Get the number of rows

    $limit_invoice = $perusahaan->kuota_invoice;

    $data['total_invoice'] = $total_invoice;
    $data['limit_invoice'] = $limit_invoice;

    $this->load->view('index', $data);
  }

  public function create_invoice()
  {
    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $res2 = $query->result_array();
    $result = $res2[0]['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $res2 = $query2->result_array();
    $result2 = $res2[0]['COUNT(id)'];

    $data = [
      'title' => 'Create Invoice',
      // 'no_invoice' => $no_inv,
      'customers' => $this->M_customer->list_customer(),
      'pendapatan' => $this->M_coa->getCoaByCode('1'),
      'persediaan' => $this->M_coa->getCoaByCode('4'),
      'count_inbox' => $result,
      'count_inbox2' => $result2,
    ];

    $data['title'] = "Create Invoice";
    $data['pages'] = "pages/financial/v_create_invoice";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->cb->from('invoice');
    $this->cb->join('t_cabang', 't_cabang.uid = invoice.id_cabang');
    $this->cb->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $this->cb->where('MONTH(invoice.created_at)', date('m'));
    $this->cb->where('YEAR(invoice.created_at)', date('Y'));
    $total_invoice = $this->cb->get()->num_rows(); // Get the number of rows

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaan = $this->db->get()->row(); // Get the number of rows

    $limit_invoice = $perusahaan->kuota_invoice;
    if ($total_invoice >= $limit_invoice) {
      $this->session->set_flashdata('swal_message', [
        'icon' => 'info', // Tetap gunakan 'info' atau 'question' untuk kesan informatif
        'title' => 'Singgasana Menunggu Anda!', // Judul yang menarik dan bertema
        'text' => 'Batas jumlah arsip keuangan (invoice) dalam perbendaharaan kerajaan Anda telah tercapai. Tambah kapasitas perbendaharaan dan kelola lebih banyak dokumen penting dengan menaikkan derajat kekuasaan Anda.',
        'confirmButtonText' => 'Klaim Takhta Sekarang!', // Kalimat persuasif untuk tombol
        'showCancelButton' => true,
        'cancelButtonText' => 'Tunda Penobatan', // Opsi yang lucu dan sesuai tema
        'redirectUrl' => base_url('subscription/upgrade')
      ]);
      redirect('financial/invoice');
    }

    $this->load->view('index', $data);
  }

  public function store_invoice($jenis)
  {
    $id_user = $this->session->userdata('nip');
    $diskon = $this->input->post('diskon');
    $ppn = $this->input->post('ppn');
    $nominal = $this->convertToNumberWithComma($this->input->post('nominal'));
    $besaran_diskon = $this->convertToNumberWithComma(($this->input->post('besaran_diskon')) ? $this->input->post('besaran_diskon') : '0');
    $besaran_ppn = $this->convertToNumberWithComma($this->input->post('besaran_ppn'));
    $besaran_pph = $this->convertToNumberWithComma($this->input->post('besaran_pph'));
    $nominal_bayar = $this->convertToNumberWithComma($this->input->post('nominal_bayar'));
    // $total_chargeable = $this->convertToNumberWithComma($this->input->post('total_chargeable'));
    $total_nonpph = $this->convertToNumberWithComma($this->input->post('total_nonpph'));
    $total_denganpph = $this->convertToNumberWithComma($this->input->post('total_denganpph'));
    $nominal_pendapatan = $this->convertToNumberWithComma($this->input->post('nominal_pendapatan'));

    // print_r($nominal);
    // exit;

    $no_inv = $this->input->post('no_invoice');

    // $status_pendapatan = $this->input->post('status_pendapatan');
    $opsi_termin = $this->input->post('opsi_termin');
    $opsi_pph = $this->input->post('opsi_pph');
    $opsi_ppn = $this->input->post('opsi_ppn');
    $coa_debit = $this->input->post('coa_debit');
    $coa_kredit = $this->input->post('coa_kredit');


    $pph = isset($opsi_pph) ? '0.02' : 0;

    $tgl_invoice = $this->input->post('tgl_invoice');
    $tahun = substr($tgl_invoice, 0, 4);

    $max_num = $this->M_invoice->select_max($tahun);

    if (!$max_num['max']) {
      $bilangan = 1; // Nilai Proses
    } else {
      $bilangan = $max_num['max'] + 1;
    }

    $month = substr($tgl_invoice, 5, 2);
    $year = substr($tgl_invoice, 2, 2);

    $no_inv = sprintf("%04d", $bilangan);
    $kode_cabang = sprintf("%02d", $this->session->userdata('kode_cabang'));



    $kop_invoice = $this->session->userdata('nama_akronim') . "-" . $kode_cabang;

    $slug = $no_inv . '/' . strtoupper($kop_invoice) . '/' . intToRoman($month) . '/' . $year;

    $keterangan = trim($this->input->post('keterangan'));

    if ($jenis == 'reguler') {
      $jenis_invoice = 'reguler';
    } else {
      $jenis_invoice = 'khusus';
    }

    // Insert ke tabel invoice
    $invoice_data = [
      'no_invoice' => $no_inv,
      'tanggal_invoice' => $tgl_invoice,
      'created_by' => $id_user,
      'keterangan' => $keterangan,
      'id_customer' => $this->input->post('customer'),
      'subtotal' => $nominal,
      'diskon' => isset($diskon) ? $diskon : '0',
      'besaran_diskon' => $besaran_diskon,
      'ppn' => $ppn,
      'besaran_ppn' => $besaran_ppn,
      'opsi_pph23' => isset($opsi_pph) ? $opsi_pph : '0',
      'opsi_ppn' => isset($opsi_ppn) ? $opsi_ppn : '0',
      'pph' => $pph,
      'besaran_pph' => $besaran_pph,
      'total_nonpph' => $total_nonpph,
      'total_denganpph' => $total_denganpph,
      'coa_debit' => $coa_debit,
      'coa_kredit' => $coa_kredit,
      'nominal_bayar' => $nominal_bayar,
      'nominal_pendapatan' => $nominal_pendapatan,
      'jenis_invoice' => $jenis_invoice,
      // 'status_pendapatan' => isset($status_pendapatan) ? $status_pendapatan : '0'
      'opsi_termin' => isset($opsi_termin) ? $opsi_termin : '0',
      'status_pendapatan' => '1',
      'slug' => $slug,
      'id_cabang' => $this->session->userdata('kode_cabang'),
      'id_company' => $this->session->userdata('user_perusahaan_id')
    ];

    $this->cb->trans_begin();
    $id_invoice = $this->M_invoice->insert($invoice_data);

    if (!$id_invoice) {
      $this->cb->trans_rollback();
      $this->session->set_flashdata('message_name', 'Failed to create invoice.');
      redirect("financial/invoice");
    }

    $items = $this->input->post('item');
    $jumlahs = $this->input->post('jumlah');
    $totals = $this->input->post('total');
    $total_amounts = $this->input->post('total_amount');

    $detail_data = [];

    if (is_array($items)) {

      for ($i = 0; $i < count($items); $i++) {
        $item = trim($items[$i]);
        $total = $this->convertToNumberWithComma($totals[$i]);
        $jumlah = $this->convertToNumberWithComma($jumlahs[$i]);
        $total_amount = $this->convertToNumberWithComma($total_amounts[$i]);

        $detail_data[] = [
          'id_invoice' => $id_invoice,
          'item' => strtoupper($item),
          'total' => $total,
          'qty' => $jumlah,
          'total_amount' => $total_amount,
          'created_by' => $id_user,
          'id_cabang' => $this->session->userdata('kode_cabang'),
          'id_company' => $this->session->userdata('user_perusahaan_id')
        ];
      }

      if (!empty($detail_data)) {
        $insert = $this->M_invoice->insert_batch($detail_data);

        if ($insert === FALSE) {
          $this->cb->trans_rollback();
          $this->session->set_flashdata('message_name', 'Failed to insert invoice details.');
          redirect("financial/invoice");
        }

        // Pastikan fungsi posting tidak mengganggu transaksi
        $this->posting($coa_debit, $coa_kredit, $keterangan, $total_denganpph, $tgl_invoice, $id_invoice);

        $this->cb->trans_commit();
        $this->session->set_flashdata('message_name', 'The invoice has been successfully created. ' . $no_inv);
        redirect("financial/invoice");
      } else {
        $this->cb->trans_rollback();
        $this->session->set_flashdata('message_name', 'Invoice detail data is empty.');
        redirect("financial/invoice");
      }
    }
  }

  public function edit_invoice($id)
  {
    $inv =  $this->M_invoice->showById($id);
    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $res2 = $query->result_array();
    $result = $res2[0]['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $res2 = $query2->result_array();
    $result2 = $res2[0]['COUNT(id)'];

    $data = [
      'title' => 'Invoice No. ' . $inv['no_invoice'],
      'inv' => $inv,
      'details' => $this->M_invoice->item_list($inv['Id']),
      'user' => $this->M_invoice->cek_user($inv['user_create']),
      'customers' => $this->M_customer->list_customer(),
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'pendapatan' => $this->M_coa->getCoaByCode('1'),
      'persediaan' => $this->M_coa->getCoaByCode('4'),
    ];

    $data['pages'] = "pages/financial/v_invoice_edit";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);

    // $pages = "invoice_edit";

    // $this->load->view($pages, $data);
  }

  public function update_invoice($id)
  {
    $id_user = $this->session->userdata('nip');
    $diskon = $this->input->post('diskon');
    $ppn = $this->input->post('ppn');
    $nominal = $this->convertToNumberWithComma($this->input->post('nominal'));
    $besaran_diskon = $this->convertToNumberWithComma(($this->input->post('besaran_diskon')) ? $this->input->post('besaran_diskon') : '0');
    $besaran_ppn = $this->convertToNumberWithComma($this->input->post('besaran_ppn'));
    $besaran_pph = $this->convertToNumberWithComma($this->input->post('besaran_pph'));
    $nominal_bayar = $this->convertToNumberWithComma($this->input->post('nominal_bayar'));
    // $total_chargeable = $this->convertToNumberWithComma($this->input->post('total_chargeable'));
    $total_nonpph = $this->convertToNumberWithComma($this->input->post('total_nonpph'));
    $total_denganpph = $this->convertToNumberWithComma($this->input->post('total_denganpph'));
    $nominal_pendapatan = $this->convertToNumberWithComma($this->input->post('nominal_pendapatan'));

    $no_inv = $this->input->post('no_invoice');

    // $status_pendapatan = $this->input->post('status_pendapatan');
    $opsi_termin = $this->input->post('opsi_termin');
    $opsi_pph = $this->input->post('opsi_pph');
    $opsi_ppn = $this->input->post('opsi_ppn');
    $coa_debit = $this->input->post('coa_debit');
    $coa_kredit = $this->input->post('coa_kredit');

    $pph = ($opsi_pph == 1) ? '0.02' : 0;


    $tgl_invoice = $this->input->post('tgl_invoice');

    $keterangan = trim($this->input->post('keterangan'));


    // Insert ke tabel invoice
    $invoice_data = [
      'no_invoice' => $no_inv,
      'tanggal_invoice' => $tgl_invoice,
      'created_by' => $id_user,
      'keterangan' => $keterangan,
      'id_customer' => $this->input->post('customer'),
      'subtotal' => $nominal,
      'diskon' => isset($diskon) ? $diskon : '0',
      'besaran_diskon' => $besaran_diskon,
      'ppn' => $ppn,
      'besaran_ppn' => $besaran_ppn,
      'opsi_pph23' => isset($opsi_pph) ? $opsi_pph : '0',
      'opsi_ppn' => isset($opsi_ppn) ? $opsi_ppn : '0',
      'pph' => $pph,
      'besaran_pph' => $besaran_pph,
      'total_nonpph' => $total_nonpph,
      'total_denganpph' => $total_denganpph,
      'coa_debit' => $coa_debit,
      'coa_kredit' => $coa_kredit,
      'nominal_bayar' => $nominal_bayar,
      'nominal_pendapatan' => $nominal_pendapatan,
      // 'status_pendapatan' => isset($status_pendapatan) ? $status_pendapatan : '0'
      'opsi_termin' => isset($opsi_termin) ? $opsi_termin : '0',
      'status_pendapatan' => '1'
    ];

    $this->cb->trans_begin();

    $inv =  $this->M_invoice->showById($id);

    $keterangan_lama = "Jurnal balik edit invoice " . $inv['no_invoice'];

    // Jurnal balik sebelum update invoice
    $coa_kredit_lama = $inv['coa_kredit'];
    $coa_debit_lama = $inv['coa_debit'];

    $this->posting($coa_kredit_lama, $coa_debit_lama, $keterangan_lama, $inv['total_denganpph'], $inv['tanggal_invoice'], $inv['Id']);

    if (!$this->M_invoice->update_invoice($id, $invoice_data)) {
      $this->cb->trans_rollback();
      $this->session->set_flashdata('message_name', 'Failed to update invoice.');
      redirect('financial/invoice');
    }

    $items = $this->input->post('item');
    $jumlahs = $this->input->post('jumlah');
    $totals = $this->input->post('total');
    $total_amounts = $this->input->post('total_amount');

    // Hapus detail invoice lama
    $this->cb->where('id_invoice', $id)->delete('invoice_details');

    // Handle detail data
    if (!empty($items)) {
      $detail_data = [];

      for ($i = 0; $i < count($items); $i++) {
        $detail_data[] = [
          'id_invoice' => $id,
          'item' => strtoupper(trim($items[$i])),
          'total' => $this->convertToNumberWithComma($totals[$i]),
          'qty' => $this->convertToNumberWithComma($jumlahs[$i]),
          'total_amount' => $this->convertToNumberWithComma($total_amounts[$i]),
          'created_by' => $id_user
        ];
      }

      if (!empty($detail_data)) {
        if (!$this->M_invoice->insert_batch($detail_data)) {
          $this->cb->trans_rollback();
          $this->session->set_flashdata('message_name', 'Failed to insert invoice details.');
          redirect("financial/invoice");
        }
      }
    }

    // Update jurnal
    // $dt_jurnal = [
    //     'tanggal' => $tgl_invoice,
    //     'akun_debit' => $coa_debit,
    //     'jumlah_debit' => $nominal_bayar,
    //     'akun_kredit' => $coa_kredit,
    //     'jumlah_kredit' => $nominal_bayar,
    //     'keterangan' => trim($keterangan),
    //     'created_by' => $id_user,
    // ];

    // if (!$this->cb->where('id_invoice', $id)->update('jurnal_neraca', $dt_jurnal)) {
    //     $this->cb->trans_rollback();
    //     $this->session->set_flashdata('message_name', 'Failed to update journal.');
    //     redirect("financial/invoice");
    // }

    $this->posting(
      $coa_debit,
      $coa_kredit,
      $keterangan,
      $total_denganpph,
      $tgl_invoice,
      $id
    );

    // Commit transaksi
    if ($this->cb->trans_status() === FALSE) {
      $this->cb->trans_rollback();
      $this->session->set_flashdata('message_name', 'Transaction failed.');
    } else {
      $this->cb->trans_commit();
      $this->session->set_flashdata('message_name', 'Invoice updated successfully.');
    }

    redirect('financial/invoice');
  }

  public function outstanding()
  {

    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $nip = $this->session->userdata('nip');

    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};


    $data = [
      'title' => 'Outstanding',
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'outstanding' => $this->M_invoice->outstanding_agent(),
    ];

    $data['pages'] = "pages/financial/v_outstanding";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);
  }

  public function paid()
  {
    // print_r($_POST);
    // exit;
    $id = $this->uri->segment(3);

    $inv =  $this->M_invoice->showById($id);
    $coa_debit = $this->input->post('coa_debit');
    $coa_kredit = $this->input->post('coa_kredit');
    $nominal_bayar = $this->convertToNumber(($this->input->post('nominal_bayar')));
    $keterangan = $this->input->post('keterangan');
    $status_bayar = $this->input->post('status_bayar');
    $tanggal_bayar = $this->input->post('tanggal_bayar');

    $nominal_j2 = $inv['subtotal'] - $inv['besaran_pph'];
    // if ($inv['besaran_ppn'] !== '0.00') {
    //     echo 'true';
    // } else {
    //     echo 'false';
    // }
    // exit;
    // kalau tidak 

    // J1: PAD berkurang sebesar nominal pendapatan, Pendapatan bertambah sebesar nominal pendapatan
    if ($nominal_bayar == $inv['nominal_pendapatan']) {
      $j1_coa_debit = $inv['coa_kredit'];
      $j1_coa_kredit = $coa_kredit;
      $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $inv['nominal_pendapatan'], $tanggal_bayar);
    } else {
      $j1_coa_debit = $inv['coa_kredit'];
      $j1_coa_kredit = $coa_kredit;
      $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $nominal_bayar, $tanggal_bayar);
    }


    // J3: Kas/Bank bertambah sebesar nominal bayar, piutang usaha keluaran berkurang sebesar nominal bayar
    $j1_coa_debit = $coa_debit;
    $j1_coa_kredit = $inv['coa_debit'];
    $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $nominal_bayar, $tanggal_bayar);

    // J2: Kas/Bank bertambah sebesar ppn, ppn keluaran bertambah sebesar ppn keluaran
    if ($inv['besaran_ppn'] !== '0.00') {
      $j1_coa_debit = $inv['coa_debit'];
      $j1_coa_kredit = "23011";
      $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $inv['besaran_ppn'], $tanggal_bayar);

      $j2_coa_debit = $inv['coa_kredit'];
      $j2_coa_kredit = $inv['coa_debit'];
      $this->posting($j2_coa_debit, $j2_coa_kredit, $keterangan, $inv['besaran_ppn'], $tanggal_bayar);
    }

    if ($inv['opsi_pph23'] == '1') {
      // J4: Kas/Bank bertambah sebesar pph, utang pph 23 bertambah sebesar pph
      $j1_coa_debit = $coa_debit;
      $j1_coa_kredit = "23014";
      $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $inv['besaran_pph'], $tanggal_bayar);
    }

    $this->log_pembayaran("invoice", $inv['Id'], $nominal_bayar, $keterangan);

    $data_invoice = [
      'status_pendapatan' => ($status_bayar == 1) ? '2' : '1',
      'status_bayar' => ($status_bayar == 1) ? '1' : '0',
      'total_termin' => $inv['total_termin'] + $nominal_bayar,
      'tanggal_bayar' => $this->input->post('tanggal_bayar'),
    ];

    $this->M_invoice->update_invoice($inv['Id'], $data_invoice);

    $this->session->set_flashdata('message_name', 'The invoice has been successfully updated. ' . $inv['no_invoice']);
    // After that you need to used redirect function instead of load view such as 
    redirect("financial/invoice");
  }

  public function void_invoice()
  {
    $no_inv = $this->uri->segment(3);

    $inv =  $this->M_invoice->show($no_inv);
    $coa_persediaan = $inv['coa_persediaan'];
    $jenis = $inv['jenis_invoice'];
    $keterangan = $this->input->post('keterangan');
    $total_biaya = $inv['total_biaya'];
    $nominal_pendapatan = $inv['nominal_pendapatan'];
    $tgl_void = date('Y-m-d');

    $data_void = [
      'status_void' => '1',
      'alasan_void' => $keterangan,
      'tanggal_void' => $tgl_void
    ];

    if ($inv) {
      // update 24 Juni 2024 jam 17:07

      $this->posting($inv['coa_kredit'], $inv['coa_debit'], $keterangan, $nominal_pendapatan, $tgl_void);

      $this->M_invoice->update_invoice($inv['Id'], $data_void);

      $this->session->set_flashdata('message_name', 'The invoice has been successfully void. ' . $no_inv);
      // After that you need to used redirect function instead of load view such as 
      redirect("financial/invoice");
    }
  }

  public function list_coa()
  {

    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $active_tab = $this->input->post('active_tab') ? $this->input->post('active_tab') : $this->session->userdata('active_tab');
    if (!$active_tab) {
      // Default to 'card2' (List COA BB) since it's the default active tab in your HTML
      $active_tab = 'card2';
    }

    // echo $active_tab;
    $this->session->set_userdata('active_tab', $active_tab);

    $keyword_sbb = ($this->input->post('keyword_sbb')) ? trim($this->input->post('keyword_sbb')) : (($this->session->userdata('search_sbb')) ? $this->session->userdata('search_sbb') : '');
    $keyword_bb = ($this->input->post('keyword_bb')) ? trim($this->input->post('keyword_bb')) : (($this->session->userdata('search_bb')) ? $this->session->userdata('search_bb') : '');

    // Reset logic for each keyword
    if ($keyword_sbb !== null) {
      $this->session->set_userdata('search_sbb', $keyword_sbb);
    }
    if ($keyword_bb !== null) {
      $this->session->set_userdata('search_bb', $keyword_bb);
    }


    $cabang = $this->input->post('cabang_select') ? $this->input->post('cabang_select') : '';
    if ($cabang === null || $cabang === '') $cabang = $this->session->userdata('kode_cabang');

    $perusahaan = $this->session->userdata('user_perusahaan_id');

    $nip = $this->session->userdata('nip');
    $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
    $query = $this->db->query($sql);
    $result = $query->row_array()['COUNT(Id)'];

    $sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
    $query2 = $this->db->query($sql2);
    $result2 = $query2->row_array()['COUNT(id)'];

    $this->cb->from('t_cabang');
    $this->cb->where('id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $cabangs = $this->cb->get()->result();

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang_s = $this->cb->get()->row();

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $perusahaansss = $this->db->get()->row();

    $this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
    $this->cb->from('v_coabb_all');
    $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
    $query = $this->cb->get();
    $all_coa_bb = $query->result_array();


    $activa = $this->M_coa->get_coa_activa_by_cabang();
    $pasiva = $this->M_coa->get_coa_pasiva_by_cabang();

    $Sumactiva = $this->M_coa->get_sum_coa_activa_by_cabang();
    $sum_activa = $Sumactiva->nominal;
    $Sumpasiva = $this->M_coa->get_sum_coa_pasiva_by_cabang();
    $sum_pasiva = $Sumpasiva->nominal;

    $pendapatan = $this->M_coa->get_sum_coa_pasiva_coalr_by_cabang();
    $beban = $this->M_coa->get_sum_coa_activa_coalr_by_cabang();

    $laba = $pendapatan->nominal - $beban->nominal;

    // --- PAGINATION FOR CARD 1 (v_coa_all) ---

    $per_page = 25;

    // Get total rows for SBB
    $total_sbb_rows = $this->M_coa->count($keyword_sbb, $cabang, 'v_coa_all');

    // Get total rows for BB
    $total_bb_rows = $this->M_coa->count_bb($keyword_bb, $perusahaan, 'v_coabb_all');

    // Prepare the configuration arrays using the new function
    $config_sbb = $this->_pagination_config($total_sbb_rows, $per_page, 'page_sbb');
    $config_bb = $this->_pagination_config($total_bb_rows, $per_page, 'page_bb');

    $page_sbb = ($this->input->get('page_sbb')) ? (($this->input->get('page_sbb') - 1) * $per_page) : 0;
    $coa_sbb = $this->M_coa->list_coa_paginate($per_page, $page_sbb, $keyword_sbb, $cabang);

    $page_bb = ($this->input->get('page_bb')) ? (($this->input->get('page_bb') - 1) * $per_page) : 0;
    $coa_bb = $this->M_coa->list_coa_bb_paginate($per_page, $page_bb, $keyword_bb, $perusahaan);

    $data = [
      'laba' => $laba,
      'activa' => $activa,
      'pasiva' => $pasiva,
      'sum_activa' => $sum_activa,
      'sum_pasiva' => $sum_pasiva,
      'page' => $page_sbb,
      'coa' => $coa_sbb,
      'page_bb' => $page_bb, // Pass the new page variable
      'coa_bb' => $coa_bb,   // Pass the new data for Card 2
      'config_sbb' => $config_sbb, // Pass the SBB config
      'config_bb' => $config_bb, // Pass the BB config

      'cabang_now' => $cabang,
      'cabang' => $cabangs,
      'is_semua_coa' => $cabang_s->ambil_semua_coa,
      'is_sawal' => $cabang_s->generate_sawal,
      'is_semua_coa_bb' => $perusahaansss->ambil_semua_coa_bb,
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'keyword_sbb' => $keyword_sbb,
      'keyword_bb' => $keyword_bb,
      'title' => "List CoA",
      'all_coa_bb' => $all_coa_bb,
      // 'cek_coa_bb' => $cek_coa_bb,
      // 'cek_coalr_bb' => $cek_coalr_bb,
      'active_tab' => $active_tab, // Pass the active tab to the view

    ];


    $data['pages'] = "pages/financial/v_list_coa";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);
  }

  private function _pagination_config($total_rows, $per_page, $query_string_segment)
  {
    $config = [
      'base_url' => site_url('financial/list_coa'),
      'total_rows' => $total_rows,
      'per_page' => $per_page,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => $query_string_segment,
      'full_tag_open' => '<ul class="pagination justify-content-end">',
      'full_tag_close' => '</ul>',
      'first_link' => "<i class='fe fe-chevrons-left'></i>",
      'last_link' => "<i class='fe fe-chevrons-right'></i>",
      'first_tag_open' => '<li class="page-item">',
      'first_tag_close' => '</li>',
      'prev_link' => "<i class='fe fe-chevron-left'></i>",
      'prev_tag_open' => '<li class="page-item">',
      'prev_tag_close' => '</li>',
      'next_link' => "<i class='fe fe-chevron-right'></i>",
      'next_tag_open' => '<li class="page-item">',
      'next_tag_close' => '</li>',
      'last_tag_open' => '<li class="page-item">',
      'last_tag_close' => '</li>',
      'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
      'cur_tag_close' => '</a></li>',
      'num_tag_open' => '<li class="page-item">',
      'num_tag_close' => '</li>',
      'attributes' => ['class' => 'page-link'],
    ];
    return $config;
  }

  public function set_active_tab_session()
  {
    // Check if the request is an AJAX request
    if ($this->input->is_ajax_request()) {
      $active_tab = $this->input->post('active_tab');
      if ($active_tab) {
        $this->session->set_userdata('active_tab', $active_tab);
        echo json_encode(['status' => 'success']);
      }
    }
  }

  public function search_coa_bb()
  {
    $search_term = $this->input->get('q'); // Get the search term from Select2

    $this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
    $this->cb->from('v_coabb_all');
    if (!empty($search_term)) {
      $this->cb->like('no_bb', $search_term); // Search by no_bb
      $this->cb->or_like('nama_perkiraan', $search_term); // Or by nama_coa_bb
    }
    $query = $this->cb->get();

    $results = $query->result_array(); // Get results as an array of associative arrays

    echo json_encode($results); // Encode results as JSON and output
  }

  public function reset_coa()
  {

    $this->session->unset_userdata('search_sbb');
    $this->session->unset_userdata('search_bb');

    redirect('financial/list_coa');
  }

  public function tambahCoa()
  {
    $no_bb = $this->input->post('no_bb');
    $no_sbb = $this->input->post('no_sbb');
    $nama_coa = $this->input->post('nama_coa');
    $saldo_awal = $this->input->post('saldo_awal');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
    $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);

    $this->session->set_userdata('active_tab', 'card1');

    if ($cek_no_bb) {
      if ($cek_no_sbb) {
        $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
        redirect($_SERVER['HTTP_REFERER']);
      } else if ($cek_nama_coa) {
        $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
        redirect($_SERVER['HTTP_REFERER']);
      } else {

        $substr_coa = substr($no_sbb, 0, 1);

        if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
          $posisi = 'AKTIVA';
        } else {
          $posisi = 'PASIVA';
        }

        // cek tabel
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
          $tabel = "t_coa_sbb";

          $data = [
            'no_bb' => $no_bb,
            'no_sbb' => $no_sbb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'nominal' => $this->_parse_rupiah($saldo_awal),
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
          $tabel = "t_coalr_sbb";
          $data = [
            'no_lr_bb' => $no_bb,
            'no_lr_sbb' => $no_sbb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'nominal' => $this->_parse_rupiah($saldo_awal),
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else {
          $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
          redirect($_SERVER['HTTP_REFERER']);
        }


        $this->cb->trans_begin();

        $query = $this->cb->insert($tabel, $data);

        if ($query) {
          $this->cb->trans_commit();
          $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
          redirect($_SERVER['HTTP_REFERER']);
        } else {
          $this->cb->trans_rollback();
          $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
          redirect($_SERVER['HTTP_REFERER']);
        }
      }
    } else {
      $this->cb->trans_rollback();
      // $this->session->set_flashdata('swal_message', [
      //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
      //   'title' => 'Error!',
      //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
      //   'confirmButtonText' => 'Mengerti',
      // ]);
      $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
      redirect($_SERVER['HTTP_REFERER']);
    }
  }

  public function tambahCoaBB()
  {
    $no_bb = $this->input->post('no_bb');
    $nama_coa = $this->input->post('nama_coa');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);

    $this->session->set_userdata('active_tab', 'card2');

    if ($cek_no_bb) {
      if ($cek_nama_coa) {
        $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
        redirect($_SERVER['HTTP_REFERER']);
      } else {

        $substr_coa = substr($no_bb, 0, 1);

        if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
          $posisi = 'AKTIVA';
        } else {
          $posisi = 'PASIVA';
        }

        // cek tabel
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
          $tabel = "t_coa_sbb";

          $data = [
            'no_bb' => $no_bb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
          $tabel = "t_coalr_sbb";
          $data = [
            'no_lr_bb' => $no_bb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else {
          $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_bb . ' tidak sesuai.');
          redirect($_SERVER['HTTP_REFERER']);
        }


        $this->cb->trans_begin();

        $query = $this->cb->insert($tabel, $data);

        if ($query) {
          $this->cb->trans_commit();
          $this->session->set_flashdata('message_name', 'CoA ' . $no_bb . ' berhasil ditambahkan.');
          redirect($_SERVER['HTTP_REFERER']);
        } else {
          $this->cb->trans_rollback();
          $this->session->set_flashdata('message_error', 'CoA ' . $no_bb . ' gagal disimpan. Ket:' . $this->cb->error());
          redirect($_SERVER['HTTP_REFERER']);
        }
      }
    } else {
      $this->cb->trans_rollback();
      // $this->session->set_flashdata('swal_message', [
      //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
      //   'title' => 'Error!',
      //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
      //   'confirmButtonText' => 'Mengerti',
      // ]);
      $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
      redirect($_SERVER['HTTP_REFERER']);
    }
  }


  private function log_pembayaran($jenis, $id_invoice, $nominal, $keterangan)
  {
    $data = [
      'kategori_pembayaran' => $jenis,
      'id_invoice' => $id_invoice,
      'nominal_bayar' => $nominal,
      'keterangan' => $keterangan,
      'user_input' => $this->session->userdata('nip'),
    ];

    $this->M_invoice->addLogPayment($data);
  }

  function convertToNumberWithComma($formattedNumber)
  {
    // Mengganti titik sebagai pemisah ribuan dengan string kosong
    $numberWithoutThousandsSeparator = str_replace(',', '', $formattedNumber);

    // Mengganti koma sebagai pemisah desimal dengan titik
    // $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);
    $standardNumber = $numberWithoutThousandsSeparator;

    // Mengonversi string ke float
    return (float) $standardNumber;
  }

  function convertToNumber($formattedNumber)
  {
    // Mengganti titik sebagai pemisah ribuan dengan string kosong
    $numberWithoutThousandsSeparator = str_replace('.', '', $formattedNumber);

    // Mengganti koma sebagai pemisah desimal dengan titik
    $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);

    // Mengonversi string ke float
    return (float) $standardNumber;
  }

  public function print_invoice($id)
  {
    $inv =  $this->M_invoice->showById($id);
    $data = [
      'title_pdf' => 'Invoice No. ' . $inv['no_invoice'],
      'invoice' => $inv,
      'details' => $this->M_invoice->item_list($inv['Id']),
      'user' => $this->M_invoice->cek_user($inv['user_create'])
    ];

    // filename dari pdf ketika didownload
    $file_pdf = 'Invoice No. ' . $inv['no_invoice'];

    // setting paper
    $paper = 'A4';

    //orientasi paper potrait / landscape
    $orientation = "portrait";

    $html = $this->load->view('pages/financial/v_invoice_pdf', $data, true);

    // run dompdf
    $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
  }


  private function prepareCoaReport(&$data, $no_coa)
  {
    $from = $this->input->post('tgl_dari');
    $to = $this->input->post('tgl_sampai');
    $keyword = $this->input->post('keyword');
    $kode_cabang = $this->session->userdata('kode_cabang');
    // return $this->cb->where('id_cabang', $kode_cabang);

    // Saldo awal periode sebelumnya
    // $last_periode = new DateTime($from);
    // $last_periode->modify('-1 month');
    // $last_periode = $last_periode->format('Y-m');
    // $coaBefore = $this->cb->where('id_cabang', $kode_cabang)
    //   ->where('periode', $last_periode)
    //   ->get('saldo_awal')
    //   ->row_array();

    // $coaBefore = $coaBefore['coa'] ?? 0; // Pastikan tidak error jika NULL

    // $coa = json_decode($coaBefore);
    // $saldo_awal = null;

    // echo '<pre>';
    // print_r($coa);
    // echo '</pre>';
    // exit;
    // Iterasi untuk mencari saldo awal
    // if ($coa) {
    //   foreach ($coa as $item) {
    //     if ($item->no_sbb == $no_coa) {
    //       $saldo_awal = $item->saldo_awal;
    //       break;
    //     }
    //   }
    // }

    // Hitung transaksi dari 1-14 November
    // $mid_start = (new DateTime($from))->modify('first day of this month')->format('Y-m-d');
    // $mid_end = (new DateTime($from))->modify('-1 day')->format('Y-m-d');

    // $transactions_before = $this->M_coa->getCoaReport($no_coa, $mid_start, $mid_end);
    // foreach ($transactions_before as $trans) {
    //   if ($trans->akun_debit == $no_coa) {
    //     $saldo_awal += $trans->jumlah_debit;
    //   } else {
    //     $saldo_awal -= $trans->jumlah_kredit;
    //   }
    // }

    // Set saldo awal untuk 15 November
    // $data['saldo_awal'] = ($saldo_awal) ? $saldo_awal : 0;
    // print_r($saldo_awal);
    // exit;

    // Hitung transaksi dari 15 November - 31 Desember
    $data['coa'] = $this->M_coa->getCoaReport($no_coa, $from, $to, $keyword);

    $data['sum_debit'] = array_sum(array_map(function ($sum) use ($no_coa) {
      return $sum->akun_debit == $no_coa ? $sum->jumlah_debit : 0;
    }, $data['coa']));

    $data['sum_kredit'] = array_sum(array_map(function ($sum) use ($no_coa) {
      return $sum->akun_kredit == $no_coa ? $sum->jumlah_kredit : 0;
    }, $data['coa']));

    $data['title'] = "Report CoA " . $no_coa;
    $data['detail_coa'] = $this->M_coa->getCoa($no_coa);
    $data['pages'] = 'pages/financial/v_report_per_coa';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  private function processDate($dateValue)
  {
    if (is_numeric($dateValue)) {
      // Handle Excel date integer
      return DateTime::createFromFormat('U', ($dateValue - 25569) * 86400)->format('Y-m-d');
    } elseif (DateTime::createFromFormat('m/d/Y', $dateValue) !== false) {
      // Handle string date format
      return DateTime::createFromFormat('m/d/Y', $dateValue)->format('Y-m-d');
    }
    // If the date format is not recognized, return null or handle accordingly
    return null;
  }

  private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $id_invoice = NULL, $base64_data = NULL, $nama_data = NULL)
  {
    // Update coa debit 
    $update_saldo_debit = $this->update_saldo_coa($coa_debit, $nominal, 'debit');
    // Update coa kredit
    $update_saldo_kredit = $this->update_saldo_coa($coa_kredit, $nominal, 'kredit');


    // Ambil saldo debit
    $saldo_debit = $this->get_saldo_coa($coa_debit);
    // Ambil saldo kredit
    $saldo_kredit = $this->get_saldo_coa($coa_kredit);

    $dt_jurnal = [
      'tanggal' => $tanggal,
      'akun_debit' => $coa_debit,
      'jumlah_debit' => $nominal,
      'akun_kredit' => $coa_kredit,
      'jumlah_kredit' => $nominal,
      'saldo_debit' => $saldo_debit,
      'saldo_kredit' => $saldo_kredit,
      'keterangan' => $keterangan,
      'created_by' => $this->session->userdata('nip'),
      'id_invoice' => ($id_invoice) ? $id_invoice : '',
      'id_cabang' => $this->session->userdata('kode_cabang'),
      'id_company' => $this->session->userdata('user_perusahaan_id'),
      'nama_file' => $nama_data,
      'file' => $base64_data
    ];

    $this->M_coa->addJurnal($dt_jurnal);

    $data_transaksi = [
      'user_id' => $this->session->userdata('nip'),
      'tgl_trs' => date('Y-m-d H:i:s'),
      'nominal' => $nominal,
      'debet' => $coa_debit,
      'kredit' => $coa_kredit,
      'keterangan' => trim($keterangan),
      'id_cabang' => $this->session->userdata('kode_cabang'),
      'id_company' => $this->session->userdata('user_perusahaan_id')
    ];

    $this->M_coa->add_transaksi($data_transaksi);
  }


  // private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $id_invoice = NULL)
  // {
  //   $substr_coa_debit = substr($coa_debit, 0, 1);
  //   $substr_coa_kredit = substr($coa_kredit, 0, 1);

  //   $debit = $this->M_coa->cek_coa($coa_debit);
  //   $kredit = $this->M_coa->cek_coa($coa_kredit);

  //   $saldo_debit_baru = 0;
  //   $saldo_kredit_baru = 0;

  //   if ($debit['posisi'] == "AKTIVA") {
  //     $saldo_debit_baru = $debit['nominal'] + $nominal;
  //   } else if ($debit['posisi'] == "PASIVA") {
  //     $saldo_debit_baru = $debit['nominal'] - $nominal;
  //   }

  //   if ($kredit['posisi'] == "AKTIVA") {
  //     $saldo_kredit_baru = $kredit['nominal'] - $nominal;
  //   } else if ($kredit['posisi'] == "PASIVA") {
  //     $saldo_kredit_baru = $kredit['nominal'] + $nominal;
  //   }

  //   // cek tabel
  //   if ($substr_coa_debit == "1" || $substr_coa_debit == "2" || $substr_coa_debit == "3") {
  //     $tabel_debit = "t_coa_sbb";
  //     $kolom_debit = "no_sbb";
  //   } else {
  //     $tabel_debit = "t_coalr_sbb";
  //     $kolom_debit = "no_lr_sbb";
  //   }

  //   if ($substr_coa_kredit == "1" || $substr_coa_kredit == "2" || $substr_coa_debit == "3") {
  //     $tabel_kredit = "t_coa_sbb";
  //     $kolom_kredit = "no_sbb";
  //   } else {
  //     $tabel_kredit = "t_coalr_sbb";
  //     $kolom_kredit = "no_lr_sbb";
  //   }

  //   $data_debit = [
  //     'nominal' => $saldo_debit_baru
  //   ];
  //   $data_kredit = [
  //     'nominal' => $saldo_kredit_baru
  //   ];

  //   $this->M_coa->update_nominal_coa($coa_debit, $data_debit, $kolom_debit, $tabel_debit);

  //   $this->M_coa->update_nominal_coa($coa_kredit, $data_kredit, $kolom_kredit, $tabel_kredit);

  //   $dt_jurnal = [
  //     'tanggal' => $tanggal,
  //     'akun_debit' => $coa_debit,
  //     'jumlah_debit' => $nominal,
  //     'akun_kredit' => $coa_kredit,
  //     'jumlah_kredit' => $nominal,
  //     'saldo_debit' => $saldo_debit_baru,
  //     'saldo_kredit' => $saldo_kredit_baru,
  //     'keterangan' => $keterangan,
  //     'created_by' => $this->session->userdata('nip'),
  //     'id_invoice' => ($id_invoice) ? $id_invoice : '',
  //     'id_cabang' => $this->session->userdata('kode_cabang')
  //   ];

  //   $this->M_coa->addJurnal($dt_jurnal);

  //   $data_transaksi = [
  //     'user_id' => $this->session->userdata('nip'),
  //     'tgl_trs' => date('Y-m-d H:i:s'),
  //     'nominal' => $nominal,
  //     'debet' => $coa_debit,
  //     'kredit' => $coa_kredit,
  //     'keterangan' => trim($keterangan),
  //     'id_cabang' => $this->session->userdata('kode_cabang')
  //   ];

  //   $this->M_coa->add_transaksi($data_transaksi);
  // }

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
    if (!$row) return FALSE;

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
  public function ajax_template_coa_list()
  {
    $list = $this->M_coa->get_datatables1();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $cat) {
      $no++;
      $row = array();
      $row[] = $no;

      // Store data in data attributes for easy retrieval with JavaScript
      $row[] = '<span data-no_bb="' . $cat->no_bb . '">' . $cat->no_bb . '</span>';
      $row[] = '<span data-no_sbb="' . $cat->no_sbb . '">' . $cat->no_sbb . '</span>';
      $row[] = '<span data-nama_coa="' . $cat->nama_perkiraan . '">' . $cat->nama_perkiraan . '</span>';

      // Input field for saldo_awal
      $row[] = '<input type="text" name="saldo_awal" class="form-control uang saldo-awal-input" value="0">';

      // Action button with a specific class for event delegation
      $row[] = '<button class="btn btn-primary submit-coa-btn" type="button">Buat</button>'; // type="button" to prevent default form submission if any parent form exists

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_coa->count_all1(),
      "recordsFiltered" => $this->M_coa->count_filtered1(),
      "data" => $data,
    );
    echo json_encode($output);
  }
  public function ajax_template_coa_bb_list()
  {
    $list = $this->M_coa->get_datatables1_bb();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $cat) {
      $no++;
      $row = array();
      $row[] = $no;

      // Store data in data attributes for easy retrieval with JavaScript
      $row[] = '<span data-no_bb="' . $cat->no_bb . '">' . $cat->no_bb . '</span>';
      $row[] = '<span data-nama_coa="' . $cat->nama_perkiraan . '">' . $cat->nama_perkiraan . '</span>';

      // Action button with a specific class for event delegation
      $row[] = '<button class="btn btn-primary submit-coa-bb-btn" type="button">Buat</button>'; // type="button" to prevent default form submission if any parent form exists

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->M_coa->count_all1_bb(),
      "recordsFiltered" => $this->M_coa->count_filtered1_bb(),
      "data" => $data,
    );
    echo json_encode($output);
  }

  public function tambahCoaAjax()
  {
    $no_bb = $this->input->post('no_bb');
    $no_sbb = $this->input->post('no_sbb');
    $nama_coa = $this->input->post('nama_coa');
    $saldo_awal = $this->input->post('saldo_awal');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
    $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);

    $this->session->set_userdata('active_tab', 'card1');

    if ($cek_no_bb) {
      if ($cek_no_sbb) {
        // $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'No. ' . $no_sbb . ' sudah ada',
          'reload' => base_url('financial/list_coa')
        ];
      } else if ($cek_nama_coa) {
        // $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
        // redirect($_SERVER['HTTP_REFERER']);

        $response = [
          'status' => "error",
          'msg' => 'CoA ' . $nama_coa . ' sudah ada',
          'reload' => base_url('financial/list_coa')
        ];
      } else {

        $substr_coa = substr($no_sbb, 0, 1);

        if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
          $posisi = 'AKTIVA';
        } else {
          $posisi = 'PASIVA';
        }

        // cek tabel
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
          $tabel = "t_coa_sbb";

          $data = [
            'no_bb' => $no_bb,
            'no_sbb' => $no_sbb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'nominal' => $this->_parse_rupiah($saldo_awal),
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
          $tabel = "t_coalr_sbb";
          $data = [
            'no_lr_bb' => $no_bb,
            'no_lr_sbb' => $no_sbb,
            'nama_perkiraan' => $nama_coa,
            'posisi' => $posisi,
            'nominal' => $this->_parse_rupiah($saldo_awal),
            'id_cabang' => $this->session->userdata('kode_cabang'),
            'id_company' => $this->session->userdata('user_perusahaan_id'),
          ];
        } else {
          // $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "error",
            'msg' => 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.',
            'reload' => base_url('financial/list_coa')
          ];
        }


        $this->cb->trans_begin();

        $query = $this->cb->insert($tabel, $data);

        if ($query) {
          $this->cb->trans_commit();
          // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "success",
            'msg' => 'CoA ' . $no_sbb . ' berhasil ditambahkan.',
            'reload' => base_url('financial/list_coa')
          ];
        } else {
          $this->cb->trans_rollback();
          // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
          // redirect($_SERVER['HTTP_REFERER']);
          $response = [
            'status' => "error",
            'msg' => 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error(),
            'reload' => base_url('financial/list_coa')
          ];
        }
      }
    } else {
      $this->cb->trans_rollback();
      $response = [
        'status' => "error",
        'msg' => 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu',
        'reload' => base_url('financial/list_coa')
      ];
    }
    echo json_encode($response);
  }
  public function tambahCoaBBAjax()
  {
    $no_bb = $this->input->post('no_bb');
    $nama_coa = $this->input->post('nama_coa');

    $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
    $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);

    $this->session->set_userdata('active_tab', 'card2');

    if ($cek_no_bb) {
      // $this->session->set_flashdata('message_error', 'No. ' . $no_sbb . ' sudah ada');
      // redirect($_SERVER['HTTP_REFERER']);
      $response = [
        'status' => "error",
        'msg' => 'No. ' . $no_bb . ' sudah ada',
        'reload' => base_url('financial/list_coa')
      ];
    } else if ($cek_nama_coa) {
      // $this->session->set_flashdata('message_error', 'CoA ' . $nama_coa . ' sudah ada');
      // redirect($_SERVER['HTTP_REFERER']);

      $response = [
        'status' => "error",
        'msg' => 'CoA ' . $nama_coa . ' sudah ada',
        'reload' => base_url('financial/list_coa')
      ];
    } else {

      $substr_coa = substr($no_bb, 0, 1);

      if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
        $posisi = 'AKTIVA';
      } else {
        $posisi = 'PASIVA';
      }

      // cek tabel
      if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
        $tabel = "t_coa_bb";

        $data = [
          'no_bb' => $no_bb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'id_company' => $this->session->userdata('user_perusahaan_id'),
        ];
      } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
        $tabel = "t_coalr_bb";
        $data = [
          'no_lr_bb' => $no_bb,
          'nama_perkiraan' => $nama_coa,
          'posisi' => $posisi,
          'id_company' => $this->session->userdata('user_perusahaan_id'),
        ];
      } else {
        // $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'Format nomor CoA ' . $no_bb . ' tidak sesuai.',
          'reload' => base_url('financial/list_coa')
        ];
      }


      $this->cb->trans_begin();

      $query = $this->cb->insert($tabel, $data);

      if ($query) {
        $this->cb->trans_commit();
        // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "success",
          'msg' => 'CoA ' . $no_bb . ' berhasil ditambahkan.',
          'reload' => base_url('financial/list_coa')
        ];
      } else {
        $this->cb->trans_rollback();
        // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
        // redirect($_SERVER['HTTP_REFERER']);
        $response = [
          'status' => "error",
          'msg' => 'CoA ' . $no_bb . ' gagal disimpan. Ket:' . $this->cb->error(),
          'reload' => base_url('financial/list_coa')
        ];
      }
    }
    echo json_encode($response);
  }
  public function ambil_semua_coa()
  {
    $this->load->view('loading');

    $this->session->set_userdata('active_tab', 'card1');

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang = $this->cb->get()->row();

    if ($cabang->ambil_semua_coa == 0) {
      $this->cb->select('no_bb, no_sbb, nama_perkiraan');
      $this->cb->from('t_coa_sbb_gabungan');
      $this->cb->group_by('no_bb, no_sbb'); // Group by the columns that define uniqueness

      $all_coa = $this->cb->get()->result();

      foreach ($all_coa as $coas) {

        $no_bb = $coas->no_bb;
        $no_sbb = $coas->no_sbb;
        // $nama_bb = $coas->nama_bb;
        $nama_coa = $coas->nama_perkiraan;
        $saldo_awal = 0;
        $cek_no_sbb = $this->M_coa->isAvailable('no_sbb', $no_sbb);
        $cek_nama_coa = $this->M_coa->isAvailable('nama_perkiraan', $nama_coa);
        $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
        if ($cek_no_bb) {
          if ($cek_no_sbb) {
            continue;
            // } else if ($cek_nama_coa) {
            //   continue;
          } else {

            $substr_coa = substr($no_sbb, 0, 1);

            if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
              $posisi = 'AKTIVA';
            } else {
              $posisi = 'PASIVA';
            }

            // cek tabel
            if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
              $tabel = "t_coa_sbb";

              $data = [
                'no_bb' => $no_bb,
                'no_sbb' => $no_sbb,
                'nama_perkiraan' => $nama_coa,
                'posisi' => $posisi,
                'nominal' => $this->_parse_rupiah($saldo_awal),
                'id_cabang' => $this->session->userdata('kode_cabang'),
                'id_company' => $this->session->userdata('user_perusahaan_id'),
              ];
            } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
              $tabel = "t_coalr_sbb";
              $data = [
                'no_lr_bb' => $no_bb,
                'no_lr_sbb' => $no_sbb,
                'nama_perkiraan' => $nama_coa,
                'posisi' => $posisi,
                'nominal' => $this->_parse_rupiah($saldo_awal),
                'id_cabang' => $this->session->userdata('kode_cabang'),
                'id_company' => $this->session->userdata('user_perusahaan_id'),
              ];
            } else {
              $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_sbb . ' tidak sesuai.');
              redirect($_SERVER['HTTP_REFERER']);
            }


            $this->cb->trans_begin();

            $query = $this->cb->insert($tabel, $data);

            if ($query) {
              $this->cb->trans_commit();
              $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
              // redirect($_SERVER['HTTP_REFERER']);
            } else {
              $this->cb->trans_rollback();
              $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
            }
          }
        } else {
          $this->cb->trans_rollback();
          // $this->session->set_flashdata('swal_message', [
          //   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
          //   'title' => 'Error!',
          //   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
          //   'confirmButtonText' => 'Mengerti',
          // ]);
          $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
          redirect($_SERVER['HTTP_REFERER']);
        }
      }

      $cabang_data = array(
        'ambil_semua_coa' => 1,
      );
      // Assuming 'users' table is in the default database
      $this->cb->where('uid', $this->session->userdata('kode_cabang')); // Assuming 'id' is the primary key for users table
      $this->cb->update('t_cabang', $cabang_data);
      $this->session->set_flashdata('message_name', 'Semua COA berhasil ditambahkan.');
    }
    redirect($_SERVER['HTTP_REFERER']);
  }
  public function ambil_semua_coa_bb()
  {
    $this->load->view('loading');

    $this->session->set_userdata('active_tab', 'card2');

    $this->db->from('utility');
    $this->db->where('Id', $this->session->userdata('user_perusahaan_id'));
    $cabang = $this->db->get()->row();

    if ($cabang->ambil_semua_coa_bb == 0) {

      $this->cb->select('no_bb, nama_perkiraan');
      $this->cb->from('t_coa_bb_gabungan');
      $this->cb->group_by('no_bb'); // Group by the columns that define uniqueness

      $all_coa = $this->cb->get()->result();

      foreach ($all_coa as $coas) {

        $no_bb = $coas->no_bb;
        // $nama_bb = $coas->nama_bb;
        $nama_coa = $coas->nama_perkiraan;
        $cek_no_bb = $this->M_coa->isAvailableBB('no_bb', $no_bb);
        $cek_nama_coa = $this->M_coa->isAvailableBB('nama_perkiraan', $nama_coa);
        if ($cek_no_bb) {
          continue;
          // } else if ($cek_nama_coa) {
          //   continue;
        } else {

          $substr_coa = substr($no_bb, 0, 1);

          if ($substr_coa == "1" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "5" || $substr_coa == "6") {
            $posisi = 'AKTIVA';
          } else {
            $posisi = 'PASIVA';
          }

          // cek tabel
          if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $tabel = "t_coa_bb";

            $data = [
              'no_bb' => $no_bb,
              'nama_perkiraan' => $nama_coa,
              'posisi' => $posisi,
              'id_company' => $this->session->userdata('user_perusahaan_id'),
            ];
          } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $tabel = "t_coalr_bb";
            $data = [
              'no_lr_bb' => $no_bb,
              'nama_perkiraan' => $nama_coa,
              'posisi' => $posisi,
              'id_company' => $this->session->userdata('user_perusahaan_id'),
            ];
          } else {
            $this->session->set_flashdata('message_error', 'Format nomor CoA ' . $no_bb . ' tidak sesuai.');
            redirect($_SERVER['HTTP_REFERER']);
          }

          var_dump($substr_coa);


          $this->cb->trans_begin();

          $query = $this->cb->insert($tabel, $data);

          if ($query) {
            $this->cb->trans_commit();
            // $this->session->set_flashdata('message_name', 'CoA ' . $no_sbb . ' berhasil ditambahkan.');
            // redirect($_SERVER['HTTP_REFERER']);
          } else {
            $this->cb->trans_rollback();
            // $this->session->set_flashdata('message_error', 'CoA ' . $no_sbb . ' gagal disimpan. Ket:' . $this->cb->error());
          }
        }
      }
      $company_data = array(
        'ambil_semua_coa_bb' => 1,
      );
      // Assuming 'users' table is in the default database
      $this->db->where('Id', $this->session->userdata('user_perusahaan_id')); // Assuming 'id' is the primary key for users table
      $this->db->update('utility', $company_data);
    }
    redirect($_SERVER['HTTP_REFERER']);
  }

  public function ajax_edit_coa($no_sbb, $id_cabang)
  {
    $this->cb->select('*');
    $this->cb->from('v_coa_all');
    $this->cb->where('no_sbb', $no_sbb);
    $this->cb->where('id_cabang', $id_cabang);
    $get_coa = $this->cb->get()->row();

    if ($get_coa->table_source == "t_coa_sbb") {
      $this->cb->select('*');
      $this->cb->from($get_coa->table_source);
      $this->cb->where('no_sbb', $no_sbb);
      $this->cb->where('id_cabang', $id_cabang);
    } else {

      $this->cb->select('*');
      $this->cb->from($get_coa->table_source);
      $this->cb->where('no_lr_sbb', $no_sbb);
      $this->cb->where('id_cabang', $id_cabang);
    }
    $data = $this->cb->get()->row();
    $response = [
      'coa_data' => $get_coa, // This will contain the COA object/array
      'data' => $data // This will contain the COA object/array
    ];
    echo json_encode($response);
  }

  public function update_coa()
  {

    $tabel = $this->input->post('table_coa');

    $data_update = [
      'nama_perkiraan'           => $this->input->post('nama_perkiraan'),
      'nominal'           => $this->input->post('nominal'),
    ];

    $this->cb->update($tabel, $data_update, array('id' => $this->input->post('id_coa')));

    redirect('financial/list_coa');
  }

  public function buat_saldo_awal()
  {
    $periode_current = date('Y-m'); // e.g., '2025-07'
    $periode = date('Y-m', strtotime('-1 month')); // e.g., '2025-06'

    // $cek = $this->M_coa->cek_saldo_awal($periode);

    $date = new DateTime($periode);

    $bulan = $date->format('m');
    $tahun = $date->format('Y');

    $last_periode = new DateTime($periode);
    $last_periode = $last_periode->modify('-1 month');
    $last_periode = $last_periode->format('Y-m');

    $getLastPeriod = $this->M_coa->cek_saldo_awal($last_periode);

    // $this->db->from('utility');
    // $this->db->where('id', $this->session->userdata('user_perusahaan_id'));
    // $perusahaans = $this->db->get()->row();

    // if ($perusahaans->generate_sawal == 0) {

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabangsss = $this->cb->get()->row();
    if ($cabangsss->generate_sawal == 0) {

      $Sumactiva = $this->M_coa->get_sum_coa_activa_by_cabang();
      $sum_activa = $Sumactiva->nominal;
      $Sumpasiva = $this->M_coa->get_sum_coa_pasiva_by_cabang();
      $sum_pasiva = $Sumpasiva->nominal;

      $balancing = $sum_activa - $sum_pasiva;
      // $pendapatan = $this->M_coa->get_sum_coa_pasiva_coalr_by_cabang();
      // $beban = $this->M_coa->get_sum_coa_activa_coalr_by_cabang();

      // $laba = $pendapatan->nominal - $beban->nominal;
      if ($balancing == 0) {

        // $activa = $this->M_coa->get_coa_activa_by_cabang();
        // $pasiva = $this->M_coa->get_coa_pasiva_by_cabang();
        $list_coa = $this->M_coa->list_coa_with_nominal();

        $updated_saldo_awal = [];

        if (!empty($list_coa)) {
          // $coaLastPeriod = json_decode($getLastPeriod['coa']);
          // $saldo_bulan_ini = $this->M_coa->calculate_saldo_awal($bulan, $tahun);

          $saldo_awal_map = [];
          // foreach ($coaLastPeriod as $saldo_awal) {
          //   $saldo_awal_map[$saldo_awal->no_sbb] = $saldo_awal;
          // }

          // foreach ($saldo_bulan_ini as $saldo_baru) {
          //   if (isset($saldo_awal_map[$saldo_baru->no_sbb])) {
          //     $saldo_awal_map[$saldo_baru->no_sbb]->saldo_awal += (float) $saldo_baru->saldo_awal;
          //   } else {
          // $saldo_awal_map[$saldo_baru->no_sbb] = (object) [
          //   'no_sbb' => $saldo_baru->no_sbb,
          //   'saldo_awal' => (float) $saldo_baru->saldo_awal,
          //   'posisi' => $saldo_baru->posisi,
          //   'table_source' => $saldo_baru->table_source,
          // ];
          //   }
          // }

          foreach ($list_coa as $l) {
            $saldo_awal_map[$l->no_sbb] = (object) [
              'no_sbb' => $l->no_sbb,
              'saldo_awal' => (float) $l->nominal,
              'posisi' => $l->posisi,
              'table_source' => $l->table_source,
            ];
          }
          $updated_saldo_awal = array_values($saldo_awal_map);
        }
        $nextMonth = ($date->modify('+1 month'));
        $nextMonth = $date->format('Y-m');

        $data = [
          'periode' => $periode,
          'created_by' => $this->session->userdata('nip'),
          'created_at' => date('Y-m-d H:i:s'),
          'slug' => 'saldo-awal-' . $nextMonth,
          'coa' => json_encode($updated_saldo_awal),
          'keterangan' => 'Saldo awal ' . format_indo($nextMonth),
          'id_cabang' => $this->session->userdata('kode_cabang'),
          'id_company' => $this->session->userdata('user_perusahaan_id')
        ];

        $this->M_coa->insert_saldo_awal($data);

        // $utility_data = array(
        //   'generate_sawal' => 1,
        // );
        // Assuming 'users' table is in the default database
        // $this->db->where('id', $this->session->userdata('user_perusahaan_id')); // Assuming 'id' is the primary key for users table
        // $this->db->update('utility', $utility_data);

        $cabang_data = array(
          'generate_sawal' => 1,
        );
        // Assuming 'users' table is in the default database
        $this->cb->where('uid', $this->session->userdata('kode_cabang')); // Assuming 'id' is the primary key for users table
        $this->cb->update('t_cabang', $cabang_data);

        // $this->session->set_flashdata('success', 'Saldo awal periode ' . format_indo($nextMonth) . ' berhasil ditetapkan');
        $this->session->set_flashdata('swal_message', [
          'icon' => 'success', // or 'success', 'warning', 'info', 'question'
          'title' => 'Berhasil!',
          'text' => 'Saldo awal periode ' . format_indo($nextMonth) . ' berhasil ditetapkan',
          'confirmButtonText' => 'Mengerti',
        ]);
      } else {
        // $this->session->set_flashdata('error', 'Saldo Tidak Balance, Silahkan di cek kembali');
        $this->session->set_flashdata('swal_message', [
          'icon' => 'error', // or 'success', 'warning', 'info', 'question'
          'title' => 'Error!',
          'text' => 'Saldo Tidak Balance, Silahkan di cek kembali',
          'confirmButtonText' => 'Mengerti',
        ]);
      }
    } else {
      // $this->session->set_flashdata('error', 'Tidak Bisa Membuat Sawal.');
      $this->session->set_flashdata('swal_message', [
        'icon' => 'error', // or 'success', 'warning', 'info', 'question'
        'title' => 'Error!',
        'text' => 'Tidak Bisa Membuat Sawal',
        'confirmButtonText' => 'Mengerti',
      ]);
    }


    redirect($_SERVER['HTTP_REFERER']);
  }

  public function list_customer()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search')) ? $this->session->userdata('search') : '');
    if ($keyword === null) $keyword = $this->session->userdata('search');
    else $this->session->set_userdata('search', $keyword);

    $cabang_now = $this->session->userdata('kode_cabang');

    $config = [
      'base_url' => site_url('customer'),
      'total_rows' => $this->M_customer->count($keyword, $cabang_now, 'customer'),
      'per_page' => 25,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => 'page',
    ];

    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = "<i class='fe fe-chevrons-left'></i>";
    $config['last_link'] = "<i class='fe fe-chevrons-right'></i>";
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = "<i class='fe fe-chevron-left'></i>";
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = "<i class='fe fe-chevron-right'></i>";
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    $this->pagination->initialize($config);

    // $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;
    $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    // $invoices = $this->m_invoice->list_invoice($config["per_page"], $page, $keyword);
    $data['customers'] = $this->M_customer->list_customer_paginate($config["per_page"], $page, $keyword, $cabang_now);

    $data['page'] = $page;
    $data['title'] = "Customer";
    $data['pages'] = "pages/customer/v_list_customer";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/customer/s_customer';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);


    // $this->load->view('customer', $data);
  }

  public function index()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search')) ? $this->session->userdata('search') : '');
    if ($keyword === null) $keyword = $this->session->userdata('search');
    else $this->session->set_userdata('search', $keyword);

    $cabang_now = $this->session->userdata('kode_cabang');

    $config = [
      'base_url' => site_url('customer'),
      'total_rows' => $this->M_customer->count($keyword, $cabang_now, 'customer'),
      'per_page' => 25,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => 'page',
    ];

    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = "<i class='fe fe-chevrons-left'></i>";
    $config['last_link'] = "<i class='fe fe-chevrons-right'></i>";
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = "<i class='fe fe-chevron-left'></i>";
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = "<i class='fe fe-chevron-right'></i>";
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    $this->pagination->initialize($config);

    // $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;
    $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    // $invoices = $this->m_invoice->list_invoice($config["per_page"], $page, $keyword);
    $data['customers'] = $this->M_customer->list_customer_paginate($config["per_page"], $page, $keyword, $cabang_now);

    $data['page'] = $page;
    $data['title'] = "Customer";
    $data['pages'] = "pages/customer/v_list_customer";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/customer/s_customer';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);


    // $this->load->view('customer', $data);
  }

  public function store_customer()
  {
    $nama_customer = $this->input->post('nama_customer');
    $slug = url_title($nama_customer, 'dash', true);

    $data = [
      'nama_customer' => strtoupper($nama_customer),
      'alamat_customer' => $this->input->post('alamat_customer'),
      'telepon_customer' => $this->input->post('telepon_customer'),
      // 'status_customer' => $this->input->post('status_customer'),
      'slug' => $slug,
      'id_cabang' => $this->session->userdata('kode_cabang'),
      'id_company' => $this->session->userdata('user_perusahaan_id')
    ];

    $old_slug = $this->uri->segment(3);

    if ($old_slug) {
      $this->M_customer->update($data, $old_slug);

      $this->session->set_flashdata('message_name', 'The customer has been successfully updated.');
    } else {
      if ($this->M_customer->is_available($slug)) {
        $this->session->set_flashdata('message_error', 'Customer ' . $nama_customer . ' sudah ada.');
      } else {
        $this->M_customer->insert($data);

        $this->session->set_flashdata('message_name', 'The customer has been successfully added.');
      }
    }

    // redirect("financial/list_customer");
    redirect("customer");
  }

  public function reset_customer()
  {
    $this->session->unset_userdata('search');
    redirect('customer');
  }

  public function consolidation()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $button_sbm = $this->input->post('button_sbm');
    $nip = $this->session->userdata('nip');

    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};

    $per_tanggal = ($this->input->post('per_tanggal') ? $this->input->post('per_tanggal') : date('Y-m-d'));

    $data = [
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'per_tanggal' => $per_tanggal
    ];

    $jenis_laporan = $this->input->post('jenis_laporan');

    // print_r($jenis_laporan);
    // exit;
    if ($jenis_laporan) {
      if ($jenis_laporan == "neraca") {
        $this->prepareNeracaConsoleReportByDate($data, $per_tanggal, $button_sbm);
      } else if ($jenis_laporan == "laba_rugi") {
        $this->prepareLabaRugiConsoleReportByDate($data, $per_tanggal, $button_sbm);
      }
      // else if ($jenis_laporan == "laba_rugi_tanpa_sawal") {
      //   $this->prepareLabaRugiConsoleReportByDateNoSawal($data, $per_tanggal, $button_sbm);
      // } else if ($jenis_laporan == "neraca_bb") {
      //   $this->prepareNeracaBbConsoleReportByDate($data, $per_tanggal, $button_sbm);
      // } else if ($jenis_laporan == "lr_bb") {
      //   $this->prepareLrBbConsoleReportByDate($data, $per_tanggal, $button_sbm);
      // }
    } else {
      $this->prepareNeracaConsoleReportByDate($data, $per_tanggal);
    }
  }

  private function prepareNeracaConsoleReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);
    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    // Step 1: get id perusahaan berdasarkan akun user
    $id_company = $this->session->userdata('user_perusahaan_id');

    // Step 2: get saldo awal berdasarkan id_company dan periode
    $coas = $this->cb->select('coa')->where(['id_company' => $id_company, 'periode' => $periode])->get('saldo_awal')->result();

    // Step 3: Gabungkan dan jumlahkan saldo_awal untuk no_sbb yang sama
    $mergedCoa = [];

    foreach ($coas as $coa) {
      // Decode JSON dari setiap record
      $coaArray = json_decode($coa->coa, true);

      // Loop setiap item dalam array coa
      foreach ($coaArray as $item) {
        $no_sbb = $item['no_sbb'];

        if (isset($mergedCoa[$no_sbb])) {
          // Kalau no_sbb sudah ada, jumlahkan saldo_awal
          $mergedCoa[$no_sbb]['saldo_awal'] += $item['saldo_awal'];
        } else {
          // Kalau belum ada, masukkan item baru
          $mergedCoa[$no_sbb] = $item;
        }
      }
    }

    // Convert ke indexed array (opsional, kalau mau tetap associative skip ini)
    $coaLastPeriod = array_values($mergedCoa);

    // part activa
    $filteredCoaAktiva = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'AKTIVA' && $item['table_source'] === 't_coa_sbb';
    });

    $activa = $this->M_coa->getNeracaConsolByDate('t_coa_sbb', 'AKTIVA', $tanggal, $id_company);

    $combinedActiva = [];

    foreach ($activa as $item) {
      if (!isset($combinedActiva[$item->no_sbb])) {
        $combinedActiva[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedActiva[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaAktiva as $item) {
      if (!isset($combinedActiva[$item['no_sbb']])) {
        $combinedActiva[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedActiva[$item['no_sbb']]->saldo_awal += $item['saldo_awal'];
      }
    }

    usort($combinedActiva, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_activa = array_sum(array_column($combinedActiva, 'saldo_awal'));


    // part pasiva
    $filteredCoaPasiva = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'PASIVA' && $item['table_source'] === 't_coa_sbb';
    });

    $pasiva = $this->M_coa->getNeracaConsolByDate('t_coa_sbb', 'PASIVA', $tanggal, $id_company);
    $combinedPasiva = [];

    foreach ($pasiva as $item) {
      if (!isset($combinedPasiva[$item->no_sbb])) {
        $combinedPasiva[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedPasiva[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaPasiva as $item) {
      if (!isset($combinedPasiva[$item['no_sbb']])) {
        $combinedPasiva[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedPasiva[$item['no_sbb']]->saldo_awal += $item['saldo_awal'];
      }
    }

    usort($combinedPasiva, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_pasiva = array_sum(array_column($combinedPasiva, 'saldo_awal'));


    // part pendapatan
    $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'PASIVA' && $item['table_source'] === 't_coalr_sbb';
    });

    $pendapatan = $this->M_coa->getNeracaConsolByDate('t_coalr_sbb', 'PASIVA', $tanggal, $id_company);
    $combinedPendapatan = [];

    foreach ($pendapatan as $item) {
      if (!isset($combinedPendapatan[$item->no_sbb])) {
        $combinedPendapatan[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedPendapatan[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaPendapatan as $item) {
      if (!isset($combinedPendapatan[$item['no_sbb']])) {
        $combinedPendapatan[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedPendapatan[$item['no_sbb']]->saldo_awal += $item['saldo_awal'];
      }
    }

    usort($combinedPendapatan, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));


    // part beban
    $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'AKTIVA' && $item['table_source'] === 't_coalr_sbb';
    });

    $beban = $this->M_coa->getNeracaConsolByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $id_company);
    $combinedBeban = [];

    foreach ($beban as $item) {
      if (!isset($combinedBeban[$item->no_sbb])) {
        $combinedBeban[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedBeban[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaBeban as $item) {
      if (!isset($combinedBeban[$item['no_sbb']])) {
        $combinedBeban[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedBeban[$item['no_sbb']]['saldo_awal'] += $item['saldo_awal'];
      }
    }

    usort($combinedBeban, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

    $laba = $total_pendapatan - $total_beban;
    $sum_pasiva = $total_pasiva + $laba;

    $data['activa'] = $combinedActiva;
    $data['sum_activa'] = $total_activa;
    $data['pasiva'] = $combinedPasiva;
    $data['laba'] = $laba;
    $data['sum_pasiva'] = $sum_pasiva;
    $data['neraca'] = $sum_pasiva - $total_activa;

    $data['title'] = 'Neraca per tanggal ' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['pages'] = 'pages/financial/v_neraca_consol_by_date';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Neraca SBB")
        ->setSubject("Neraca SBB")
        ->setDescription("Neraca SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Neraca SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Neraca SBB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'AKTIVA');
      $sheet->setCellValue('E2', 'PASIVA');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_activa ?? 0);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $sum_pasiva ?? 0);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;

      foreach ($combinedActiva as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
          $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
          $numrowActiva++;
        endif;
      }


      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($combinedPasiva as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
          $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
          $numrowPasiva++;
        endif;
      }

      $sheet->setCellValue('E' . $numrowPasiva, '3103001');
      $sheet->setCellValue('F' . $numrowPasiva, 'LABA TAHUN BERJALAN');
      $sheet->setCellValue('G' . $numrowPasiva, $laba);

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Neraca per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  private function prepareLabaRugiConsoleReportByDate($data, $tanggal, $button_sbm = null)
  {
    $date = new DateTime($tanggal);
    $date->modify('first day of previous month');
    $periode = $date->format('Y-m');

    // Step 1: get id perusahaan berdasarkan akun user
    $id_company = $this->session->userdata('user_perusahaan_id');

    // Step 2: get saldo awal berdasarkan id_company dan periode
    $coas = $this->cb->select('coa')->where(['id_company' => $id_company, 'periode' => $periode])->get('saldo_awal')->result();

    // Step 3: Gabungkan dan jumlahkan saldo_awal untuk no_sbb yang sama
    $mergedCoa = [];

    foreach ($coas as $coa) {
      // Decode JSON dari setiap record
      $coaArray = json_decode($coa->coa, true);

      // Loop setiap item dalam array coa
      foreach ($coaArray as $item) {
        $no_sbb = $item['no_sbb'];

        if (isset($mergedCoa[$no_sbb])) {
          // Kalau no_sbb sudah ada, jumlahkan saldo_awal
          $mergedCoa[$no_sbb]['saldo_awal'] += $item['saldo_awal'];
        } else {
          // Kalau belum ada, masukkan item baru
          $mergedCoa[$no_sbb] = $item;
        }
      }
    }

    // Convert ke indexed array (opsional, kalau mau tetap associative skip ini)
    $coaLastPeriod = array_values($mergedCoa);

    // part pendapatan
    $filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'PASIVA' && $item['table_source'] === 't_coalr_sbb';
    });

    $pendapatan = $this->M_coa->getNeracaConsolByDate('t_coalr_sbb', 'PASIVA', $tanggal, $id_company);
    $combinedPendapatan = [];

    foreach ($pendapatan as $item) {
      if (!isset($combinedPendapatan[$item->no_sbb])) {
        $combinedPendapatan[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedPendapatan[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaPendapatan as $item) {
      if (!isset($combinedPendapatan[$item['no_sbb']])) {
        $combinedPendapatan[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedPendapatan[$item['no_sbb']]['saldo_awal'] += $item['saldo_awal'];
      }
    }

    usort($combinedPendapatan, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));


    // part beban
    $filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
      return $item['posisi'] === 'AKTIVA' && $item['table_source'] === 't_coalr_sbb';
    });

    $beban = $this->M_coa->getNeracaConsolByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $id_company);
    $combinedBeban = [];

    foreach ($beban as $item) {
      if (!isset($combinedBeban[$item->no_sbb])) {
        $combinedBeban[$item->no_sbb] = (object) [
          'no_sbb' => $item->no_sbb,
          'saldo_awal' => $item->saldo_awal,
        ];
      } else {
        $combinedBeban[$item->no_sbb]['saldo_awal'] += $item->saldo_awal;
      }
    }

    foreach ($filteredCoaBeban as $item) {
      if (!isset($combinedBeban[$item['no_sbb']])) {
        $combinedBeban[$item['no_sbb']] = (object) [
          'no_sbb' => $item['no_sbb'],
          'saldo_awal' => $item['saldo_awal'],
        ];
      } else {
        $combinedBeban[$item['no_sbb']]['saldo_awal'] += $item['saldo_awal'];
      }
    }

    usort($combinedBeban, function ($a, $b) {
      return strcmp($a->no_sbb, $b->no_sbb);
    });
    $total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));


    $data['biaya'] = $combinedBeban;
    $data['pendapatan'] = $combinedPendapatan;
    $data['sum_biaya'] = $total_beban;
    $data['sum_pendapatan'] = $total_pendapatan;
    $data['total_pendapatan'] = $total_pendapatan - $total_beban;

    $data['title'] = 'Laba rugi per tanggal ' . format_indo($tanggal);
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/financial/s_financial';
    $data['pages'] = 'pages/financial/v_laba_rugi_consol_by_date';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('Bariskode')
        ->setLastModifiedBy('Bariskode')
        ->setTitle("Laba rugi SBB")
        ->setSubject("Laba rugi SBB")
        ->setDescription("Laba rugi SBB per tanggal " . format_indo($tanggal))
        ->setKeywords("Laba rugi SBB");

      // Merge cells untuk header utama
      $sheet->mergeCells('A1:G1');
      $sheet->mergeCells('A2:C2');
      $sheet->mergeCells('E2:G2');

      // Isi data header
      $sheet->setCellValue('A1', 'Laba rugi SBB per tanggal ' . format_indo($tanggal));
      $sheet->setCellValue('A2', 'BEBAN');
      $sheet->setCellValue('E2', 'PENDAPATAN');
      $sheet->setCellValue('B3', 'Total: ');
      $sheet->setCellValue('C3', $total_beban);
      $sheet->setCellValue('F3', 'Total: ');
      $sheet->setCellValue('G3', $total_pendapatan);

      // Buat sub-header untuk tabel
      $sheet->setCellValue('A4', 'No. CoA');
      $sheet->setCellValue('B4', 'Nama CoA');
      $sheet->setCellValue('C4', 'Nominal');
      $sheet->setCellValue('E4', 'No. CoA');
      $sheet->setCellValue('F4', 'Nama CoA');
      $sheet->setCellValue('G4', 'Nominal');

      // Tambahkan data Aktiva
      $numrowActiva = 5;
      foreach ($combinedBeban as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
          $sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
          $numrowActiva++;
        endif;
      }

      // Tambahkan data Pasiva
      $numrowPasiva = 5;
      foreach ($combinedPendapatan as $t) {
        $coa = $this->M_coa->getCoa($t->no_sbb);
        if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0) :
          $sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
          $sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
          $sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
          $numrowPasiva++;
        endif;
      }

      // Set auto size untuk semua kolom
      foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
      }

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="Laba rugi per tanggal ' . format_indo($tanggal) . '.xls"');
      header('Cache-Control: max-age=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');

      $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
      $objWriter->save('php://output');
      exit;
    } else {
      $this->load->view('index', $data);
    }
  }

  public function download_file($record_id)
  {
    // 1. Load the model and retrieve the record data
    // Assuming your model is named Financial_model

    $record = $this->cb->from('jurnal_neraca')->where('id', $record_id)->get()->row();

    if (empty($record) || empty($record->file) || empty($record->nama_file)) {
      // Handle case where record is not found or no file is attached
      show_error('File not found or no attachment available.', 404);
      return;
    }

    // 2. Extract Data URI components
    $base64_string = $record->file;
    $file_name = $record->nama_file;

    // Check if it's a valid Data URI format
    // if (!preg_match('/^data:(\w+\/\w+);base64,(.*)$/s', $base64_string, $matches)) {
    //   show_error('Invalid Base64 file format.', 500);
    //   return;
    // }
    // The [^;]+ part allows almost any character in the MIME type until the next semicolon
    if (!preg_match('/^data:([^;]+);base64,(.*)$/s', $base64_string, $matches)) {
      show_error('Invalid Base64 file format (Regex failed).', 500);
      return;
    }

    // $matches[1] will be the MIME type (e.g., application/vnd.openxmlformats-officedocument.wordprocessingml.document)
    // $matches[2] will be the raw Base64 data
    $mime_type = $matches[1];
    $base64_content = $matches[2];

    // if (strpos($base64_string, 'data:') === 0) {
    //   // Use the flexible regex if the prefix is present
    //   if (!preg_match('/^data:([^;]+);base64,(.*)$/s', $base64_string, $matches)) {
    //     show_error('Invalid Base64 format.', 500);
    //     return;
    //   }
    //   $mime_type = $matches[1];
    //   $base64_content = $matches[2];
    // } else {
    //   // Prefix is missing. We must reconstruct it.
    //   $extension = pathinfo($file_name, PATHINFO_EXTENSION);
    //   $mime_type_lookup = [
    //     'xls' => 'application/vnd.ms-excel',
    //     'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //     'doc' => 'application/msword',
    //     'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    //     'pdf' => 'application/pdf',
    //     'png' => 'image/png',
    //     'jpg' => 'image/jpeg',
    //     'jpeg' => 'image/jpeg',
    //     // ... add all allowed types
    //   ];

    //   if (!isset($mime_type_lookup[strtolower($extension)])) {
    //     show_error('Unknown file type for download.', 500);
    //     return;
    //   }

    //   $mime_type = $mime_type_lookup[strtolower($extension)];
    //   $base64_content = $base64_string; // Assume the DB column holds ONLY the raw Base64 data
    // }


    // 3. Decode the Base64 content
    $file_content = base64_decode($base64_content);

    // 4. Send headers to force download (using CI3 Download Helper)
    $this->load->helper('download');

    // Force download the decoded content
    force_download($file_name, $file_content, $mime_type);
  }

  public function bukubesar()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $nip = $this->session->userdata('nip');

    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};

    $per_tanggal = ($this->input->post('per_tanggal') ? $this->input->post('per_tanggal') : date('Y-m-d'));

    $data = [
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'per_tanggal' => $per_tanggal
    ];

    $button_sbm = $this->input->post('button_sbm');
    $tahun = $this->input->post('per_tahun') ? $this->input->post('per_tahun') : date('Y');
    $tahun_before = $tahun - 1;
    $bulan_saldo_awal = $tahun_before . '-12';

    $saldo_awal = $this->cb->where('periode', $bulan_saldo_awal)->get('saldo_awal')->row_array();
    $saldo_awal_data = $saldo_awal ? json_decode($saldo_awal['coa']) : [];

    $saldo_awal_indexed = [];
    foreach ($saldo_awal_data as $sa) {
      $saldo_awal_indexed[$sa->no_sbb] = $sa->saldo_awal;
    }
    $data['saldo_awal'] = $saldo_awal_indexed; // Sudah dalam format array dengan key no_sbb
    // $data['saldo_awal_raw'] = $saldo_awal_data;

    $list_coa = $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'))->where('id_cabang', $this->session->userdata('kode_cabang'))->get('v_coa_all')->result();



    $description = 'Buku besar ' . $this->session->userdata('nama_perusahaan') . ' per tahun ' . $tahun;
    $data = [
      'description' => $description,
      // 'list_coa' => $list_coa,
      'tahun' => $tahun,
      'saldo_awal' => $saldo_awal_indexed
    ];

    $data['list_coa'] = $list_coa;
    $data['per_tahun'] = $tahun;

    if ($button_sbm == "excel") {
      // Clear output buffer untuk avoid corrupt
      if (ob_get_length()) {
        ob_end_clean();
      }

      error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $description = 'Buku besar ' . $this->session->userdata('nama_perusahaan') . ' per tahun ' . $tahun;

      $excel->getProperties()->setCreator('KodeSis')
        ->setLastModifiedBy('KodeSis')
        ->setTitle('Buku besar')
        ->setSubject('Buku besar')
        ->setDescription($description)
        ->setKeywords('Buku besar');

      // Header utama
      $sheet->setCellValue('A1', $description);
      $sheet->mergeCells('A1:D1');

      // Style header utama (opsional)
      $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
      $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $numRow = 3;

      if ($list_coa) {
        foreach ($list_coa as $lc) :
          $saldo_awal_value = isset($saldo_awal_indexed[$lc->no_sbb]) ? $saldo_awal_indexed[$lc->no_sbb] : 0;

          $transaction = $this->M_coa->getCoaReportAnnually($lc->no_sbb, $tahun);

          if ($transaction) {
            // Header per COA
            $sheet->setCellValue('A' . $numRow, $lc->no_sbb);
            $sheet->setCellValue('B' . $numRow, strtoupper($lc->nama_perkiraan));
            $sheet->setCellValue('D' . $numRow, 'IDR');

            // Style header COA
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFill()
              ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
              ->getStartColor()->setRGB('E8E8E8');

            $numRow++;

            // Sub-header tabel
            $sheet->setCellValue('A' . $numRow, 'Tanggal');
            $sheet->setCellValue('B' . $numRow, 'Keterangan');
            $sheet->setCellValue('C' . $numRow, 'Debit');
            $sheet->setCellValue('D' . $numRow, 'Kredit');

            // Style sub-header
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getAlignment()
              ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $numRow++;

            // Data transaksi
            $total_debit = 0;
            $total_kredit = 0;

            foreach ($transaction as $tr) {
              if ($lc->no_sbb == $tr->akun_debit) {
                $sheet->setCellValue('A' . $numRow, date('d/m/Y', strtotime($tr->tanggal)));
                $sheet->setCellValue('B' . $numRow, $tr->keterangan);
                $sheet->setCellValue('C' . $numRow, $tr->jumlah_debit);
                $sheet->setCellValue('D' . $numRow, '-');
                $total_debit += $tr->jumlah_debit;
              } else {
                $sheet->setCellValue('A' . $numRow, date('d/m/Y', strtotime($tr->tanggal)));
                $sheet->setCellValue('B' . $numRow, $tr->keterangan);
                $sheet->setCellValue('C' . $numRow, '-');
                $sheet->setCellValue('D' . $numRow, $tr->jumlah_kredit);
                $total_kredit += $tr->jumlah_kredit;
              }

              // Format angka
              $sheet->getStyle('C' . $numRow)->getNumberFormat()
                ->setFormatCode('#,##0');
              $sheet->getStyle('D' . $numRow)->getNumberFormat()
                ->setFormatCode('#,##0');

              $numRow++;
            }

            // $mutasi = $total_debit - $total_kredit;
            if ($lc->posisi === "AKTIVA") {
              $mutasi = $total_debit - $total_kredit;
            } else {
              $mutasi = $total_kredit - $total_debit;
            }

            // Total
            $sheet->setCellValue('A' . $numRow, 'Total');
            $sheet->setCellValue('C' . $numRow, $total_debit);
            $sheet->setCellValue('D' . $numRow, $total_kredit);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('C' . $numRow . ':D' . $numRow)->getNumberFormat()
              ->setFormatCode('#,##0');
            $numRow++;

            // Saldo Awal
            $sheet->setCellValue('A' . $numRow, 'Saldo Awal');
            $sheet->setCellValue('D' . $numRow, $saldo_awal_value);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('D' . $numRow)->getNumberFormat()
              ->setFormatCode('#,##0');
            $numRow++;

            // Mutasi
            $sheet->setCellValue('A' . $numRow, 'Mutasi');
            $sheet->setCellValue('D' . $numRow, $mutasi);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('D' . $numRow)->getNumberFormat()
              ->setFormatCode('#,##0');
            $numRow++;

            // Saldo Akhir
            // $selisih = $total_debit - $total_kredit;
            $saldo_akhir = $saldo_awal_value + $mutasi;

            $sheet->setCellValue('A' . $numRow, 'Saldo Akhir');
            $sheet->setCellValue('D' . $numRow, $saldo_akhir);
            $sheet->getStyle('A' . $numRow . ':D' . $numRow)->getFont()->setBold(true);
            $sheet->getStyle('D' . $numRow)->getNumberFormat()
              ->setFormatCode('#,##0');

            $numRow += 2; // Spacing antar COA
          }
        endforeach;

        // Set auto size untuk semua kolom
        foreach (range('A', 'D') as $columnID) {
          $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Clear any remaining output
        if (ob_get_length()) {
          ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $description . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
      }
    } else if ($button_sbm == "pdf") {

      $description = 'Buku besar ' . $this->session->userdata('nama_perusahaan') . ' per tahun ' . $tahun;

      $data = [
        'description' => $description,
        'list_coa' => $list_coa,
        'tahun' => $tahun,
        'saldo_awal' => $saldo_awal_indexed
      ];

      $file_pdf = $description;

      $paper = 'A4';

      $orientation = "portrait";

      $this->load->view('pages/financial/v_print_pdf_buku_besar_annually', $data);
      // Build HTML
      // $html = $this->load->view('print_pdf_buku_besar', $data, true);

      // $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    } else {
      $data['title'] = 'Buku besar';
      $data['pages'] = "pages/financial/v_reportbb_annually";
      $data['utility'] = $this->db->get('utility')->row_array();
      // $data['pages_script'] = 'script/financial/s_financial';
      $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
      $this->load->view('index', $data);
    }
  }

  public function bukubesarMonthly()
  {
    $nip = $this->session->userdata('nip');

    // Fetch counts
    $result = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
    $result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};

    $per_tanggal = ($this->input->post('per_tanggal') ? $this->input->post('per_tanggal') : date('Y-m-d'));

    $data = [
      'count_inbox' => $result,
      'count_inbox2' => $result2,
      'per_tanggal' => $per_tanggal
    ];

    $button_sbm = $this->input->post('button_sbm');
    $tahun = $this->input->post('per_tahun') ? $this->input->post('per_tahun') : date('Y');
    $bulan = $this->input->post('per_bulan') ? $this->input->post('per_bulan') : date('m');

    $tahun_before = $tahun - 1;
    $bulan_before = str_pad($bulan - 1, 2, '0', STR_PAD_LEFT);

    $bulan_saldo_awal = $tahun_before . '-' . $bulan_before;

    $saldo_awal = $this->cb->where('periode', $bulan_saldo_awal)->get('saldo_awal')->row_array();
    $saldo_awal_data = $saldo_awal ? json_decode($saldo_awal['coa']) : [];

    $saldo_awal_indexed = [];
    foreach ($saldo_awal_data as $sa) {
      $saldo_awal_indexed[$sa->no_sbb] = $sa->saldo_awal;
    }
    $data['saldo_awal'] = $saldo_awal_indexed; // Sudah dalam format array dengan key no_sbb
    // $data['saldo_awal_raw'] = $saldo_awal_data;

    $list_coa = $this->cb->get('v_coa_all')->result();

    $periode = $tahun . '-' . $bulan;

    $data['list_coa'] = $list_coa;
    $data['per_periode'] = $periode;
    $data['per_tahun'] = $tahun;
    $data['per_bulan'] = $bulan;

    $a = date('F Y', strtotime($periode . '-01'));
    $data['bulan'] = $a;

    $description = 'Buku besar ' . $this->session->userdata('nama_perusahaan') . ' per bulan ' . $a;
    $data['description'] = $description;


    if ($button_sbm == "pdf") {

      $data['description'] = $description;
      $data = [
        'description' => $description,
        'per_periode' => $periode,
        'list_coa' => $list_coa,
        'tahun' => $tahun,
        'saldo_awal' => $saldo_awal_indexed,
        'bulan' => $a
      ];

      $this->load->view('pages/financial/v_print_pdf_buku_besar_monthly', $data);
      // Build HTML
      // $html = $this->load->view('print_pdf_buku_besar', $data, true);

      // $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    } else {
      $data['title'] = 'Buku besar';
      $data['pages'] = "pages/financial/v_reportbb_monthly";
      $data['utility'] = $this->db->get('utility')->row_array();
      // $data['pages_script'] = 'script/financial/s_financial';
      $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
      $this->load->view('index', $data);
    }
  }
}
