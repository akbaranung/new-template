<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Closing_nota extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_closing_nota');
		$this->load->model('M_item_nota');
		$this->load->model('M_nota');
		$this->load->model('M_coa');
		$this->load->library('pagination');

		// Check if user is logged in
		if (!$this->session->userdata('nip')) {
			redirect('auth/login');
		}

		$this->cb = $this->load->database('corebank', TRUE);
		date_default_timezone_set('Asia/Jakarta');
	}

	// Halaman utama list closing
	public function index()
	{
		$data['title'] = 'Closing Kasir';

		// Search & Filter
		$search = $this->input->get('search');
		$tanggal_dari = $this->input->get('tanggal_dari');
		$tanggal_sampai = $this->input->get('tanggal_sampai');

		// Pagination config
		$config['base_url'] = base_url('closing_nota/index');
		$config['total_rows'] = $this->M_closing_nota->count_all($search, $tanggal_dari, $tanggal_sampai);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;

		// Pagination styling (Bootstrap 4)
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = 'First';
		$config['last_link'] = 'Last';
		$config['first_tag_open'] = '<li class="page-item">';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li class="page-item">';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
		$config['attributes'] = array('class' => 'page-link');

		// Reuse query string
		$query_string = '';
		if ($search)
			$query_string .= '&search=' . urlencode($search);
		if ($tanggal_dari)
			$query_string .= '&tanggal_dari=' . urlencode($tanggal_dari);
		if ($tanggal_sampai)
			$query_string .= '&tanggal_sampai=' . urlencode($tanggal_sampai);

		if ($query_string) {
			$config['suffix'] = '?' . ltrim($query_string, '&');
			$config['first_url'] = $config['base_url'] . '?' . ltrim($query_string, '&');
		}

		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		$data['closing'] = $this->M_closing_nota->get_all($config['per_page'], $page, $search, $tanggal_dari, $tanggal_sampai);
		$data['pagination'] = $this->pagination->create_links();

		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['search'] = $search;
		$data['tanggal_dari'] = $tanggal_dari;
		$data['tanggal_sampai'] = $tanggal_sampai;

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/nota/v_closing_index";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Form closing
	public function form()
	{
		$data['title'] = 'Proses Closing Kasir';
		$tanggal = $this->input->get('tanggal') ?: date('Y-m-d');

		// Check apakah tanggal sudah closing
		if ($this->M_closing_nota->is_closed($tanggal)) {
			$this->session->set_flashdata('error', 'Tanggal ' . date('d/m/Y', strtotime($tanggal)) . ' sudah di-closing!');
			redirect('closing_nota');
		}

		// Get nota belum closing
		$data['nota_belum_closing'] = $this->M_nota->get_belum_closing($tanggal);

		// Hitung summary
		$summary = $this->M_nota->get_summary_by_metode($tanggal);

		$data['tanggal'] = $tanggal;
		$data['total_transaksi'] = 0;
		$data['total_penjualan_cash'] = 0;
		$data['total_penjualan_qris'] = 0;
		$data['total_penjualan_card'] = 0;
		$data['total_penjualan_piutang'] = 0;
		$data['total_penjualan'] = 0;
		$data['total_hpp'] = 0;
		$data['total_hpp_cash'] = 0;
		$data['total_hpp_qris'] = 0;
		$data['total_hpp_card'] = 0;
		$data['laba_kotor'] = 0;

		foreach ($summary as $s) {
			$data['total_transaksi'] += $s->total_transaksi;
			$data['total_penjualan'] += $s->total_penjualan;
			$data['total_hpp'] += $s->total_hpp;
			$data['laba_kotor'] += $s->laba_kotor;

			if ($s->metode_bayar == 'cash') {
				$data['total_penjualan_cash'] = $s->total_penjualan;
				$data['total_hpp_cash'] += $s->total_hpp;
			} else if ($s->metode_bayar == 'qris') {
				$data['total_penjualan_qris'] = $s->total_penjualan;
				$data['total_hpp_qris'] += $s->total_hpp;
			} else if ($s->metode_bayar == 'card') {
				$data['total_penjualan_card'] = $s->total_penjualan;
				$data['total_hpp_card'] += $s->total_hpp;
			} else {
				$data['total_penjualan_piutang'] = $s->total_penjualan;
				$data['total_hpp'] += $s->total_hpp;
			}
		}

		// Get COA list
		$data['coa_list'] = $this->M_coa->list_coa(); // Sesuaikan dengan method di M_coa

		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/nota/v_closing_form";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Proses closing
	public function proses()
	{
		$this->db->trans_start();

		try {
			$tanggal        = $this->input->post('tanggal');
			$coa_kas        = $this->input->post('coa_kas');        // COA Kas (Debit)
			$coa_qris       = $this->input->post('coa_qris');        // COA Qris (Debit)
			$coa_card        = $this->input->post('coa_card');        // COA Card (Debit)
			$coa_pendapatan = $this->input->post('coa_pendapatan'); // COA Pendapatan (Kredit)

			// Ambil semua nota belum closing
			$nota_list = $this->M_nota->get_belum_closing($tanggal);

			if (empty($nota_list)) {
				echo json_encode(['status' => 'error', 'message' => 'Tidak ada nota yang perlu di-closing!']);
				return;
			}

			// Buat record closing header
			$total_penjualan = array_sum(array_column((array)$nota_list, 'total_penjualan'));
			$total_hpp       = array_sum(array_column((array)$nota_list, 'total_hpp'));
			$total_laba      = $total_penjualan - $total_hpp;

			$data_closing = [
				'tanggal'         => $tanggal,
				'total_transaksi' => count($nota_list),
				'total_penjualan' => $total_penjualan,
				'total_hpp'       => $total_hpp,
				'laba_kotor'      => $total_laba,
				'id_cabang'       => $this->session->userdata('kode_cabang'),
				'id_company'      => $this->session->userdata('user_perusahaan_id'),
				'created_by'      => $this->session->userdata('nip'),
				'created_at'      => date('Y-m-d H:i:s')
			];

			$id_closing = $this->M_closing_nota->insert($data_closing);

			// Update status nota jadi closed
			foreach ($nota_list as $nota) {
				$this->M_nota->update_closing($nota->id, $id_closing);
			}

			// ============================================================
			// JURNAL BARU: Split per COA Persediaan
			// ============================================================

			$data_closing = [];
			foreach ($nota_list as $nota) {
				$jenis_bayar = $nota->metode_bayar;
				$details = $this->M_nota->get_detail($nota->id);

				foreach ($details as $detail) {
					$item = $this->M_item_nota->get_by_id($detail->id_item);
					$coa_item_persediaan = $item ? $item->coa_persediaan : null;

					if (empty($coa_item_persediaan)) continue;

					$subtotal_hpp = $detail->qty * $detail->harga_modal;
					$laba_item = ($detail->qty * $detail->harga_jual) - $subtotal_hpp;

					// if ($subtotal_hpp <= 0) continue;

					// Inisialisasi array bertingkat jika belum ada
					if (!isset($data_closing[$jenis_bayar][$coa_item_persediaan])) {
						$data_closing[$jenis_bayar][$coa_item_persediaan] = [
							'hpp' => 0,
							'laba' => 0
						];
					}

					$data_closing[$jenis_bayar][$coa_item_persediaan]['hpp'] += $subtotal_hpp;
					$data_closing[$jenis_bayar][$coa_item_persediaan]['laba'] += $laba_item;
				}
			}

			foreach ($data_closing as $jenis => $per_coa) {

				// Tentukan COA Kas berdasarkan jenis bayar (Mapping)
				// Anda bisa menyesuaikan logic mapping ini sesuai database/kebutuhan
				if ($jenis == 'cash') {
					$coa_kas_tujuan = $coa_kas;
				}

				if ($jenis == 'card') {
					$coa_kas_tujuan = $coa_card;
				}

				if ($jenis == 'qris') {
					$coa_kas_tujuan = $coa_qris;
				}

				foreach ($per_coa as $coa_persediaan => $nilai) {
					if ($nilai['hpp'] > 0) {
						$keterangan = "Closing Kasir $tanggal - HPP [COA: $coa_persediaan]";

						$this->posting(
							$coa_kas_tujuan,    // Debit: Kas sesuai jenis bayar
							$coa_persediaan,    // Kredit: Persediaan
							$keterangan,
							$nilai['hpp'],
							$tanggal,
							'CLOSING-' . date('Ymd') . '-' . $id_closing
						);
					}


					if ($nilai['laba'] > 0) {
						$ket_laba = "Closing Kasir $tanggal - Pendapatan ($jenis) [Asal COA: $coa_pendapatan]";
						$this->posting(
							$coa_kas_tujuan,    // Debit: Kas (Sesuai Jenis)
							$coa_pendapatan,    // Kredit: Pendapatan
							$ket_laba,
							$nilai['laba'],
							$tanggal,
							'CLOSING-' . date('Ymd') . '-' . $id_closing
						);
					}
				}
			}

			// Kumpulkan HPP per coa_persediaan dari semua nota detail
			// $hpp_per_coa = []; // ['coa_persediaan' => total_hpp]

			// foreach ($nota_list as $nota) {
			// 	$details = $this->M_nota->get_detail($nota->id);

			// 	foreach ($details as $detail) {
			// 		// Ambil coa_persediaan dari item
			// 		$item = $this->M_item_nota->get_by_id($detail->id_item);
			// 		$coa_item_persediaan = $item ? $item->coa_persediaan : null;

			// 		if (empty($coa_item_persediaan)) continue;

			// 		$subtotal_hpp = $detail->qty * $detail->harga_modal;

			// 		if ($subtotal_hpp <= 0) continue; // ← Skip kalau harga_modal 0 atau subtotal_hpp 0

			// 		if (!isset($hpp_per_coa[$coa_item_persediaan])) {
			// 			$hpp_per_coa[$coa_item_persediaan] = 0;
			// 		}
			// 		$hpp_per_coa[$coa_item_persediaan] += $subtotal_hpp;
			// 	}
			// 	$hpp_per_coa['jenis'] = $nota->metode_bayar;
			// }

			// // Jurnal 1 per COA Persediaan:
			// // Debit Kas lawan Kredit Persediaan (sebesar HPP per COA)
			// foreach ($hpp_per_coa as $coa_persediaan => $nominal_hpp) {
			// 	if ($nominal_hpp <= 0) continue;

			// 	$keterangan = 'Closing Kasir ' . $tanggal . ' - HPP [COA: ' . $coa_persediaan . ']';
			// 	$this->posting(
			// 		$coa_kas,           // Debit: Kas
			// 		$coa_persediaan,    // Kredit: Persediaan (per item)
			// 		$keterangan,
			// 		$nominal_hpp,
			// 		$tanggal,
			// 		'CLOSING-' . date('Ymd') . '-' . $id_closing
			// 	);
			// }

			// // Jurnal 2: Debit Kas lawan Kredit Pendapatan (sebesar Laba)
			// if ($total_laba > 0) {
			// 	$keterangan_laba = 'Closing Kasir ' . $tanggal . ' - Pendapatan';
			// 	$this->posting(
			// 		$coa_kas,           // Debit: Kas
			// 		$coa_pendapatan,    // Kredit: Pendapatan
			// 		$keterangan_laba,
			// 		$total_laba,
			// 		$tanggal,
			// 		'CLOSING-' . date('Ymd') . '-' . $id_closing
			// 	);
			// }

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal proses closing!']);
			} else {
				echo json_encode([
					'status'   => 'success',
					'message'  => 'Closing kasir berhasil diproses!',
					'redirect' => base_url('closing_nota')
				]);
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	// Posting jurnal (gunakan function yang udah ada)
	private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $id_invoice = NULL)
	{
		// Update coa debit 
		// $update_saldo_debit = $this->update_saldo_coa($coa_debit, $nominal, 'debit');
		// Update coa kredit
		// $update_saldo_kredit = $this->update_saldo_coa($coa_kredit, $nominal, 'kredit');

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
			// 'nama_file' => NULL,
			// 'file' => NULL
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

	// Helper function update saldo COA (sesuaikan dengan yang udah ada)
	private function update_saldo_coa($id_coa, $nominal, $type)
	{
		return $this->M_coa->update_saldo($id_coa, $nominal, $type);
	}

	// Helper function get saldo COA (sesuaikan dengan yang udah ada)
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

	// Detail closing
	public function detail($id)
	{
		$data['title'] = 'Detail Closing Kasir';
		$data['closing'] = $this->M_closing_nota->get_by_id($id);

		if (!$data['closing']) {
			$this->session->set_flashdata('error', 'Data tidak ditemukan!');
			redirect('closing_nota');
		}

		$data['nota_list'] = $this->M_closing_nota->get_nota_by_closing($id);
		$data['total_transaksi'] = 0;
		$data['tanggal'] = date('Y-m-d');

		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/nota/v_closing_detail";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}
}
