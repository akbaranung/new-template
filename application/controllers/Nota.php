<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nota extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_nota');
		$this->load->model('M_items');
		$this->load->library('pagination');

		// Check if user is logged in
		if (!$this->session->userdata('nip')) {
			redirect('auth/login');
		}

		$this->cb = $this->load->database('corebank', TRUE);
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
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

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
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Save nota
	public function save()
	{
		$this->db->trans_start();

		try {
			$no_nota = $this->input->post('no_nota');
			$tanggal = $this->input->post('tanggal') . ' ' . date('H:i:s');
			// $customer = trim($this->input->post('customer'));
			$customer = "";
			$metode_bayar = $this->input->post('metode_bayar');

			// Detail items
			$id_items = $this->input->post('id_item');
			$qtys = $this->input->post('qty');
			$harga_juals = $this->input->post('harga_jual');

			// Validasi
			if (empty($id_items) || count($id_items) == 0) {
				echo json_encode(['status' => 'error', 'message' => 'Item barang harus diisi!']);
				return;
			}

			// Hitung total
			$total_penjualan = 0;
			$total_hpp = 0;

			// Validasi stok & hitung total
			foreach ($id_items as $key => $id_item) {
				if (empty($id_item))
					continue;

				$qty = str_replace(',', '.', $qtys[$key]);
				$item = $this->M_items->get_by_id($id_item);

				// Validasi item ada
				if (!$item) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Barang tidak ditemukan!'
					]);
					return;
				}

				// Validasi qty > 0
				if (
					$qty <= 0
				) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Qty untuk ' . $item->nama_item . ' harus lebih dari 0!'
					]);
					return;
				}

				// Cek stok tersedia
				if ($item->stok <= 0) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Stok ' . $item->nama_item . ' habis! (Stok: 0)'
					]);
					return;
				}

				// Cek stok cukup
				if ($item->stok < $qty) {
					echo json_encode([
						'status' => 'error',
						'message' => 'Stok ' . $item->nama_item . ' tidak mencukupi! (Tersedia: ' . number_format($item->stok, 2) . ' ' . $item->satuan . ')'
					]);
					return;
				}

				$harga_jual = str_replace('.', '', $harga_juals[$key]);
				$subtotal_jual = $qty * $harga_jual;
				$subtotal_hpp = $qty * $item->harga_modal; // HPP dari harga modal average

				$total_penjualan += $subtotal_jual;
				$total_hpp += $subtotal_hpp;
			}
			$laba_kotor = $total_penjualan - $total_hpp;

			// Insert header nota
			$data_header = [
				'no_nota' => $no_nota,
				'tanggal' => $tanggal,
				'customer' => $customer,
				'total_penjualan' => $total_penjualan,
				'total_hpp' => $total_hpp,
				'laba_kotor' => $laba_kotor,
				'metode_bayar' => $metode_bayar,
				'is_closed' => 0, // Belum closing
				'id_cabang' => $this->session->userdata('kode_cabang'),
				'id_company' => $this->session->userdata('user_perusahaan_id'),
				'created_by' => $this->session->userdata('nip'),
				'created_at' => date('Y-m-d H:i:s')
			];

			$id_nota = $this->M_nota->insert($data_header);

			// Insert detail & update stok items
			foreach ($id_items as $key => $id_item) {
				if (empty($id_item))
					continue;

				$qty = str_replace(',', '.', $qtys[$key]);
				$harga_jual = str_replace('.', '', $harga_juals[$key]);

				$item = $this->M_items->get_by_id($id_item);
				$harga_modal = $item->harga_modal; // Ambil harga modal average

				$subtotal_jual = $qty * $harga_jual;
				$subtotal_hpp = $qty * $harga_modal;

				// Insert detail
				$data_detail = [
					'id_nota' => $id_nota,
					'id_item' => $id_item,
					'qty' => $qty,
					'harga_jual' => $harga_jual,
					'harga_modal' => $harga_modal,
					'subtotal_jual' => $subtotal_jual,
					'subtotal_hpp' => $subtotal_hpp
				];

				$this->M_nota->insert_detail($data_detail);

				// Update stok items (kurang) dengan average method
				$this->M_items->update_stok_with_average($id_item, $qty, 0, 'subtract');
			}

			// TIDAK ADA POSTING JURNAL DI SINI
			// Jurnal akan dibuat saat CLOSING KASIR

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data!']);
			} else {
				echo json_encode([
					'status' => 'success',
					'message' => 'Nota berhasil disimpan!',
					'id_nota' => $id_nota,
					'no_nota' => $no_nota,
					'redirect' => base_url('nota')
				]);
			}
		} catch (Exception $e) {
			$this->db->trans_rollback();
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
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}

	// Print nota
	public function print_nota($id)
	{
		$data['nota'] = $this->M_nota->get_by_id($id);

		if (!$data['nota']) {
			$this->session->set_flashdata('error', 'Data tidak ditemukan!');
			redirect('nota');
		}

		$data['detail'] = $this->M_nota->get_detail($id);

		// Load view tanpa template (pure HTML untuk print)
		$this->load->view('pages/nota/v_print', $data);
	}
}
