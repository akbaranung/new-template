<style>
  .open-memo {
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
        <div class="card-header">
          <!-- <strong class="card-title">Task List</strong> -->
          <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modalCreateTask"><i class="fe fe-plus"></i> Create New Task</button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('task') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Isi dengan nama task yang akan dicari" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('task') ?>" class="btn btn-warning">Tampilkan Semua</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <table class="table table-hover table-sm">
            <thead style="background-color:#3498db;">
              <tr>
                <th style="color: white;">No</th>
                <th style="color: white;">Task Name</th>
                <th style="color: white;">PIC</th>
                <th style="color: white;">Status</th>
                <th style="color: white;">Created</th>
                <th style="color: white;">#</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (empty($data_task)) { ?>
                <tr>
                  <td colspan="5" class="text-center">Data tidak ditemukan</td>
                </tr>
                <?php } else {
                $nip = $this->session->userdata('nip');
                foreach ($data_task as $data) {
                  $user_read = $this->db->select('Id')->from('task')->where('Id', $data->Id)->like('read', $nip, 'both')->get()->num_rows();
                  $task_cek = $this->db->get_where('task_detail', ['id_task' => $data->Id]);

                  if ($user_read) {
                    $font_weight = 'normal';
                  } else {
                    $font_weight = 'bold';
                  }

                  if ($task_cek->num_rows() > 0) {
                    foreach ($task_cek->result()  as $tc) {
                      if ($tc->due_date > date('Y-m-d')) {
                        $activity = 1;
                      } else {
                        $activity = 0;
                      }
                    }
                  } else {
                    $activity = 0;
                  }

                  if ($data->activity == '1' and $activity == 1) {
                    $status = "<p class='badge badge-pill badge-success'>Open</p>";
                  } else if ($data->activity == '3') {
                    $status = "<p class='badge badge-pill badge-secondary'>Closed</p>";
                  } else {
                    $status = "<p class='badge badge-pill badge-danger'>Over Due</p>";
                  }

                ?>
                  <tr>
                    <td onclick="openTask(<?= $data->Id ?>)" class="open-memo"><?= ++$page; ?></td>
                    <td style="max-width: 150px;" onclick="openTask(<?= $data->Id ?>)" class="open-memo"><?= $data->name ?></td>
                    <td onclick="openTask(<?= $data->Id ?>)" class="open-memo"><?= $data->nama; ?></td>
                    <td onclick="openTask(<?= $data->Id ?>)" class="open-memo"><?= $status; ?></td>
                    <td onclick="openTask(<?= $data->Id ?>)" class="open-memo"><?= date('d/m/y | H:i:s', strtotime($data->date_created)) ?></td>
                    <td>
                      <?php if ($data->pic == $this->session->userdata('nip')) { ?>
                        <a href="<?= site_url('task/edit_task/' . $data->Id) ?>" class="btn btn-outline-success"><span class="fe fe-edit-3"></span></a>
                      <?php } ?>

                      <?php if (empty($user_read)) { ?>
                        <span class="badge badge-pill badge-danger">New</span>
                      <?php } ?>
                    </td>
                  </tr>
              <?php }
              } ?>
            </tbody>
          </table>

          <!-- Pagination -->
          <nav aria-label="Table Paging" class="mb-0">
            <?= $pagination ?>
          </nav>

        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Modal -->
<div class="modal fade" id="modalCreateTask" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Create New Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="<?= site_url('task/save_task') ?>" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group row">
            <label for="judul" class="col-sm-3 col-form-label">Task Name <strong>(*)</strong></label>
            <div class="col-sm-9">
              <input class="form-control" name="judul" id="judul" type="text">
            </div>
          </div>
          <div class="form-group row">
            <label for="tujuan" class="col-sm-3 col-form-label">Task Member <strong>(*)</strong></label>
            <div class="col-sm-9">
              <select name="member[]" id="member" class="form-control select2" multiple></select>
            </div>
          </div>
          <div class="form-group row">
            <label for="attach" class="col-sm-3 col-form-label">Task Activity</label>
            <div class="col-sm-9">
              <select name="activity" id="activity" class="form-control">
                <option value="1">Open</option>
                <option value="2">Pending</option>
                <option value="3">Close</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="attach" class="col-sm-3 col-form-label">Description</label>
            <div class="col-sm-9">
              <textarea name="description" id="description" class="form-control"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn mb-2 btn-primary btn-submit">Submit Task</button>
        </div>
      </form>
    </div>
  </div>
</div>