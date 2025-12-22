<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Pinjaman extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->cb = $this->load->database('corebank', TRUE);

		if ($this->session->userdata('isLogin') == FALSE) {
			redirect('home');
		}

		$this->load->model(['M_pinjaman', 'M_nasabah', 'M_coa']);
	}

	public function index()
	{
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
			'title' => "Daftar Pengajuan Pinjaman",
			'pages' => "pages/pinjaman/v_daftar_pengajuan",
			'utility' => $this->db->get('utility')->row_array(),
			'menus' => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'pengajuan' => $this->M_pinjaman->get_all_pengajuan()
		];

		$this->load->view('index', $data);
	}

	public function simulasi()
	{
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
			'title' => "Simulasi pinjaman",
			'pages' => "pages/pinjaman/v_simulasi",
			'utility' => $this->db->get('utility')->row_array(),
			'menus' => $this->M_menu->get_accessible_menus($this->session->userdata('nip'))
		];

		$this->load->view('index', $data);
	}

	public function print_simulasi()
	{
		// Validasi request method
		if ($this->input->method() !== 'post') {
			show_404();
			return;
		}

		// Ambil data dari POST
		$data['jumlah_pinjaman'] = $this->input->post('jumlah_pinjaman');
		$data['lama_pinjaman'] = $this->input->post('lama_pinjaman');
		$data['bunga_pinjaman'] = $this->input->post('bunga_pinjaman');
		$data['jenis_bunga'] = $this->input->post('jenis_bunga');

		// Load view print
		$this->load->view('pages/pinjaman/v_print_simulasi', $data);
	}

	// Halaman form pengajuan pinjaman
	public function ajukan()
	{
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
			'title' => "Pengajuan pinjaman",
			'pages' => "pages/pinjaman/v_form_pengajuan",
			'utility' => $this->db->get('utility')->row_array(),
			'menus' => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'nasabah' => $this->M_nasabah->get_all_active()
		];

		$this->load->view('index', $data);
	}

	// Get rekening nasabah via AJAX
	public function get_rekening_nasabah()
	{
		$id_nasabah = $this->input->post('id_nasabah');
		$rekening = $this->M_pinjaman->get_rekening_by_nasabah($id_nasabah);

		echo json_encode($rekening);
	}

	// Submit pengajuan pinjaman
	public function submit_pengajuan()
	{
		// Clean input dulu sebelum validasi
		$jumlah_pinjaman = str_replace('.', '', $this->input->post('jumlah_pinjaman'));
		$_POST['jumlah_pinjaman'] = $jumlah_pinjaman; // Override POST data

		$this->form_validation->set_rules('id_nasabah', 'Nasabah', 'required|numeric');
		$this->form_validation->set_rules('jumlah_pinjaman', 'Jumlah Pinjaman', 'required|numeric');
		$this->form_validation->set_rules('lama_pinjaman', 'Lama Pinjaman', 'required|numeric');
		$this->form_validation->set_rules('bunga_per_tahun', 'Bunga Per Tahun', 'required|numeric');
		$this->form_validation->set_rules('jenis_bunga', 'Jenis Bunga', 'required|in_list[anuitas,flat]');
		$this->form_validation->set_rules('jenis_pinjaman', 'Jenis Pinjaman', 'required|in_list[modal_kerja,konsumsi]');
		$this->form_validation->set_rules('tanggal_dropping', 'Tanggal Dropping', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('pinjaman/ajukan');
			return;
		}

		// Generate nomor pengajuan
		$no_pengajuan = $this->M_pinjaman->generate_no_pengajuan();

		// Hitung angsuran
		$pokok = $jumlah_pinjaman;
		$tenor = $this->input->post('lama_pinjaman');
		$bunga = $this->input->post('bunga_per_tahun');
		$jenis = $this->input->post('jenis_bunga');

		$hasil = $this->hitung_pinjaman($pokok, $bunga, $tenor, $jenis);

		// BULATKAN hasil perhitungan
		$angsuran_per_bulan = round($hasil['angsuran_bulanan']);
		$total_bunga = round($hasil['total_bunga']);
		$total_pembayaran = round($hasil['total_bayar']);

		// Insert data pengajuan
		$data_pengajuan = [
			'no_pengajuan' => $no_pengajuan,
			'id_nasabah' => $this->input->post('id_nasabah'),
			'jumlah_pinjaman' => $pokok,
			'lama_pinjaman' => $tenor,
			'bunga_per_tahun' => $bunga,
			'jenis_bunga' => $jenis,
			'jenis_pinjaman' => $this->input->post('jenis_pinjaman'),
			'tanggal_pengajuan' => date('Y-m-d'),
			'tanggal_dropping' => $this->input->post('tanggal_dropping'),
			'angsuran_per_bulan' => $angsuran_per_bulan,
			'total_bunga' => $total_bunga,
			'total_pembayaran' => $total_pembayaran,
			'keterangan' => $this->input->post('keterangan'),
			'created_by' => $this->session->userdata('user_user_id'),
			'status' => 'pending'
		];

		$id_pengajuan = $this->M_pinjaman->insert_pengajuan($data_pengajuan);

		if ($id_pengajuan) {
			// Insert detail angsuran
			$this->M_pinjaman->insert_detail_angsuran($id_pengajuan, $hasil['detail'], $this->input->post('tanggal_dropping'));

			$this->session->set_flashdata('message_name', 'Pengajuan pinjaman berhasil! Nomor pengajuan: ' . $no_pengajuan);
			redirect('pinjaman');
		} else {
			$this->session->set_flashdata('message_error', 'Gagal mengajukan pinjaman!');
			redirect('pinjaman/simulasi');
		}
	}

	// Detail pengajuan
	public function detail($id)
	{
		$pengajuan = $this->M_pinjaman->get_pengajuan_by_id($id);

		if (!$pengajuan) {
			$this->session->set_flashdata('error', 'Data pengajuan tidak ditemukan!');
			redirect('pinjaman');
			return;
		}

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
			'title' => "Detail Pengajuan pinjaman",
			'pages' => "pages/pinjaman/v_detail_pengajuan",
			'utility' => $this->db->get('utility')->row_array(),
			'menus' => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'pengajuan' => $pengajuan,
			'detail_angsuran' => $this->M_pinjaman->get_detail_angsuran($id)
		];

		$this->load->view('index', $data);
	}

	// Form approval (khusus keuangan)
	public function approval($id)
	{
		// Check role keuangan
		if ($this->session->userdata('role') != 'Keuangan') {
			$this->session->set_flashdata('error', 'Anda tidak memiliki akses!');
			redirect('pinjaman');
			return;
		}

		$pengajuan = $this->M_pinjaman->get_pengajuan_by_id($id);
		$rekening_nasabah = $this->M_pinjaman->get_rekening_by_nasabah($pengajuan->id_nasabah);
		$coa = $this->M_coa->list_coa();

		// print_r($pengajuan);
		// exit;

		if (!$pengajuan || $pengajuan->status != 'pending') {
			$this->session->set_flashdata('error', 'Pengajuan tidak valid atau sudah diproses!');
			redirect('pinjaman');
			return;
		}

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
			'title' => "Approval & Pencairan Pinjaman",
			'pages' => "pages/pinjaman/v_approval",
			'utility' => $this->db->get('utility')->row_array(),
			'menus' => $this->M_menu->get_accessible_menus($this->session->userdata('nip')),
			'pengajuan' => $pengajuan,
			'rekening_nasabah' => $rekening_nasabah,
			'coa' => $coa,
			'detail_angsuran' => $this->M_pinjaman->get_detail_angsuran($id)
		];

		$this->load->view('index', $data);
	}

	// Submit approval
	public function submit_approval()
	{
		$this->form_validation->set_rules('id_pengajuan', 'ID Pengajuan', 'required|numeric');
		$this->form_validation->set_rules('action', 'Action', 'required|in_list[approve,reject]');
		$this->form_validation->set_rules('no_rekening_asal', 'No Rekening Asal', 'required_if[action,approve]');
		$this->form_validation->set_rules('no_rekening_tujuan', 'No Rekening Tujuan', 'required_if[action,approve]');
		$this->form_validation->set_rules('coa_debit', 'COA Debit', 'required_if[action,approve]');
		$this->form_validation->set_rules('coa_kredit', 'COA Kredit', 'required_if[action,approve]');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('pinjaman/daftar_pengajuan');
			return;
		}

		$id_pengajuan = $this->input->post('id_pengajuan');
		$action = $this->input->post('action');

		if ($action == 'reject') {
			// Reject pengajuan
			$update = $this->M_pinjaman->update_pengajuan($id_pengajuan, [
				'status' => 'rejected'
			]);

			if ($update) {
				$this->session->set_flashdata('success', 'Pengajuan pinjaman ditolak!');
			} else {
				$this->session->set_flashdata('error', 'Gagal menolak pengajuan!');
			}
		} else {
			// Approve & cairkan
			$data_pencairan = [
				'id_pengajuan' => $id_pengajuan,
				'tanggal_pencairan' => date('Y-m-d'),
				'no_rekening_asal' => $this->input->post('no_rekening_asal'),
				'no_rekening_tujuan' => $this->input->post('no_rekening_tujuan'),
				'coa_debit' => $this->input->post('coa_debit'),
				'coa_kredit' => $this->input->post('coa_kredit'),
				'catatan_keuangan' => $this->input->post('catatan_keuangan'),
				'approved_by' => $this->session->userdata('user_id')
			];

			echo '<pre>';
			print_r($data_pencairan);
			echo '</pre>';
			exit;
			$result = $this->M_pinjaman->process_pencairan($id_pengajuan, $data_pencairan);

			if ($result) {
				$this->session->set_flashdata('success', 'Pinjaman berhasil dicairkan!');
			} else {
				$this->session->set_flashdata('error', 'Gagal mencairkan pinjaman!');
			}
		}

		redirect('pinjaman/daftar_pengajuan');
	}

	// Private function untuk hitung pinjaman
	private function hitung_pinjaman($pokok, $bunga_tahunan, $tenor, $jenis)
	{
		if ($jenis == 'anuitas') {
			return $this->hitung_anuitas($pokok, $bunga_tahunan, $tenor);
		} else {
			return $this->hitung_flat($pokok, $bunga_tahunan, $tenor);
		}
	}

	private function hitung_anuitas($pokok, $bunga_tahunan, $tenor)
	{
		$bunga_bulanan = $bunga_tahunan / 100 / 12;
		$angsuran = $pokok * ($bunga_bulanan * pow(1 + $bunga_bulanan, $tenor)) / (pow(1 + $bunga_bulanan, $tenor) - 1);

		$detail = [];
		$sisa = $pokok;
		$total_bunga = 0;

		for ($i = 1; $i <= $tenor; $i++) {
			$bunga = $sisa * $bunga_bulanan;
			$pokok_bayar = $angsuran - $bunga;
			$sisa -= $pokok_bayar;
			$total_bunga += $bunga;

			$detail[] = [
				'angsuran_ke' => $i,
				'angsuran' => $angsuran,
				'pokok' => $pokok_bayar,
				'bunga' => $bunga,
				'sisa' => max(0, $sisa)
			];
		}

		return [
			'angsuran_bulanan' => $angsuran,
			'total_bunga' => $total_bunga,
			'total_bayar' => $pokok + $total_bunga,
			'detail' => $detail
		];
	}

	private function hitung_flat($pokok, $bunga_tahunan, $tenor)
	{
		$bunga_bulanan = ($pokok * ($bunga_tahunan / 100) * ($tenor / 12)) / $tenor;
		$pokok_bulanan = $pokok / $tenor;
		$angsuran = $pokok_bulanan + $bunga_bulanan;

		$detail = [];
		$sisa = $pokok;
		$total_bunga = 0;

		for ($i = 1; $i <= $tenor; $i++) {
			$sisa -= $pokok_bulanan;
			$total_bunga += $bunga_bulanan;

			$detail[] = [
				'angsuran_ke' => $i,
				'angsuran' => $angsuran,
				'pokok' => $pokok_bulanan,
				'bunga' => $bunga_bulanan,
				'sisa' => max(0, $sisa)
			];
		}

		return [
			'angsuran_bulanan' => $angsuran,
			'total_bunga' => $total_bunga,
			'total_bayar' => $pokok + $total_bunga,
			'detail' => $detail
		];
	}

	// Print detail (bonus)
	public function print_detail($id)
	{
		$data['pengajuan'] = $this->M_pinjaman->get_pengajuan_by_id($id);
		$data['detail_angsuran'] = $this->M_pinjaman->get_detail_angsuran($id);

		if (!$data['pengajuan']) {
			show_404();
			return;
		}

		$this->load->view('pages/pinjaman/v_print_detail', $data);
	}
}
