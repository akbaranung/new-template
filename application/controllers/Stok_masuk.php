<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stok_masuk extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_stok_masuk');
		$this->load->model('M_item_nota');
		$this->load->model('M_coa'); // Untuk pilih COA
		$this->load->library('pagination');

		// Check if user is logged in
		if (!$this->session->userdata('nip')) {
			redirect('auth/login');
		}

		$this->cb = $this->load->database('corebank', TRUE);
	}

	// Halaman utama list stok masuk
	public function index()
	{
		$data['title'] = 'Input Stok Barang';

		// Search
		$search = $this->input->get('search');

		// Pagination config
		$config['base_url'] = base_url('stok_masuk/index');
		$config['total_rows'] = $this->M_stok_masuk->count_all($search);
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

		// Reuse query string for search
		if ($search) {
			$config['suffix'] = '?search=' . urlencode($search);
			$config['first_url'] = $config['base_url'] . '?search=' . urlencode($search);
		}

		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['stok_masuk'] = $this->M_stok_masuk->get_all($config['per_page'], $page, $search);
		$data['pagination'] = $this->pagination->create_links();
		$data['search'] = $search;

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/items/v_stok_masuk";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Form input stok
	public function form()
	{
		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['title'] = 'Input Stok Barang';
		$data['no_transaksi'] = $this->M_stok_masuk->generate_no_transaksi();
		$data['coa_list'] = $this->M_coa->list_coa();

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/items/v_form_stok_masuk";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Modal pilih COA
	public function modal_coa()
	{
		$metode_bayar = $this->input->get('metode');
		$data['metode_bayar'] = $metode_bayar;
		$data['coa_list'] = $this->M_coa->list_coa(); // Sesuaikan dengan method di M_coa

		$this->load->view('pages/items/v_modal_coa_stok_masuk', $data);
	}

	// Save stok masuk
	public function save()
	{
		$this->db->trans_start();

		try {
			$no_transaksi   = $this->input->post('no_transaksi');
			$tanggal        = $this->input->post('tanggal');
			$supplier       = trim($this->input->post('supplier'));
			$metode_bayar   = $this->input->post('metode_bayar');
			$coa_kas_utang  = $this->input->post('coa_kas_utang'); // ← hanya ini yang dipilih user
			$harga_juals    = $this->input->post('harga_jual');

			$id_items     = $this->input->post('id_item');
			$qtys         = $this->input->post('qty');
			$harga_modals = $this->input->post('harga_modal');

			if (empty($id_items) || count($id_items) == 0) {
				echo json_encode(['status' => 'error', 'message' => 'Item barang harus diisi!']);
				return;
			}

			$allowed_metode = ['cash'];
			if (!in_array($metode_bayar, $allowed_metode)) {
				echo json_encode(['status' => 'error', 'message' => 'Metode pembayaran tidak valid!']);
				return;
			}

			// Hitung total & kumpulkan nominal per coa_persediaan
			$total_nominal  = 0;
			$nominal_per_coa = []; // ← ['coa_persediaan' => total_nominal]

			foreach ($id_items as $key => $id_item) {
				if (empty($id_item)) continue;

				$qty         = str_replace(',', '.', $qtys[$key]);
				$harga_modal = str_replace('.', '', $harga_modals[$key]);
				$harga_jual  = str_replace('.', '', $harga_juals[$key]);
				$subtotal    = $qty * $harga_modal;
				$total_nominal += $subtotal;

				// Ambil coa_persediaan dari master item
				$item = $this->M_item_nota->get_by_id($id_item);
				$coa_persediaan = $item ? $item->coa_persediaan : null;

				if (!empty($coa_persediaan) && $subtotal > 0) {
					if (!isset($nominal_per_coa[$coa_persediaan])) {
						$nominal_per_coa[$coa_persediaan] = 0;
					}
					$nominal_per_coa[$coa_persediaan] += $subtotal;
				}
			}

			// Insert header
			$data_header = [
				'no_transaksi'  => $no_transaksi,
				'tanggal'       => $tanggal,
				'supplier'      => $supplier,
				'total_nominal' => $total_nominal,
				'metode_bayar'  => $metode_bayar,
				'id_cabang'     => $this->session->userdata('kode_cabang'),
				'id_company'    => $this->session->userdata('user_perusahaan_id'),
				'created_by'    => $this->session->userdata('nip'),
				'created_at'    => date('Y-m-d H:i:s')
			];

			$id_stok_masuk = $this->M_stok_masuk->insert($data_header);

			// Insert detail & update stok
			foreach ($id_items as $key => $id_item) {
				if (empty($id_item)) continue;

				$qty         = str_replace(',', '.', $qtys[$key]);
				$harga_modal = str_replace('.', '', $harga_modals[$key]);
				$harga_jual  = str_replace('.', '', $harga_juals[$key]);
				$subtotal    = $qty * $harga_modal;

				$data_detail = [
					'id_stok_masuk' => $id_stok_masuk,
					'id_item'       => $id_item,
					'qty'           => $qty,
					'harga_modal'   => $harga_modal,
					'harga_jual'    => $harga_jual,
					'subtotal'      => $subtotal
				];

				$this->M_stok_masuk->insert_detail($data_detail);
				$this->M_item_nota->update_stok_with_average($id_item, $qty, $harga_modal, 'add');
				$this->M_item_nota->update_harga_jual($id_item, $harga_jual);
			}

			// Posting jurnal split per coa_persediaan
			foreach ($nominal_per_coa as $coa_persediaan => $nominal) {
				$keterangan = 'Input Stok - ' . $no_transaksi . ' - Supplier: ' . $supplier . ' [COA: ' . $coa_persediaan . ']';
				$this->posting($coa_persediaan, $coa_kas_utang, $keterangan, $nominal, $tanggal, $no_transaksi);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data!']);
			} else {
				echo json_encode([
					'status'   => 'success',
					'message'  => 'Input stok berhasil disimpan!',
					'redirect' => base_url('stok_masuk')
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
		// Implementasi sesuai dengan system lo
		// Ini placeholder, sesuaikan dengan M_coa lo
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

	// Detail stok masuk
	public function detail($id)
	{
		$data['title'] = 'Detail Input Stok';
		$data['stok_masuk'] = $this->M_stok_masuk->get_by_id($id);

		if (!$data['stok_masuk']) {
			$this->session->set_flashdata('error', 'Data tidak ditemukan!');
			redirect('stok_masuk');
		}

		$data['detail'] = $this->M_stok_masuk->get_detail($id);

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
		$data['pages'] = "pages/items/v_detail_stok_masuk";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}
}
