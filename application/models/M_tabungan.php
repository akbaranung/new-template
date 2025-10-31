<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


class M_tabungan extends CI_Model
{
    protected $cb;

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        $this->cb = $this->load->database('corebank', TRUE);
    }

    var $table = 't_tabungan';
    var $column_order = array('no_tabungan', 'nama', 'nama_tabungan', 'status_tabungan', 'no_urut', 'nominal', 'spread_rate', 'nominal_blokir', 'pos_rate', 'nolsp'); //set column field database for datatable orderable
    var $column_search = array('no_tabungan', 'nama', 'nama_tabungan', 'status_tabungan', 'no_urut', 'nominal', 'spread_rate', 'nominal_blokir', 'pos_rate', 'nolsp'); //set column field database for datatable searchable 
    var $order = array('no_urut' => 'desc'); // default order 

    function _get_datatables_query($nasabah = null)
    {

        $this->cb->select('t_tabungan.*, t_nasabah.nama, t_jenistabungan.nama_tabungan');
        $this->cb->from('t_tabungan');
        $this->cb->join('t_nasabah', 't_tabungan.no_cib = t_nasabah.no_cib');
        $this->cb->join('t_jenistabungan', 't_tabungan.jenis_tabungan = t_jenistabungan.kode_tabungan');
        if ($nasabah == null) {
            $this->cb->where('t_tabungan.no_cib', $nasabah);
        }
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

    function get_datatables($nasabah)
    {
        $this->_get_datatables_query($nasabah);
        if ($_POST['length'] != -1)
            $this->cb->limit($_POST['length'], $_POST['start']);
        $query = $this->cb->get();
        return $query->result();
    }

    function count_filtered($nasabah)
    {
        $this->_get_datatables_query($nasabah);
        $query = $this->cb->get();
        return $query->num_rows();
    }

    function count_all()
    {

        $this->_get_datatables_query();
        $query = $this->cb->get();

        return $this->cb->count_all_results();
    }
    function get_nasabah()
    {

        $this->cb->select('*');
        $this->cb->from('t_nasabah');
        $query = $this->cb->get();

        return $query->result();
    }

    function get_jenis_tabungan()
    {

        $this->cb->select('*');
        $this->cb->from('t_jenistabungan');
        $query = $this->cb->get();

        return $query->result();
    }

    public function generate_next_no_tabungan()
    {
        $this->cb->trans_start();

        // 1. Get the maximum existing number (PURELY NUMERIC)
        $sql = "
        SELECT 
            MAX(no_tabungan) as latest_number -- Find MAX directly
        FROM 
            t_tabungan
        FOR UPDATE
    ";

        // Execute the query
        $query = $this->cb->query($sql);
        $row = $query->row();

        // 2. Calculate the new number
        // Handle NULL or 0 from an empty table
        $latest_num = (int)$row->latest_number;

        if ($latest_num === 0) {
            $new_num = 1;
        } else {
            $new_num = $latest_num + 1; // Now this should correctly increment
        }

        // 3. Set the new no_tabungan
        $new_no_tabungan = $new_num;

        $this->cb->trans_complete();

        if ($this->cb->trans_status() === FALSE) {
            return NULL;
        }

        return $new_no_tabungan;
    }

    public function generate_next_no_urut()
    {
        // Start the transaction
        $this->cb->trans_start();

        // 1. Get the maximum existing number (using lock for safety)

        // NOTE: Adjust the '4' if your number starts at a different position (e.g., 'TB-001' -> number starts at position 4)
        $num_start_position = 4;

        $sql = "
            SELECT 
                MAX(CAST(SUBSTRING(no_tabungan, ?) AS UNSIGNED)) as latest_number
            FROM 
                t_tabungan
            FOR UPDATE  -- This locks the table row/index until the transaction is complete
        ";

        // Use query binding to prevent SQL injection for the position number
        $query = $this->cb->query($sql, array($num_start_position));
        $row = $query->row();

        // 2. Calculate the new number
        $latest_num = (int)$row->latest_number; // Cast to integer for safety

        if ($latest_num === 0) {
            $new_num = 1; // Start at 1 if no records exist
        } else {
            $new_num = $latest_num + 1; // Increment
        }

        // 3. Format the new no_tabungan
        $prefix = ''; // Adjust your prefix here
        $num_length = 5; // Adjust the desired padding length (e.g., 001, 010, 100)

        $new_no_tabungan = $prefix . str_pad($new_num, $num_length, '0', STR_PAD_LEFT);

        // Commit the transaction (this releases the lock)
        $this->cb->trans_complete();

        // Check if the transaction was successful
        if ($this->cb->trans_status() === FALSE) {
            // Handle error, maybe return NULL or throw an exception
            return NULL;
        }

        return $new_no_tabungan;
    }

    function get_tabungan($id)
    {

        $this->cb->select('*');
        $this->cb->from('t_tabungan');
        $this->cb->where('no_cib', $id);
        $query = $this->cb->get();

        return $query->row();
    }
}
