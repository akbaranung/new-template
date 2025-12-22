<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_stok_masuk extends CI_Model
{

	private $table = 'stok_masuk';
	private $table_detail = 'stok_masuk_detail';

	public function __construct()
	{
		parent::__construct();
	}

	// Get all stok masuk dengan pagination dan search
	public function get_all($limit = null, $start = null, $search = null)
	{
		$this->cb->select('stok_masuk.*, COUNT(stok_masuk_detail.id) as total_item');
		$this->cb->from($this->table);
		$this->cb->join($this->table_detail, 'stok_masuk_detail.id_stok_masuk = stok_masuk.id', 'left');
		$this->cb->where('stok_masuk.id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('stok_masuk.id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('stok_masuk.no_transaksi', $search);
			$this->cb->or_like('stok_masuk.supplier', $search);
			$this->cb->group_end();
		}

		$this->cb->group_by('stok_masuk.id');
		$this->cb->order_by('stok_masuk.tanggal', 'DESC');
		$this->cb->order_by('stok_masuk.id', 'DESC');

		if ($limit !== null && $start !== null) {
			$this->cb->limit($limit, $start);
		}

		return $this->cb->get()->result();
	}

	// Count all stok masuk untuk pagination
	public function count_all($search = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('no_transaksi', $search);
			$this->cb->or_like('supplier', $search);
			$this->cb->group_end();
		}

		return $this->cb->count_all_results($this->table);
	}

	// Get stok masuk by ID dengan detail
	public function get_by_id($id)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Get detail stok masuk
	public function get_detail($id_stok_masuk)
	{
		$this->cb->select('stok_masuk_detail.*, items.kode_item, items.nama_item, items.satuan');
		$this->cb->from($this->table_detail);
		$this->cb->join('items', 'items.id = stok_masuk_detail.id_item');
		$this->cb->where('stok_masuk_detail.id_stok_masuk', $id_stok_masuk);
		return $this->cb->get()->result();
	}

	// Generate nomor transaksi otomatis
	public function generate_no_transaksi()
	{
		$tanggal = date('Ymd');
		$prefix = 'SM-' . $tanggal . '-';

		$this->cb->select('no_transaksi');
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->like('no_transaksi', $prefix, 'after');
		$this->cb->order_by('id', 'DESC');
		$this->cb->limit(1);
		$query = $this->cb->get($this->table);

		if ($query->num_rows() > 0) {
			$last_no = $query->row()->no_transaksi;
			$last_number = (int) substr($last_no, -3);
			$new_number = $last_number + 1;
		} else {
			$new_number = 1;
		}

		return $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
	}

	// Insert stok masuk (header)
	public function insert($data)
	{
		$this->cb->insert($this->table, $data);
		return $this->cb->insert_id();
	}

	// Insert stok masuk detail
	public function insert_detail($data)
	{
		return $this->cb->insert($this->table_detail, $data);
	}

	// Delete stok masuk (jika diperlukan untuk fitur future)
	public function delete($id)
	{
		// Delete detail first
		$this->cb->where('id_stok_masuk', $id);
		$this->cb->delete($this->table_detail);

		// Delete header
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->delete($this->table);
	}
}
