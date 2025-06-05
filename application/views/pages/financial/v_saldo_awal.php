<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Closing / Saldo Awal</strong></p>
        </div>
        <div class="card-body">
          <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
            <div class="row mb-4">
              <div class="col-md-3 col-xs-12">
                <input type="month" class="form-control" name="periode" value="<?= $this->input->post('periode') ?>">
              </div>
              <div class="col-md-3 col-xs-12">
                <button type="submit" class="btn btn-primary">Closing EoM</button>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table id="datatable" class="table table-stripped" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Closing Periode</th>
                  <th class="text-center">Keterangan</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (($saldo)) {
                  $no = 1;
                  foreach ($saldo as $c) : ?>
                    <tr>
                      <td class="text-right"><?= $no++ ?>.</td>
                      <td><?= format_indo($c->periode) ?></td>
                      <td><?= $c->keterangan ?></td>
                      <td class="text-center"><a href="<?= base_url('financial/closing/' . $c->periode) ?>" class="btn btn-primary btn-sm">Detail</a></td>
                    </tr>
                  <?php
                  endforeach;
                } else { ?>
                  <tr>
                    <td colspan="4" class="text-center">No data available</td>
                  </tr>
                <?php
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->