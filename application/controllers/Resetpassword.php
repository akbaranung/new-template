<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resetpassword extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(['M_coa', 'M_user_access', 'M_perusahaans', 'M_reset_password']);

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
        $data['pages_script'] = 'script/reset_password/s_reset_password_list';
        $data['user'] = $this->db->get_where('users', ['nip' => $nip])->row_array();
        $data['pages'] = 'pages/reset_password/v_reset_password_list';
        $data['menus'] = $this->M_menu->get_accessible_menus($nip);

        $this->load->view('index', $data);
    }

    public function ajax_list()
    {
        $list = $this->M_reset_password->get_datatables();
        $data = array();
        $crs = "";
        $no = $_POST['start'];
        // Date formatting

        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->nama_perusahaan;
            $row[] = $cat->nama_cabang;
            $row[] = $cat->nama;
            $row[] = $cat->nip;


            $row[] = '<button onclick="ResetPasswordUser(' . $cat->id . ')" class="btn btn-warning btn-di-td">
        Reset Password
      </button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_reset_password->count_all(),
            "recordsFiltered" => $this->M_reset_password->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function reset_password_user($id)
    {
        if ($this->session->userdata('nip') == 'bariskode') {
            $new_password = '12345';
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $data = [
                'password' => $hashed_password // Store the HASHED value
            ];

            $this->db->where('id', $id);
            $update_successful = $this->db->update('users', $data); // $update_successful will be TRUE or FALSE   
            if ($update_successful) {
                // Update was successful
                echo json_encode(array("status" => TRUE));
            } else {
                // Update failed (e.g., database connection error, query error)
                echo json_encode(array("status" => FALSE, "message" => "Database update failed."));
            }
        } else {
            echo json_encode(array("status" => FALSE, "message" => "NO ACCESS."));
        }
    }

    public function resetalluserpassword_progress()
    {
        // 1. Security Check: Ensure only 'bariskode' can run this (assuming 'bariskode' is the super-admin)
        if ($this->session->userdata('username') !== 'bariskode') {
            echo json_encode(array("status" => FALSE, "message" => "NO ACCESS: Insufficient permissions."));
            exit();
        }

        // 2. Prepare the secure new password
        $new_password = '12345';
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $data = [
            'password' => $hashed_password // Store the HASHED value
        ];

        // --- CRITICAL FIX 1: Fetching ALL users to update ---
        // Use get()->result() to get an array of all users, not row()
        // CRITICAL FIX 2: The WHERE clause for exclusion
        // We use where('nip !=', 'bariskode') to exclude the specified NIP.

        $this->db->select('id'); // Only select the IDs, as that's all we need
        $this->db->from('users'); // Assuming your user table is 'users', consistent with the update call
        $this->db->where('nip !=', 'bariskode');
        $users_to_update = $this->db->get()->result(); // Use result() to get multiple rows (an array of objects)

        // Variable to track the total number of updates and success count
        $total_users = count($users_to_update);
        $successful_updates = 0;

        // --- CRITICAL FIX 3: Loop and Update ---
        // Note: It is far more efficient to use a single SQL UPDATE query (see alternative below)
        foreach ($users_to_update as $user) {
            $this->db->where('id', $user->id);
            $update_successful = $this->db->update('users', $data);

            if ($update_successful) {
                $successful_updates++;
            }
            // IMPORTANT: DO NOT echo json_encode inside the loop! 
            // This would break the AJAX response after the first iteration.
        }

        // 3. Final Single JSON Response
        if ($successful_updates === $total_users && $total_users > 0) {
            // All updates succeeded
            echo json_encode(array("status" => TRUE, "message" => "Successfully reset passwords for all $total_users users."));
        } else if ($total_users === 0) {
            // No users found to update
            echo json_encode(array("status" => TRUE, "message" => "No users found to update (excluding 'bariskode')."));
        } else {
            // Some or all updates failed (e.g., $successful_updates < $total_users)
            $failed_count = $total_users - $successful_updates;
            echo json_encode(array("status" => FALSE, "message" => "Warning: $successful_updates out of $total_users passwords reset. $failed_count updates failed."));
        }
    }
}
