<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_nota extends CI_Model
{

	private $table = 'nota';
	private $table_detail = 'nota_detail';

	public function __construct()
	{
		parent::__construct();
	}

	// Get all nota dengan pagination dan search
	public function get_all($limit = null, $start = null, $search = null, $tanggal_dari = null, $tanggal_sampai = null)
	{
		$this->cb->select('nota.*, COUNT(nota_detail.id) as total_item');
		$this->cb->from($this->table);
		$this->cb->join($this->table_detail, 'nota_detail.id_nota = nota.id', 'left');
		$this->cb->where('nota.id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('nota.id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('nota.no_nota', $search);
			$this->cb->or_like('nota.customer', $search);
			$this->cb->group_end();
		}

		if ($tanggal_dari && $tanggal_sampai) {
			$this->cb->where('DATE(nota.tanggal) >=', $tanggal_dari);
			$this->cb->where('DATE(nota.tanggal) <=', $tanggal_sampai);
		}

		$this->cb->group_by('nota.id');
		$this->cb->order_by('nota.tanggal', 'DESC');
		$this->cb->order_by('nota.id', 'DESC');

		if ($limit !== null && $start !== null) {
			$this->cb->limit($limit, $start);
		}

		return $this->cb->get()->result();
	}

	// Count all nota untuk pagination
	public function count_all($search = null, $tanggal_dari = null, $tanggal_sampai = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('no_nota', $search);
			$this->cb->or_like('customer', $search);
			$this->cb->group_end();
		}

		if ($tanggal_dari && $tanggal_sampai) {
			$this->cb->where('DATE(tanggal) >=', $tanggal_dari);
			$this->cb->where('DATE(tanggal) <=', $tanggal_sampai);
		}

		return $this->cb->count_all_results($this->table);
	}

	// Get nota by ID dengan detail
	public function get_by_id($id)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Get detail nota
	public function get_detail($id_nota)
	{
		$this->cb->select('nota_detail.*, items.kode_item, items.nama_item, items.satuan');
		$this->cb->from($this->table_detail);
		$this->cb->join('items', 'items.id = nota_detail.id_item');
		$this->cb->where('nota_detail.id_nota', $id_nota);
		return $this->cb->get()->result();
	}

	// Generate nomor nota otomatis
	public function generate_no_nota()
	{
		$tanggal = date('Ymd');
		$prefix = 'NT-' . $tanggal . '-';

		$this->cb->select('no_nota');
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->like('no_nota', $prefix, 'after');
		$this->cb->order_by('id', 'DESC');
		$this->cb->limit(1);
		$query = $this->cb->get($this->table);

		if ($query->num_rows() > 0) {
			$last_no = $query->row()->no_nota;
			$last_number = (int) substr($last_no, -3);
			$new_number = $last_number + 1;
		} else {
			$new_number = 1;
		}

		return $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
	}

	// Insert nota (header)
	public function insert($data)
	{
		$this->cb->insert($this->table, $data);
		return $this->cb->insert_id();
	}

	// Insert nota detail
	public function insert_detail($data)
	{
		return $this->cb->insert($this->table_detail, $data);
	}

	// Get nota belum closing (untuk closing kasir)
	public function get_belum_closing($tanggal)
	{
		$this->cb->where('DATE(tanggal)', $tanggal);
		$this->cb->where('is_closed', 0);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->order_by('tanggal', 'ASC');
		return $this->cb->get($this->table)->result();
	}

	// Update status closing
	public function update_closing($id_nota, $id_closing)
	{
		$data = [
			'is_closed' => '1',
			'id_closing' => $id_closing,
			'updated_at' => date('Y-m-d H:i:s')
		];

		$this->cb->where('id', $id_nota);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		return $this->cb->update($this->table, $data);
	}

	// Get summary nota by metode bayar (untuk closing)
	public function get_summary_by_metode($tanggal)
	{
		$this->cb->select('
            metode_bayar,
            COUNT(id) as total_transaksi,
            SUM(total_penjualan) as total_penjualan,
            SUM(total_hpp) as total_hpp,
            SUM(laba_kotor) as laba_kotor
        ');
		$this->cb->where('DATE(tanggal)', $tanggal);
		$this->cb->where('is_closed', 0);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->group_by('metode_bayar');

		return $this->cb->get($this->table)->result();
	}
}
