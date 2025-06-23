<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once('PHPExcel.php');

class UserAccess extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('M_user_access'); // We'll create this model
    $this->load->model('M_login');
  }

  // public function check_access()
  // {
  //   $nip = $this->session->userdata('nip');
  //   $url = $this->uri->segment(1); // sesuaikan dengan struktur URL kamu
  //   $has_access = $this->M_menu->has_access($nip, $url);

  //   if (!$has_access) {
  //     // show_error('Unauthorized access. You do not have permission to access this page.', 403, 'Access Denied');
  //     // atau redirect ke view khusus:
  //     redirect('errors/unauthorized');
  //   }
  // }

  // public function edit_access($user_id)
  // {
  //   // Get user details
  //   $data['user'] = $this->M_user_access->get_user_by_id($user_id); // Assuming user_id is the NIP or actual user ID
  //   if (empty($data['user'])) {
  //     show_404(); // User not found
  //   }

  //   // Get all available menus
  //   $data['all_menus'] = $this->M_user_access->get_all_menus();

  //   // Get the current menu access for this user
  //   $current_access = $this->M_user_access->get_user_access($user_id);
  //   $data['user_menu_ids'] = [];
  //   if (!empty($current_access) && !empty($current_access->menu_id)) {
  //     // Convert comma-separated string to an array of integers
  //     $data['user_menu_ids'] = array_map('intval', explode(',', $current_access->menu_id));
  //   }

  //   $data['title'] = 'Edit Menu Access for ' . $data['user']->nama; // Adjust 'nama' to your user table's name field

  //   $this->load->view('templates/header', $data);
  //   $this->load->view('templates/sidebar');
  //   $this->load->view('user_access/edit_access', $data); // This view will have the checkboxes
  //   $this->load->view('templates/footer');
  // }

  // public function save_access()
  // {
  //   $this->form_validation->set_rules('user_id', 'User ID', 'required|trim');
  //   // The menu_ids are optional, if no menus are selected, it means no access
  //   $this->form_validation->set_rules('menu_ids[]', 'Menu Access', 'callback_valid_menu_ids');

  //   if ($this->form_validation->run() == FALSE) {
  //     // If validation fails, reload the edit form
  //     $user_id = $this->input->post('user_id');
  //     // Flash data for error messages (or handle via AJAX for a more modern approach)
  //     $this->session->set_flashdata('error', validation_errors());
  //     redirect('user_access/edit_access/' . $user_id);
  //   } else {
  //     $user_id = $this->input->post('user_id');
  //     $nip = $this->input->post('nip');
  //     $selected_menu_ids = $this->input->post('menu_ids'); // This will be an array of selected menu IDs

  //     if (empty($selected_menu_ids)) {
  //       $menu_id_string = ''; // No access
  //     } else {
  //       // Ensure unique IDs and convert to comma-separated string
  //       $menu_id_string = implode(',', array_unique($selected_menu_ids));
  //     }

  //     // Save the access
  //     if ($this->M_user_access->save_user_access($nip, $menu_id_string)) {
  //       $this->session->set_flashdata('success', 'User menu access updated successfully!');
  //     } else {
  //       $this->session->set_flashdata('error', 'Failed to update user menu access. Please try again.');
  //     }
  //     redirect('user_access'); // Redirect back to the user list
  //   }
  // }

  // // Custom validation callback to ensure menu IDs are integers
  // public function valid_menu_ids($str)
  // {
  //   if (is_array($str)) {
  //     foreach ($str as $id) {
  //       if (!is_numeric($id) || $id <= 0) {
  //         $this->form_validation->set_message('valid_menu_ids', 'Invalid menu ID detected.');
  //         return FALSE;
  //       }
  //     }
  //   }
  //   return TRUE;
  // }

  // // Optional: Delete Access (if you want to provide a specific delete button)
  // public function delete_access($user_id)
  // {
  //   if ($this->M_user_access->save_user_access($user_id, '')) { // Set menu_id to empty string
  //     $this->session->set_flashdata('success', 'User menu access revoked.');
  //   } else {
  //     $this->session->set_flashdata('error', 'Failed to revoke user menu access.');
  //   }
  //   redirect('user_access');
  // }
}
