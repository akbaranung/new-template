<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title">
            <strong>Financial Entry</strong>
          </p>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
              <a class="btn btn-warning btn-sm" href="<?= base_url('src/format/format_data.xlsx') ?>" download style="font-size: 12px;padding: 5px 10px;color: white;">
                Download Format Data
              </a>
              <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#upload_modal" type="button" style="color: white;">
                Upload Data
              </button>
              <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
                Input Multiple
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= site_url('financial/financial_entry/debit') ?>">Multi Kredit</a>
                <a class="dropdown-item" href="<?= site_url('financial/financial_entry/kredit') ?>">Multi Debit</a>
              </div>
            </div>
          </div>
          <form class="form-label-left input_mask" method="POST" action="<?= base_url('financial/process_financial_entry') ?>">
            <div class="row">
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Debit</label>
                <select name="neraca_debit" id="neraca_debit" class="form-control" style="width: 100%;" required>
                  <option value="">-- Pilih pos neraca debit</option>
                  <?php
                  foreach ($coa as $c) :
                  ?>
                    <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                  <?php
                  endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Kredit</label>
                <select name="neraca_kredit" id="neraca_kredit" class="form-control" style="width: 100%;" required>
                  <option value="">-- Pilih pos neraca kredit</option>
                  <?php
                  foreach ($coa as $c) :
                  ?>
                    <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?> </option>
                  <?php
                  endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Nominal</label>
                <!-- <input type="text" class="form-control" name="input_nominal" id="input_nominal" placeholder="Nominal" oninput="format_angka()" onkeypress="return onlyNumberKey(event)" autofocus required> -->
                <input type="text" class="form-control uang" name="input_nominal" id="input_nominal" placeholder="Nominal" autofocus required>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Keterangan</label>
                <input type="text" class="form-control" name="input_keterangan" id="input_keterangan" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" required>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-9 col-sm-9">
                <button type="button" class="btn btn-primary">Cancel</button>
                <button class="btn btn-primary" type="reset">Reset</button>
                <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Upload Financial Entry</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <form id="upload_file_fe">
            <div class="col-md-12 col-sm-12  offset-md-3 mt-3">
              <label for="" class="form-label">File Format Data</label>
              <input class="form-control-file" type="file" name="format_data" id="format_data">
            </div>
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="upload_fe()">Save</button>
      </div>
    </div>
  </div>
</div>