<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">TELLO</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <strong class="card-title">Create Card</strong>
        </div>
        <div class="card-body">
          <form method="post" action="<?= site_url('task/save_task_detail/' . $this->uri->segment(3)) ?>" enctype="multipart/form-data">
            <div class="form-group row">
              <label for="judul" class="col-sm-3 col-form-label">Card Name <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input class="form-control" name="judul" id="judul" type="text">
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Card Responsible <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="responsible" id="responsible" class="form-control select2">
                  <option value="">Select Responsible</option>
                  <?php foreach ($member as $m) { ?>
                    <option value="<?= $m->nip ?>"><?= $m->nama ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="attach" class="col-sm-3 col-form-label">Description</label>
              <div class="col-sm-6">
                <textarea name="description" id="description" class="form-control"></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="date" class="col-sm-3 col-form-label">Start and Due Date <strong>(*)</strong></label>
              <div class="col-sm-3">
                <input type="date" class="form-control" name="start" id="start">
              </div>
              <div class="col-sm-3">
                <input type="date" class="form-control" name="end" id="end">
              </div>
            </div>
            <div class="form-group row">
              <label for="attach" class="col-sm-3 col-form-label">Attachment</label>
              <div class="col-sm-6">
                <input type="file" class="form-control-file" name="attach[]" id="attach" multiple>
              </div>
            </div>
            <div class="form-group row">
              <label for="attach" class="col-sm-3 col-form-label">Card Activity <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="activity" id="activity" class="form-control">
                  <option value="1">Open</option>
                  <option value="2">Pending</option>
                  <option value="3">Close</option>
                </select>
              </div>
            </div>
            <a href="<?= site_url('task') ?>" class="btn btn-warning">Kembali</a>
            <button type="submit" class="btn btn-primary btn-submit">Submit Task</button>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->