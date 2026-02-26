<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Financial extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();
		$this->load->model(['M_coa', 'M_customer', 'M_invoice', 'M_login', 'M_project']);
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
		// $this->cb->where('no_sbb', '20304');
		// $this->cb->or_where('no_sbb', '20301');
		$this->cb->where_not_in('no_sbb', ['20304', '20301']);
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

			$activa     = $this->M_coa->getNeracaByDate('t_coa_sbb', 'AKTIVA', $tanggal, $periode);
			$pasiva     = $this->M_coa->getNeracaByDate('t_coa_sbb', 'PASIVA', $tanggal, $periode);
			$pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
			$beban      = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

			// ── Part Aktiva ──────────────────────────────────────────
			$combinedActiva = [];
			foreach ($activa as $item) {
				if (!isset($combinedActiva[$item->no_sbb])) {
					$combinedActiva[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaAktiva as $item) {
				if (!isset($combinedActiva[$item->no_sbb])) {
					$combinedActiva[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedActiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			usort($combinedActiva, fn($a, $b) => strcmp($a->no_sbb, $b->no_sbb));
			$total_activa = array_sum(array_column($combinedActiva, 'saldo_awal'));

			// Grouped BB Aktiva
			$bbActiva = [];
			foreach ($combinedActiva as $item) {
				$key = substr($item->no_sbb, 0, 3);
				$bbActiva[$key] = ($bbActiva[$key] ?? 0) + $item->saldo_awal;
			}
			$groupedActiva = $bbActiva; // key = no_bb, value = total saldo

			// ── Part Pasiva ──────────────────────────────────────────
			$filteredCoaPasiva = array_filter($coaLastPeriod, function ($item) {
				return $item->posisi === 'PASIVA' && $item->table_source === 't_coa_sbb';
			});

			$combinedPasiva = [];
			foreach ($pasiva as $item) {
				if (!isset($combinedPasiva[$item->no_sbb])) {
					$combinedPasiva[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaPasiva as $item) {
				if (!isset($combinedPasiva[$item->no_sbb])) {
					$combinedPasiva[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPasiva[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			usort($combinedPasiva, fn($a, $b) => strcmp($a->no_sbb, $b->no_sbb));
			$total_pasiva = array_sum(array_column($combinedPasiva, 'saldo_awal'));

			// Grouped BB Pasiva
			$bbPasiva = [];
			foreach ($combinedPasiva as $item) {
				$key = substr($item->no_sbb, 0, 3);
				$bbPasiva[$key] = ($bbPasiva[$key] ?? 0) + $item->saldo_awal;
			}
			$groupedPasiva = $bbPasiva;

			// ── Part Pendapatan ──────────────────────────────────────
			$filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
				return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
			});
			$combinedPendapatan = [];
			foreach ($pendapatan as $item) {
				if (!isset($combinedPendapatan[$item->no_sbb])) {
					$combinedPendapatan[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaPendapatan as $item) {
				if (!isset($combinedPendapatan[$item->no_sbb])) {
					$combinedPendapatan[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			$total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

			// ── Part Beban ───────────────────────────────────────────
			$filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
				return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
			});
			$combinedBeban = [];
			foreach ($beban as $item) {
				if (!isset($combinedBeban[$item->no_sbb])) {
					$combinedBeban[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaBeban as $item) {
				if (!isset($combinedBeban[$item->no_sbb])) {
					$combinedBeban[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			$total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

			$laba       = $total_pendapatan - $total_beban;
			$sum_pasiva = $total_pasiva + $laba;

			// ── Pass ke view ─────────────────────────────────────────
			$data['activa']         = $combinedActiva;
			$data['grouped_activa'] = $groupedActiva;
			$data['sum_activa']     = $total_activa;
			$data['pasiva']         = $combinedPasiva;
			$data['grouped_pasiva'] = $groupedPasiva;
			$data['laba']           = $laba;
			$data['sum_pasiva']     = $sum_pasiva;
			$data['neraca']         = $sum_pasiva - $total_activa;
		} else {
			$this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
		}

		$data['title']        = 'Neraca per tanggal ' . format_indo($tanggal);
		$data['utility']      = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_financial';
		$data['pages']        = 'pages/financial/v_neraca_by_date';
		$data['menus']        = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		if ($button_sbm == "excel") {
			require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

			$excel = new PHPExcel();
			$sheet = $excel->getActiveSheet();

			$excel->getProperties()
				->setCreator('Bariskode')
				->setLastModifiedBy('Bariskode')
				->setTitle("Neraca SBB")
				->setSubject("Neraca SBB")
				->setDescription("Neraca SBB per tanggal " . format_indo($tanggal))
				->setKeywords("Neraca SBB");

			// ── Header Excel ─────────────────────────────────────────
			$sheet->mergeCells('A1:G1');
			$sheet->mergeCells('A2:C2');
			$sheet->mergeCells('E2:G2');

			$sheet->setCellValue('A1', 'Neraca SBB per tanggal ' . format_indo($tanggal));
			// $sheet->setCellValue('A2', 'AKTIVA');
			// $sheet->setCellValue('E2', 'PASIVA');
			$sheet->setCellValue('B3', 'Total AKTIVA: ');
			$sheet->setCellValue('C3', $total_activa ?? 0);
			$sheet->setCellValue('F3', 'Total PASIVA: ');
			$sheet->setCellValue('G3', $sum_pasiva ?? 0);

			$sheet->setCellValue('A4', 'No. CoA');
			$sheet->setCellValue('B4', 'Nama CoA');
			$sheet->setCellValue('C4', 'Nominal');
			$sheet->setCellValue('E4', 'No. CoA');
			$sheet->setCellValue('F4', 'Nama CoA');
			$sheet->setCellValue('G4', 'Nominal');

			// ── Data Aktiva + Subtotal BB ─────────────────────────────
			$numrowActiva  = 5;
			$activaList    = array_values($combinedActiva);
			$prevBBActiva  = null;

			for ($i = 0; $i < count($activaList); $i++) {
				$t   = $activaList[$i];
				$coa = $this->M_coa->getCoa($t->no_sbb);

				if ($coa['table_source'] != "t_coa_sbb" || $coa['posisi'] != 'AKTIVA') continue;

				$thisBB = substr($t->no_sbb, 0, 3);
				$nextBB = isset($activaList[$i + 1]) ? substr($activaList[$i + 1]->no_sbb, 0, 3) : null;

				if ($t->saldo_awal != 0) {
					$sheet->setCellValue('A' . $numrowActiva, $t->no_sbb);
					$sheet->setCellValue('B' . $numrowActiva, $coa['nama_perkiraan']);
					$sheet->setCellValue('C' . $numrowActiva, $t->saldo_awal);
					$numrowActiva++;
				}

				// Subtotal BB jika grup ganti atau elemen terakhir
				if ($thisBB !== $nextBB) {
					$coaBB      = $this->M_coa->getCoaBB($thisBB);
					$namaBB     = $coaBB ? $coaBB['nama_perkiraan'] : '-';
					$subtotalBB = $groupedActiva[$thisBB] ?? 0;

					if ($subtotalBB != 0) {
						// Style italic/grey untuk baris subtotal
						$styleSubtotal = [
							'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
							'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
						];
						$sheet->getStyle("A{$numrowActiva}:C{$numrowActiva}")->applyFromArray($styleSubtotal);

						$sheet->setCellValue('A' . $numrowActiva, $thisBB);
						$sheet->setCellValue('B' . $numrowActiva, 'Total ' . $namaBB);
						$sheet->setCellValue('C' . $numrowActiva, $subtotalBB);
						$numrowActiva++;

						// Baris kosong pemisah antar grup
						$numrowActiva++;
					}
				}
			}

			// ── Data Pasiva + Subtotal BB ─────────────────────────────
			$numrowPasiva = 5;
			$pasivaList   = array_values($combinedPasiva);

			for ($i = 0; $i < count($pasivaList); $i++) {
				$t   = $pasivaList[$i];
				$coa = $this->M_coa->getCoa($t->no_sbb);

				if ($coa['table_source'] != "t_coa_sbb" || $coa['posisi'] != 'PASIVA') continue;

				$thisBB = substr($t->no_sbb, 0, 3);
				$nextBB = isset($pasivaList[$i + 1]) ? substr($pasivaList[$i + 1]->no_sbb, 0, 3) : null;

				if ($t->saldo_awal != 0) {
					$sheet->setCellValue('E' . $numrowPasiva, $t->no_sbb);
					$sheet->setCellValue('F' . $numrowPasiva, $coa['nama_perkiraan']);
					$sheet->setCellValue('G' . $numrowPasiva, $t->saldo_awal);
					$numrowPasiva++;
				}

				// Subtotal BB jika grup ganti atau elemen terakhir
				if ($thisBB !== $nextBB) {
					$coaBB      = $this->M_coa->getCoaBB($thisBB);
					$namaBB     = $coaBB ? $coaBB['nama_perkiraan'] : '-';
					$subtotalBB = $groupedPasiva[$thisBB] ?? 0;

					if ($subtotalBB != 0) {
						$styleSubtotal = [
							'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
							'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
						];
						$sheet->getStyle("E{$numrowPasiva}:G{$numrowPasiva}")->applyFromArray($styleSubtotal);

						$sheet->setCellValue('E' . $numrowPasiva, $thisBB);
						$sheet->setCellValue('F' . $numrowPasiva, 'Total ' . $namaBB);
						$sheet->setCellValue('G' . $numrowPasiva, $subtotalBB);
						$numrowPasiva++;

						// Baris kosong pemisah
						$numrowPasiva++;
					}
				}
			}

			// Baris laba tahun berjalan
			$sheet->setCellValue('E' . $numrowPasiva, '31030');
			$sheet->setCellValue('F' . $numrowPasiva, 'LABA TAHUN BERJALAN');
			$sheet->setCellValue('G' . $numrowPasiva, $laba ?? 0);

			foreach (range('A', 'G') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
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
		$data['sum_biaya']        = 0;
		$data['sum_pendapatan']   = 0;
		$data['biaya']            = [];
		$data['pendapatan']       = [];
		$data['grouped_biaya']    = [];
		$data['grouped_pendapatan'] = [];

		if ($cek) {
			$coaLastPeriod = json_decode($cek['coa']);

			$pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal, $periode);
			$beban      = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal, $periode);

			// ── Part Pendapatan ──────────────────────────────────────
			$filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
				return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
			});
			$combinedPendapatan = [];
			foreach ($pendapatan as $item) {
				if (!isset($combinedPendapatan[$item->no_sbb])) {
					$combinedPendapatan[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaPendapatan as $item) {
				if (!isset($combinedPendapatan[$item->no_sbb])) {
					$combinedPendapatan[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			usort($combinedPendapatan, fn($a, $b) => strcmp($a->no_sbb, $b->no_sbb));
			$total_pendapatan = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

			// Grouped BB Pendapatan
			$bbPendapatan = [];
			foreach ($combinedPendapatan as $item) {
				$key = substr($item->no_sbb, 0, 3);
				$bbPendapatan[$key] = ($bbPendapatan[$key] ?? 0) + $item->saldo_awal;
			}
			$groupedPendapatan = $bbPendapatan;

			// ── Part Beban ───────────────────────────────────────────
			$filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
				return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
			});
			$combinedBeban = [];
			foreach ($beban as $item) {
				if (!isset($combinedBeban[$item->no_sbb])) {
					$combinedBeban[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			foreach ($filteredCoaBeban as $item) {
				if (!isset($combinedBeban[$item->no_sbb])) {
					$combinedBeban[$item->no_sbb] = (object)['no_sbb' => $item->no_sbb, 'saldo_awal' => $item->saldo_awal];
				} else {
					$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
				}
			}
			usort($combinedBeban, fn($a, $b) => strcmp($a->no_sbb, $b->no_sbb));
			$total_beban = array_sum(array_column($combinedBeban, 'saldo_awal'));

			// Grouped BB Beban
			$bbBeban = [];
			foreach ($combinedBeban as $item) {
				$key = substr($item->no_sbb, 0, 3);
				$bbBeban[$key] = ($bbBeban[$key] ?? 0) + $item->saldo_awal;
			}
			$groupedBeban = $bbBeban;

			$data['biaya']              = $combinedBeban;
			$data['grouped_biaya']      = $groupedBeban;
			$data['pendapatan']         = $combinedPendapatan;
			$data['grouped_pendapatan'] = $groupedPendapatan;
			$data['sum_biaya']          = $total_beban;
			$data['sum_pendapatan']     = $total_pendapatan;
			$data['total_pendapatan']   = $total_pendapatan - $total_beban;
		} else {
			$this->session->set_flashdata('message_error', 'Closing bulan ' . format_indo($periode) . ' tidak ditemukan');
		}

		$data['title']        = 'Laba rugi per tanggal ' . format_indo($tanggal);
		$data['utility']      = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_financial';
		$data['pages']        = 'pages/financial/v_laba_rugi_by_date';
		$data['menus']        = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		if ($button_sbm == "excel") {
			require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

			$excel = new PHPExcel();
			$sheet = $excel->getActiveSheet();

			$excel->getProperties()
				->setCreator('Bariskode')
				->setLastModifiedBy('Bariskode')
				->setTitle("Laba rugi SBB")
				->setSubject("Laba rugi SBB")
				->setDescription("Laba rugi SBB per tanggal " . format_indo($tanggal))
				->setKeywords("Laba rugi SBB");

			// ── Header Excel ─────────────────────────────────────────
			$sheet->mergeCells('A1:G1');
			$sheet->mergeCells('A2:C2');
			$sheet->mergeCells('E2:G2');

			$sheet->setCellValue('A1', 'Laba rugi SBB per tanggal ' . format_indo($tanggal));
			// $sheet->setCellValue('A2', 'BEBAN');
			// $sheet->setCellValue('E2', 'PENDAPATAN');
			$sheet->setCellValue('B3', 'Total BEBAN: ');
			$sheet->setCellValue('C3', $total_beban ?? 0);
			$sheet->setCellValue('F3', 'Total PENDAPATAN: ');
			$sheet->setCellValue('G3', $total_pendapatan ?? 0);

			$sheet->setCellValue('A4', 'No. CoA');
			$sheet->setCellValue('B4', 'Nama CoA');
			$sheet->setCellValue('C4', 'Nominal');
			$sheet->setCellValue('E4', 'No. CoA');
			$sheet->setCellValue('F4', 'Nama CoA');
			$sheet->setCellValue('G4', 'Nominal');

			// ── Data Beban + Subtotal BB ──────────────────────────────
			$numrowBeban = 5;
			$bebanList   = array_values($combinedBeban);

			for ($i = 0; $i < count($bebanList); $i++) {
				$t   = $bebanList[$i];
				$coa = $this->M_coa->getCoa($t->no_sbb);

				if ($coa['table_source'] != "t_coalr_sbb" || $coa['posisi'] != 'AKTIVA') continue;

				$thisBB = substr($t->no_sbb, 0, 3);
				$nextBB = isset($bebanList[$i + 1]) ? substr($bebanList[$i + 1]->no_sbb, 0, 3) : null;

				if ($t->saldo_awal != 0) {
					$sheet->setCellValue('A' . $numrowBeban, $t->no_sbb);
					$sheet->setCellValue('B' . $numrowBeban, $coa['nama_perkiraan']);
					$sheet->setCellValue('C' . $numrowBeban, $t->saldo_awal);
					$numrowBeban++;
				}

				// Subtotal BB
				if ($thisBB !== $nextBB) {
					$coaBB      = $this->M_coa->getCoaBB($thisBB);
					$namaBB     = $coaBB ? $coaBB['nama_perkiraan'] : '-';
					$subtotalBB = $groupedBeban[$thisBB] ?? 0;

					if ($subtotalBB != 0) {
						$styleSubtotal = [
							'font' => ['italic' => true, 'color' => ['rgb' => '888888']],
							'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
						];
						$sheet->getStyle("A{$numrowBeban}:C{$numrowBeban}")->applyFromArray($styleSubtotal);
						$sheet->setCellValue('A' . $numrowBeban, $thisBB);
						$sheet->setCellValue('B' . $numrowBeban, 'Total ' . $namaBB);
						$sheet->setCellValue('C' . $numrowBeban, $subtotalBB);
						$numrowBeban++;
						$numrowBeban++; // baris kosong pemisah
					}
				}
			}

			// ── Data Pendapatan + Subtotal BB ────────────────────────
			$numrowPendapatan = 5;
			$pendapatanList   = array_values($combinedPendapatan);

			for ($i = 0; $i < count($pendapatanList); $i++) {
				$t   = $pendapatanList[$i];
				$coa = $this->M_coa->getCoa($t->no_sbb);

				if ($coa['table_source'] != "t_coalr_sbb" || $coa['posisi'] != 'PASIVA') continue;

				$thisBB = substr($t->no_sbb, 0, 3);
				$nextBB = isset($pendapatanList[$i + 1]) ? substr($pendapatanList[$i + 1]->no_sbb, 0, 3) : null;

				if ($t->saldo_awal != 0) {
					$sheet->setCellValue('E' . $numrowPendapatan, $t->no_sbb);
					$sheet->setCellValue('F' . $numrowPendapatan, $coa['nama_perkiraan']);
					$sheet->setCellValue('G' . $numrowPendapatan, $t->saldo_awal);
					$numrowPendapatan++;
				}

				// Subtotal BB
				if ($thisBB !== $nextBB) {
					$coaBB      = $this->M_coa->getCoaBB($thisBB);
					$namaBB     = $coaBB ? $coaBB['nama_perkiraan'] : '-';
					$subtotalBB = $groupedPendapatan[$thisBB] ?? 0;

					if ($subtotalBB != 0) {
						$styleSubtotal = [
							'font' => ['italic' => true, 'color' => ['rgb' => '888888']],
							'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F0F0F0']],
						];
						$sheet->getStyle("E{$numrowPendapatan}:G{$numrowPendapatan}")->applyFromArray($styleSubtotal);
						$sheet->setCellValue('E' . $numrowPendapatan, $thisBB);
						$sheet->setCellValue('F' . $numrowPendapatan, 'Total ' . $namaBB);
						$sheet->setCellValue('G' . $numrowPendapatan, $subtotalBB);
						$numrowPendapatan++;
						$numrowPendapatan++; // baris kosong pemisah
					}
				}
			}

			foreach (range('A', 'G') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
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
				if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0):
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
				if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0):
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

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// exit;

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
		$access_menu_all = $this->M_menu->get_allowed_routes($this->session->userdata('nip'));

		if (!$has_access and !in_array('financial/financial_entry', $access_menu_all)) {
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
		if ($this->session->userdata('is_premium')) {

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
		}
		// else {
		//   echo "Gak Masuk";
		//   exit();
		// }


		$this->cb->trans_start(); // Mulai transaksi
		$id_invoice = NULL;

		if ($jenis == "multi_kredit") {
			$coa_debit = $this->input->post('neraca_debit');
			$coa_kredit = $this->input->post('accounts');
			$nominal = $this->input->post('nominals');

			if (is_array($coa_kredit) && is_array($nominal)) {
				foreach ($coa_kredit as $i => $kredit) {
					$this->posting($coa_debit, $kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice, $base64_data, $file_name);
				}
			}
		} elseif ($jenis == "multi_debit") {
			$coa_debit = $this->input->post('accounts');
			$coa_kredit = $this->input->post('neraca_kredit');
			$nominal = $this->input->post('nominals');

			if (is_array($coa_debit) && is_array($nominal)) {
				foreach ($coa_debit as $i => $debit) {
					$this->posting($debit, $coa_kredit, $keterangan, $this->_parse_rupiah($nominal[$i]), $tanggal_transaksi, $id_invoice, $base64_data, $file_name);
				}
			}
		} else {
			$coa_debit = $this->input->post('neraca_debit');
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
				if ($rowIndex < 3)
					continue;

				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);

				$data = [];
				foreach ($cellIterator as $cell) {
					$data[] = $cell->getValue();
				}

				// Extract and process row data
				$coa_debit = isset($data[0]) ? (string) $data[0] : null;
				$coa_kredit = isset($data[1]) ? (string) $data[1] : null;
				$nominal = isset($data[2]) ? (string) $data[2] : null;
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
			if (file_exists($file_path))
				unlink($file_path);
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
		// $data['pages_script'] = '';
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

		$this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
		$this->cb->from('v_coabb_all');
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$query = $this->cb->get();
		$all_coa_bb = $query->result_array();

		$data['all_coa_bb'] = $all_coa_bb;

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
		$keyword = $this->input->post('keyword');

		if ($keyword && $no_coa == "") {
			$no_coa = "ALL";
		}

		if ($no_coa) {
			$this->prepareCoaReport($data, $no_coa, $keyword);
		} else {
			$data['title'] = "Report CoA";
			$data['daftar_coa'] = $this->M_coa->list_coa();
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
		$inv = $this->M_invoice->showById($id);

		// Cek jenis invoice, redirect ke halaman edit yang sesuai
		if ($inv['jenis_invoice'] == 'agen_smu' || $inv['jenis_invoice'] == 'sales') {
			redirect('financial/edit_invoice_sales/' . $id);
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

		$inv = $this->M_invoice->showById($id);

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

		$inv = $this->M_invoice->showById($id);
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
			$j1_coa_kredit = "20301";
			$this->posting($j1_coa_debit, $j1_coa_kredit, $keterangan, $inv['besaran_ppn'], $tanggal_bayar);

			$j2_coa_debit = $inv['coa_kredit'];
			$j2_coa_kredit = $inv['coa_debit'];
			$this->posting($j2_coa_debit, $j2_coa_kredit, $keterangan, $inv['besaran_ppn'], $tanggal_bayar);
		}

		if ($inv['opsi_pph23'] == '1') {
			// J4: Kas/Bank bertambah sebesar pph, utang pph 23 bertambah sebesar pph
			$j1_coa_debit = $coa_debit;
			$j1_coa_kredit = "20304";
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

		$inv = $this->M_invoice->show($no_inv);
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
		if ($cabang === null || $cabang === '')
			$cabang = $this->session->userdata('kode_cabang');

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
		$coa_bb = $this->M_coa->list_coa_bb_paginate($per_page, $page_bb, $keyword_bb, $cabang);

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
			// $this->cb->trans_rollback();
			// $this->session->set_flashdata('swal_message', [
			//   'icon' => 'error', // or 'success', 'warning', 'info', 'question'
			//   'title' => 'Error!',
			//   'text' => 'Saldo Nomor BB ' . $no_bb . ' Tidak di temukan, Silahkan di buat BB terlebih dahulu',
			//   'confirmButtonText' => 'Mengerti',
			// ]);
			// $this->session->set_flashdata('message_error', 'Nomor COA BB ' . $no_bb . ' Tidak Di Temukan. Silahkan buat BB terlebih dahulu');
			// redirect($_SERVER['HTTP_REFERER']);
			$this->session->set_flashdata('message_error', 'CoA BB dengan Nomor ' . $no_bb . ' sudah ada');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			if ($cek_nama_coa) {
				$this->session->set_flashdata('message_error', 'CoA BB dengan Nama ' . $nama_coa . ' sudah ada');
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
					$tabel = "t_coa_bb";

					$data = [
						'no_bb' => $no_bb,
						'nama_perkiraan' => $nama_coa,
						'posisi' => $posisi,
						'id_cabang' => $this->session->userdata('kode_cabang'),
						'id_company' => $this->session->userdata('user_perusahaan_id'),
					];
				} else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
					$tabel = "t_coalr_bb";
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
		$inv = $this->M_invoice->showById($id);
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


	private function prepareCoaReport(&$data, $no_coa, $keyword = NULL)
	{
		$from = $this->input->post('tgl_dari');
		$to = $this->input->post('tgl_sampai');
		// $keyword = $this->input->post('keyword');
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

		// Hitung net total hanya jika no_coa == ALL dan keyword ada isinya
		if ($no_coa == "ALL") {
			$sum_debit = 0;
			$sum_kredit = 0;

			foreach ($data['coa'] as $a) {
				$coa_debit  = $this->M_coa->getCoa($a->akun_debit);
				$coa_kredit = $this->M_coa->getCoa($a->akun_kredit);

				// Kolom Debit
				if ($coa_debit['posisi'] == 'AKTIVA') {
					$sum_debit += $a->jumlah_debit;
				} else { // PASIVA
					$sum_debit -= $a->jumlah_debit;
				}

				// Kolom Kredit
				if ($coa_kredit['posisi'] == 'PASIVA') {
					$sum_kredit += $a->jumlah_kredit;
				} else { // AKTIVA
					$sum_kredit -= $a->jumlah_kredit;
				}
			}

			$data['sum_debit'] = $sum_debit;
			$data['sum_kredit'] = $sum_kredit;
		} else {
			$data['sum_debit'] = array_sum(array_map(function ($sum) use ($no_coa) {
				return $sum->akun_debit == $no_coa ? $sum->jumlah_debit : 0;
			}, $data['coa']));

			$data['sum_kredit'] = array_sum(array_map(function ($sum) use ($no_coa) {
				return $sum->akun_kredit == $no_coa ? $sum->jumlah_kredit : 0;
			}, $data['coa']));
		}

		$data['title'] = "Report CoA " . $no_coa;
		$data['detail_coa'] = $this->M_coa->getCoa($no_coa);
		$data['daftar_coa'] = $this->M_coa->list_coa();
		$data['pages'] = 'pages/financial/v_report_per_coa';
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_financial';
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		// echo '<pre>';
		// print_r($data['coa']);
		// echo '</pre>';
		// exit;

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
		if (!$row)
			return FALSE;

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
							'id_cabang' => $this->session->userdata('kode_cabang'),
							'id_company' => $this->session->userdata('user_perusahaan_id'),
						];
					} else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
						$tabel = "t_coalr_bb";
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

	public function ajax_edit_coa_bb($no_bb, $id_cabang)
	{
		$this->cb->select('*');
		$this->cb->from('v_coabb_all');
		$this->cb->where('no_bb', $no_bb);
		$this->cb->where('id_company', $id_cabang);
		$get_coa = $this->cb->get()->row();

		if ($get_coa->table_source == "t_coa_bb") {
			$this->cb->select('*');
			$this->cb->from($get_coa->table_source);
			$this->cb->where('no_bb', $no_bb);
			$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
			$this->cb->where('id_company', $id_cabang);
		} else {

			$this->cb->select('*');
			$this->cb->from($get_coa->table_source);
			$this->cb->where('no_lr_bb', $no_bb);
			$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
			$this->cb->where('id_company', $id_cabang);
		}
		$data = $this->cb->get()->row();
		$response = [
			'coa_data' => $get_coa, // This will contain the COA object/array
			'data' => $data // This will contain the COA object/array
		];
		echo json_encode($response);
	}

	public function update_coa_bb()
	{

		$tabel = $this->input->post('table_coa');

		$data_update = [
			// 'no_bb'           => $this->input->post('no_bb'),
			// 'no_sbb'           => $this->input->post('no_sbb'),
			'nama_perkiraan' => $this->input->post('nama_perkiraan'),
		];

		// $data_update['no_bb'] = $this->input->post('no_bb');

		$this->cb->update($tabel, $data_update, array('id' => $this->input->post('id_coa')));

		redirect('financial/list_coa');
	}

	public function ajax_edit_coa($no_sbb, $id_cabang)
	{
		// echo $no_sbb;
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
			'nama_perkiraan' => $this->input->post('nama_perkiraan'),
			'nominal' => $this->input->post('nominal'),
		];

		$this->cb->update($tabel, $data_update, array('id' => $this->input->post('id_coa')));

		redirect('financial/list_coa');
	}

	public function update_coa_tanpa_saldo()
	{

		$tabel = $this->input->post('table_coa');

		$data_update = [
			// 'no_bb'           => $this->input->post('no_bb'),
			// 'no_sbb'           => $this->input->post('no_sbb'),
			'nama_perkiraan' => $this->input->post('nama_perkiraan'),
		];

		if ($this->input->post('nominal') == 0) {
			if ($tabel == "t_coa_sbb") {
				$data_update['no_bb'] = $this->input->post('no_bb');
			} else {
				$data_update['no_lr_bb'] = $this->input->post('no_bb');
			}
			$data_update['no_sbb'] = $this->input->post('no_sbb');
		}

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
		if ($keyword === null)
			$keyword = $this->session->userdata('search');
		else
			$this->session->set_userdata('search', $keyword);

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
		if ($keyword === null)
			$keyword = $this->session->userdata('search');
		else
			$this->session->set_userdata('search', $keyword);

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

		redirect("financial/list_customer");
		// redirect("customer");
	}

	public function reset_customer()
	{
		$this->session->unset_userdata('search');
		redirect('financial/list_customer');
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
				// ✅ Ubah jadi ->
				$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
			}
		}

		foreach ($filteredCoaBeban as $item) {
			if (!isset($combinedBeban[$item['no_sbb']])) {
				$combinedBeban[$item['no_sbb']] = (object) [
					'no_sbb' => $item['no_sbb'],
					'saldo_awal' => $item['saldo_awal'],
				];
			} else {
				// ✅ Ubah jadi ->
				$combinedBeban[$item['no_sbb']]->saldo_awal += $item['saldo_awal'];
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
				if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0):
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
				if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0):
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

		// Loop 1: $beban (object)
		foreach ($beban as $item) {
			if (!isset($combinedBeban[$item->no_sbb])) {
				$combinedBeban[$item->no_sbb] = (object) [
					'no_sbb' => $item->no_sbb,
					'saldo_awal' => $item->saldo_awal,
				];
			} else {
				// ✅ Ubah jadi ->
				$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
			}
		}

		// Loop 2: $filteredCoaBeban (array)
		foreach ($filteredCoaBeban as $item) {
			if (!isset($combinedBeban[$item['no_sbb']])) {
				$combinedBeban[$item['no_sbb']] = (object) [
					'no_sbb' => $item['no_sbb'],
					'saldo_awal' => $item['saldo_awal'],
				];
			} else {
				// ✅ Ubah jadi ->
				$combinedBeban[$item['no_sbb']]->saldo_awal += $item['saldo_awal'];
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
				if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA' && $t->saldo_awal != 0):
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
				if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA' && $t->saldo_awal != 0):
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
				foreach ($list_coa as $lc):
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

	public function sales()
	{
		$keyword_opt = $this->input->get('keyword_opt');
		$keyword = trim($this->input->get('keyword', true) ?? '');

		$config = [
			'base_url' => site_url('financial/sales'),
			'total_rows' => $this->M_invoice->sales_count($keyword, $keyword_opt),
			'per_page' => 10,
			'uri_segment' => 3,
			'num_links' => 5,

			'full_tag_open' => '<ul class="pagination justify-content-end mb-0">',
			'full_tag_close' => '</ul>',

			'first_link' => false,
			'last_link' => false,

			// PREV
			'prev_link' => '<span class="pagination-prev">Previous</span>',
			'prev_tag_open' => '<li class="page-item">',
			'prev_tag_close' => '</li>',

			// NEXT
			'next_link' => '<span class="pagination-next">Next</span>',
			'next_tag_open' => '<li class="page-item">',
			'next_tag_close' => '</li>',

			// CURRENT PAGE (blue box)
			'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
			'cur_tag_close' => '</a></li>',

			// OTHER PAGES
			'num_tag_open' => '<li class="page-item">',
			'num_tag_close' => '</li>',
		];

		// apply page-link class only to numbers
		$config['attributes'] = ['class' => 'page-link'];

		// Override attributes for next/prev so CI doesn't auto-apply
		$config['anchor_class'] = '';

		$config['use_page_numbers'] = TRUE;

		$page = $this->uri->segment(3);
		if (!$page || $page < 1) {
			$page = 1;
		}
		$offset = ($page - 1) * $config['per_page'];



		$this->pagination->initialize($config);

		$page = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
		$sales = $this->M_invoice->list_sales($config["per_page"], $offset, $keyword, $keyword_opt);

		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$result = $query->row_array()['COUNT(Id)'];

		$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query2 = $this->db->query($sql2);
		$result2 = $query2->row_array()['COUNT(id)'];

		$data = [
			'page' => $page,
			'sales' => $sales,
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'keyword_opt' => $keyword_opt,
			'keyword' => $keyword,
			'title' => "Sales",
			'customers' => $this->M_customer->list_customer(''),
		];

		$data['title'] = "Daftar Sales";
		$data['pages'] = "pages/financial/v_sales";
		$data['utility'] = $this->db->get('utility')->row_array();
		// $data['pages_script'] = 'script/financial/s_financial';
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
		$data['agents'] = $this->cb->select('agent_name')->group_by('agent_name')->order_by('agent_name', 'ASC')->get('sales')->result();

		// print_r($data['agent']);
		// exit;

		$this->load->view('index', $data);
	}

	public function upload_sales()
	{
		$id_cabang = $this->session->userdata('kode_cabang');
		$nip = $this->session->userdata('nip');

		// Upload handler
		if (!isset($_FILES['file_excel']['name']) || $_FILES['file_excel']['name'] == '') {
			$this->session->set_flashdata('error', 'File Excel belum dipilih');
			redirect('financial/sales');
		}

		$file_tmp = $_FILES['file_excel']['tmp_name'];

		// Load PhpSpreadsheet
		require APPPATH . 'third_party/autoload.php';

		// Include PhpSpreadsheet from third_party
		require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_tmp);
		$spreadsheet = $reader->load($file_tmp);
		$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

		// ========== VALIDASI HEADER ========== //
		$expected_headers = [
			'A' => 'Tanggal Terbang',
			'B' => 'Airline Name',
			'C' => 'Vendor',
			'D' => 'Kode Komodity',
			'E' => 'Jenis Barang',
			'F' => 'Shipper Name',
			'G' => 'AGENT NAME',
			'H' => 'NO SMU',
			'I' => 'ORI',
			'J' => 'DEST',
			'K' => 'FLT NO',
			'L' => 'Koli',
			'M' => 'Kg',
			'N' => 'CHWT',
			'O' => 'SELLING PRICE',
			'P' => 'FREIGHT',
			'Q' => '%',
			'R' => 'SURCHARGE',
			'S' => 'HHT',
			'T' => 'ADM FEE',
			'U' => 'PPN SMU',
			'V' => 'TOTAL FREIGHT',
			'W' => 'HANDLING CHARGE',
			'X' => 'ASURANSI',
			'Y' => 'EXTRA PACKING',
			'Z' => 'HANDLING DEST',
			'AA' => 'OTHER CHARGE',
			'AB' => 'SUB TOTAL TAGIHAN'
		];

		// Ambil baris pertama (header)
		$header_row = $sheet[1];

		// Cek setiap kolom
		$header_errors = [];
		foreach ($expected_headers as $col => $expected_name) {
			$actual_name = trim($header_row[$col] ?? '');

			// Normalisasi untuk case-insensitive comparison
			if (strtolower($actual_name) !== strtolower($expected_name)) {
				$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
			}

			// if ($actual_name !== $expected_name) {
			// 	$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
			// }
		}

		// Jika ada error header, kirim response error
		if (!empty($header_errors)) {
			ob_clean();
			echo json_encode([
				'status' => 'error',
				'error_type' => 'header_validation',
				'message' => 'File Excel yang Anda upload tidak sesuai format template!',
				'total_errors' => count($header_errors),
				'error_details' => $header_errors
			]);
			exit;
		}
		// ========== END VALIDASI HEADER ========== //

		// Mulai transaksi
		$this->cb->trans_begin();

		$inserted = 0;
		$skipped = [];
		$show = [];

		foreach ($sheet as $i => $row) {

			// Skip header (baris 1)
			if ($i == 1)
				continue;

			// Jika baris kosong → skip
			if ($row['A'] == null && $row['H'] == null)
				continue;

			$no_smu = ($row['H']) ? trim($row['H']) : '';

			// === CEK DUPLIKASI === //
			$existing = $this->cb->get_where('sales', ['no_smu' => $no_smu])->row();
			if ($existing) {
				$skipped[] = [
					'no_smu' => $no_smu,
					'uploaded_at' => $existing->upload_at
				];
				continue;
			}

			// === INSERT DATA === //
			$data = [
				'tanggal_terbang' => date('Y-m-d', strtotime($row['A'])),
				'airline_name' => $row['B'],
				'vendor' => $row['C'],
				'kode_komoditi' => $row['D'],
				'jenis_barang' => $row['E'],
				'shipper_name' => $row['F'],
				'agent_name' => $row['G'],
				'no_smu' => $row['H'],
				'origin' => $row['I'],
				'destination' => $row['J'],
				'flight_number' => $row['K'],
				'koli' => (int) $row['L'],
				'gross' => ($row['M'] == '-' or $row['M'] == '') ? 0 : str_replace(',', '.', $row['M']),
				'chargeable_weight' => ($row['N'] == '-' or $row['N'] == '') ? 0 : str_replace(',', '.', $row['N']),
				'selling_price' => ($row['O'] == '-' or $row['O'] == '') ? 0 : str_replace(',', '', $row['O']),
				'freight' => ($row['P'] == '-' or $row['P'] == '') ? 0 : str_replace(',', '', $row['P']),
				'surcharge_percent' => rtrim($row['Q'], '%'),
				'surcharge_nominal' => ($row['R'] == '-' or $row['R'] == '') ? 0 : str_replace(',', '', $row['R']),
				'hht' => ($row['S'] == '-' or $row['S'] == '') ? 0 : str_replace(',', '', $row['S']),
				'admin_fee' => ($row['T'] == '-' or $row['T'] == '') ? 0 : str_replace(',', '', $row['T']),
				'ppn_smu' => ($row['U'] == '-' or $row['U'] == '') ? 0 : str_replace(',', '', $row['U']),
				'total_freight' => ($row['V'] == '-' or $row['V'] == '') ? 0 : str_replace(',', '', $row['V']),
				'handling_charge' => ($row['W'] == '-' or $row['W'] == '') ? 0 : str_replace(',', '', $row['W']),
				'asuransi' => ($row['X'] == '-' or $row['X'] == '') ? 0 : str_replace(',', '', $row['X']),
				'extra_packing' => ($row['Y'] == '-' or $row['Y'] == '') ? 0 : str_replace(',', '', $row['Y']),
				'handling_dest' => ($row['Z'] == '-' or $row['Z'] == '') ? 0 : str_replace(',', '', $row['Z']),
				'other_charge' => ($row['AA'] == '-' or $row['AA'] == '') ? 0 : str_replace(',', '', $row['AA']),
				'sub_total_tagihan' => ($row['AB'] == '-' or $row['AB'] == '') ? 0 : str_replace(',', '', $row['AB']),
				'vat_percent' => rtrim($row['AC'], '%'),
				'vat_nominal' => ($row['AD'] == '-' or $row['AD'] == '') ? 0 : str_replace(',', '', $row['AD']),
				'total_tagihan' => ($row['AE'] == '-' or $row['AE'] == '') ? 0 : str_replace(',', '', $row['AE']),
				'diskon_customer' => ($row['AF'] == '-' or $row['AF'] == '') ? 0 : str_replace(',', '', $row['AF']),
				'grand_total_tagihan' => ($row['AG'] == '-' or $row['AG'] == '') ? 0 : str_replace(',', '', $row['AG']),
				'upload_at' => date('Y-m-d H:i:s'),
				'upload_by' => $nip,
				'id_cabang' => $id_cabang,
				'is_billing' => 0,
				'status_sales' => 1
			];

			// $show[] = $data;

			$this->cb->insert('sales', $data);
			$inserted++;
		}

		// echo '<pre>';
		// print_r($show);
		// echo '</pre>';
		// exit;

		// Commit / rollback
		if ($this->cb->trans_status() === false) {
			$this->cb->trans_rollback();
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal upload data.'
			]);
			exit;
		} else {
			$this->cb->trans_commit();

			$msg = "Berhasil upload <b>$inserted</b> data sales.";
			if (!empty($skipped)) {
				$msg .= "<br><br><b>Data yang tidak diupload (duplikat SMU):</b><ul>";
				foreach ($skipped as $s) {
					$msg .= "<li>SMU: <b>" . $s['no_smu'] . "</b> — sudah diupload pada <b>" . $s['uploaded_at'] . "</b></li>";
				}
				$msg .= "</ul>";
			}

			// $this->session->set_flashdata('success', $msg);
		}

		// === SIMPAN LOG UPLOAD === //
		$log_data = [
			'filename' => $_FILES['file_excel']['name'],
			'uploaded_by' => $nip,
			'total_rows' => count($sheet) - 1,
			'inserted_rows' => $inserted,
			'skipped_rows' => count($skipped),
			'uploaded_at' => date('Y-m-d H:i:s'),
			'full_log' => json_encode($skipped)
		];

		$this->cb->insert('sales_upload_log', $log_data);

		$response = [
			'status' => 'success',
			'inserted' => $inserted,
			'message' => $msg,  // ✅ TAMBAHKAN INI!
			'skipped' => $skipped,
			'filename' => $_FILES['file_excel']['name'],
			'total_rows' => count($sheet) - 1,
			'inserted_rows' => $inserted,
			'skipped_rows' => count($skipped),
		];

		ob_clean(); // hapus output buffer sebelum kirim JSON

		echo json_encode($response);
		exit;
	}

	public function create_invoice_sales()
	{
		// print_r($_POST);

		$nip = $this->session->userdata('nip');
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$result = $query->row_array()['COUNT(Id)'];

		$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query2 = $this->db->query($sql2);
		$result2 = $query2->row_array()['COUNT(id)'];

		$data = [
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'title' => "Create Invoice Sales",
			'customers' => $this->M_customer->list_customer(''),

			'pendapatan' => $this->M_coa->getCoaByCode('1'),
			'persediaan' => $this->M_coa->getCoaByCode('4'),
		];

		$data['title'] = "Daftar Sales";
		$data['pages'] = "pages/financial/v_create_invoice_sales";
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

		$this->cb->select('
			sales.*,
			COALESCE(purchase.hhp_pusat, 0) as hpp_pusat,
			COALESCE(purchase.asuransi, 0) as asuransi_hpp,
			COALESCE(purchase.ho_charge, 0) as ho_charge,
			COALESCE(purchase.total_hpp_smu, 0) as total_hpp_smu,
			COALESCE(purchase.hpp_grand_total_program, 0) as hpp_program,
			COALESCE(purchase.hpp_jasa_gudang, 0) as hpp_jasa_gudang,
			COALESCE(purchase.ra, 0) as ra,
			COALESCE(purchase.handling_ra, 0) as handling_ra,
			COALESCE(purchase.hpp_packing, 0) as hpp_packing,
			COALESCE(purchase.hpp_handling_dest, 0) as hpp_handling_dest,
			COALESCE(purchase.marketing_fee, 0) as marketing_fee,
			COALESCE(purchase.hpp_handling, 0) as hpp_handling,
			0 as hpp_other
		');
		$this->cb->join('purchase', 'purchase.no_smu = sales.no_smu AND purchase.tanggal_terbang = sales.tanggal_terbang', 'left');

		// PENTING: Filter harus untuk SALES, bukan PURCHASE!
		$this->cb->where('sales.is_billing', '0');
		$this->cb->where('sales.agent_name', $this->input->post('agent_name'));
		$this->cb->where('sales.tanggal_terbang >=', $this->input->post('tanggal_dari'));
		$this->cb->where('sales.tanggal_terbang <=', $this->input->post('tanggal_sampai'));

		$data['sales'] = $this->cb->get('sales')->result_array();

		// echo '<pre>';
		// print_r($data['sales']);
		// echo '</pre>';
		// exit;

		$this->load->view('index', $data);
	}


	public function upload_purchase()
	{
		$id_cabang = $this->session->userdata('kode_cabang');
		$nip = $this->session->userdata('nip');

		// Upload handler
		if (!isset($_FILES['file_excel_purchase']['name']) || $_FILES['file_excel_purchase']['name'] == '') {
			$this->session->set_flashdata('error', 'File Excel belum dipilih');
			redirect('financial/sales');
		}

		$file_tmp = $_FILES['file_excel_purchase']['tmp_name'];

		// Load PhpSpreadsheet
		require APPPATH . 'third_party/autoload.php';

		// Include PhpSpreadsheet from third_party
		require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_tmp);
		$spreadsheet = $reader->load($file_tmp);
		$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

		// ========== VALIDASI HEADER ========== //
		$expected_headers = [
			'A' => 'Tanggal Terbang',
			'B' => 'Airline Name',
			'C' => 'Vendor',
			'D' => 'Kode Komodity',
			'E' => 'Jenis Barang',
			'F' => 'Shipper Name',
			'G' => 'AGENT NAME',
			'H' => 'NO SMU',
			'I' => 'ORI',
			'J' => 'DEST',
			'K' => 'FLT NO',
			'L' => 'Koli',
			'M' => 'Kg',
			'N' => 'CHWT',
			'O' => 'HPP PUSAT',
			'P' => 'ASURANSI',
			'Q' => 'HO CHARGE',
			'R' => 'TOTAL HPP SMU',
			'S' => 'HPP GRAND TOTAL PROGRAM',
			'T' => 'HPP JASA GUDANG',
			'U' => 'RA',
			'V' => 'HANDLING RA',
			'W' => 'HPP PACKING',
			'X' => 'HPP OTHER CHARGE',
			'Y' => 'HPP HANDLING',
			'Z' => 'HPP HANDLING DEST',
			'AA' => 'MARKETING FEE'
		];

		// Ambil baris pertama (header)
		$header_row = $sheet[1];

		// Cek setiap kolom
		$header_errors = [];
		foreach ($expected_headers as $col => $expected_name) {
			$actual_name = trim($header_row[$col] ?? '');

			// Normalisasi untuk case-insensitive comparison
			if (strtolower($actual_name) !== strtolower($expected_name)) {
				$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
			}

			// if ($actual_name !== $expected_name) {
			// 	$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
			// }
		}

		// Jika ada error header, kirim response error
		if (!empty($header_errors)) {
			ob_clean();
			echo json_encode([
				'status' => 'error',
				'error_type' => 'header_validation',
				'message' => 'File Excel yang Anda upload tidak sesuai format template!',
				'total_errors' => count($header_errors),
				'error_details' => $header_errors
			]);
			exit;
		}
		// ========== END VALIDASI HEADER ========== //

		// Mulai transaksi
		$this->cb->trans_begin();

		$inserted = 0;
		$skipped = [];
		$show = [];

		foreach ($sheet as $i => $row) {

			// Skip header (baris 1)
			if ($i == 1)
				continue;

			// Jika baris kosong → skip
			if ($row['A'] == null && $row['H'] == null)
				continue;

			$no_smu = ($row['H']) ? trim($row['H']) : '';

			// === CEK DUPLIKASI === //
			$existing = $this->cb->get_where('purchase', ['no_smu' => $no_smu])->row();
			if ($existing) {
				$skipped[] = [
					'no_smu' => $no_smu,
					'uploaded_at' => $existing->upload_at
				];
				continue;
			}

			// === INSERT DATA === //
			$data = [
				'tanggal_terbang' => date('Y-m-d', strtotime($row['A'])),
				'airline_name' => $row['B'],
				'vendor' => $row['C'],
				'kode_komoditi' => $row['D'],
				'jenis_barang' => $row['E'],
				'shipper_name' => $row['F'],
				'agent_name' => $row['G'],
				'no_smu' => $row['H'],
				'origin' => $row['I'],
				'destination' => $row['J'],
				'flight_number' => $row['K'],
				'koli' => (int) $row['L'],
				'gross' => ($row['M'] == '-' or $row['M'] == '') ? 0 : str_replace(',', '.', $row['M']),
				'chargeable_weight' => ($row['N'] == '-' or $row['N'] == '') ? 0 : str_replace(',', '.', $row['N']),
				'hhp_pusat' => ($row['O'] == '-' or $row['O'] == '') ? 0 : str_replace(',', '', $row['O']),
				'asuransi' => ($row['P'] == '-' or $row['P'] == '') ? 0 : str_replace(',', '', $row['P']),
				'ho_charge' => ($row['Q'] == '-' or $row['Q'] == '') ? 0 : str_replace(',', '', $row['Q']),
				'total_hpp_smu' => ($row['R'] == '-' or $row['R'] == '') ? 0 : str_replace(',', '', $row['R']),
				'hpp_grand_total_program' => ($row['S'] == '-' or $row['S'] == '') ? 0 : str_replace(',', '', $row['S']),
				'hpp_jasa_gudang' => ($row['T'] == '-' or $row['T'] == '') ? 0 : str_replace(',', '', $row['T']),
				'ra' => ($row['U'] == '-' or $row['U'] == '') ? 0 : str_replace(',', '', $row['U']),
				'handling_ra' => ($row['V'] == '-' or $row['V'] == '') ? 0 : str_replace(',', '', $row['V']),
				'hpp_packing' => ($row['W'] == '-' or $row['W'] == '') ? 0 : str_replace(',', '', $row['W']),
				'hpp_other_charge' => ($row['X'] == '-' or $row['X'] == '') ? 0 : str_replace(',', '', $row['X']),
				'hpp_handling' => ($row['Y'] == '-' or $row['Y'] == '') ? 0 : str_replace(',', '', $row['Y']),
				'hpp_handling_dest' => ($row['Z'] == '-' or $row['Z'] == '') ? 0 : str_replace(',', '', $row['Z']),
				'marketing_fee' => ($row['AA'] == '-' or $row['AA'] == '') ? 0 : str_replace(',', '', $row['AA']),
				'upload_by' => $nip,
				'id_cabang' => $id_cabang,
			];

			// $show[] = $data;

			$this->cb->insert('purchase', $data);
			$inserted++;
		}

		// echo '<pre>';
		// print_r($show);
		// echo '</pre>';
		// exit;

		// Commit / rollback
		if ($this->cb->trans_status() === false) {
			$this->cb->trans_rollback();
			echo json_encode([
				'status' => 'error',
				'message' => 'Gagal upload data.'
			]);
			exit;
		} else {
			$this->cb->trans_commit();

			$msg = "Berhasil upload <b>$inserted</b> data sales.";
			if (!empty($skipped)) {
				$msg .= "<br><br><b>Data yang tidak diupload (duplikat SMU):</b><ul>";
				foreach ($skipped as $s) {
					$msg .= "<li>SMU: <b>" . $s['no_smu'] . "</b> — sudah diupload pada <b>" . $s['uploaded_at'] . "</b></li>";
				}
				$msg .= "</ul>";
			}

			// $this->session->set_flashdata('success', $msg);
		}

		// === SIMPAN LOG UPLOAD === //
		$log_data = [
			'filename' => $_FILES['file_excel_purchase']['name'],
			'uploaded_by' => $nip,
			'total_rows' => count($sheet) - 1,
			'inserted_rows' => $inserted,
			'skipped_rows' => count($skipped),
			'uploaded_at' => date('Y-m-d H:i:s'),
			'full_log' => json_encode($skipped)
		];

		$this->cb->insert('purchase_upload_log', $log_data);

		$response = [
			'status' => 'success',
			'inserted' => $inserted,
			'message' => $msg,  // ✅ TAMBAHKAN INI!
			'skipped' => $skipped,
			'filename' => $_FILES['file_excel']['name'],
			'total_rows' => count($sheet) - 1,
			'inserted_rows' => $inserted,
			'skipped_rows' => count($skipped),
		];

		ob_clean(); // hapus output buffer sebelum kirim JSON

		echo json_encode($response);
		exit;
	}

	public function save_invoice_sales()
	{
		$id_user = $this->session->userdata('nip');
		$kode_cabang = $this->session->userdata('kode_cabang'); // ✅ Definisikan di awal

		// Ambil data POST
		$subtotal = $this->convertToNumberWithComma($this->input->post('subtotal'));
		$vat = $this->convertToNumberWithComma($this->input->post('vat'));
		$pph23 = $this->convertToNumberWithComma($this->input->post('pph23'));
		$total_nonpph = $this->convertToNumberWithComma($this->input->post('total_nonpph'));
		$total_denganpph = $this->convertToNumberWithComma($this->input->post('total_denganpph'));
		$total_biaya = $this->convertToNumberWithComma($this->input->post('total_biaya'));
		$nominal_bayar = $this->convertToNumberWithComma($this->input->post('nominal_bayar'));
		$gross_profit = $this->convertToNumberWithComma($this->input->post('gross_profit'));
		$profit_margin = $this->input->post('profit_margin');

		$opsi_termin = $this->input->post('opsi_termin');
		$opsi_pph = $this->input->post('pph23_check');
		$coa_debit = $this->input->post('coa_debit');
		$coa_kredit = $this->input->post('coa_kredit');
		$tgl_invoice = $this->input->post('tanggal_invoice');
		$keterangan = trim($this->input->post('keterangan'));

		$pph = isset($opsi_pph) ? '0.02' : 0;

		// ===== ✅ VALIDASI COA DI AWAL (SEBELUM PROSES APAPUN) ===== //

		// Cek COA Debit (dari form - biasanya Persediaan)
		$cek_coa_debit = $this->cb
			->where('no_sbb', $coa_debit)
			->where('id_cabang', $kode_cabang)
			->get('t_coa_sbb')
			->row_array();

		if (!$cek_coa_debit) {
			$this->session->set_flashdata('message_error', "COA Debit ($coa_debit) tidak ditemukan di cabang ini.");
			redirect('financial/create_invoice_sales'); // ✅ redirect ke form create
			return;
		}

		// Cek COA Kredit (dari form - biasanya Piutang)
		$cek_coa_kredit = $this->cb
			->where('no_sbb', $coa_kredit)
			->where('id_cabang', $kode_cabang)
			->get('t_coa_sbb')
			->row_array();

		if (!$cek_coa_kredit) {
			$this->session->set_flashdata('message_error', "COA Kredit ($coa_kredit) tidak ditemukan di cabang ini.");
			redirect('financial/create_invoice_sales');
			return;
		}

		// Cek COA untuk Jurnal 2: Piutang Usaha (13010)
		$coa_debit_2 = "13010";
		$cek_coa_debit_2 = $this->cb
			->where('no_sbb', $coa_debit_2)
			->where('id_cabang', $kode_cabang)
			->get('t_coa_sbb')
			->row_array();

		if (!$cek_coa_debit_2) {
			$this->session->set_flashdata('message_error', 'COA 13010 - Piutang Usaha tidak ditemukan di cabang ini.');
			redirect('financial/create_invoice_sales');
			return;
		}

		// Cek COA untuk Jurnal 2: PAD-Operasional Lainnya (40401)
		$coa_kredit_2 = "40401";
		$cek_coa_kredit_2 = $this->cb
			->where('no_lr_sbb', $coa_kredit_2) // ✅ Pakai no_lr_sbb karena ini t_coalr_sbb
			->where('id_cabang', $kode_cabang)
			->get('t_coalr_sbb')
			->row_array();

		if (!$cek_coa_kredit_2) {
			$this->session->set_flashdata('message_error', 'COA 40401 - PAD Operasional Lainnya tidak ditemukan di cabang ini.');
			redirect('financial/create_invoice_sales');
			return;
		}

		// ===== VALIDASI SELESAI - LANJUT PROSES ===== //

		// Generate nomor invoice
		$tahun = substr($tgl_invoice, 0, 4);
		$max_num = $this->M_invoice->select_max($tahun);
		$bilangan = !$max_num['max'] ? 1 : $max_num['max'] + 1;

		$month = substr($tgl_invoice, 5, 2);
		$year = substr($tgl_invoice, 2, 2);
		$no_inv = sprintf("%04d", $bilangan);
		$kode_cabang_format = sprintf("%02d", $kode_cabang);
		$kop_invoice = $this->session->userdata('nama_akronim') . "-" . $kode_cabang_format;
		$slug = $no_inv . '/' . strtoupper($kop_invoice) . '/' . intToRoman($month) . '/' . $year;

		// Data invoice header
		$invoice_data = [
			'no_invoice' => $no_inv,
			'tanggal_invoice' => $tgl_invoice,
			'created_by' => $id_user,
			'keterangan' => $keterangan,
			'id_customer' => $this->input->post('bill_to'),
			'subtotal' => $subtotal,
			'besaran_ppn' => $vat,
			'ppn' => '0.11',
			'opsi_pph23' => isset($opsi_pph) ? $opsi_pph : '0',
			'pph' => $pph,
			'besaran_pph' => $pph23,
			'total_nonpph' => $total_nonpph,
			'total_denganpph' => $total_denganpph,
			'coa_debit' => $coa_debit,
			'coa_kredit' => $coa_kredit,
			'total_biaya' => $total_biaya,
			'nominal_bayar' => $nominal_bayar,
			'nominal_pendapatan' => $gross_profit,
			'jenis_invoice' => 'agen_smu', // ✅ hapus duplikasi
			'opsi_termin' => isset($opsi_termin) ? $opsi_termin : '0',
			'status_pendapatan' => '1',
			'slug' => $slug,
			'id_cabang' => $kode_cabang,
			'id_company' => $this->session->userdata('user_perusahaan_id')
		];

		$this->cb->trans_begin();

		// Insert invoice
		$id_invoice = $this->M_invoice->insert($invoice_data);

		if (!$id_invoice) {
			$this->cb->trans_rollback();
			$this->session->set_flashdata('message_error', 'Failed to create invoice.');
			redirect("financial/sales");
		}

		// Ambil array data sales
		$sales_ids = array_keys($this->input->post('koli'));
		$kolis = $this->input->post('koli');
		$actuals = $this->input->post('actual');
		$chwts = $this->input->post('chwt');
		$selling_prices = $this->input->post('selling_price');
		$freights = $this->input->post('freight');
		$vat_nominals = $this->input->post('vat_nominal');
		$subtotal_rows = $this->input->post('subtotal_row');
		$total_hpp_rows = $this->input->post('total_hpp_row');
		$profit_rows = $this->input->post('profit_row');

		// Detail charges
		$surcharge_percents = $this->input->post('surcharge_percent');
		$surcharge_nominals = $this->input->post('surcharge_nominal');
		$hhts = $this->input->post('hht');
		$admin_fees = $this->input->post('admin_fee');
		$ppn_smus = $this->input->post('ppn_smu');
		$total_freights = $this->input->post('total_freight');
		$handling_charges = $this->input->post('handling_charge');
		$asuransis = $this->input->post('asuransi');
		$extra_packings = $this->input->post('extra_packing');
		$handling_dests = $this->input->post('handling_dest');
		$other_charges = $this->input->post('other_charge');

		// HPP breakdown
		$hpp_pusats = $this->input->post('hpp_pusat');
		$ho_charges = $this->input->post('ho_charge');
		$hpp_programs = $this->input->post('hpp_program');
		$hpp_jasa_gudangs = $this->input->post('hpp_jasa_gudang');
		$ras = $this->input->post('ra');
		$handling_ras = $this->input->post('handling_ra');
		$hpp_packings = $this->input->post('hpp_packing');
		$hpp_handlings = $this->input->post('hpp_handling');
		$hpp_handling_dests = $this->input->post('hpp_handling_dest');
		$marketing_fees = $this->input->post('marketing_fee');
		$hpp_others = $this->input->post('hpp_other');
		$asuransi_hpps = $this->input->post('asuransi_hpp');

		$detail_data = [];

		foreach ($sales_ids as $sales_id) {
			$detail_data[] = [
				'id_invoice' => $id_invoice,
				'id_sales' => $sales_id,
				'koli' => $kolis[$sales_id],
				'actual' => $this->convertToNumberWithComma($actuals[$sales_id]),
				'chwt' => $this->convertToNumberWithComma($chwts[$sales_id]),
				'selling_price' => $this->convertToNumberWithComma($selling_prices[$sales_id]),
				'freight' => $this->convertToNumberWithComma($freights[$sales_id]),
				'vat_nominal' => $this->convertToNumber($vat_nominals[$sales_id]),
				'subtotal_row' => $this->convertToNumberWithComma($subtotal_rows[$sales_id]),
				'total_hpp_row' => $this->convertToNumberWithComma($total_hpp_rows[$sales_id]),
				'profit_row' => $this->convertToNumberWithComma($profit_rows[$sales_id]),

				// Charges
				'surcharge_percent' => $surcharge_percents[$sales_id],
				'surcharge_nominal' => $this->convertToNumberWithComma($surcharge_nominals[$sales_id]),
				'hht' => $this->convertToNumberWithComma($hhts[$sales_id]),
				'admin_fee' => $this->convertToNumberWithComma($admin_fees[$sales_id]),
				'ppn_smu' => $this->convertToNumberWithComma($ppn_smus[$sales_id]),
				'total_freight' => $this->convertToNumberWithComma($total_freights[$sales_id]),
				'handling_charge' => $this->convertToNumberWithComma($handling_charges[$sales_id]),
				'asuransi' => $this->convertToNumberWithComma($asuransis[$sales_id]),
				'extra_packing' => $this->convertToNumberWithComma($extra_packings[$sales_id]),
				'handling_dest' => $this->convertToNumberWithComma($handling_dests[$sales_id]),
				'other_charge' => $this->convertToNumberWithComma($other_charges[$sales_id]),

				// HPP
				'hpp_pusat' => $this->convertToNumberWithComma($hpp_pusats[$sales_id]),
				'ho_charge' => $this->convertToNumberWithComma($ho_charges[$sales_id]),
				'hpp_program' => $this->convertToNumberWithComma($hpp_programs[$sales_id]),
				'hpp_jasa_gudang' => $this->convertToNumberWithComma($hpp_jasa_gudangs[$sales_id]),
				'ra' => $this->convertToNumberWithComma($ras[$sales_id]),
				'handling_ra' => $this->convertToNumberWithComma($handling_ras[$sales_id]),
				'hpp_packing' => $this->convertToNumberWithComma($hpp_packings[$sales_id]),
				'hpp_handling' => $this->convertToNumberWithComma($hpp_handlings[$sales_id]),
				'hpp_handling_dest' => $this->convertToNumberWithComma($hpp_handling_dests[$sales_id]),
				'marketing_fee' => $this->convertToNumberWithComma($marketing_fees[$sales_id]),
				'hpp_other' => $this->convertToNumberWithComma($hpp_others[$sales_id]),
				'asuransi_hpp' => $this->convertToNumberWithComma($asuransi_hpps[$sales_id]),

				'created_by' => $id_user,
				'id_cabang' => $kode_cabang,
				'id_company' => $this->session->userdata('user_perusahaan_id')
			];
		}

		if (!empty($detail_data)) {
			$insert = $this->M_invoice->insert_batch_sales($detail_data);

			if ($insert) {
				foreach ($sales_ids as $sales_id) {
					$update_sales = [
						'is_billing' => '1',
						'id_invoice' => $id_invoice,
						'tanggal_billing' => date('Y-m-d H:i:s')
					];
					$this->cb->where('Id', $sales_id)->update('sales', $update_sales);
				}

				// ✅ Jurnal 1: Piutang Usaha bertambah, Persediaan berkurang
				$this->posting($coa_debit, $coa_kredit, $keterangan, $total_biaya, $tgl_invoice, $id_invoice, NULL, NULL);

				// ✅ Jurnal 2: Piutang Usaha bertambah, PAD-Operasional bertambah (tanpa pengecekan lagi karena sudah di awal)
				$this->posting($coa_debit_2, $coa_kredit_2, $keterangan, $gross_profit, $tgl_invoice, $id_invoice);

				$this->cb->trans_commit();
				$this->session->set_flashdata('message_name', 'Sales invoice berhasil dibuat: ' . $no_inv);
			} else {
				$this->cb->trans_rollback();
				$this->session->set_flashdata('message_error', 'Gagal buat invoice.');
			}
			redirect("financial/invoice");
		}
	}

	public function edit_invoice_sales($id)
	{
		$inv = $this->M_invoice->showById($id);

		// Validasi jenis invoice
		if ($inv['jenis_invoice'] != 'agen_smu' && $inv['jenis_invoice'] != 'sales') {
			$this->session->set_flashdata('message_error', 'Invoice ini bukan jenis sales/agen_smu');
			redirect('financial/invoice');
		}

		// Cek status, tidak bisa edit jika sudah bayar atau void
		if ($inv['status_bayar'] == '1') {
			$this->session->set_flashdata('message_error', 'Invoice sudah lunas, tidak dapat diedit');
			redirect('financial/invoice');
		}

		if ($inv['status_void'] == '1') {
			$this->session->set_flashdata('message_error', 'Invoice sudah void, tidak dapat diedit');
			redirect('financial/invoice');
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

		// Ambil detail invoice sales
		$sales_details = $this->M_invoice->get_invoice_sales_details($id);

		$data = [
			'title' => 'Edit Invoice Sales No. ' . $inv['no_invoice'],
			'inv' => $inv,
			'sales' => $sales_details,
			'customers' => $this->M_customer->list_customer(),
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'pendapatan' => $this->M_coa->getCoaByCode('1'),
			'persediaan' => $this->M_coa->getCoaByCode('4'),
			'coa_kas' => $this->M_coa->getCoaByCode('1'),
			'coa_pendapatan' => $this->M_coa->getCoaByCode('4'),
		];

		$data['pages'] = "pages/financial/v_edit_invoice_sales";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_invoice_sales_edit';
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
		$this->load->view('index', $data);
	}

	public function update_invoice_sales()
	{
		$id_invoice = $this->input->post('id_invoice');
		$id_user = $this->session->userdata('nip');

		// Ambil data POST
		$subtotal = $this->convertToNumberWithComma($this->input->post('subtotal'));
		$vat = $this->convertToNumberWithComma($this->input->post('vat'));
		$pph23 = $this->convertToNumberWithComma($this->input->post('pph23'));
		$total_nonpph = $this->convertToNumberWithComma($this->input->post('total_nonpph'));
		$total_denganpph = $this->convertToNumberWithComma($this->input->post('total_denganpph'));
		$total_biaya = $this->convertToNumberWithComma($this->input->post('total_biaya'));
		$nominal_bayar = $this->convertToNumberWithComma($this->input->post('nominal_bayar'));
		$gross_profit = $this->convertToNumberWithComma($this->input->post('gross_profit'));
		$profit_margin = $this->input->post('profit_margin');

		$opsi_termin = $this->input->post('opsi_termin');
		$opsi_pph = $this->input->post('pph23_check');
		$coa_debit = $this->input->post('coa_debit');
		$coa_kredit = $this->input->post('coa_kredit');
		$tgl_invoice = $this->input->post('tanggal_invoice');
		$keterangan = trim($this->input->post('keterangan'));

		$pph = isset($opsi_pph) ? '0.02' : 0;

		// Data invoice header untuk update
		$invoice_data = [
			'tanggal_invoice' => $tgl_invoice,
			'updated_by' => $id_user,
			'updated_at' => date('Y-m-d H:i:s'),
			'keterangan' => $keterangan,
			'id_customer' => $this->input->post('bill_to'),
			'subtotal' => $subtotal,
			'besaran_ppn' => $vat,
			'ppn' => '0.11',
			'opsi_pph23' => isset($opsi_pph) ? $opsi_pph : '0',
			'pph' => $pph,
			'besaran_pph' => $pph23,
			'total_nonpph' => $total_nonpph,
			'total_denganpph' => $total_denganpph,
			'coa_debit' => $coa_debit,
			'coa_kredit' => $coa_kredit,
			'total_biaya' => $total_biaya,
			'nominal_bayar' => $nominal_bayar,
			'nominal_pendapatan' => $gross_profit,
			'opsi_termin' => isset($opsi_termin) ? $opsi_termin : '0',
		];

		$this->cb->trans_begin();

		// ===== AMBIL DATA INVOICE LAMA ===== //
		$inv = $this->M_invoice->showById($id_invoice);

		if (!$inv) {
			$this->cb->trans_rollback();
			$this->session->set_flashdata('message_error', 'Invoice tidak ditemukan.');
			redirect('financial/invoice');
		}

		// ===== JURNAL BALIK SEBELUM UPDATE ===== //
		$keterangan_balik = "Jurnal balik edit invoice sales " . $inv['no_invoice'];
		$coa_kredit_lama = $inv['coa_kredit'];
		$coa_debit_lama = $inv['coa_debit'];
		$total_biaya_lama = $inv['total_biaya'];
		$gross_profit_lama = $inv['nominal_pendapatan'];

		// Jurnal balik 1: Balik Piutang Usaha & Persediaan
		$this->posting($coa_kredit_lama, $coa_debit_lama, $keterangan_balik, $total_biaya_lama, $inv['tanggal_invoice'], $inv['Id'], NULL, NULL, NULL);

		// Jurnal balik 2: Balik Piutang Usaha & PAD-Operasional
		$coa_debit_2_lama = "13010";
		$coa_kredit_2_lama = "40401";
		$this->posting($coa_kredit_2_lama, $coa_debit_2_lama, $keterangan_balik, $gross_profit_lama, $inv['tanggal_invoice'], $inv['Id']);

		// ===== UPDATE INVOICE HEADER ===== //
		if (!$this->M_invoice->update_invoice($id_invoice, $invoice_data)) {
			$this->cb->trans_rollback();
			$this->session->set_flashdata('message_error', 'Failed to update invoice.');
			redirect('financial/invoice');
		}

		// ===== AMBIL ARRAY DATA SALES ===== //
		$sales_ids = array_keys($this->input->post('koli'));
		$kolis = $this->input->post('koli');
		$actuals = $this->input->post('actual');
		$chwts = $this->input->post('chwt');
		$selling_prices = $this->input->post('selling_price');
		$freights = $this->input->post('freight');
		$vat_nominals = $this->input->post('vat_nominal');
		$subtotal_rows = $this->input->post('subtotal_row');
		$total_hpp_rows = $this->input->post('total_hpp_row');
		$profit_rows = $this->input->post('profit_row');

		// Detail charges
		$surcharge_percents = $this->input->post('surcharge_percent');
		$surcharge_nominals = $this->input->post('surcharge_nominal');
		$hhts = $this->input->post('hht');
		$admin_fees = $this->input->post('admin_fee');
		$ppn_smus = $this->input->post('ppn_smu');
		$total_freights = $this->input->post('total_freight');
		$handling_charges = $this->input->post('handling_charge');
		$asuransis = $this->input->post('asuransi');
		$extra_packings = $this->input->post('extra_packing');
		$handling_dests = $this->input->post('handling_dest');
		$other_charges = $this->input->post('other_charge');

		// HPP breakdown
		$hpp_pusats = $this->input->post('hpp_pusat');
		$ho_charges = $this->input->post('ho_charge');
		$hpp_programs = $this->input->post('hpp_program');
		$hpp_jasa_gudangs = $this->input->post('hpp_jasa_gudang');
		$ras = $this->input->post('ra');
		$handling_ras = $this->input->post('handling_ra');
		$hpp_packings = $this->input->post('hpp_packing');
		$hpp_handlings = $this->input->post('hpp_handling');
		$hpp_handling_dests = $this->input->post('hpp_handling_dest');
		$marketing_fees = $this->input->post('marketing_fee');
		$hpp_others = $this->input->post('hpp_other');
		$asuransi_hpps = $this->input->post('asuransi_hpp');

		// ===== HAPUS DETAIL INVOICE LAMA ===== //
		$this->cb->where('id_invoice', $id_invoice)->delete('invoice_sales_detail');

		// ===== INSERT DETAIL BARU ===== //
		$detail_data = [];
		foreach ($sales_ids as $sales_id) {
			$detail_data[] = [
				'id_invoice' => $id_invoice,
				'id_sales' => $sales_id,
				'koli' => $kolis[$sales_id],
				'actual' => $this->convertToNumberWithComma($actuals[$sales_id]),
				'chwt' => $this->convertToNumberWithComma($chwts[$sales_id]),
				'selling_price' => $this->convertToNumberWithComma($selling_prices[$sales_id]),
				'freight' => $this->convertToNumberWithComma($freights[$sales_id]),
				'vat_nominal' => $this->convertToNumber($vat_nominals[$sales_id]),
				'subtotal_row' => $this->convertToNumberWithComma($subtotal_rows[$sales_id]),
				'total_hpp_row' => $this->convertToNumberWithComma($total_hpp_rows[$sales_id]),
				'profit_row' => $this->convertToNumberWithComma($profit_rows[$sales_id]),

				// Charges
				'surcharge_percent' => $surcharge_percents[$sales_id],
				'surcharge_nominal' => $this->convertToNumberWithComma($surcharge_nominals[$sales_id]),
				'hht' => $this->convertToNumberWithComma($hhts[$sales_id]),
				'admin_fee' => $this->convertToNumberWithComma($admin_fees[$sales_id]),
				'ppn_smu' => $this->convertToNumberWithComma($ppn_smus[$sales_id]),
				'total_freight' => $this->convertToNumberWithComma($total_freights[$sales_id]),
				'handling_charge' => $this->convertToNumberWithComma($handling_charges[$sales_id]),
				'asuransi' => $this->convertToNumberWithComma($asuransis[$sales_id]),
				'extra_packing' => $this->convertToNumberWithComma($extra_packings[$sales_id]),
				'handling_dest' => $this->convertToNumberWithComma($handling_dests[$sales_id]),
				'other_charge' => $this->convertToNumberWithComma($other_charges[$sales_id]),

				// HPP
				'hpp_pusat' => $this->convertToNumberWithComma($hpp_pusats[$sales_id]),
				'ho_charge' => $this->convertToNumberWithComma($ho_charges[$sales_id]),
				'hpp_program' => $this->convertToNumberWithComma($hpp_programs[$sales_id]),
				'hpp_jasa_gudang' => $this->convertToNumberWithComma($hpp_jasa_gudangs[$sales_id]),
				'ra' => $this->convertToNumberWithComma($ras[$sales_id]),
				'handling_ra' => $this->convertToNumberWithComma($handling_ras[$sales_id]),
				'hpp_packing' => $this->convertToNumberWithComma($hpp_packings[$sales_id]),
				'hpp_handling' => $this->convertToNumberWithComma($hpp_handlings[$sales_id]),
				'hpp_handling_dest' => $this->convertToNumberWithComma($hpp_handling_dests[$sales_id]),
				'marketing_fee' => $this->convertToNumberWithComma($marketing_fees[$sales_id]),
				'hpp_other' => $this->convertToNumberWithComma($hpp_others[$sales_id]),
				'asuransi_hpp' => $this->convertToNumberWithComma($asuransi_hpps[$sales_id]),

				'updated_by' => $id_user,
				'updated_at' => date('Y-m-d H:i:s'),
			];
		}

		if (!empty($detail_data)) {
			if (!$this->M_invoice->insert_batch_sales($detail_data)) {
				$this->cb->trans_rollback();
				$this->session->set_flashdata('message_error', 'Gagal update detail invoice.');
				redirect('financial/invoice');
			}
		}

		// ===== POSTING JURNAL BARU ===== //

		// Jurnal 1: Piutang Usaha bertambah (dari total_biaya), Persediaan berkurang sebesar total_biaya
		$this->posting($coa_debit, $coa_kredit, $keterangan, $total_biaya, $tgl_invoice, $id_invoice, NULL, NULL, NULL);

		// Jurnal 2: Piutang Usaha bertambah (pendapatan), PAD-Operasional Lainnya bertambah
		$coa_debit_2 = "13010";
		$coa_kredit_2 = "40401";
		$this->posting($coa_debit_2, $coa_kredit_2, $keterangan, $gross_profit, $tgl_invoice, $id_invoice);

		// ===== COMMIT TRANSAKSI ===== //
		if ($this->cb->trans_status() === FALSE) {
			$this->cb->trans_rollback();
			$this->session->set_flashdata('message_error', 'Transaction failed.');
		} else {
			$this->cb->trans_commit();
			$this->session->set_flashdata('message_name', 'Invoice sales berhasil diupdate.');
		}

		redirect('financial/invoice');
	}

	public function show_margin($id)
	{
		$nip = $this->session->userdata('nip');

		// Get memo & task count (standard untuk header)
		$sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$result = $query->row_array()['COUNT(Id)'];

		$sql2 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
		$query2 = $this->db->query($sql2);
		$result2 = $query2->row_array()['COUNT(id)'];

		// Get sales data by ID
		$this->cb->where('Id', $id);
		$sales = $this->cb->get('sales')->row_array();

		if (!$sales) {
			show_404();
			return;
		}

		// Get purchase data berdasarkan no_smu dan tanggal_terbang
		$this->cb->where('no_smu', $sales['no_smu']);
		$this->cb->where('tanggal_terbang', $sales['tanggal_terbang']);
		$purchase = $this->cb->get('purchase')->row_array();

		// ===== HITUNG REVENUE ===== //
		$subtotal = $sales['sub_total_tagihan'];
		$vat = $sales['vat_nominal'];
		$total_tagihan = $sales['grand_total_tagihan'];

		// ===== HITUNG TOTAL HPP ===== //
		$total_hpp = 0;
		if ($purchase) {
			$total_hpp =
				($purchase['hhp_pusat'] ?? 0) +
				($purchase['ho_charge'] ?? 0) +
				($purchase['hpp_jasa_gudang'] ?? 0) +
				($purchase['ra'] ?? 0) +
				($purchase['handling_ra'] ?? 0) +
				($purchase['hpp_packing'] ?? 0) +
				($purchase['hpp_handling'] ?? 0) +
				($purchase['hpp_handling_dest'] ?? 0) +
				($purchase['marketing_fee'] ?? 0) +
				($purchase['hpp_other_charge'] ?? 0) +
				($purchase['asuransi'] ?? 0);
		}

		// ===== HITUNG PROFIT & MARGIN ===== //
		$gross_profit = $subtotal - $total_hpp;
		$profit_margin = $subtotal > 0 ? ($gross_profit / $subtotal * 100) : 0;

		// Prepare data untuk view
		$data = [
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'title' => "Margin Profit - SMU " . $sales['no_smu'],
			'sales' => $sales,
			'purchase' => $purchase,
			'subtotal' => $subtotal,
			'vat' => $vat,
			'total_tagihan' => $total_tagihan,
			'total_hpp' => $total_hpp,
			'gross_profit' => $gross_profit,
			'profit_margin' => $profit_margin,
		];

		$data['pages'] = "pages/financial/v_show_margin";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	public function proses_penihilan()
	{
		// ===== VALIDASI PASSWORD ===== //
		$password = $this->input->post('password');
		$tanggal_transaksi = $this->input->post('tanggal_transaksi');

		// Validasi form submission
		$this->form_validation->set_rules('tanggal_transaksi', 'Tanggal Transaksi', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$error_msg = validation_errors() ? strip_tags(validation_errors()) : 'Form tidak lengkap';
			$this->session->set_flashdata('message_error', $error_msg);
			redirect('financial/closing');
			return;
		}

		// Validasi password user
		$nip = $this->session->userdata('nip');
		$user_data = $this->M_login->datapengguna($this->session->userdata('username'));

		if (!password_verify($password, $user_data->password)) {
			$this->session->set_flashdata('message_error', 'Password yang Anda masukkan salah!');
			redirect('financial/closing');
			return;
		}

		$kode_cabang = $this->session->userdata('kode_cabang');

		// Validasi tanggal
		if (!$tanggal_transaksi) {
			$this->session->set_flashdata('message_error', 'Tanggal transaksi tidak valid.');
			redirect('financial/closing');
			return;
		}

		// ===== LOGIC SAMA DENGAN prepareNeracaReportByDate ===== //
		$date = new DateTime($tanggal_transaksi);
		$date->modify('first day of previous month');
		$periode = $date->format('Y-m');

		// ===== STEP 1: Ambil Saldo Awal dari Periode Sebelumnya ===== //
		$saldo_awal_data = $this->M_coa->cek_saldo_awal($periode);

		if (empty($saldo_awal_data)) {
			$this->session->set_flashdata('message_error', 'Saldo awal periode ' . $periode . ' tidak ditemukan. Harap lakukan closing akhir bulan periode sebelumnya terlebih dahulu.');
			redirect('financial/closing');
			return;
		}

		// Decode saldo awal
		$coaLastPeriod = json_decode($saldo_awal_data['coa']);

		// ===== STEP 2: Ambil Transaksi Periode Berjalan ===== //
		// PERSIS SAMA dengan prepareNeracaReportByDate
		$pendapatan = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'PASIVA', $tanggal_transaksi, $periode);
		$beban = $this->M_coa->getNeracaByDate('t_coalr_sbb', 'AKTIVA', $tanggal_transaksi, $periode);

		// ===== STEP 3: Gabungkan Data - PERSIS SAMA dengan prepareNeracaReportByDate ===== //

		// Part Pendapatan
		$filteredCoaPendapatan = array_filter($coaLastPeriod, function ($item) {
			return $item->posisi === 'PASIVA' && $item->table_source === 't_coalr_sbb';
		});

		$combinedPendapatan = [];

		foreach ($pendapatan as $item) {
			if (!isset($combinedPendapatan[$item->no_sbb])) {
				$combinedPendapatan[$item->no_sbb] = (object) [
					'no_sbb' => $item->no_sbb,
					'nama_perkiraan' => $item->nama_perkiraan ?? '',
					'posisi' => 'PASIVA',
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
					'nama_perkiraan' => $item->nama_perkiraan ?? '',
					'posisi' => 'PASIVA',
					'saldo_awal' => $item->saldo_awal,
				];
			} else {
				$combinedPendapatan[$item->no_sbb]->saldo_awal += $item->saldo_awal;
			}
		}

		$total_pendapatan_display = array_sum(array_column($combinedPendapatan, 'saldo_awal'));

		// Part Beban
		$filteredCoaBeban = array_filter($coaLastPeriod, function ($item) {
			return $item->posisi === 'AKTIVA' && $item->table_source === 't_coalr_sbb';
		});

		$combinedBeban = [];

		foreach ($beban as $item) {
			if (!isset($combinedBeban[$item->no_sbb])) {
				$combinedBeban[$item->no_sbb] = (object) [
					'no_sbb' => $item->no_sbb,
					'nama_perkiraan' => $item->nama_perkiraan ?? '',
					'posisi' => 'AKTIVA',
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
					'nama_perkiraan' => $item->nama_perkiraan ?? '',
					'posisi' => 'AKTIVA',
					'saldo_awal' => $item->saldo_awal,
				];
			} else {
				$combinedBeban[$item->no_sbb]->saldo_awal += $item->saldo_awal;
			}
		}

		$total_beban_display = array_sum(array_column($combinedBeban, 'saldo_awal'));

		// ===== STEP 4: Ambil COA Laba Ditahan ===== //
		$coa_laba_ditahan = "32010";
		$id_laba_ditahan = $this->cb
			->where('no_sbb', $coa_laba_ditahan)
			->where('id_cabang', $kode_cabang)
			->get('t_coa_sbb')
			->row_array()['id'] ?? null;

		if (!$id_laba_ditahan) {
			$this->session->set_flashdata('message_error', 'COA laba ditahan tidak ditemukan.');
			redirect('financial/closing');
			return;
		}

		// Debug: Uncomment untuk cek data
		// echo '<pre>';
		// echo "Total Pendapatan: " . number_format($total_pendapatan_display, 0, ',', '.') . "\n";
		// echo "Total Beban: " . number_format($total_beban_display, 0, ',', '.') . "\n";
		// echo "\nDetail Pendapatan:\n";
		// print_r($combinedPendapatan);
		// echo "\nDetail Beban:\n";
		// print_r($combinedBeban);
		// echo '</pre>';
		// exit;

		// Persiapan data log
		$log_data = [
			'tanggal_proses' => date('Y-m-d H:i:s'),
			'tanggal_transaksi' => $tanggal_transaksi,
			'periode_saldo_awal' => $periode,
			'kode_cabang' => $kode_cabang,
			'nip' => $nip,
			'username' => $this->session->userdata('username'),
			'ip_address' => $this->input->ip_address()
		];

		$total_pendapatan = 0;
		$total_beban = 0;
		$count_jurnal_pendapatan = 0;
		$count_jurnal_beban = 0;
		$detail_logs = [];

		$this->cb->trans_start();

		// ===== STEP 5: Posting Jurnal Penihilan ===== //

		// Posting Pendapatan
		foreach ($combinedPendapatan as $coa) {
			$saldo_akhir = $coa->saldo_awal;

			// Skip jika saldo = 0
			if ($saldo_akhir == 0) continue;

			$nominal = abs($saldo_akhir);

			// PENDAPATAN (PASIVA) - Debit COA, Kredit Laba Ditahan
			$id_jurnal = $this->posting(
				$coa->no_sbb,              // Debit: COA Pendapatan
				$coa_laba_ditahan,         // Kredit: Laba Ditahan
				"PENIHILAN PENDAPATAN SECARA SISTEM - " . strtoupper($coa->nama_perkiraan),
				$nominal,
				$tanggal_transaksi,
				null,
				null,
				null
			);

			$detail_logs[] = [
				'tipe' => 'PENDAPATAN',
				'no_coa' => $coa->no_sbb,
				'nama_coa' => $coa->nama_perkiraan,
				'saldo_sebelum' => $saldo_akhir,
				'nominal' => $nominal,
				'id_jurnal' => $id_jurnal
			];

			$total_pendapatan += $nominal;
			$count_jurnal_pendapatan++;
		}

		// Posting Beban
		foreach ($combinedBeban as $coa) {
			$saldo_akhir = $coa->saldo_awal;

			// Skip jika saldo = 0
			if ($saldo_akhir == 0) continue;

			$nominal = abs($saldo_akhir);

			// BEBAN (AKTIVA) - Debit Laba Ditahan, Kredit COA
			$id_jurnal = $this->posting(
				$coa_laba_ditahan,         // Debit: Laba Ditahan
				$coa->no_sbb,              // Kredit: COA Beban
				"PENIHILAN BEBAN SECARA SISTEM - " . strtoupper($coa->nama_perkiraan),
				$nominal,
				$tanggal_transaksi,
				null,
				null,
				null
			);

			$detail_logs[] = [
				'tipe' => 'BEBAN',
				'no_coa' => $coa->no_sbb,
				'nama_coa' => $coa->nama_perkiraan,
				'saldo_sebelum' => $saldo_akhir,
				'nominal' => $nominal,
				'id_jurnal' => $id_jurnal
			];

			$total_beban += $nominal;
			$count_jurnal_beban++;
		}

		// Lengkapi data log
		$log_data['total_pendapatan'] = $total_pendapatan;
		$log_data['total_beban'] = $total_beban;
		$log_data['laba_rugi'] = $total_pendapatan - $total_beban;
		$log_data['jumlah_jurnal_pendapatan'] = $count_jurnal_pendapatan;
		$log_data['jumlah_jurnal_beban'] = $count_jurnal_beban;
		$log_data['status'] = 'SUCCESS';
		$log_data['keterangan'] = "Proses penihilan berhasil. Total Pendapatan: Rp " . number_format($total_pendapatan, 0, ',', '.') .
			", Total Beban: Rp " . number_format($total_beban, 0, ',', '.') .
			", Laba/Rugi: Rp " . number_format($total_pendapatan - $total_beban, 0, ',', '.');

		// Insert log utama
		$this->cb->insert('t_log_penihilan', $log_data);
		$id_log = $this->cb->insert_id();

		// Insert log detail - HANYA JIKA ADA DATA
		if (!empty($detail_logs)) {
			foreach ($detail_logs as &$detail) {
				$detail['id_log_penihilan'] = $id_log;
			}
			$this->cb->insert_batch('t_log_penihilan_detail', $detail_logs);
		}

		$this->cb->trans_complete();

		if ($this->cb->trans_status() === FALSE) {
			$this->session->set_flashdata('message_error', 'Gagal melakukan proses penihilan.');
		} else {
			$this->session->set_flashdata('message_name', 'Berhasil melakukan proses penihilan untuk periode ' . format_indo($tanggal_transaksi) . '. Total Laba/Rugi: Rp ' . number_format($total_pendapatan - $total_beban, 0, ',', '.'));
		}

		redirect('financial/closing');
	}

	public function ajax_edit_report_coa($id)
	{
		$this->cb->select('*');
		$this->cb->from('jurnal_neraca');
		$this->cb->where('id', $id);
		$get_coa = $this->cb->get()->row();
		$response = [
			'data' => $get_coa, // This will contain the COA object/array
		];
		echo json_encode($response);
	}

	public function update_report_per_coa()
	{

		$akun_debit = $this->input->post('neraca_debit');
		$akun_kredit = $this->input->post('neraca_kredit');
		$input_keterangan = $this->input->post('input_keterangan');
		$tanggal = $this->input->post('tanggal');
		$file = $this->input->post('file');
		$input_nominal = $this->input->post('input_nominal');

		// print_r($input_nominal);exit;

		// // Remove everything that is NOT a digit (0-9)
		// $clean_nominal = preg_replace('/[^0-9]/', '', $input_nominal);

		// // Convert to integer (optional, but good for calculations)
		// $input_nominal = (int)$clean_nominal;


		// BARU - support desimal format Indonesia (titik=ribuan, koma=desimal)
		// $input_nominal = str_replace('.', '', $input_nominal); // hapus titik ribuan
		// $input_nominal = str_replace(',', '.', $input_nominal); // koma → titik desimal
		// $input_nominal = (float)$input_nominal;
		// echo $final_nominal; // Output: 200000
		// exit();

		$data_update = [
			'tanggal'           => $tanggal,
			'akun_debit'           => $akun_debit,
			'jumlah_debit'           => $input_nominal,
			'akun_kredit'           => $akun_kredit,
			'jumlah_kredit'           => $input_nominal,
			'keterangan'           => $input_keterangan,
		];

		// echo '<pre>';
		// print_r($data_update);
		// echo '</pre>';
		// exit;

		$base64_data = null; // Initialize the variable to hold the Base64 string
		$file_name = null;   // <--- New variable to hold the file name
		$file_input_name = 'file'; // The name of your <input type="file">
		if ($this->session->userdata('is_premium')) {

			if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] != UPLOAD_ERR_NO_FILE) {
				echo "MASUK";


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

					redirect('financial/coa_report');

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
					redirect('financial/coa_report');

					return;
				}

				// 2. Encode the content to Base64
				$encoded_content = base64_encode($file_content);

				// 3. Create the full Data URI string (MIME type is crucial here)
				$base64_data = 'data:' . $file['type'] . ';base64,' . $encoded_content;

				// echo "File Base64 :" . $base64_data;
				// echo "File Name :" . $file_name;
				// exit();
				$data_update['nama_file'] = $file_name; // Change 'nama_file_kolom' to your actual DB column name
				$data_update['file'] = $base64_data; // Change 'nama_file_kolom' to your actual DB column name

			}
		}


		$this->cb->update('jurnal_neraca', $data_update, array('id' => $this->input->post('id')));
		$this->session->set_flashdata('message_name', "Berhasil Update Arus Kas");

		redirect('financial/coa_report');
	}
	public function hapus_arus_kas()
	{
		$id = $this->input->post('id');

		// 1. Basic validation for ID
		if (empty($id)) { // Using empty() is often better for checking if a variable is considered "empty"
			echo json_encode(['status' => 'error', 'message' => 'ID Arus Kas tidak ditemukan atau tidak valid.']);
			return;
		}

		// 2. Optional: Check if the record exists before attempting deletion
		// This provides a more specific error message if the ID doesn't exist
		$this->cb->where('id', $id);
		$query = $this->cb->get('jurnal_neraca');

		if ($query->num_rows() == 0) {
			echo json_encode(['status' => 'info', 'message' => 'Arus Kas tidak ditemukan atau sudah dihapus.']);
			return;
		}

		// 3. Attempt the deletion
		$this->cb->where('id', $id);
		$delete_result = $this->cb->delete('jurnal_neraca');

		// 4. Check the direct result of the delete operation and affected rows
		if ($delete_result) { // $delete_result will be TRUE on successful query execution
			if ($this->cb->affected_rows() > 0) {
				echo json_encode(['status' => 'success', 'message' => 'Arus Kas berhasil dihapus.']);
			} else {
				// This 'else' block means the query ran without error but affected 0 rows.
				// Given the num_rows() check above, this is now less likely unless
				// something very unusual happened between check and delete.
				// Could also happen if a row was deleted by another process milliseconds before.
				echo json_encode(['status' => 'info', 'message' => 'Arus Kas tidak ditemukan atau sudah dihapus. (Affected rows 0)']);
			}
		} else {
			// This 'else' block means the DELETE query itself failed (e.g., database error, syntax error).
			// You might want to log this error.
			error_log("Database delete error for ID: " . $id . " - " . $this->db->error()['message']);
			echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus Arus Kas. Silakan coba lagi.']);
		}
	}

	public function project()
	{
		$has_access = $this->M_menu->has_access();
		if (!$has_access) {
			show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
		}

		$keyword = trim($this->input->post('keyword', true) ?? '');
		$nip     = $this->session->userdata('nip');

		$config = [
			'base_url'        => site_url('financial/project'),
			'total_rows'      => $this->M_project->count_project($keyword),
			'per_page'        => 20,
			'uri_segment'     => 3,
			'num_links'       => 10,
			'full_tag_open'   => '<ul class="pagination" style="margin:0 0">',
			'full_tag_close'  => '</ul>',
			'first_link'      => false,
			'last_link'       => false,
			'prev_link'       => '«',
			'prev_tag_open'   => '<li class="prev">',
			'prev_tag_close'  => '</li>',
			'next_link'       => '»',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
		];
		$this->pagination->initialize($config);

		$page     = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
		$projects = $this->M_project->list_project($config['per_page'], $page, $keyword);

		$result  = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
		$result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};


		$this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
		$this->cb->from('v_coabb_all');
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$query = $this->cb->get();
		$all_coa_bb = $query->result_array();

		$data = [
			'page'          => $page,
			'projects'      => $projects,
			'keyword'       => $keyword,
			'count_inbox'   => $result,
			'count_inbox2'  => $result2,
			'title'         => 'Daftar Project',
			'pages'         => 'pages/financial/v_project',
			'utility'       => $this->db->get('utility')->row_array(),
			'pages_script'  => 'script/financial/s_financial',
			'menus'         => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'invoices'		=> [],
			'all_coa_bb'	=> $all_coa_bb,
		];

		$this->load->view('index', $data);
	}

	// =============================================
	// FORM CREATE PROJECT
	// =============================================
	public function create_project()
	{
		// $has_access = $this->M_menu->has_access();
		// if (!$has_access) {
		// 	show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
		// }

		$nip     = $this->session->userdata('nip');
		$result  = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
		$result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};


		$this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
		$this->cb->from('v_coabb_all');
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$query = $this->cb->get();
		$all_coa_bb = $query->result_array();

		$data = [
			'no_project'    => $this->M_project->generate_no_project(),
			'coa'           => $this->M_coa->list_coa(),
			'count_inbox'   => $result,
			'count_inbox2'  => $result2,
			'title'         => 'Create Project',
			'pages'         => 'pages/financial/v_project_create',
			'utility'       => $this->db->get('utility')->row_array(),
			// 'pages_script'  => 'script/financial/s_financial',
			'menus'         => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'all_coa_bb'	=> $all_coa_bb,
		];

		$this->load->view('index', $data);
	}

	// =============================================
	// FORM EDIT PROJECT
	// =============================================
	public function edit_project($id)
	{
		// $has_access = $this->M_menu->has_access();
		// if (!$has_access) {
		// 	show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
		// }

		$project = $this->M_project->get_project($id);
		if (!$project) {
			show_error('Data tidak ditemukan.', 404);
		}

		$nip     = $this->session->userdata('nip');
		$result  = $this->db->query("SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');")->row()->{'COUNT(Id)'};
		$result2 = $this->db->query("SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` LIKE '%$nip%') AND activity='1'")->row()->{'COUNT(id)'};

		$data = [
			'project'        => $project,
			'project_detail' => $this->M_project->get_project_detail($id),
			'coa'            => $this->M_coa->list_coa(),
			'count_inbox'    => $result,
			'count_inbox2'   => $result2,
			'title'          => 'Edit Project',
			'pages'          => 'pages/financial/v_project_edit',
			'utility'        => $this->db->get('utility')->row_array(),
			'pages_script'   => 'script/financial/s_financial',
			'menus'          => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
		];

		$this->load->view('index', $data);
	}

	// =============================================
	// PROCESS SAVE PROJECT (INSERT)
	// =============================================
	public function process_save_project()
	{
		$kode_cabang = $this->session->userdata('kode_cabang');
		$nip         = $this->session->userdata('nip');

		// Upload file jika ada
		$file      = null;
		$nama_file = null;
		if (!empty($_FILES['file_upload']['name'])) {
			$file_tmp  = $_FILES['file_upload']['tmp_name'];
			$nama_file = $_FILES['file_upload']['name'];
			$file      = file_get_contents($file_tmp);
		}

		$header = [
			'no_project'  => $this->input->post('no_project'),
			'tanggal'     => $this->input->post('tanggal'),
			'keterangan'  => strtoupper($this->input->post('keterangan')),
			'file'        => $file,
			'nama_file'   => $nama_file,
			'created_by'  => $nip,
			'id_cabang'   => $kode_cabang,
		];

		// Build detail rows
		$no_coas   = $this->input->post('no_coa');
		$nominals  = $this->input->post('nominal');
		$posisis   = $this->input->post('posisi');

		$details = [];
		foreach ($no_coas as $i => $no_coa) {
			if (empty($no_coa) || empty($nominals[$i])) continue;

			// Bersihkan format rupiah jika ada
			$nominal = preg_replace('/[^0-9]/', '', $nominals[$i]);

			$details[] = [
				'no_coa'  => $no_coa,
				'nominal' => $nominal,
				'posisi'  => $posisis[$i],
			];
		}

		$status = $this->M_project->save_project($header, $details);

		if ($status) {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'success',
				'title' => 'Berhasil!',
				'text'  => 'Project berhasil disimpan.',
			]);
		} else {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'error',
				'title' => 'Gagal!',
				'text'  => 'Terjadi kesalahan, project gagal disimpan.',
			]);
		}

		redirect('financial/project');
	}

	// =============================================
	// PROCESS UPDATE PROJECT (EDIT)
	// =============================================
	public function process_update_project($id)
	{
		$project = $this->M_project->get_project($id);
		if (!$project) show_error('Data tidak ditemukan.', 404);

		// Upload file jika ada, pakai yang lama kalau tidak ada upload baru
		$file      = $project['file'];
		$nama_file = $project['nama_file'];
		if (!empty($_FILES['file_upload']['name'])) {
			$file_tmp  = $_FILES['file_upload']['tmp_name'];
			$nama_file = $_FILES['file_upload']['name'];
			$file      = file_get_contents($file_tmp);
		}

		$header = [
			'tanggal'    => $this->input->post('tanggal'),
			'keterangan' => strtoupper($this->input->post('keterangan')),
			'file'       => $file,
			'nama_file'  => $nama_file,
		];

		// Build detail rows
		$no_coas  = $this->input->post('no_coa');
		$nominals = $this->input->post('nominal');
		$posisis  = $this->input->post('posisi');

		$details = [];
		foreach ($no_coas as $i => $no_coa) {
			if (empty($no_coa) || empty($nominals[$i])) continue;

			$nominal = preg_replace('/[^0-9]/', '', $nominals[$i]);

			$details[] = [
				'no_coa'  => $no_coa,
				'nominal' => $nominal,
				'posisi'  => $posisis[$i],
			];
		}

		$status = $this->M_project->update_project($id, $header, $details);

		if ($status) {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'success',
				'title' => 'Berhasil!',
				'text'  => 'Project berhasil diupdate.',
			]);
		} else {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'error',
				'title' => 'Gagal!',
				'text'  => 'Terjadi kesalahan, project gagal diupdate.',
			]);
		}

		redirect('financial/project');
	}

	// =============================================
	// DELETE PROJECT
	// =============================================
	public function delete_project($id)
	{
		$project = $this->M_project->get_project($id);
		if (!$project) show_error('Data tidak ditemukan.', 404);

		$status = $this->M_project->delete_project($id);

		if ($status) {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'success',
				'title' => 'Berhasil!',
				'text'  => 'Project berhasil dihapus.',
			]);
		} else {
			$this->session->set_flashdata('swal_message', [
				'icon'  => 'error',
				'title' => 'Gagal!',
				'text'  => 'Terjadi kesalahan, project gagal dihapus.',
			]);
		}

		redirect('financial/project');
	}

	// =============================================
	// DOWNLOAD FILE PROJECT
	// =============================================
	public function download_project_file($id)
	{
		$project = $this->M_project->get_project($id);
		if (!$project || empty($project['file'])) show_error('File tidak ditemukan.', 404);

		$nama_file = $project['nama_file'] ?? 'attachment';
		$ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

		$mime_types = [
			'pdf'  => 'application/pdf',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xls'  => 'application/vnd.ms-excel',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'doc'  => 'application/msword',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
		];

		$mime = $mime_types[$ext] ?? 'application/octet-stream';

		header('Content-Type: ' . $mime);
		header('Content-Disposition: attachment; filename="' . $nama_file . '"');
		header('Content-Length: ' . strlen($project['file']));
		echo $project['file'];
		exit;
	}

	public function print_project($id)
	{
		$project = $this->M_project->get_project($id);
		if (!$project) show_error('Data tidak ditemukan.', 404);

		$user = $this->M_invoice->cek_user($project['created_by']);

		$data = [
			'title_pdf'      => 'Project ' . $project['no_project'],
			'project'        => $project,
			'project_detail' => $this->M_project->get_project_detail($id),
			'user'           => $user,
		];

		$file_pdf    = 'Project ' . $project['no_project'];
		$paper       = 'A4';
		$orientation = 'portrait';

		$html = $this->load->view('pages/financial/v_project_print', $data, true);

		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}
}
