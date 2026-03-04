<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Items extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->cb = $this->load->database('corebank', TRUE);
		$this->load->model('M_item_nota');
		$this->load->model('M_coa');
		$this->load->library('pagination');

		// Check if user is logged in
		if (!$this->session->userdata('nip')) {
			redirect('auth/login');
		}
	}

	// Halaman utama list items
	public function index()
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

		$data['title'] = 'Master Barang';

		// Search
		$search = $this->input->get('search');

		// Pagination config
		$config['base_url'] = base_url('items/index');
		$config['total_rows'] = $this->M_item_nota->count_all($search);
		$config['per_page'] = 20;
		$config['uri_segment'] = 3;

		// Bootstrap Pagination Styling
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

		if ($search) {
			$config['suffix'] = '?search=' . urlencode($search);
			$config['first_url'] = $config['base_url'] . '?search=' . urlencode($search);
		}

		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		// ✅ Ambil data items
		$data['items'] = $this->M_item_nota->get_all($config['per_page'], $page, $search);
		$data['pagination'] = $this->pagination->create_links();
		$data['search'] = $search;

		// ✅ Tambahkan data lain (tanpa overwrite data lama)
		$data['count_inbox'] = $result;
		$data['count_inbox2'] = $result2;
		$data['pages'] = "pages/items/index";
		$data['utility'] = $this->db->get('utility')->row_array();
		$data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

		$this->load->view('index', $data);
	}


	// Form tambah/edit (AJAX)
	public function form($id = null)
	{
		if ($id) {
			$data['item'] = $this->M_item_nota->get_by_id($id);
			$data['mode'] = 'edit';
		} else {
			$data['item'] = null;
			$data['mode'] = 'add';
			$data['kode_item'] = $this->M_item_nota->generate_kode();
		}

		$data['coa_list'] = $this->M_coa->list_coa();
		$this->load->view('pages/items/form', $data);
	}

	// Save (insert/update)
	public function save()
	{
		$id = $this->input->post('id');
		$kode_item = $this->input->post('kode_item');
		$nama_item = trim($this->input->post('nama_item'));
		$satuan = $this->input->post('satuan');
		$harga_modal = str_replace('.', '', $this->input->post('harga_modal'));
		$harga_jual = str_replace('.', '', $this->input->post('harga_jual'));
		// $stok = str_replace(',', '.', $this->input->post('stok'));
		$coa_persediaan = $this->input->post('coa_persediaan'); // ← BARU

		// Validasi
		if (empty($nama_item)) {
			echo json_encode(['status' => 'error', 'message' => 'Nama barang harus diisi!']);
			return;
		}

		if (empty($coa_persediaan)) {
			echo json_encode(['status' => 'error', 'message' => 'COA Persediaan harus dipilih!']);
			return;
		}

		// Check kode exists
		if ($this->M_item_nota->is_kode_exists($kode_item, $id)) {
			echo json_encode(['status' => 'error', 'message' => 'Kode barang sudah digunakan!']);
			return;
		}

		$data = [
			'kode_item' => $kode_item,
			'nama_item' => $nama_item,
			'satuan' => $satuan,
			'harga_modal' => $harga_modal,
			'harga_jual' => $harga_jual,
			'coa_persediaan' => $coa_persediaan, // ← BARU
			'id_cabang' => $this->session->userdata('kode_cabang'),
			'id_company' => $this->session->userdata('user_perusahaan_id')
		];

		if ($id) {
			// Update
			$data['updated_at'] = date('Y-m-d H:i:s');
			$result = $this->M_item_nota->update($id, $data);
			$message = 'Data barang berhasil diupdate!';
		} else {
			// Insert
			$data['created_at'] = date('Y-m-d H:i:s');
			$result = $this->M_item_nota->insert($data);
			$message = 'Data barang berhasil ditambahkan!';
		}

		if ($result) {
			echo json_encode(['status' => 'success', 'message' => $message]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data!']);
		}
	}

	// Delete
	public function delete()
	{
		$id = $this->input->post('id');

		$result = $this->M_item_nota->delete($id);

		if ($result) {
			echo json_encode(['status' => 'success', 'message' => 'Data barang berhasil dihapus!']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data!']);
		}
	}

	// Get item for select2 (AJAX)
	public function get_items()
	{
		$search = $this->input->get('q');
		$items = $this->M_item_nota->get_for_select2($search);

		$data = [];
		foreach ($items as $item) {
			$data[] = [
				'id' => $item->id,
				'text' => $item->kode_item . ' - ' . $item->nama_item,
				'kode_item' => $item->kode_item,
				'nama_item' => $item->nama_item,
				'satuan' => $item->satuan,
				'harga_jual' => $item->harga_jual,
				'harga_modal' => $item->harga_modal,
				'stok' => $item->stok,
				'coa_persediaan' => $item->coa_persediaan,
			];
		}

		echo json_encode(['results' => $data]);
	}
}
