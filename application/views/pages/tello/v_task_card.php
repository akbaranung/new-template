<style>
  .open-task-detail {
    cursor: pointer;
  }

  p.badge {
    min-width: 60px;
    margin-bottom: 0;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">TELLO</h1>
      <div class="card shadow mb-4">
        <div class="card-header text-center">
          <p class="card-title"><strong>Card List</strong></p>
          <p class="btn btn-success" style="width: fit-content;"><?= $task->name ?></p>
          <p><?= $task->comment ?></p>
          <p>
            Member Name :
            <?php
            $data_nip = explode(';', $task->member);
            foreach ($data_nip as $x) {
              if ($x != '') {
                $this->db->where('nip', $x);
                $get = $this->db->get('users')->row_array();
                echo $get['nama'] . ', ';
              }
            }
            ?>
          </p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <a href="<?= site_url('task') ?>" class="btn btn-warning mb-3"><i class="fe fe-chevron-left"></i> Back</a>
            </div>
          </div>
          <table class="table table-hover table-sm">
            <thead style="background-color:#3498db;">
              <tr>
                <th style="color: white;">Card Name</th>
                <th style="color: white;">Responsible</th>
                <th style="color: white;">Start Date</th>
                <th style="color: white;">Due Date</th>
                <th style="color: white;">Activity</th>
                <th style="color: white;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (empty($task_detail)) { ?>
                <tr>
                  <td colspan="6" class="text-center">Data tidak ditemukan</td>
                </tr>
                <?php } else {
                $nip = $this->session->userdata('nip');
                foreach ($task_detail as $data) {
                  $user_read = $this->db->select('id_detail')->from('task_detail')->where('id_detail', $data->id_detail)->like('read', $nip, 'both')->get()->num_rows();
                  if ($data->activity == '1') {
                    $activity = "<p class='badge badge-pill badge-success'>Open</p>";;
                  } else if ($data->activity == '2') {
                    $activity = "<p class='badge badge-pill badge-warning'>Pendig</p>";
                  } else {
                    $activity = "<p class='badge badge-pill badge-secondary'>Closed</p>";
                  }
                ?>
                  <tr>
                    <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->task_name; ?></td>
                    <td style="max-width: 150px;" class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->nama ?></td>
                    <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->start_date; ?></td>
                    <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->due_date; ?></td>
                    <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $activity ?></td>
                    <td>
                      <a href="#" class="btn btn-outline-success"><span class="fe fe-edit-3"></span></a>
                      <?php if (empty($user_read)) { ?>
                        <span class="badge badge-pill badge-danger">New</span>
                      <?php } ?>
                    </td>
                  </tr>
              <?php }
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->