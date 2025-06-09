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
                Input Multiple
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
                <label for="file_upload" class="form-label">Upload file (opsional)</label>
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