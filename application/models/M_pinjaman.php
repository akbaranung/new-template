<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pinjaman extends CI_Model
{

	// Generate nomor pengajuan otomatis
	public function generate_no_pengajuan()
	{
		$prefix = 'PJ/' . date('Ym') . '/';

		// Ambil nomor terakhir bulan ini
		$this->cb->select('no_pengajuan');
		$this->cb->from('pengajuan_pinjaman');
		$this->cb->like('no_pengajuan', $prefix, 'after');
		$this->cb->order_by('id', 'DESC');
		$this->cb->limit(1);
		$query = $this->cb->get();

		if ($query->num_rows() > 0) {
			$last_no = $query->row()->no_pengajuan;
			$last_seq = (int) substr($last_no, -4);
			$new_seq = $last_seq + 1;
		} else {
			$new_seq = 1;
		}

		return $prefix . str_pad($new_seq, 4, '0', STR_PAD_LEFT);
	}

	// Insert pengajuan pinjaman
	public function insert_pengajuan($data)
	{
		$this->cb->insert('pengajuan_pinjaman', $data);
		return $this->cb->insert_id();
	}

	// Insert detail angsuran (jadwal cicilan)
	public function insert_detail_angsuran($id_pengajuan, $detail, $tanggal_dropping)
	{
		$tanggal = new DateTime($tanggal_dropping);

		foreach ($detail as $item) {
			// Tambah 1 bulan untuk jatuh tempo
			$tanggal->modify('+1 month');

			$data = [
				'id_pengajuan' => $id_pengajuan,
				'angsuran_ke' => $item['angsuran_ke'],
				'tanggal_jatuh_tempo' => $tanggal->format('Y-m-d'),
				'angsuran' => $item['angsuran'],
				'pokok' => $item['pokok'],
				'bunga' => $item['bunga'],
				'sisa_pinjaman' => $item['sisa'],
				'status_bayar' => 'belum'
			];

			$this->cb->insert('detail_angsuran', $data);
		}

		return true;
	}

	// Get all pengajuan
	public function get_all_pengajuan()
	{
		$this->cb->select('p.*, n.no_cib, n.nama as nama_nasabah, n.no_ktp');
		$this->cb->from('pengajuan_pinjaman p');
		$this->cb->join('t_nasabah n', 'p.id_nasabah = n.no_cib', 'left');
		$this->cb->order_by('p.created_at', 'DESC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Get pengajuan by ID
	public function get_pengajuan_by_id($id)
	{
		$this->cb->select('p.*, n.no_cib, n.nama as nama_nasabah, n.no_ktp, n.alamat, n.no_telp');
		$this->cb->from('pengajuan_pinjaman p');
		$this->cb->join('t_nasabah n', 'p.id_nasabah = n.no_cib', 'left');
		$this->cb->where('p.id', $id);
		$query = $this->cb->get();

		return $query->row();
	}

	// Get detail angsuran
	public function get_detail_angsuran($id_pengajuan)
	{
		$this->cb->from('detail_angsuran');
		$this->cb->where('id_pengajuan', $id_pengajuan);
		$this->cb->order_by('angsuran_ke', 'ASC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Get rekening by nasabah
	public function get_rekening_by_nasabah($id_nasabah)
	{
		$this->cb->from('t_tabungan');
		$this->cb->where('no_cib', $id_nasabah);
		$this->cb->where('status_tabungan', 'Aktif');
		$this->cb->order_by('created', 'DESC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Update status pengajuan
	public function update_pengajuan($id, $data)
	{
		$this->cb->where('id', $id);
		return $this->cb->update('pengajuan_pinjaman', $data);
	}

	// Process pencairan (approval + update saldo)
	public function process_pencairan($id_pengajuan, $data_pencairan)
	{
		// Start transaction
		$this->cb->trans_start();

		// 1. Insert data pencairan
		$this->cb->insert('pencairan_pinjaman', $data_pencairan);

		// 2. Update status pengajuan jadi 'disbursed'
		$this->cb->where('id', $id_pengajuan);
		$this->cb->update('pengajuan_pinjaman', [
			'status' => 'disbursed'
		]);

		// 3. Get data pengajuan untuk jumlah pinjaman
		$this->cb->select('jumlah_pinjaman');
		$this->cb->from('pengajuan_pinjaman');
		$this->cb->where('id', $id_pengajuan);
		$pengajuan = $this->cb->get()->row();

		// 4. Update saldo rekening nasabah (tambah)
		$this->cb->set('saldo', 'saldo + ' . $pengajuan->jumlah_pinjaman, FALSE);
		$this->cb->where('no_rekening', $data_pencairan['no_rekening_tujuan']);
		$this->cb->update('rekening_nasabah');

		// 5. Insert ke jurnal (optional - tergantung kebutuhan)
		// Kamu bisa tambahkan insert ke tabel jurnal di sini

		// Complete transaction
		$this->cb->trans_complete();

		return $this->cb->trans_status();
	}

	// Get pengajuan by status
	public function get_pengajuan_by_status($status)
	{
		$this->cb->select('p.*, n.no_nasabah, n.nama as nama_nasabah, n.no_ktp');
		$this->cb->from('pengajuan_pinjaman p');
		$this->cb->join('nasabah n', 'p.id_nasabah = n.no_cib', 'left');
		$this->cb->where('p.status', $status);
		$this->cb->order_by('p.created_at', 'DESC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Get total pinjaman by nasabah
	public function get_total_pinjaman_nasabah($id_nasabah)
	{
		$this->cb->select_sum('jumlah_pinjaman');
		$this->cb->from('pengajuan_pinjaman');
		$this->cb->where('id_nasabah', $id_nasabah);
		$this->cb->where_in('status', ['approved', 'disbursed']);
		$query = $this->cb->get();

		return $query->row()->jumlah_pinjaman ?? 0;
	}

	// Get pinjaman aktif by nasabah
	public function get_pinjaman_aktif_nasabah($id_nasabah)
	{
		$this->cb->select('p.*, COUNT(da.id) as total_angsuran, 
                          SUM(CASE WHEN da.status_bayar = "lunas" THEN 1 ELSE 0 END) as sudah_bayar');
		$this->cb->from('pengajuan_pinjaman p');
		$this->cb->join('detail_angsuran da', 'p.id = da.id_pengajuan', 'left');
		$this->cb->where('p.id_nasabah', $id_nasabah);
		$this->cb->where('p.status', 'disbursed');
		$this->cb->group_by('p.id');
		$this->cb->having('sudah_bayar < total_angsuran');
		$query = $this->cb->get();

		return $query->result();
	}

	// Check apakah nasabah punya pinjaman aktif
	public function has_active_loan($id_nasabah)
	{
		$this->cb->select('p.id');
		$this->cb->from('pengajuan_pinjaman p');
		$this->cb->join('detail_angsuran da', 'p.id = da.id_pengajuan');
		$this->cb->where('p.id_nasabah', $id_nasabah);
		$this->cb->where('p.status', 'disbursed');
		$this->cb->where('da.status_bayar !=', 'lunas');
		$this->cb->limit(1);
		$query = $this->cb->get();

		return $query->num_rows() > 0;
	}

	// Get angsuran jatuh tempo
	public function get_angsuran_jatuh_tempo($tanggal = null)
	{
		if (!$tanggal) {
			$tanggal = date('Y-m-d');
		}

		$this->cb->select('da.*, p.no_pengajuan, p.id_nasabah, n.no_nasabah, n.nama as nama_nasabah');
		$this->cb->from('detail_angsuran da');
		$this->cb->join('pengajuan_pinjaman p', 'da.id_pengajuan = p.id');
		$this->cb->join('nasabah n', 'p.id_nasabah = n.no_cib');
		$this->cb->where('da.tanggal_jatuh_tempo <=', $tanggal);
		$this->cb->where('da.status_bayar', 'belum');
		$this->cb->order_by('da.tanggal_jatuh_tempo', 'ASC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Get statistik dashboard
	public function get_dashboard_stats()
	{
		$stats = [];

		// Total pengajuan pending
		$this->cb->where('status', 'pending');
		$stats['pending'] = $this->cb->count_all_results('pengajuan_pinjaman');

		// Total pinjaman dicairkan
		$this->cb->where('status', 'disbursed');
		$stats['disbursed'] = $this->cb->count_all_results('pengajuan_pinjaman');

		// Total nilai pinjaman aktif
		$this->cb->select_sum('jumlah_pinjaman');
		$this->cb->where('status', 'disbursed');
		$query = $this->cb->get('pengajuan_pinjaman');
		$stats['total_pinjaman'] = $query->row()->jumlah_pinjaman ?? 0;

		// Total angsuran tertunggak
		$this->cb->where('status_bayar', 'belum');
		$this->cb->where('tanggal_jatuh_tempo <', date('Y-m-d'));
		$stats['tunggakan'] = $this->cb->count_all_results('detail_angsuran');

		return $stats;
	}

	// Delete pengajuan (only if status pending)
	public function delete_pengajuan($id)
	{
		// Check status
		$this->cb->select('status');
		$this->cb->where('id', $id);
		$pengajuan = $this->cb->get('pengajuan_pinjaman')->row();

		if (!$pengajuan || $pengajuan->status != 'pending') {
			return false;
		}

		// Start transaction
		$this->cb->trans_start();

		// Delete detail angsuran
		$this->cb->where('id_pengajuan', $id);
		$this->cb->delete('detail_angsuran');

		// Delete pengajuan
		$this->cb->where('id', $id);
		$this->cb->delete('pengajuan_pinjaman');

		$this->cb->trans_complete();

		return $this->cb->trans_status();
	}
}
