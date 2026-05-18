<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_item_nota extends CI_Model
{
	// 

	private $table = 'items';

	public function __construct()
	{
		parent::__construct();
	}

	// Get all items dengan pagination dan search
	public function get_all($limit = null, $start = null, $search = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('kode_item', $search);
			$this->cb->or_like('nama_item', $search);
			$this->cb->group_end();
		}

		if ($limit !== null && $start !== null) {
			$this->cb->limit($limit, $start);
		}

		$this->cb->order_by('nama_item', 'ASC');
		return $this->cb->get($this->table)->result();
	}

	// Count all items untuk pagination
	public function count_all($search = null)
	{
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search) {
			$this->cb->group_start();
			$this->cb->like('kode_item', $search);
			$this->cb->or_like('nama_item', $search);
			$this->cb->group_end();
		}

		return $this->cb->count_all_results($this->table);
	}

	// Get item by ID
	public function get_by_id($id)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Get item by kode
	public function get_by_kode($kode_item)
	{
		$this->cb->where('kode_item', $kode_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->get($this->table)->row();
	}

	// Generate kode item otomatis
	public function generate_kode()
	{
		$this->cb->select('kode_item');
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$this->cb->like('kode_item', 'ITM-', 'after');
		$this->cb->order_by('id', 'DESC');
		$this->cb->limit(1);
		$query = $this->cb->get($this->table);

		if ($query->num_rows() > 0) {
			$last_kode = $query->row()->kode_item;
			$last_number = (int) substr($last_kode, 4);
			$new_number = $last_number + 1;
		} else {
			$new_number = 1;
		}

		return 'ITM-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
	}

	// Insert item
	public function insert($data)
	{
		return $this->cb->insert($this->table, $data);
	}

	// Update item
	public function update($id, $data)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->update($this->table, $data);
	}

	// Delete item
	public function delete($id)
	{
		$this->cb->where('id', $id);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		return $this->cb->delete($this->table);
	}

	// Update stok dengan average method
	public function update_stok_with_average($id_item, $qty, $harga_beli, $type = 'add')
	{
		// Validasi parameter
		if (empty($id_item) || $qty <= 0) {
			log_message('error', 'update_stok_with_average: Parameter tidak lengkap');
			return false;
		}

		$this->cb->where('id', $id_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$item = $this->cb->get($this->table)->row();

		if (!$item)
			return false;

		if ($type == 'add') {
			$nilai_lama = $item->nilai_persediaan;
			$stok_lama  = $item->stok;
			$total_stok = $stok_lama + $qty;

			// ← Skenario A: kalau harga_modal = 0, average tidak berubah
			if (!empty($harga_beli) && floatval($harga_beli) > 0) {
				$nilai_baru   = floatval($qty) * floatval($harga_beli);
				$total_nilai  = $nilai_lama + $nilai_baru;
				$harga_modal_average = ($total_stok > 0) ? ($total_nilai / $total_stok) : 0;
			} else {
				// harga_beli = 0 → pakai nilai & harga_modal lama
				$total_nilai         = $nilai_lama;
				$harga_modal_average = $item->harga_modal;
			}

			$data = [
				'stok'              => $total_stok,
				'harga_modal'       => $harga_modal_average,
				'nilai_persediaan'  => $total_nilai,
				'updated_at'        => date('Y-m-d H:i:s')
			];
		} else {
			// Pengurangan Stok (Penjualan) - nanti untuk nota
			$nilai_berkurang = $qty * $item->harga_modal; // pakai harga_modal (average)

			$stok_baru = $item->stok - $qty;
			$nilai_baru = $item->nilai_persediaan - $nilai_berkurang;

			// Validasi stok tidak boleh negatif
			if ($stok_baru < 0) {
				log_message('error', 'update_stok_with_average: Stok tidak mencukupi');
				return false;
			}

			$data = [
				'stok' => $stok_baru,
				'nilai_persediaan' => max(0, $nilai_baru), // Jangan sampai nilai negatif
				'updated_at' => date('Y-m-d H:i:s')
			];
			// harga_modal tetap (tidak berubah saat penjualan)
		}

		$this->cb->where('id', $id_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		return $this->cb->update($this->table, $data);
	}

	// Update harga modal (last price)
	// public function update_harga_modal($id_item, $harga_modal)
	// {
	//     $data = [
	//         'harga_modal' => $harga_modal,
	//         'updated_at' => date('Y-m-d H:i:s')
	//     ];

	//     $this->cb->where('id', $id_item);
	//     $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
	//     $this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

	//     return $this->cb->update($this->table, $data);
	// }

	// Check if kode exists
	public function is_kode_exists($kode_item, $exclude_id = null)
	{
		$this->cb->where('kode_item', $kode_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($exclude_id) {
			$this->cb->where('id !=', $exclude_id);
		}

		$query = $this->cb->get($this->table);
		return $query->num_rows() > 0;
	}

	// Get items for dropdown (select2)
	public function get_for_select2($search = '')
	{
		$this->cb->select('id, kode_item, nama_item, satuan, harga_jual, harga_modal, stok, coa_persediaan');
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		if ($search != '') {
			$this->cb->group_start();
			$this->cb->like('nama_item', $search);
			$this->cb->or_like('kode_item', $search);
			$this->cb->group_end();
		}

		$this->cb->order_by('nama_item', 'ASC');

		if ($search == '') {
			$this->cb->limit(20);
		}
		
		return $this->cb->get($this->table)->result();
	}

	public function update_harga_jual($id_item, $harga_jual)
	{
		$data = [
			'harga_jual' => $harga_jual,
			'updated_at' => date('Y-m-d H:i:s')
		];

		$this->cb->where('id', $id_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));

		return $this->cb->update($this->table, $data);
	}

	public function kurangi_stok_invoice($id_item, $qty, $ref_id, $no_ref, $created_by)
	{
		$this->cb->where('id', $id_item);
		$this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
		$this->cb->where('id_company', $this->session->userdata('user_perusahaan_id'));
		$item = $this->cb->get($this->table)->row();

		if (!$item) return false;

		$stok_before  = $item->stok;
		$nilai_before = $item->nilai_persediaan;
		$harga_modal  = $item->harga_modal;

		$stok_baru  = $stok_before - $qty;
		$nilai_berkurang = $qty * $harga_modal;
		$nilai_baru = max(0, $nilai_before - $nilai_berkurang);

		if ($stok_baru < 0) {
			log_message('error', 'kurangi_stok_invoice: stok tidak mencukupi untuk item ' . $id_item);
			return false;
		}

		// Update stok & nilai_persediaan item
		$update = $this->cb->where('id', $id_item)
			->where('id_cabang', $this->session->userdata('kode_cabang'))
			->where('id_company', $this->session->userdata('user_perusahaan_id'))
			->update($this->table, [
				'stok'             => $stok_baru,
				'nilai_persediaan' => $nilai_baru,
				'updated_at'       => date('Y-m-d H:i:s'),
			]);

		if (!$update) return false;

		// Catat ke stok_log
		$log = [
			'id_item'            => $id_item,
			'qty_before'         => $stok_before,
			'qty_after'          => $stok_baru,
			'qty_change'         => -$qty,
			'nilai_before'       => $nilai_before,
			'nilai_after'        => $nilai_baru,
			'harga_modal_before' => $harga_modal,
			'harga_modal_after'  => $harga_modal,   // harga_modal tidak berubah saat penjualan
			'type'               => 'penjualan',
			'ref_table'          => 'invoice',
			'ref_id'             => $ref_id,
			'no_ref'             => $no_ref,
			'keterangan'         => 'Penjualan via invoice',
			'id_cabang'          => $this->session->userdata('kode_cabang'),
			'id_company'         => $this->session->userdata('user_perusahaan_id'),
			'created_by'         => $created_by,
			'created_at'         => date('Y-m-d H:i:s'),
		];

		return $this->cb->insert('stok_log', $log);
	}
}
