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
          <strong class="card-title">Form Edit Card</strong>
        </div>
        <div class="card-body">
          <form method="post" action="<?= site_url('task/save_detail_task') ?>" enctype="multipart/form-data">
            <input type="hidden" name="id_task" value="<?= $this->uri->segment(3) ?>">
            <input type="hidden" value="<?= $this->uri->segment(4) ?>" name="id_card">
            <div class="form-group row">
              <label for="project-name" class="col-sm-3 col-form-label">Card Name <strong>(*)</strong></label>
              <div class="col-sm-9">
                <input class="form-control" name="project-name" id="project-name" type="text" value="<?= $detail_task->task_name ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Card Responsible <strong>(*)</strong></label>
              <div class="col-sm-9">
                <select name="responsible" id="responsible" class="form-control select2">
                  <?php foreach ($member as $val) :
                  ?>
                    <option value="<?= $val->nip ?>" <?= $val->nip == $detail_task->responsible ? 'selected' : '' ?>><?= $val->nama ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="description" class="col-sm-3 col-form-label">Decription <strong>(*)</strong></label>
              <div class="col-sm-9">
                <textarea name="description" id="description" class="form-control"><?= $detail_task->description ?></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="attach" class="col-sm-3 col-form-label">Start and Due Date <strong>(*)</strong></label>
              <div class="col-sm-5">
                <input type="date" class="form-control" name="start" value="<?= $detail_task->start_date ?>">
              </div>
              <div class="col-sm-4">
                <input type="date" class="form-control" name="end" value="<?= $detail_task->due_date ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="description" class="col-sm-3 col-form-label">Activity <strong>(*)</strong></label>
              <div class="col-sm-9">
                <select name="activity" id="activity" class="form-control select2">
                  <option value="1" <?= $detail_task->activity == '1' ? 'selected' : '' ?>>Open</option>
                  <option value="2" <?= $detail_task->activity == '2' ? 'selected' : '' ?>>Pending</option>
                  <option value="3" <?= $detail_task->activity == '3' ? 'selected' : '' ?>>Close</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <a href="<?= site_url('task/task_view/') . $detail_task->id_task ?>" class="btn mb-2 btn-warning">Kembali</a>
              <button type="submit" class="btn mb-2 btn-primary btn-submit">Update Card</button>
            </div>

          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->