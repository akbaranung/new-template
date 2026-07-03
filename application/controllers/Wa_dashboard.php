<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wa_dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('isLogin') == FALSE) {
            redirect('home');
        }
        $this->load->library('whatsapp');
    }

    public function index()
    {

        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $data['title'] = 'Wa Gateway Dashboard';
        $data['utility'] = $this->db->get('utility')->row_array();
        $data['pages_script'] = 'script/whatsapp/s_whatsapp';
        $data['pages'] = 'pages/whatsapp/v_dashboard';
        $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
        $data['status'] = $this->whatsapp->status();

        $this->load->view('index', $data);
    }

    public function status()
    {
        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $result = $this->whatsapp->status();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function qr()
    {
        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $result = $this->whatsapp->send_raw_get('/qr');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function logout()
    {
        if ($this->session->userdata('username') == "bariskode") {
            $has_access = TRUE;
        } else {
            $has_access = FALSE;
        }

        if (!$has_access) {
            show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
        }

        $result = $this->whatsapp->send_raw_post('/logout');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
}
