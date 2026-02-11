<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hris extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['session', 'upload']);
		$this->load->helper(['url', 'file', 'number', 'app']);
		$this->load->library(['pdfgenerator']);

		$this->cb = $this->load->database('corebank', TRUE);

		// Check if user is logged in
		if (!$this->session->userdata('isLogin')) {
			redirect('login');
		}
	}

	public function index()
	{
		$query = $this->db->query("SELECT * FROM employees");
		$data['employees'] = $query->result();

		$this->load->view('hris/employee_list', $data);
	}

	public function upload()
	{
		$has_access = $this->M_menu->has_access();

		if (!$has_access) {
			show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
		}

		$nip = $this->session->userdata('nip');

		// Count inbox
		$sql = "SELECT COUNT(Id) as total FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$result = $query->row()->total;

		// Count tasks
		$sql2 = "SELECT COUNT(id) as total FROM task WHERE (`member` LIKE '%$nip%' OR `pic` LIKE '%$nip%') AND activity='1'";
		$query2 = $this->db->query($sql2);
		$result2 = $query2->row()->total;

		$data = [
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'title' => "Upload Gaji Karyawan",
		];

		$data['pages'] = "pages/hris/v_upload_gaji";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_financial';
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	/**
	 * Upload Gaji Monthly
	 * Handle upload file Excel untuk gaji bulanan karyawan
	 */
	public function upload_gaji_monthly()
	{
		$nip = $this->session->userdata('nip');
		$username = $this->session->userdata('username');

		// Validasi file upload
		if (!isset($_FILES['userfile']['name']) || $_FILES['userfile']['name'] == '') {
			$this->session->set_flashdata('notif', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> File Excel belum dipilih</div>');
			redirect('hris/upload');
		}

		$file_tmp = $_FILES['userfile']['tmp_name'];

		// Load PhpSpreadsheet
		require APPPATH . 'third_party/autoload.php';
		require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';

		try {
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_tmp);
			$spreadsheet = $reader->load($file_tmp);
			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

			// ========== VALIDASI HEADER ========== //
			$expected_headers = [
				'A' => 'NO',
				'B' => 'NAMA',
				'C' => 'JABATAN',
				'D' => 'TANGGAL MASUK',
				'E' => 'GAJI POKOK',
				'F' => 'TUNJANGAN FUNGSIONAL',
				'G' => 'TUNJANGAN JABATAN',
				'H' => 'TUNJANGAN TRANSPORTASI',
				'I' => 'TUNJANGAN MAKAN',
				'J' => 'INSENTIF',
				'K' => 'UANG LEMBUR',
				'L' => 'TUNJANGAN BPJS TK',
				'M' => 'TUNJANGAN BPJS KESEHATAN',
				'N' => 'TOTAL GAJI',
				'O' => 'POTONGAN KASBON PERUSAHAAN',
				'P' => 'POTONGAN WFH',
				'Q' => 'POTONGAN ABSENSI',
				'R' => 'POTONGAN DATANG TERLAMBAT',
				'S' => 'POTONGAN PULANG CEPAT',
				'T' => 'POTONGAN BPJSTK',
				'U' => 'POTONGAN SIMPANAN KOPERASI',
				'V' => 'POTONGAN PINJAMAN KOPERASI',
				'W' => 'POTONGAN BPJS KESEHATAN',
				'X' => 'TOTAL POTONGAN',
				'Y' => 'JML GAJI YG DITERIMA',
				'Z' => 'JUMLAH HARI KERJA',
				'AA' => 'KETIDAK HADIRAN',
				'AB' => 'SURAT DOKTER',
				'AC' => 'POTONG CUTI',
				'AD' => 'NO. ID',
				'AE' => 'TGL_GAJI',
				'AF' => 'PPH21',
			];

			// Ambil baris pertama (header) - biasanya row 2 kalau ada title
			$header_row = $sheet[1]; // Sesuaikan dengan struktur Excel-mu

			// Cek setiap kolom
			$header_errors = [];
			foreach ($expected_headers as $col => $expected_name) {
				$actual_name = trim($header_row[$col] ?? '');

				// Case-insensitive comparison
				if (strtolower($actual_name) !== strtolower($expected_name)) {
					$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
				}
			}

			// Jika ada error header
			if (!empty($header_errors)) {
				$error_msg = '<div class="alert alert-danger alert-dismissible fade show">';
				$error_msg .= '<h5><i class="mdi mdi-alert-circle"></i> File Excel Tidak Sesuai Format!</h5>';
				$error_msg .= '<hr><b>Total Error:</b> ' . count($header_errors) . '<br><ul>';
				foreach ($header_errors as $err) {
					$error_msg .= '<li>' . $err . '</li>';
				}
				$error_msg .= '</ul></div>';

				$this->session->set_flashdata('notif', $error_msg);
				redirect('hris/upload');
			}
			// ========== END VALIDASI HEADER ========== //

			// Mulai transaksi
			$this->db->trans_begin();

			$inserted = 0;
			$skipped = [];
			$errors = [];

			foreach ($sheet as $i => $row) {
				// Skip header rows (row 1 dan 2)
				if ($i <= 1) continue;

				// Skip empty rows
				if (empty($row['B']) && empty($row['AD'])) continue;

				$nip_employee = trim($row['AD']);
				$bulan_gaji = trim($row['AE']);

				// === CEK DUPLIKASI === //
				$existing = $this->db->get_where('gaji', [
					'nip' => $nip_employee,
					'bulan_gaji' => $bulan_gaji,
					'pembayaran' => 1
				])->row();

				if ($existing) {
					$skipped[] = [
						'nama' => $row['B'],
						'nip' => $nip_employee,
						'bulan_gaji' => $bulan_gaji,
						'reason' => 'Data sudah ada'
					];
					continue;
				}

				// === INSERT DATA === //
				$data = [
					'nama'          => $row['B'],
					'jabatan'       => $row['C'],
					'gapok'         => $this->clean_number($row['E']),
					'tu_fungsional' => $this->clean_number($row['F']),
					'tu_jabatan'    => $this->clean_number($row['G']),
					'tu_transport'  => $this->clean_number($row['H']),
					'tu_makan'      => $this->clean_number($row['I']),
					'tu_insentif'   => $this->clean_number($row['J']),
					'tu_lembur'     => $this->clean_number($row['K']),
					'tu_bpjs_tk'    => $this->clean_number($row['L']),
					'tu_bpjs_kes'   => $this->clean_number($row['M']),
					'gross_gaji'    => $this->clean_number($row['N']),
					'pot_kasbon'    => $this->clean_number($row['O']),
					'pot_wfh'       => $this->clean_number($row['P']),
					'pot_absen'     => $this->clean_number($row['Q']),
					'pot_terlambat' => $this->clean_number($row['R']),
					'pot_pulang'    => $this->clean_number($row['S']),
					'pot_bpjs_tk'   => $this->clean_number($row['T']),
					'simp_koperasi' => $this->clean_number($row['U']),
					'pot_koperasi'  => $this->clean_number($row['V']),
					'pot_bpjs_kes'  => $this->clean_number($row['W']),
					'pot_total'     => $this->clean_number($row['X']),
					'net_gaji'      => $this->clean_number($row['Y']),
					'hari_kerja'    => $this->clean_number($row['Z']),
					'tidak_hadir'   => $this->clean_number($row['AA']),
					'surat_dokter'  => $this->clean_number($row['AB']),
					'potong_cuti'   => $this->clean_number($row['AC']),
					'pph21'         => $this->clean_number($row['AF']),
					'nip'           => $nip_employee,
					'bulan_gaji'    => $bulan_gaji,
					'pembayaran'    => 1, // 1 = Monthly
					'user_upload'   => $username,
					'created_at'    => date('Y-m-d H:i:s')
				];

				$this->db->insert('gaji', $data);
				$inserted++;
			}

			// Commit / rollback
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> Gagal upload data gaji bulanan.</div>');
				redirect('hris/upload');
			} else {
				$this->db->trans_commit();

				// Build success message
				$msg = '<div class="alert alert-success alert-dismissible fade show">';
				$msg .= '<h5><i class="mdi mdi-check-circle"></i> Upload Berhasil!</h5>';
				$msg .= '<hr>Berhasil upload <b>' . $inserted . '</b> data gaji bulanan.';

				if (!empty($skipped)) {
					$msg .= '<br><br><b>Data yang tidak diupload (duplikat):</b><ul>';
					foreach ($skipped as $s) {
						$msg .= '<li><b>' . $s['nama'] . '</b> (NIP: ' . $s['nip'] . ') - Bulan: ' . $s['bulan_gaji'] . '</li>';
					}
					$msg .= '</ul>';
				}
				$msg .= '</div>';

				// === SIMPAN LOG UPLOAD === //
				$log_data = [
					'filename'      => $_FILES['userfile']['name'],
					'uploaded_by'   => $nip,
					'jenis_gaji'    => 'monthly',
					'total_rows'    => count($sheet) - 2,
					'inserted_rows' => $inserted,
					'skipped_rows'  => count($skipped),
					'uploaded_at'   => date('Y-m-d H:i:s'),
					'full_log'      => json_encode($skipped)
				];
				$this->db->insert('gaji_upload_log', $log_data);

				$this->session->set_flashdata('notif', $msg);
				redirect('hris/upload');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('notif', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> Terjadi kesalahan: ' . $e->getMessage() . '</div>');
			redirect('hris/upload');
		}
	}

	/**
	 * Upload Gaji Daily
	 * Handle upload file Excel untuk gaji harian karyawan
	 */
	public function upload_gaji_daily()
	{
		$nip = $this->session->userdata('nip');
		$username = $this->session->userdata('username');

		// Validasi file upload
		if (!isset($_FILES['userfile']['name']) || $_FILES['userfile']['name'] == '') {
			$this->session->set_flashdata('notif2', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> File Excel belum dipilih</div>');
			redirect('hris/upload');
		}

		$file_tmp = $_FILES['userfile']['tmp_name'];

		// Load PhpSpreadsheet
		require APPPATH . 'third_party/autoload.php';
		require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';

		try {
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_tmp);
			$spreadsheet = $reader->load($file_tmp);
			$sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

			// ========== VALIDASI HEADER ========== //
			$expected_headers = [
				'A' => 'NO',
				'B' => 'NAMA',
				'C' => 'JABATAN',
				'D' => 'GAPOK',
				'E' => 'T.U TRANSPORT',
				'F' => 'T.U MAKAN',
				'G' => 'GROSS GAJI',
				'H' => 'HARI KERJA',
				'I' => 'UPAH PERHARI',
				'J' => 'HARI KERJA BERJALAN',
				'K' => 'POT ABSEN',
				'L' => 'KEBIJAKAN PRSH',
				'M' => 'HOK DIBAYAR',
				'N' => 'INSENTIF BACKUP',
				'O' => 'T.U BPJS KES',
				'P' => 'T.U LEMBUR',
				'Q' => 'T.U INSENTIF',
				'R' => 'SIMP KOPERASI',
				'S' => 'POT KASBON',
				'T' => 'POT BPJS TK',
				'U' => 'POT TOTAL',
				'V' => 'NET GAJI',
				'W' => 'NIP',
				'X' => 'BULAN GAJI',
				'Y' => 'PERIODE GAJI',
				'Z' => 'TMT',
				'AA' => 'WFH',
				'AB' => 'TOTAL PERIODE BERJALAN',
				'AC' => 'POT TERLAMBAT'
			];

			// Ambil baris header (sesuaikan dengan Excel-mu)
			$header_row = $sheet[2];

			// Cek header
			$header_errors = [];
			foreach ($expected_headers as $col => $expected_name) {
				$actual_name = trim($header_row[$col] ?? '');
				if (strtolower($actual_name) !== strtolower($expected_name)) {
					$header_errors[] = "Kolom <b>$col</b>: harapan '<b>$expected_name</b>', ditemukan '<b>$actual_name</b>'";
				}
			}

			if (!empty($header_errors)) {
				$error_msg = '<div class="alert alert-danger alert-dismissible fade show">';
				$error_msg .= '<h5><i class="mdi mdi-alert-circle"></i> File Excel Tidak Sesuai Format!</h5>';
				$error_msg .= '<hr><b>Total Error:</b> ' . count($header_errors) . '<br><ul>';
				foreach ($header_errors as $err) {
					$error_msg .= '<li>' . $err . '</li>';
				}
				$error_msg .= '</ul></div>';

				$this->session->set_flashdata('notif2', $error_msg);
				redirect('hris/upload');
			}
			// ========== END VALIDASI HEADER ========== //

			// Mulai transaksi
			$this->db->trans_begin();

			$inserted = 0;
			$skipped = [];

			foreach ($sheet as $i => $row) {
				// Skip header rows
				if ($i <= 2) continue;

				// Skip empty rows
				if (empty($row['B']) && empty($row['W'])) continue;

				$nip_employee = trim($row['W']);
				$bulan_gaji = trim($row['X']);

				// === CEK DUPLIKASI === //
				$existing = $this->db->get_where('gaji', [
					'nip' => $nip_employee,
					'bulan_gaji' => $bulan_gaji,
					'pembayaran' => 2
				])->row();

				if ($existing) {
					$skipped[] = [
						'nama' => $row['B'],
						'nip' => $nip_employee,
						'bulan_gaji' => $bulan_gaji,
						'reason' => 'Data sudah ada'
					];
					continue;
				}

				// === INSERT DATA === //
				$data = [
					'nama'                  => $row['B'],
					'jabatan'               => $row['C'],
					'gapok'                 => $this->clean_number($row['D']),
					'tu_transport'          => $this->clean_number($row['E']),
					'tu_makan'              => $this->clean_number($row['F']),
					'gross_gaji'            => $this->clean_number($row['G']),
					'hari_kerja'            => $this->clean_number($row['H']),
					'upah_perhari'          => $this->clean_number($row['I']),
					'hari_kerja_berjalan'   => $this->clean_number($row['J']),
					'pot_absen'             => $this->clean_number($row['K']),
					'kebijakan_prsh'        => $this->clean_number($row['L']),
					'hok_dibayar'           => $this->clean_number($row['M']),
					'insentif_backup'       => $this->clean_number($row['N']),
					'tu_bpjs_kes'           => $this->clean_number($row['O']),
					'tu_lembur'             => $this->clean_number($row['P']),
					'tu_insentif'           => $this->clean_number($row['Q']),
					'simp_koperasi'         => $this->clean_number($row['R']),
					'pot_kasbon'            => $this->clean_number($row['S']),
					'pot_bpjs_tk'           => $this->clean_number($row['T']),
					'pot_total'             => $this->clean_number($row['U']),
					'net_gaji'              => $this->clean_number($row['V']),
					'nip'                   => $nip_employee,
					'bulan_gaji'            => $bulan_gaji,
					'periode_gaji'          => $row['Y'],
					'tmt'                   => $row['Z'],
					'wfh'                   => $this->clean_number($row['AA']),
					'total_periode_berjalan' => $this->clean_number($row['AB']),
					'pot_terlambat'         => $this->clean_number($row['AC']),
					'pembayaran'            => 2, // 2 = Daily
					'user_upload'           => $username,
					'created_at'            => date('Y-m-d H:i:s')
				];

				$this->db->insert('gaji', $data);
				$inserted++;
			}

			// Commit / rollback
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('notif2', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> Gagal upload data gaji harian.</div>');
				redirect('hris/upload');
			} else {
				$this->db->trans_commit();

				// Build success message
				$msg = '<div class="alert alert-success alert-dismissible fade show">';
				$msg .= '<h5><i class="mdi mdi-check-circle"></i> Upload Berhasil!</h5>';
				$msg .= '<hr>Berhasil upload <b>' . $inserted . '</b> data gaji harian.';

				if (!empty($skipped)) {
					$msg .= '<br><br><b>Data yang tidak diupload (duplikat):</b><ul>';
					foreach ($skipped as $s) {
						$msg .= '<li><b>' . $s['nama'] . '</b> (NIP: ' . $s['nip'] . ') - Bulan: ' . $s['bulan_gaji'] . '</li>';
					}
					$msg .= '</ul>';
				}
				$msg .= '</div>';

				// === SIMPAN LOG UPLOAD === //
				$log_data = [
					'filename'      => $_FILES['userfile']['name'],
					'uploaded_by'   => $nip,
					'jenis_gaji'    => 'daily',
					'total_rows'    => count($sheet) - 2,
					'inserted_rows' => $inserted,
					'skipped_rows'  => count($skipped),
					'uploaded_at'   => date('Y-m-d H:i:s'),
					'full_log'      => json_encode($skipped)
				];
				$this->db->insert('gaji_upload_log', $log_data);

				$this->session->set_flashdata('notif2', $msg);
				redirect('hris/upload');
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('notif2', '<div class="alert alert-danger alert-dismissible fade show"><b>ERROR!</b> Terjadi kesalahan: ' . $e->getMessage() . '</div>');
			redirect('hris/upload');
		}
	}

	public function list_gaji()
	{
		$has_access = $this->M_menu->has_access();

		if (!$has_access) {
			show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
		}

		$nip = $this->session->userdata('nip');

		// Count inbox
		$sql = "SELECT COUNT(Id) as total FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
		$query = $this->db->query($sql);
		$result = $query->row()->total;

		// Count tasks
		$sql2 = "SELECT COUNT(id) as total FROM task WHERE (`member` LIKE '%$nip%' OR `pic` LIKE '%$nip%') AND activity='1'";
		$query2 = $this->db->query($sql2);
		$result2 = $query2->row()->total;

		$this->cb->select('no_bb as id, CONCAT(no_bb, " - ", nama_perkiraan) as text');
		$this->cb->from('v_coabb_all');
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$query = $this->cb->get();
		$all_coa_bb = $query->result_array();

		$bulan = $this->input->post('date_pic');

		$slip = "";

		if ($bulan) {
			$sql = "SELECT * FROM gaji WHERE (nip = '$nip') AND (DATE_FORMAT(bulan_gaji, '%Y-%m') = '$bulan')";
			$query = $this->db->query($sql);
			$slip = $query->result();
		}

		$data = [
			'count_inbox' => $result,
			'count_inbox2' => $result2,
			'title' => "Slip Gaji Karyawan",
			'all_coa_bb' => $all_coa_bb,
			'invoices' => [],
			'slip' => $slip,
		];

		$data['pages'] = "pages/hris/v_list_gaji";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['pages_script'] = 'script/financial/s_financial';
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	public function slip_gaji_pdf()
	{
		$id = $this->uri->segment(3);

		$nip = $this->session->userdata('nip');

		$sql = "SELECT * FROM gaji WHERE Id=$id AND nip='$nip';";
		$query = $this->db->query($sql);
		$slip = $query->row();

		$file_pdf = 'Slip Gaji Bulan ' . periode($slip->bulan_gaji) . ' - ' . $slip->nama;
		$data = [
			'title_pdf' => $file_pdf,
			'slip' => $slip,
		];

		// filename dari pdf ketika didownload

		// setting paper
		$paper = 'A4';

		//orientasi paper potrait / landscape
		$orientation = "portrait";

		if (empty($data['slip'])) {
			echo "<script>alert('Data tidak ditemukan!');window.location.href = '" . base_url() . "app/cetak_gaji';</script>";
		} else {
			if ($data['slip']->pembayaran == 1) {
				$html = $this->load->view('pages/hris/v_slip_gaji', $data, true);
			} elseif ($data['slip']->pembayaran == 2) {;
				$html = $this->load->view('pages/hris/v_slip_gaji2', $data, true);
			}
		}

		// $this->load->view('pages/hris/v_slip_gaji', $data);
		// run dompdf
		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}

	/**
	 * Clean number format from Excel
	 */
	private function clean_number($value)
	{
		if (empty($value) || $value == '-') {
			return 0;
		}

		// Remove formatting and convert to number
		$value = str_replace([',', '.', ' ', 'Rp'], '', $value);
		return floatval($value);
	}
}
