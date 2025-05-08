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
          <strong class="card-title">Form Edit Task</strong>
        </div>
        <div class="card-body">
          <form method="post" action="<?= site_url('task/save_task') ?>" enctype="multipart/form-data">
            <div class="form-group row">
              <label for="judul" class="col-sm-3 col-form-label">Task Name <strong>(*)</strong></label>
              <div class="col-sm-9">
                <input class="form-control" name="judul" id="judul" type="text" value="<?= $task->name ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Task Member <strong>(*)</strong></label>
              <div class="col-sm-9">
                <select name="member[]" id="member" class="form-control select2" multiple>
                  <?php foreach ($member as $m) :
                    $user = $this->db->select('nama')->from('users')->where('nip', $m)->get()->row();
                  ?>
                    <option value="<?= $m ?>" <?= (strpos($task->member, $m) !== false) ? 'selected' : '' ?>><?= $user->nama ?></option>
                  <?php endforeach ?>
                </select>
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
            <div class="modal-footer">
              <a href="<?= site_url('task') ?>" class="btn mb-2 btn-warning">Kembali</a>
              <button type="submit" class="btn mb-2 btn-primary btn-submit">Update Task</button>
            </div>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->