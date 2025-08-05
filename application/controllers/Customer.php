<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->library(['session', 'pagination']);
    $this->load->helper(['string', 'url', 'date']);
    $this->load->model('M_customer');

    $this->cb = $this->load->database('corebank', TRUE);

    if (!$this->session->userdata('nip')) {
      redirect('login');
    }
  }

  public function index()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search')) ? $this->session->userdata('search') : '');
    if ($keyword === null) $keyword = $this->session->userdata('search');
    else $this->session->set_userdata('search', $keyword);

    $cabang_now = $this->session->userdata('kode_cabang');

    $config = [
      'base_url' => site_url('customer'),
      'total_rows' => $this->M_customer->count($keyword, $cabang_now, 'customer'),
      'per_page' => 25,
      'uri_segment' => 3,
      'num_links' => 10,
      'use_page_numbers' => TRUE,
      'enable_query_strings' => TRUE,
      'page_query_string' => TRUE,
      'reuse_query_string' => TRUE,
      'query_string_segment' => 'page',
    ];

    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = "<i class='fe fe-chevrons-left'></i>";
    $config['last_link'] = "<i class='fe fe-chevrons-right'></i>";
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = "<i class='fe fe-chevron-left'></i>";
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = "<i class='fe fe-chevron-right'></i>";
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    $this->pagination->initialize($config);

    // $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;
    $page = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    // $invoices = $this->m_invoice->list_invoice($config["per_page"], $page, $keyword);
    $data['customers'] = $this->M_customer->list_customer_paginate($config["per_page"], $page, $keyword, $cabang_now);

    $data['page'] = $page;
    $data['title'] = "Customer";
    $data['pages'] = "pages/customer/v_list_customer";
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/customer/s_customer';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);


    // $this->load->view('customer', $data);
  }

  public function store()
  {
    $nama_customer = $this->input->post('nama_customer');
    $slug = url_title($nama_customer, 'dash', true);

    $data = [
      'nama_customer' => strtoupper($nama_customer),
      'alamat_customer' => $this->input->post('alamat_customer'),
      'telepon_customer' => $this->input->post('telepon_customer'),
      // 'status_customer' => $this->input->post('status_customer'),
      'slug' => $slug,
      'id_cabang' => $this->session->userdata('kode_cabang'),
    ];

    $old_slug = $this->uri->segment(3);

    if ($old_slug) {
      $this->M_customer->update($data, $old_slug);

      $this->session->set_flashdata('message_name', 'The customer has been successfully updated.');
    } else {
      if ($this->M_customer->is_available($slug)) {
        $this->session->set_flashdata('message_error', 'Customer ' . $nama_customer . ' sudah ada.');
      } else {
        $this->M_customer->insert($data);

        $this->session->set_flashdata('message_name', 'The customer has been successfully added.');
      }
    }

    redirect("customer");
  }

  public function edit($id)
  {
    $id = $this->uri->segment(4);

    $data = [
      'title' => 'Edit Category',
      'pages' => 'dashboard/pages/category/v_add_category',
      'category' => $this->M_Category->detail_category($id),
      'user' => $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array()
    ];

    $this->load->view('dashboard/index', $data);
  }

  public function reset()
  {
    $this->session->unset_userdata('search');
    redirect('customer');
  }
}
