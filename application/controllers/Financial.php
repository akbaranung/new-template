<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Financial extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_coa', 'M_Customer', 'M_invoice']);
    $this->load->helper(['number']);
    $this->load->library(['pdfgenerator']);

    $this->cb = $this->load->database('corebank', TRUE);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
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

      $excel->getProperties()->setCreator('SLS')
        ->setLastModifiedBy('SLS')
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

      $excel->getProperties()->setCreator('SLS')
        ->setLastModifiedBy('SLS')
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
      $this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
    }
    $data['title'] = 'Neraca per tanggal ' . format_indo($tanggal);
    $data['pages'] = 'pages/financial/v_neraca_bb_by_date';

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('SLS')
        ->setLastModifiedBy('SLS')
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
      $this->load->view('neraca_bb_by_date', $data);
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

    if ($button_sbm == "excel") {
      require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

      $excel = new PHPExcel();
      $sheet = $excel->getActiveSheet();

      $excel->getProperties()->setCreator('SLS')
        ->setLastModifiedBy('SLS')
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
      $this->load->view('labarugi_bb_by_date', $data);
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



    if ($jenis == "debit") {
      $data['pages'] = 'pages/financial/v_financial_entry_debit';
    } else if ($jenis == "kredit") {
      $data['pages'] = 'pages/financial/v_financial_entry_kredit';
    } else {
      $data['pages'] = 'pages/financial/v_financial_entry';
    }

    $this->load->view('index', $data);
  }

  public function process_financial_entry($jenis = null)
  {
    $keterangan = trim($this->input->post('input_keterangan'));
    $tanggal_transaksi = $this->input->post('tanggal');

    $this->cb->trans_start(); // Mulai transaksi
    $id_invoice = NULL;

    if ($jenis == "multi_kredit") {
      $coa_debit  = $this->input->post('neraca_debit');
      $coa_kredit = $this->input->post('accounts');
      $nominal    = $this->input->post('nominals');

      if (is_array($coa_kredit) && is_array($nominal)) {
        foreach ($coa_kredit as $i => $kredit) {
          $this->posting($coa_debit, $kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice);
        }
      }
    } elseif ($jenis == "multi_debit") {
      $coa_debit  = $this->input->post('accounts');
      $coa_kredit = $this->input->post('neraca_kredit');
      $nominal    = $this->input->post('nominals');

      if (is_array($coa_debit) && is_array($nominal)) {
        foreach ($coa_debit as $i => $debit) {
          $this->posting($debit, $coa_kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice);
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
      $this->posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal_transaksi, $id_invoice);
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
    $config['upload_path'] = FCPATH . 'uploads/financial_entry';
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

        $this->posting(
          $coa_debit,
          $coa_kredit,
          $keterangan,
          $nominal,
          $tanggal,
          $jenis_fe = 'single'
        );

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
        echo json_encode(['status' => true, 'message' => 'File processed successfully']);
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
      'id_cabang' => $this->session->userdata('kode_cabang')
    ];

    if (!$cek) {
      $this->M_coa->insert_saldo_awal($data);
      $this->session->set_flashdata('message_name', 'Closing bulan ' . format_indo($periode) . 'Saldo awal periode ' . format_indo($nextMonth) . ' berhasil ditetapkan');
    } else {
      $this->M_coa->update_saldo_awal($periode, $data);
      $this->session->set_flashdata('message_name', 'Closing bulan ' . format_indo($periode) . ' sudah diperbarui');
    }

    redirect($_SERVER['HTTP_REFERER']);
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
      'coa_kas' => $this->M_coa->getCoaByCode('1201'),
      'coa_pendapatan' => $this->M_coa->getCoaByCode('410'),
      'keyword' => $keyword,
      'title' => "Invoice",
      'customers' => $this->M_Customer->list_customer(''),
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
      'customers' => $this->M_Customer->list_customer(),
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
      'customers' => $this->M_Customer->list_customer(),
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

    $this->posting($coa_kredit_lama, $coa_debit_lama, $keterangan_lama, $inv['nominal_pendapatan'], $inv['tanggal_invoice'], $inv['Id']);

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
    $j1_coa_debit = $inv['coa_kredit'];
    $j1_coa_kredit = $coa_kredit;
    $this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $inv['nominal_pendapatan'], $tanggal_bayar);

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
    $kode_cabang = $this->session->userdata('kode_cabang');
    // return $this->cb->where('id_cabang', $kode_cabang);

    // Saldo awal periode sebelumnya
    $last_periode = new DateTime($from);
    $last_periode->modify('-1 month');
    $last_periode = $last_periode->format('Y-m');
    $coaBefore = $this->cb->where('id_cabang', $kode_cabang)
      ->where('periode', $last_periode)
      ->get('saldo_awal')
      ->row_array();

    $coaBefore = $coaBefore['coa'] ?? 0; // Pastikan tidak error jika NULL

    $coa = json_decode($coaBefore);
    $saldo_awal = null;

    // echo '<pre>';
    // print_r($coa);
    // echo '</pre>';
    // exit;
    // Iterasi untuk mencari saldo awal
    if ($coa) {
      foreach ($coa as $item) {
        if ($item->no_sbb == $no_coa) {
          $saldo_awal = $item->saldo_awal;
          break;
        }
      }
    }

    // Hitung transaksi dari 1-14 November
    $mid_start = (new DateTime($from))->modify('first day of this month')->format('Y-m-d');
    $mid_end = (new DateTime($from))->modify('-1 day')->format('Y-m-d');

    $transactions_before = $this->M_coa->getCoaReport($no_coa, $mid_start, $mid_end);
    foreach ($transactions_before as $trans) {
      if ($trans->akun_debit == $no_coa) {
        $saldo_awal += $trans->jumlah_debit;
      } else {
        $saldo_awal -= $trans->jumlah_kredit;
      }
    }

    // Set saldo awal untuk 15 November
    $data['saldo_awal'] = ($saldo_awal) ? $saldo_awal : 0;
    // print_r($saldo_awal);
    // exit;

    // Hitung transaksi dari 15 November - 31 Desember
    $data['coa'] = $this->M_coa->getCoaReport($no_coa, $from, $to);

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

  private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $id_invoice = NULL)
  {
    // Update coa debit 
    $this->update_saldo_coa($coa_debit, $nominal, 'debit');
    // Update coa kredit
    $this->update_saldo_coa($coa_kredit, $nominal, 'kredit');

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
      'id_cabang' => $this->session->userdata('kode_cabang')
    ];

    $this->M_coa->addJurnal($dt_jurnal);

    $data_transaksi = [
      'user_id' => $this->session->userdata('nip'),
      'tgl_trs' => date('Y-m-d H:i:s'),
      'nominal' => $nominal,
      'debet' => $coa_debit,
      'kredit' => $coa_kredit,
      'keterangan' => trim($keterangan),
      'id_cabang' => $this->session->userdata('kode_cabang')
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
    if (!$row) return;

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
}
