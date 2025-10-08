<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Closing / Saldo Awal</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title"><strong>Closing / Saldo Awal</strong></p>
        </div> -->
        <div class="card-body">
          <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#proses-closing">Closing EoM</a>
          <p class="mt-4"><strong>* Harap untuk melakukan closing setiap akhir bulan, untuk membentuk saldo awal bulan berikutnya!</strong></p>
          <!-- <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
            <div class="row mb-4">
              <div class="col-md-3 col-xs-12">
                <input type="month" class="form-control" name="periode" value="<?= $this->input->post('periode') ?>">
              </div>
              <div class="col-md-3 col-xs-12">
                <button type="submit" class="btn btn-primary">Closing EoM</button>
              </div>
            </div>
          </form> -->
          <div class="table-responsive mt-3">
            <table id="" class="table table-sm table-stripped" style="width:100%">
              <thead class="thead-dark">
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


<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="proses-closing">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Proses Closing EoM
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
        <div class="modal-body">
          <p><strong>Masukan periode dan password anda terlebih dahulu untuk memproses closing EoM</strong></p>
          <div class="form-group row">
            <div class="col-12">
              <label for="form-label">Periode</label>
              <input type="month" class="form-control" name="periode" value="<?= date('Y-m') ?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-submit">
            Proses
          </button>
        </div>
      </form>
    </div>
  </div>
</div>