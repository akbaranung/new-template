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
              <a href="<?= site_url('financial/financial_entry') ?>" class="btn btn-primary btn-sm">Single</a>
              <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
                Input Multiple <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= site_url('financial/financial_entry/debit') ?>">Multi Kredit</a>
                <a class="dropdown-item" href="<?= site_url('financial/financial_entry/kredit') ?>">Multi Debit</a>
              </div>
            </div>
          </div>
          <form class="form-label-left input_mask" method="POST" action="<?= site_url('financial/process_financial_entry/multi_kredit') ?>" enctype="multipart/form-data">
            <div class="col-md-6 col-xs-12 form-group has-feedback">
              <label for="" class="form-label">Coa Debit</label>
              <select name="neraca_debit" id="neraca_debit" class="form-control select2" style="width: 100%" required>
                <option value="">:: Pilih pos neraca debit</option>
                <?php foreach ($coa as $c) : ?>
                  <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>">
                    <?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th>Coa Kredit</th>
                  <th>Nominal</th>
                </tr>
              </thead>
              <tbody id="journal-entries">
                <tr>
                  <td>
                    <select name="accounts[]" class="form-control select2" style="width: 100%" required>
                      <option value="">:: Pilih akun</option>
                      <?php foreach ($coa as $c) : ?>
                        <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <input type="text" class="form-control uang nominal-input" name="nominals[]" placeholder="Nominal" required>
                  </td>
                </tr>
              </tbody>
              <tbody>
                <tr>
                  <td colspan="2" class="text-right">
                    <button type="button" class="btn btn-secondary" id="add-row">Tambah Baris</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="row">
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="" class="form-label">Keterangan</label>
                <textarea name="input_keterangan" id="input_keterangan" class="form-control" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" rows="3" required></textarea>
              </div>
              <div class="col-md-6 col-xs-12 form-group has-feedback">
                <label for="file_upload" class="form-label">Upload file (opsional) <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                    <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                  </svg></label>
                <input type="file" name="file_upload" id="file_upload" class="form-control-file">
              </div>
            </div>
            <div class="row">
              <div class="col-md-9 col-sm-9">
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