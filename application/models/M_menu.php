<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_menu extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get_accessible_menus($user_id)
  {
    // Fetch menus that the user has access to
    $this->db->select('menus.*');
    $this->db->from('menus');
    $this->db->join('user_menu_access', 'menus.id = user_menu_access.menu_id');
    $this->db->where('user_menu_access.user_id', $user_id);
    $this->db->order_by('menus.sort_order', 'ASC');
    $query = $this->db->get();
    $menus = $query->result();

    $menu_tree = [];
    foreach ($menus as $menu) {
      if ($menu->parent_id == 0) {
        $menu->submenus = [];
        $menu_tree[$menu->Id] = $menu;
      } else {
        $menu_tree[$menu->parent_id]->submenus[] = $menu;
      }
    }

    return $menu_tree;
  }

  public function has_access()
  {
    $nip = $this->session->userdata('nip');
    $url = uri_string();
    $this->db->select('m.*');
    $this->db->from('menus m');
    $this->db->join('user_menu_access uma', 'm.id = uma.menu_id');
    $this->db->where('uma.user_id', $nip);
    $this->db->where('m.url', $url);
    return $this->db->get()->row(); // kembalikan baris jika ada akses
  }
}
