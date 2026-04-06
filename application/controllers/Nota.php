<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nota extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_nota');
		$this->load->model('M_item_nota');
		$this->load->library('pagination');

		// Check if user is logged in
		if (!$this->session->userdata('nip')) {
			redirect('auth/login');
		}

		$this->cb = $this->load->database('corebank', TRUE);
		date_default_timezone_set('Asia/Jakarta');
	}

	// Halaman utama list nota
	public function index()
	{
		$data['title'] = 'Nota Penjualan';

		// Search & Filter
		$search = $this->input->get('search');
		$tanggal_dari = $this->input->get('tanggal_dari');
		$tanggal_sampai = $this->input->get('tanggal_sampai');

		// Pagination config
		$config['base_url'] = base_url('nota/index');
		$config['total_rows'] = $this->M_nota->count_all($search, $tanggal_dari, $tanggal_sampai);
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

		$nip = $this->session->userdata('nip');

		$sql = "SELECT COUNT(Id) as jml FROM memo 
            WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') 
            AND (`read` NOT LIKE '%$nip%')";
		$result = $this->db->query($sql)->row()->jml;

		$sql2 = "SELECT COUNT(id) as jml FROM task 
             WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') 
             and activity='1'";
		$result2 = $this->db->query($sql2)->row()->jml;

		$data['nota'] = $this->M_nota->get_all($config['per_page'], $page, $search, $tanggal_dari, $tanggal_sampai);
		$data['pagination'] = $this->pagination->create_links();
		$data['search'] = $search;
		$data['tanggal_dari'] = $tanggal_dari;
		$data['tanggal_sampai'] = $tanggal_sampai;

		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/nota/v_index";
		$data['utility'] = $this->db->where('Id', $this->session->userdata('user_perusahaan_id'))->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$id_cabang            = $this->session->userdata('kode_cabang');
		$data['struk_cabang'] = $this->get_struk_setting($data['utility'], $id_cabang);

		$this->load->view('index', $data);
	}

	// Form nota
	public function form()
	{
		$data['title'] = 'Buat Nota Penjualan';
		$data['no_nota'] = $this->M_nota->generate_no_nota();

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
		$data['pages'] = "pages/nota/v_form";
		$data['utility'] = $this->db->where('Id', $this->session->userdata('user_perusahaan_id'))->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// save nota
	public function save()
	{
		$this->cb->trans_start();

		try {
			$no_nota      = $this->input->post('no_nota');
			$tanggal      = $this->input->post('tanggal') . ' ' . date('H:i:s');
			$customer     = trim($this->input->post('customer'));
			$metode_bayar = $this->input->post('metode_bayar');
			$no_kartu     = trim($this->input->post('no_kartu'));

			// Validasi metode bayar
			$allowed_metode = ['cash', 'qris', 'card'];
			if (!in_array($metode_bayar, $allowed_metode)) {
				echo json_encode(['status' => 'error', 'message' => 'Metode pembayaran tidak valid!']);
				return;
			}

			// Validasi no_kartu wajib kalau card
			if ($metode_bayar === 'card' && empty($no_kartu)) {
				echo json_encode(['status' => 'error', 'message' => 'No. kartu harus diisi untuk pembayaran Card!']);
				return;
			}

			// Detail items
			$id_items    = $this->input->post('id_item');
			$qtys        = $this->input->post('qty');
			$harga_juals = $this->input->post('harga_jual');

			if (empty($id_items) || count($id_items) == 0) {
				echo json_encode(['status' => 'error', 'message' => 'Item barang harus diisi!']);
				return;
			}

			// Agregasi qty per id_item untuk validasi stok gabungan
			$qty_per_item = [];
			foreach ($id_items as $key => $id_item) {
				if (empty($id_item)) continue;
				$qty = floatval(str_replace(',', '.', $qtys[$key]));
				if (!isset($qty_per_item[$id_item])) {
					$qty_per_item[$id_item] = 0;
				}
				$qty_per_item[$id_item] += $qty;
			}

			// Validasi stok gabungan per item
			foreach ($qty_per_item as $id_item => $total_qty) {
				$item = $this->M_item_nota->get_by_id($id_item);

				if (!$item) {
					echo json_encode(['status' => 'error', 'message' => 'Barang tidak ditemukan!']);
					return;
				}

				if ($total_qty <= 0) {
					echo json_encode([
						'status'  => 'error',
						'message' => 'Qty untuk ' . $item->nama_item . ' harus lebih dari 0!'
					]);
					return;
				}

				if ($item->stok <= 0) {
					echo json_encode([
						'status'  => 'error',
						'message' => 'Stok ' . $item->nama_item . ' habis!'
					]);
					return;
				}

				if ($item->stok < $total_qty) {
					echo json_encode([
						'status'  => 'error',
						'message' => 'Stok ' . $item->nama_item . ' tidak mencukupi! ' .
							'(Tersedia: ' . number_format($item->stok, 2) . ', ' .
							'Total diminta: ' . number_format($total_qty, 2) . ' ' . $item->satuan . ')'
					]);
					return;
				}
			}

			// Hitung total penjualan & HPP
			$total_penjualan = 0;
			$total_hpp       = 0;

			foreach ($id_items as $key => $id_item) {
				if (empty($id_item)) continue;

				$qty        = floatval(str_replace(',', '.', $qtys[$key]));
				$harga_jual = floatval(str_replace('.', '', $harga_juals[$key]));
				$item       = $this->M_item_nota->get_by_id($id_item);

				$total_penjualan += $qty * $harga_jual;
				$total_hpp       += $qty * $item->harga_modal;
			}

			// Setelah loop hitung $total_penjualan & $total_hpp, tambah:
			$diskon_tipe  = $this->input->post('diskon_tipe');
			$diskon_nilai = floatval($this->input->post('diskon_nilai'));

			// Whitelist
			if (!in_array($diskon_tipe, ['persen', 'nominal'])) {
				$diskon_tipe = 'persen';
			}

			$diskon_amount = 0;
			if ($diskon_tipe === 'persen') {
				$diskon_nilai  = min($diskon_nilai, 100); // max 100%
				$diskon_amount = $total_penjualan * ($diskon_nilai / 100);
			} else {
				$diskon_amount = min($diskon_nilai, $total_penjualan); // max = total
			}

			$total_setelah_diskon = $total_penjualan - $diskon_amount;
			$laba_kotor = $total_penjualan - $total_hpp; // laba dari harga normal, tidak terpengaruh diskon

			// Insert header nota
			$data_header = [
				'no_nota'         => $no_nota,
				'tanggal'         => $tanggal,
				'customer'        => $customer,
				'diskon_tipe'     => $diskon_nilai > 0 ? $diskon_tipe : null,
				'diskon_nilai'    => $diskon_nilai,
				'diskon_amount'   => $diskon_amount,
				'total_penjualan' => $total_setelah_diskon, // simpan total SETELAH diskon
				'total_hpp'       => $total_hpp,
				'laba_kotor'      => $laba_kotor,
				'metode_bayar'    => $metode_bayar,
				'no_kartu'        => ($metode_bayar === 'card') ? $no_kartu : null,
				'is_closed'       => 0,
				'id_cabang'       => $this->session->userdata('kode_cabang'),
				'id_company'      => $this->session->userdata('user_perusahaan_id'),
				'created_by'      => $this->session->userdata('nip'),
				'created_at'      => date('Y-m-d H:i:s')
			];

			$id_nota = $this->M_nota->insert($data_header);

			if (!$id_nota) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan header nota!']);
				return;
			}

			// Insert detail & update stok
			foreach ($id_items as $key => $id_item) {
				if (empty($id_item)) continue;

				$qty         = floatval(str_replace(',', '.', $qtys[$key]));
				$harga_jual  = floatval(str_replace('.', '', $harga_juals[$key]));
				$item        = $this->M_item_nota->get_by_id($id_item);
				$harga_modal = $item->harga_modal;

				$subtotal_jual = $qty * $harga_jual;
				$subtotal_hpp  = $qty * $harga_modal;

				$data_detail = [
					'id_nota'       => $id_nota,
					'id_item'       => $id_item,
					'qty'           => $qty,
					'harga_jual'    => $harga_jual,
					'harga_modal'   => $harga_modal,
					'subtotal_jual' => $subtotal_jual,
					'subtotal_hpp'  => $subtotal_hpp
				];
				$this->M_nota->insert_detail($data_detail);

				$stok_before        = $item->stok;
				$nilai_before       = $item->nilai_persediaan;
				$harga_modal_before = $item->harga_modal;

				$this->M_item_nota->update_stok_with_average($id_item, $qty, $harga_modal, 'subtract');

				$item_after = $this->M_item_nota->get_by_id($id_item);

				$log_data = [
					'id_item'            => $id_item,
					'qty_before'         => $stok_before,
					'qty_after'          => $item_after->stok,
					'qty_change'         => -$qty,
					'nilai_before'       => $nilai_before,
					'nilai_after'        => $item_after->nilai_persediaan,
					'harga_modal_before' => $harga_modal_before,
					'harga_modal_after'  => $item_after->harga_modal,
					'type'               => 'penjualan',
					'ref_id'             => $id_nota,
					'no_ref'             => $no_nota,
					'id_cabang'          => $this->session->userdata('kode_cabang'),
					'id_company'         => $this->session->userdata('user_perusahaan_id'),
					'created_by'         => $this->session->userdata('nip'),
					'created_at'         => date('Y-m-d H:i:s')
				];
				$this->cb->insert('stok_log', $log_data);
			}

			$this->cb->trans_complete();

			if ($this->cb->trans_status() === FALSE) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data, transaksi dibatalkan!']);
			} else {
				echo json_encode([
					'status'   => 'success',
					'message'  => 'Nota berhasil disimpan!',
					'id_nota'  => $id_nota,
					'no_nota'  => $no_nota,
					'redirect' => base_url('nota')
				]);
			}
		} catch (Exception $e) {
			$this->cb->trans_rollback();
			echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	// Detail nota
	public function detail($id)
	{
		$data['title'] = 'Detail Nota Penjualan';
		$data['nota'] = $this->M_nota->get_by_id($id);

		if (!$data['nota']) {
			$this->session->set_flashdata('error', 'Data tidak ditemukan!');
			redirect('nota');
		}

		$data['detail'] = $this->M_nota->get_detail($id);

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
		$data['pages'] = "pages/nota/v_detail";
		$data['utility'] = $this->db->where('Id', $this->session->userdata('user_perusahaan_id'))->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Print nota
	public function print_nota($id)
	{
		$data['nota']    = $this->M_nota->get_by_id($id);
		if (!$data['nota']) {
			redirect('nota');
		}

		$data['detail']  = $this->M_nota->get_detail($id);
		$data['utility'] = $this->db->where('Id', $this->session->userdata('user_perusahaan_id'))->get('utility')->row_array();

		$id_cabang = $this->session->userdata('kode_cabang');
		$s = $this->get_struk_setting($data['utility'], $id_cabang);

		// nama_toko fallback ke nama_perusahaan kalau kosong
		$data['setting'] = [
			'nama_toko'         => !empty($s['nama_toko']) ? $s['nama_toko'] : ($data['utility']['nama_perusahaan'] ?? 'NAMA TOKO'),
			'footer_1'          => $s['footer_1'],
			'footer_2'          => $s['footer_2'],
			'footer_3'          => $s['footer_3'],
			'show_kasir'        => (int)$s['show_kasir'],
			'show_harga_satuan' => (int)$s['show_harga_satuan'],
			'auto_print'        => (int)$s['auto_print'],
		];

		$lebar = $s['lebar_kertas'];
		$data['struk_css'] = $lebar === '58' ? [
			'lebar'          => '58mm',
			'font_size'      => '10px',
			'font_size_kecil' => '9px',
			'font_toko'      => '13px',
			'padding'        => '2mm',
			'padding_print'  => '1mm',
			'col_nama'       => '50%',
			'col_qty'        => '15%',
			'col_harga'      => '35%',
			'font_total'     => '11px',
		] : [
			'lebar'          => '80mm',
			'font_size'      => '12px',
			'font_size_kecil' => '11px',
			'font_toko'      => '16px',
			'padding'        => '4mm',
			'padding_print'  => '2mm',
			'col_nama'       => '55%',
			'col_qty'        => '15%',
			'col_harga'      => '30%',
			'font_total'     => '13px',
		];

		$this->load->view('pages/nota/v_print_struk_nota', $data);
	}

	public function setting_struk()
	{
		if (!$this->input->post()) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid request!']);
			return;
		}

		$id_cabang = $this->session->userdata('kode_cabang');
		$utility   = $this->db->get('utility')->row_array();

		// Baca JSON existing, kalau kosong/invalid mulai dari array kosong
		$struk_setting = [];
		if (!empty($utility['struk_setting'])) {
			$decoded = json_decode($utility['struk_setting'], true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$struk_setting = $decoded;
			}
		}

		// Update hanya untuk cabang yang sedang login
		$struk_setting[$id_cabang] = [
			'lebar_kertas'      => $this->input->post('struk_lebar_kertas'),
			'nama_toko'         => trim($this->input->post('struk_nama_toko')),
			'footer_1'          => trim($this->input->post('struk_footer_1')),
			'footer_2'          => trim($this->input->post('struk_footer_2')),
			'footer_3'          => trim($this->input->post('struk_footer_3')),
			'show_kasir'        => $this->input->post('struk_show_kasir')        ? 1 : 0,
			'show_harga_satuan' => $this->input->post('struk_show_harga_satuan') ? 1 : 0,
			'auto_print'        => $this->input->post('struk_auto_print')        ? 1 : 0,
		];

		$this->db->where('Id', $this->session->userdata('user_perusahaan_id'))->update('utility', ['struk_setting' => json_encode($struk_setting)]);

		echo json_encode(['status' => 'success', 'message' => 'Pengaturan struk cabang berhasil disimpan!']);
	}

	private function get_struk_setting($utility, $id_cabang)
	{
		$default = [
			'lebar_kertas'      => '80',
			'nama_toko'         => '',
			'footer_1'          => 'Terima kasih atas kunjungan Anda',
			'footer_2'          => 'Barang yang sudah dibeli',
			'footer_3'          => 'tidak dapat dikembalikan',
			'show_kasir'        => 1,
			'show_harga_satuan' => 1,
			'auto_print'        => 1,
		];

		// Layer 2: kolom lama sebagai fallback global
		$global = [
			'lebar_kertas'      => $utility['struk_lebar_kertas']      ?? $default['lebar_kertas'],
			'nama_toko'         => $utility['struk_nama_toko']          ?? $default['nama_toko'],
			'footer_1'          => $utility['struk_footer_1']           ?? $default['footer_1'],
			'footer_2'          => $utility['struk_footer_2']           ?? $default['footer_2'],
			'footer_3'          => $utility['struk_footer_3']           ?? $default['footer_3'],
			'show_kasir'        => isset($utility['struk_show_kasir'])        ? (int)$utility['struk_show_kasir']        : $default['show_kasir'],
			'show_harga_satuan' => isset($utility['struk_show_harga_satuan']) ? (int)$utility['struk_show_harga_satuan'] : $default['show_harga_satuan'],
			'auto_print'        => isset($utility['struk_auto_print'])        ? (int)$utility['struk_auto_print']        : $default['auto_print'],
		];

		// Layer 1: JSON per-cabang
		if (!empty($utility['struk_setting'])) {
			$json = json_decode($utility['struk_setting'], true);
			if (json_last_error() === JSON_ERROR_NONE && isset($json[$id_cabang])) {
				$cabang_setting = $json[$id_cabang];
				// Merge: per-cabang override global, global override default
				return array_merge($global, $cabang_setting);
			}
		}

		return $global;
	}
}
