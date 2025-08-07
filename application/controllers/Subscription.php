<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('isLogin') == FALSE) {
            $this->session->set_flashdata('error', 'Your session has expired');
            redirect('auth');
        } else if (!$this->session->userdata('nama_perusahaan')) {
            redirect('auth');
        }

        $this->load->model(['M_user_access', 'M_perusahaans']);

        $this->cb = $this->load->database('corebank', TRUE);
    }

    public function upgrade()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Perusahaan';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages'] = 'pages/subscription/v_upgrade';
        // $data['pages_script'] = 'script/perusahaan/s_perusahaan';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }
}
