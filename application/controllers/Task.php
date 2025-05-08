<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends CI_Controller
{

  public function __construct()
  {

    parent::__construct();
    $this->load->model(['M_task']);

    if ($this->session->userdata('isLogin') == FALSE) {
      redirect('home');
    }

    date_default_timezone_set('Asia/Jakarta');
  }

  public function index()
  {
    $has_access = $this->M_menu->has_access();

    if (!$has_access) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    $keyword = htmlspecialchars($this->input->get('search') ?? '', ENT_QUOTES, 'UTF-8');
    //pagination settings
    $config['base_url'] = site_url('app/inbox');
    $config['total_rows'] = $this->M_task->task_count($this->session->userdata('nip'), $keyword);
    $config['per_page'] = "20";
    $config["uri_segment"] = 3;
    $config["num_links"] = 10;
    $config['enable_query_strings'] = TRUE;
    $config['page_query_string'] = TRUE;
    $config['use_page_numbers'] = TRUE;
    $config['reuse_query_string'] = TRUE;
    $config['query_string_segment'] = 'page';

    // integrate bootstrap pagination
    $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
    $config['full_tag_close'] = '</ul>';
    $config['first_link'] = true;
    $config['last_link'] = true;
    $config['first_tag_open'] = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['prev_link'] = 'Previous';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = 'Next';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');

    // initialize pagination
    $this->pagination->initialize($config);
    $data['page'] = ($this->input->get('page')) ? (($this->input->get('page') - 1) * $config['per_page']) : 0;
    $data['data_task'] = $this->M_task->task_get($config["per_page"], $data['page'], $this->session->userdata('nip'), $keyword);
    $data['pagination'] = $this->pagination->create_links();

    $data['title'] = 'Task List';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/task/s_task';
    $data['pages'] = 'pages/tello/v_task';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $this->load->view('index', $data);
  }

  public function task_view($id = null)
  {
    $data['task'] = $this->M_task->task_get_detail($id);
    $cek_detail = $this->db->select('id_detail')->from('task_detail')->where('id_task', $id)->get()->num_rows();

    if (empty($data['task'])) {
      show_error('Unauthorize Privilage!', 401, 'Access Denied');
    }

    if ($cek_detail == true) {
      $this->db->select('*');
      $this->db->from('users as a');
      $this->db->where('b.id_task', $id);
      $this->db->join('task_detail as b', 'a.nip = b.responsible');
      $this->db->order_by('activity', 'ASC');
      $this->db->order_by('date_created', 'DESC');
      $data['task_detail'] = $this->db->get()->result();

      $data['title'] = 'Card List';
      $data['utility'] = $this->db->get('utility')->row_array();
      $data['pages_script'] = 'script/task/s_task';
      $data['pages'] = 'pages/tello/v_task_card';
      $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
      $this->load->view('index', $data);
    } else {
      $this->session->set_flashdata('warning', 'Card/detail task belum tersedia! Buat terlebih dahulu!');
      redirect('task/detail_task/' . $id);
    }
  }

  public function save_task()
  {
    $task_name = $this->input->post('judul');
    $member = $this->input->post('member[]');
    $activity = $this->input->post('activity');
    $description = $this->input->post('description');

    // Set rules form validation
    $this->form_validation->set_rules('judul', 'Task name', 'required|trim', array('required' => '%s wajib diisi!'));
    $this->form_validation->set_rules('member[]', 'Member', 'required', array('required' => '%s wajib dipilih!'));

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $member_task = '';
      $i = 0;
      foreach ($member as $m) {
        $member_task .= $m . ';';
        $get_member[] = $this->db->get_where('users', ['nip' => $m])->row_array();
        $phone_member[] = $get_member[$i]['phone'];
      }

      $save_task = [
        'name' => $task_name,
        'member' => $member_task,
        'activity' => $activity,
        'comment' => $description,
        'pic' => $this->session->userdata('nip')
      ];

      $this->db->insert('task', $save_task);
      $last_id = $this->db->insert_id();

      // Send wa
      $nama_session = $this->session->userdata('nama');
      $msg = "There's a new task\nTask Name : *$task_name*\n\nCreated By :  *$nama_session*";

      $send_wa = implode(',', $phone_member);
      $this->api_whatsapp->wa_notif($msg, $send_wa);

      $response = [
        'success' => true,
        'msg' => 'Task berhasil dibuat! selanjutnya buat card atau detail task!',
        'reload' => site_url('task/detail_task/' . $last_id)
      ];
    }

    echo json_encode($response);
  }

  public function edit_task($id)
  {
    $data['task'] = $this->db->select('*')->from('task')->where('id', $id)->get()->row();

    if (empty($data['task'])) {
      show_error('Unauthorize Privilage!', 401, 'Access Denied');
    }

    $data['member'] = explode(';', $data['task']->member);
    $data['sendto'] = $this->M_task->sendto($this->session->userdata('level_jabatan'), $this->session->userdata('bagian'));

    $data['title'] = 'Edit Task';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/task/s_task';
    $data['pages'] = 'pages/tello/v_task_edit';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));
    $this->load->view('index', $data);
  }

  public function detail_task($id)
  {
    $data['title'] = 'Create Card Task';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/task/s_task';
    $data['pages'] = 'pages/tello/v_create_card';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $get_task = $this->db->select('member')->from('task')->where('Id', $id)->get()->row_array();
    $nip_member = explode(';', $get_task['member']);

    $this->db->where_in('nip', $nip_member);
    $data['member'] = $this->db->get('users')->result();

    $this->load->view('index', $data);
  }

  public function save_task_detail($id = null)
  {
    $task = $this->db->get_where('task', ['id' => $id])->row_array();

    $card_name = $this->input->post('judul');
    $responsible = $this->input->post('responsible');
    $description = $this->input->post('description');
    $start = $this->input->post('start');
    $end = $this->input->post('end');
    $activity = $this->input->post('activity');

    $files = $_FILES['attach']['name'];

    $this->form_validation->set_rules('judul', 'Card name', 'required|trim', array('required' => '%s wajib diisi!'));
    $this->form_validation->set_rules('responsible', 'Responsible', 'required', array('required' => '%s wajib dipilih!'));
    $this->form_validation->set_rules('start', 'Start date', 'required', array('required' => '%s wajib diisi!'));
    $this->form_validation->set_rules('end', 'End date', 'required', array('required' => '%s wajib diisi!'));
    $this->form_validation->set_rules('activity', 'Activity', 'required', array('required' => '%s wajib dipilih!'));

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      // Menghitung file yang diupload
      $filesCount = count($files);
      $uploadedFiles = [];
      $errors = [];
      $uploadedFileName = [];

      $hasFile = false;
      for ($i = 0; $i < $filesCount; $i++) {
        if ($files[$i] != '') {
          $hasFile = true;
          break;
        }
      }

      if ($hasFile) {
        $totalSize = 0;
        for ($i = 0; $i < $filesCount; $i++) {
          $totalSize += $_FILES['attach']['size'][$i];
        }

        if ($totalSize > 15 * 1024 * 1024) {
          $response = [
            'success' => FALSE,
            'msg' => 'toal ukuran file tidak boleh lebih dari 15MB.'
          ];
          return;
        }

        for ($i = 0; $i < $filesCount; $i++) {
          $_FILES['file']['name']     = $_FILES['attach']['name'][$i];
          $_FILES['file']['type']     = $_FILES['attach']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['attach']['tmp_name'][$i];
          $_FILES['file']['error']    = $_FILES['attach']['error'][$i];
          $_FILES['file']['size']     = $_FILES['attach']['size'][$i];

          $config['upload_path']   = './uploads/task_comment';
          $config['allowed_types'] = '*';
          $config['max_size']      = 2048;
          $config['encrypt_name']  = TRUE;

          $this->upload->initialize($config);

          if ($this->upload->do_upload('file')) {
            $uploadData = $this->upload->data();
            $uploadedFiles[] = $uploadData['full_path'];
            $uploadedFileName[] = $uploadData['file_name'];
          } else {
            $errors[] = $this->upload->display_errors();
            break; // Stop dan batal jika ada gagal
          }
        }

        // Hapus file jika gagal
        if (!empty($errors)) {
          foreach ($uploadedFiles as $filePath) {
            if (file_exists($filePath)) {
              unlink($filePath);
            }
          }
          $response = [
            'success' => FALSE,
            'msg' => 'Error : ' . $errors[0]
          ];
          echo json_encode($response);
          return;
        } else {
          $attach = implode(';', $uploadedFileName);
          $insert = [
            "id_task" => $id,
            "task_name" => $card_name,
            "responsible" => $responsible,
            "description" => $description,
            "start_date" => $start,
            "due_date" => $end,
            "activity" => $activity,
            "attachment" => $attach,
            "read" => 0,
          ];

          $this->db->insert('task_detail', $insert);

          //Send notif wa
          $nama_session = $this->session->userdata('nama');
          $user = $this->db->select('phone')->from('users')->where('nip', $responsible)->get()->row_array();
          $msg = "There's a new card\nTask Name:*$task[name]*\nCard Name : *$card_name*\n\nCreated By :  *$nama_session*";

          $this->api_whatsapp->wa_notif($msg, $user['phone']);

          $response = [
            'success' => true,
            'msg' => 'Card Berhasil Dibuat!'
          ];
        }
      } else {
        $insert = [
          "id_task" => $id,
          "task_name" => $card_name,
          "responsible" => $responsible,
          "description" => $description,
          "start_date" => $start,
          "due_date" => $end,
          "activity" => $activity,
          "read" => 0,
        ];

        $this->db->insert('task_detail', $insert);

        //Send notif wa
        $nama_session = $this->session->userdata('nama');
        $user = $this->db->select('phone')->from('users')->where('nip', $responsible)->get()->row_array();
        $msg = "There's a new card\nTask Name:*$task[name]*\nCard Name : *$card_name*\n\nCreated By :  *$nama_session*";

        $this->api_whatsapp->wa_notif($msg, $user['phone']);

        $response = [
          'success' => true,
          'msg' => 'Card Berhasil Dibuat!'
        ];
      }
    }

    echo json_encode($response);
  }

  public function card_view($id = null)
  {
    $data['title'] = 'Card Detail';
    $data['utility'] = $this->db->get('utility')->row_array();
    $data['pages_script'] = 'script/task/s_task';
    $data['pages'] = 'pages/tello/v_card_detail';
    $data['menus'] = $this->M_menu->get_accessible_menus($this->session->userdata('nip'));

    $data['detail_task'] = $this->db->select('a.*, b.*, c.nama')->from('task_detail a')->join('task b', 'b.id = a.id_task', 'left')->join('users c', 'c.nip = a.responsible')->where('a.id_detail', $id)->get()->row_array();

    $data['task_comment_member'] = $this->db->select('a.*, b.*')->from('users a')->join('task_detail_comment b', 'a.nip = b.member')->where('b.id_task_detail', $id)->order_by('date_created', 'desc')->get()->result();

    $this->db->select('*,c.activity as status_task,b.activity,b.comment as comment,b.date_created');
    $this->db->where('b.id_detail', $id);
    $this->db->from('users as a');
    $this->db->join('task_detail as b', 'a.nip=b.responsible');
    $this->db->join('task as c', 'b.id_task=c.id');
    $data['task_comment'] = $this->db->get()->row_array();

    if (!$data['detail_task']) {
      show_error('Forbidden Access: You do not have permission to view this page.', 403, '403 Forbidden');
    }

    // Update Read Task Detail
    $nip = $this->session->userdata('nip');
    $sql = "SELECT task_detail.read FROM task_detail WHERE id_detail ='$id'";
    $query = $this->db->query($sql);
    $result = $query->row();
    $kalimat = $result->read;

    if ($result) {
      $kalimat1 = $kalimat . ' ' . $nip;
      $data_update1    = array(
        'read'    => $kalimat1
      );
      $this->db->where('id_detail', $id);
      $this->db->update('task_detail', $data_update1);
    }

    $this->load->view('index', $data);
  }

  public function activity_comment()
  {
    $id = $this->input->post('id_detail');
    $comment = $this->input->post('comment');

    if (!empty($_FILES['file']['name'])) {
      $files = $_FILES['file']['name'];
    } else {
      $files = [];
    }

    $this->form_validation->set_rules('comment',  'Comment', 'required');

    if ($this->form_validation->run() == FALSE) {
      $response = [
        'success' => false,
        'msg' => array_values($this->form_validation->error_array())[0]
      ];
    } else {
      $filesCount = count($files);
      $uploadedFiles = [];
      $errors = [];
      $uploadedFileName = [];

      $hasFile = false;
      for ($i = 0; $i < $filesCount; $i++) {
        if ($files[$i] != '') {
          $hasFile = true;
          break;
        }
      }

      if ($hasFile) {
        $totalSize = 0;
        for ($i = 0; $i < $filesCount; $i++) {
          $totalSize += $_FILES['file']['size'][$i];
        }

        if ($totalSize > 15 * 1024 * 1024) {
          $response = [
            'success' => FALSE,
            'msg' => 'toal ukuran file tidak boleh lebih dari 15MB.'
          ];
          return;
        }

        for ($i = 0; $i < $filesCount; $i++) {
          $_FILES['file_temp']['name']     = $_FILES['file']['name'][$i];
          $_FILES['file_temp']['type']     = $_FILES['file']['type'][$i];
          $_FILES['file_temp']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
          $_FILES['file_temp']['error']    = $_FILES['file']['error'][$i];
          $_FILES['file_temp']['size']     = $_FILES['file']['size'][$i];

          $config['upload_path']   = './uploads/task_comment';
          $config['allowed_types'] = '*';
          $config['max_size']      = 2048;
          $config['encrypt_name']  = TRUE;

          $this->upload->initialize($config);

          if ($this->upload->do_upload('file_temp')) {
            $uploadData = $this->upload->data();
            $uploadedFiles[] = $uploadData['full_path'];
            $uploadedFileName[] = $uploadData['file_name'];
          } else {
            $errors[] = $this->upload->display_errors();
            break; // Stop dan batal jika ada gagal
          }
        }

        // Hapus file jika gagal
        if (!empty($errors)) {
          foreach ($uploadedFiles as $filePath) {
            if (file_exists($filePath)) {
              unlink($filePath);
            }
          }
          $response = [
            'success' => FALSE,
            'msg' => 'Error : ' . $errors[0]
          ];
          echo json_encode($response);
          return;
        } else {
          $attach = implode(';', $uploadedFileName);
          $attach_name = implode(';', $files);
          $insert = [
            "id_task_detail" => $id,
            "comment_member" => $comment,
            "attachment" => $attach,
            "attachment_name" => $attach_name,
            "member" => $this->session->userdata('nip')
          ];

          $this->db->insert('task_detail_comment', $insert);

          // update task detail
          $this->db->set('read', '0');
          $this->db->where('id_detail', $id);
          $this->db->update('task_detail');

          //Update Task
          $task_detail = $this->db->get_where('task_detail', ['id_detail' => $id])->row();
          $task = $this->db->get_where('task', ['id' => $task_detail->id_task])->row();

          $this->db->set('read', '0');
          $this->db->where('id', $task->id);
          $this->db->update('task');

          // //Send notif wa
          // $nama_session = $this->session->userdata('nama');
          // $user = $this->db->select('phone')->from('users')->where('nip', $responsible)->get()->row_array();
          // $msg = "There's a new card\nTask Name:*$task[name]*\nCard Name : *$card_name*\n\nCreated By :  *$nama_session*";

          // $this->api_whatsapp->wa_notif($msg, $user['phone']);

          $response = [
            'success' => true,
            'msg' => 'Success add activity!'
          ];
        }
      } else {
        $insert = [
          "id_task_detail" => $id,
          "comment_member" => $comment,
          "member" => $this->session->userdata('nip')
        ];

        $this->db->insert('task_detail_comment', $insert);

        // update task detail
        $this->db->set('read', '0');
        $this->db->where('id_detail', $id);
        $this->db->update('task_detail');

        //Update Task
        $task_detail = $this->db->get_where('task_detail', ['id_detail' => $id])->row();
        $task = $this->db->get_where('task', ['id' => $task_detail->id_task])->row();

        $this->db->set('read', '0');
        $this->db->where('id', $task->id);
        $this->db->update('task');

        $response = [
          'success' => true,
          'msg' => 'Success add activity!'
        ];
      }
    }

    echo json_encode($response);
  }

  public function search_user_task()
  {
    $search = $this->input->get('q');
    $page = (int) $this->input->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $users = $this->M_task->search_user_task($search, $limit + 1, $offset);

    $results = [];
    $more = false;

    if (count($users) > $limit) {
      $more = true;
      array_pop($users); // Remove the extra item
    }

    foreach ($users as $user) {
      $results[] = [
        'id' => $user->nip,
        'text' => $user->nama
      ];
    }

    echo json_encode([
      'items' => $results,
      'more' => $more
    ]);
  }
}
