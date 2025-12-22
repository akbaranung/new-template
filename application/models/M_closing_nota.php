<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_closing_nota extends CI_Model
{

	private $table = 'closing_kasir';

	public function __construct()
	{
		parent::__construct();
	}

	// Get all closing dengan pagination
	public function get_all($limit = null, $start = null, $search = null, $tanggal_dari = null, $tanggal_sampai = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->like('tanggal', $search);
		}

		if ($tanggal_dari && $tanggal_sampai) {
			$this->cb->where('tanggal >=', $tanggal_dari);
			$this->cb->where('tanggal <=', $tanggal_sampai);
		}

		$this->cb->order_by('tanggal', 'DESC');
		$this->cb->order_by('id', 'DESC');

		if ($limit !== null && $start !== null) {
			$this->cb->limit($limit, $start);
		}

		return $this->cb->get($this->table)->result();
	}

	// Count all closing
	public function count_all($search = null, $tanggal_dari = null, $tanggal_sampai = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->like('tanggal', $search);
		}

		if ($tanggal_dari && $tanggal_sampai) {
			$this->cb->where('tanggal >=', $tanggal_dari);
			$this->cb->where('tanggal <=', $tanggal_sampai);
		}

		return $this->cb->count_all_results($this->table);
	}

	// Get closing by ID
	public function get_by_id($id)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Get closing by tanggal
	public function get_by_tanggal($tanggal)
	{
		$this->cb->where('tanggal', $tanggal);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Check if tanggal sudah closing
	public function is_closed($tanggal)
	{
		$this->cb->where('tanggal', $tanggal);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->where('status', 'closed');
		return $this->cb->count_all_results($this->table) > 0;
	}

	// Insert closing
	public function insert($data)
	{
		$this->cb->insert($this->table, $data);
		return $this->cb->insert_id();
	}

	// Get nota yang sudah di-closing
	public function get_nota_by_closing($id_closing)
	{
		$this->cb->select('nota.*, COUNT(nota_detail.id) as total_item');
		$this->cb->from('nota');
		$this->cb->join('nota_detail', 'nota_detail.id_nota = nota.id', 'left');
		$this->cb->where('nota.id_closing', $id_closing);
		$this->cb->where('nota.id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('nota.id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->group_by('nota.id');
		$this->cb->order_by('nota.tanggal', 'ASC');
		return $this->cb->get()->result();
	}
}
