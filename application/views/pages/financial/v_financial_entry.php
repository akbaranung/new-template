<style>
  .validation-error-alert {
    display: none;
    /* Initially hide the warning */
    margin-top: 10px;
    padding: 10px;

    /* White-Red Background */
    background-color: #ffe8e8;
    /* Very light red/pink */

    /* Red Border */
    border: 1px solid #dc3545;
    /* Standard red color */

    /* Text Styling */
    color: #dc3545;
    /* Red text color */
    border-radius: 4px;
    /* Optional: smooth corners */
    font-size: 14px;
  }
</style>
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial Entry</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title">
            <strong>Financial Entry</strong>
          </p>
        </div> -->
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
              <!-- <a class="btn btn-warning btn-sm" href="<?= base_url('src/format/format_data.xlsx') ?>" download style="font-size: 12px;padding: 5px 10px;color: white;">
                Download Format Data
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg>
              </a> -->
              <a class="btn btn-pink btn-sm" id="downloadFormatBtn" href="#" style="font-size: 12px;padding: 5px 10px;color: white;">
                Download Format Data
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg>
              </a>
              <button class="btn btn-primary btn-sm" id="uploadDataBtn" type="button" style="color: white;">
                Upload Data
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg>
              </button>
              <button type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
                Input Multiple
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item premium-check" id="financial_entry_kredit_a" data-target-url="<?= site_url('financial/financial_entry/debit') ?>" href="#">Multi Kredit</a>
                <a class="dropdown-item premium-check" id="financial_entry_debit_a" data-target-url="<?= site_url('financial/financial_entry/kredit') ?>" href="#">Multi Debit</a>
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
              <div class="col-md-12 col-xs-12 form-group has-feedback">
                <div id="warningMessage" class="validation-error-alert">

                </div>
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
                <button type="submit" id="btn-submit" class="btn btn-pink" disabled>Submit</button>
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