<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class M_reset_password extends CI_Model
{
    protected $cb;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        $this->cb = $this->load->database('corebank', TRUE);
    }

    var $table = 'users';
    var $column_order = array('users.id', 'nama_perusahaan', 'nama_cabang', 'nama', 'nip'); //set column field database for datatable orderable
    var $column_search = array('users.id', 'nama_perusahaan', 'nama_cabang', 'nama', 'nip'); //set column field database for datatable searchable
    var $order = array('users.id' => 'desc'); // default order

    function _get_datatables_query()
    {

        $this->db->select('users.*, utility.nama_perusahaan, t_cabang.nama_cabang');
        $this->db->from('users');
        $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
        $this->db->join('utility', 'utility.Id = t_cabang.id_perusahaan');
        $this->db->where('nip !=', 'bariskode');
        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            // $this->db->order_by(key($order), $order[key($order)]);
            foreach ($order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {

        $this->_get_datatables_query();
        $query = $this->db->get();

        return $this->db->count_all_results();
    }
}
