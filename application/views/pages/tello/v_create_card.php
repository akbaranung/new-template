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
              <label for="attach" class="col-sm-3 col-form-label">Attachment <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg></label>
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