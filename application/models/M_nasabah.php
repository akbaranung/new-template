<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class M_nasabah extends CI_Model
{
	protected $cb;

	public function __construct()
	{
		parent::__construct(); // Call the parent constructor
		$this->cb = $this->load->database('corebank', TRUE);
	}

	var $table = 't_nasabah';
	var $column_order = array('no_cib', 'nama', 'alamat', 'no_ktp', 'no_telp', 'ahli_waris', 'kode_pos', 'nama_ibu_kandung', 'pekerjaan', 'kode_ao', 'nama_panggilan', 'tgl_lahir', 'tempat_lahir', 'kota', 'tgl_pendaftaran', 'tipe_nasabah', 'nama_segmen', 'warga_negara'); //set column field database for datatable orderable
	var $column_search = array('no_cib', 'nama', 'alamat', 'no_ktp', 'no_telp', 'ahli_waris', 'kode_pos', 'nama_ibu_kandung', 'pekerjaan', 'kode_ao', 'nama_panggilan', 'tgl_lahir', 'tempat_lahir', 'kota', 'tgl_pendaftaran', 'tipe_nasabah', 'nama_segmen', 'warga_negara'); //set column field database for datatable searchable 
	var $order = array('no_cib' => 'desc'); // default order 

	function _get_datatables_query()
	{

		$this->cb->select('*');
		$this->cb->from('t_nasabah');
		$this->cb->join('t_segnasabah', 't_nasabah.segmen_nasabah = t_segnasabah.kode_segmen');
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if ($_POST['search']['value']) // if datatable send POST for search
			{

				if ($i === 0) // first loop
				{
					$this->cb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->cb->like($item, $_POST['search']['value']);
				} else {
					$this->cb->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i) //last loop
					$this->cb->group_end(); //close bracket
			}
			$i++;
		}

		if (isset($_POST['order'])) // here order processing
		{
			$this->cb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			// $this->cb->order_by(key($order), $order[key($order)]);
			foreach ($order as $key => $value) {
				$this->cb->order_by($key, $value);
			}
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if ($_POST['length'] != -1)
			$this->cb->limit($_POST['length'], $_POST['start']);
		$query = $this->cb->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->cb->get();
		return $query->num_rows();
	}

	function count_all()
	{

		$this->_get_datatables_query();
		$query = $this->cb->get();

		return $this->cb->count_all_results();
	}
	function get_segnasabah()
	{

		$this->cb->select('*');
		$this->cb->from('t_segnasabah');
		$query = $this->cb->get();

		return $query->result();
	}

	function get_tipe()
	{

		$this->cb->select('*');
		$this->cb->from('t_tipenasabah');
		$query = $this->cb->get();

		return $query->result();
	}

	function get_nasabah($id)
	{

		$this->cb->select('*');
		$this->cb->from('t_nasabah');
		$this->cb->where('no_cib', $id);
		$query = $this->cb->get();

		return $query->row();
	}


	// Get all active nasabah
	public function get_all_active()
	{
		$this->cb->from('t_nasabah');
		// $this->cb->where('status', 'Aktif'); // Asumsi ada field status
		$this->cb->order_by('nama', 'ASC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Get nasabah by ID
	public function get_by_id($no_cib)
	{
		$this->cb->from('t_nasabah');
		$this->cb->where('no_cib', $no_cib);
		$query = $this->cb->get();

		return $query->row();
	}

	// Get nasabah with rekening count
	public function get_with_rekening_count()
	{
		$this->cb->select('n.*, COUNT(r.id) as total_rekening');
		$this->cb->from('t_nasabah n');
		$this->cb->join('rekening_nasabah r', 'n.id = r.id_nasabah AND r.status = "Aktif"', 'left');
		$this->cb->group_by('n.id');
		$this->cb->order_by('n.nama', 'ASC');
		$query = $this->cb->get();

		return $query->result();
	}

	// Search nasabah
	public function search($keyword)
	{
		$this->cb->from('t_nasabah');
		$this->cb->group_start();
		$this->cb->like('no_nasabah', $keyword);
		$this->cb->or_like('nama', $keyword);
		$this->cb->or_like('nik', $keyword);
		$this->cb->or_like('no_hp', $keyword);
		$this->cb->group_end();
		$this->cb->where('status', 'Aktif');
		$this->cb->order_by('nama', 'ASC');
		$query = $this->cb->get();

		return $query->result();
	}
}
