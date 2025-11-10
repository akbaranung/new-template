<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(['M_coa', 'M_user_access', 'M_perusahaans', 'M_report']);

        $this->cb = $this->load->database('corebank', TRUE);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {

        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $nip = $this->session->userdata('nip');

        $data['title'] = 'Cabang';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/report/s_report_list';
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages'] = 'pages/report/v_report_list';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function ajax_list()
    {
        $list = $this->M_report->get_datatables();
        $data = array();
        $crs = "";
        $no = $_POST['start'];
        // Date formatting
        $indonesian_months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        $now = (new DateTime())->format('Y-m-d H:i:s');

        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nama_perusahaan;
            $row[] = $cat->nama;
            $row[] = $cat->issueType;
            $row[] = $cat->issueDetails;

            $end_date_obj = new DateTime($cat->postdates);

            $formatted_end_date = strtr($end_date_obj->format('d F Y'), $indonesian_months);

            $row[] = $formatted_end_date;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_report->count_all(),
            "recordsFiltered" => $this->M_report->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
}
