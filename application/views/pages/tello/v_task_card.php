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
      <h1 class="page-title">TELLO CARD LIST</h1>
      <div class="card shadow mb-4">
        <div class="card-header d-flex flex-column justify-content-center align-items-center text-center">
          <div class="alert alert-primary rounded shadow w-100 d-inline-block">
            <!-- <p class="card-title"><strong>Card List</strong></p> -->
            <p><?= $task->name ?></p>
            <p class="mb-0"><?= $task->comment ?></p>
          </div>
          <div class="alert alert-pink rounded shadow w-100 d-inline-block">
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
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <a href="<?= site_url('task') ?>" class="btn btn-warning mb-3"><i class="fe fe-chevron-left"></i> Back</a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover table-sm">
              <thead style="background-color:#3498db;">
                <tr>
                  <th style="color: white;">Card Name</th>
                  <th style="color: white;">Responsible</th>
                  <th style="color: white;">Start Date</th>
                  <th style="color: white;">Due Date</th>
                  <th style="color: white;">Activity</th>
                  <th style="color: white;">Action</th>
                  <th style="color: white;"></th>
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
                    $user_read = $this->db->select('id_detail')->from('task_detail')
                      ->where('id_detail', $data->id_detail)
                      // ->like('read', $nip, 'both')
                      // ->like('CONCAT(";", read, ";")', ';' . $nip . ';', 'both')
                      ->where("CONCAT(' ', `read`, ' ') LIKE '% " . $nip . " %'")
                      ->get()->num_rows();
                    if ($data->activity == '1') {
                      // $activity = "<p class='badge badge-pill badge-success'>Open</p>";;
                      $activity = "Open";
                      $background_class = "bg-primary text-white";
                    } else if ($data->activity == '2') {
                      // $activity = "<p class='badge badge-pill badge-warning'>Pending</p>";
                      $activity = "Pending";
                      $background_class = "bg-warning text-white";
                    } else {
                      // $activity = "<p class='badge badge-pill badge-secondary'>Closed</p>";
                      $activity = "Closed";
                      $background_class = "bg-secondary text-white";
                    }
                  ?>
                    <tr>
                      <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->task_name; ?></td>
                      <td style="max-width: 150px;" class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->nama ?></td>
                      <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->start_date; ?></td>
                      <td class="open-task-detail" onclick="openCard(<?= $data->id_detail ?>)"><?= $data->due_date; ?></td>
                      <td class="open-task-detail <?= $background_class ?>" onclick="openCard(<?= $data->id_detail ?>)"><?= $activity ?></td>
                      <td>
                        <a href="<?= site_url('task/card_edit/') . $data->id_task . '/' . $data->id_detail ?>" class="btn btn-outline-pink"><span class="fe fe-edit-3"></span></a>
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
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->