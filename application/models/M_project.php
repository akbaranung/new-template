<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_project extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	// =============================================
	// Generate nomor project
	// Format: PRJ-[KODE_CABANG]-[TAHUN]-0001
	// Reset per cabang per tahun
	// =============================================
	public function generate_no_project()
	{
		$kode_cabang = $this->session->userdata('kode_cabang');
		$tahun = date('Y');
		$prefix = "PRJ-{$kode_cabang}-{$tahun}-";

		$last = $this->cb
			->like('no_project', $prefix, 'after')
			->where('id_cabang', $kode_cabang)
			->order_by('id', 'DESC')
			->limit(1)
			->get('project_header')
			->row();

		if ($last) {
			$last_no = (int) substr($last->no_project, -4);
			$new_no  = str_pad($last_no + 1, 4, '0', STR_PAD_LEFT);
		} else {
			$new_no = '0001';
		}

		return $prefix . $new_no;
	}

	// =============================================
	// List project dengan pagination
	// =============================================
	public function list_project($limit, $offset, $keyword = '')
	{
		$kode_cabang = $this->session->userdata('kode_cabang');

		$this->cb->select('ph.*, 
            (SELECT SUM(pd.nominal) FROM project_detail pd WHERE pd.id_project = ph.id AND pd.posisi = "DEBIT") as total_debit,
            (SELECT SUM(pd.nominal) FROM project_detail pd WHERE pd.id_project = ph.id AND pd.posisi = "KREDIT") as total_kredit
        ');
		$this->cb->from('project_header ph');
		$this->cb->where('ph.id_cabang', $kode_cabang);

		if ($keyword) {
			$this->cb->group_start();
			$this->cb->like('ph.no_project', $keyword);
			$this->cb->or_like('ph.keterangan', $keyword);
			$this->cb->or_like('ph.created_by', $keyword);
			$this->cb->group_end();
		}

		$this->cb->order_by('ph.id', 'DESC');
		$this->cb->limit($limit, $offset);

		return $this->cb->get()->result_array();
	}

	// =============================================
	// Count project untuk pagination
	// =============================================
	public function count_project($keyword = '')
	{
		$kode_cabang = $this->session->userdata('kode_cabang');
		$this->cb->where('id_cabang', $kode_cabang);

		if ($keyword) {
			$this->cb->group_start();
			$this->cb->like('no_project', $keyword);
			$this->cb->or_like('keterangan', $keyword);
			$this->cb->or_like('created_by', $keyword);
			$this->cb->group_end();
		}

		return $this->cb->count_all_results('project_header');
	}

	// =============================================
	// Get detail project by id
	// =============================================
	public function get_project($id)
	{
		$kode_cabang = $this->session->userdata('kode_cabang');
		return $this->cb
			->where('id', $id)
			->where('id_cabang', $kode_cabang)
			->get('project_header')
			->row_array();
	}

	// =============================================
	// Get detail lines (project_detail) by id_project
	// =============================================
	public function get_project_detail($id_project)
	{
		return $this->cb
			->where('id_project', $id_project)
			->get('project_detail')
			->result_array();
	}

	// =============================================
	// Save project header + detail (insert)
	// =============================================
	public function save_project($header, $details)
	{
		$this->cb->trans_start();

		$this->cb->insert('project_header', $header);
		$id_project = $this->cb->insert_id();

		foreach ($details as $d) {
			$d['id_project'] = $id_project;
			$this->cb->insert('project_detail', $d);
		}

		$this->cb->trans_complete();

		return $this->cb->trans_status();
	}

	// =============================================
	// Update project header + detail
	// =============================================
	public function update_project($id, $header, $details)
	{
		$this->cb->trans_start();

		$this->cb->where('id', $id)->update('project_header', $header);

		// Hapus detail lama, insert ulang
		$this->cb->where('id_project', $id)->delete('project_detail');

		foreach ($details as $d) {
			$d['id_project'] = $id;
			$this->cb->insert('project_detail', $d);
		}

		$this->cb->trans_complete();

		return $this->cb->trans_status();
	}

	// =============================================
	// Delete project
	// =============================================
	public function delete_project($id)
	{
		// project_detail akan terhapus otomatis via FK CASCADE
		return $this->cb->where('id', $id)->delete('project_header');
	}
}
