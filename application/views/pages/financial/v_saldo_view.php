<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Closing / Saldo Awal</strong></p>
        </div>
        <div class="card-body">
          <?php
          if ($this->uri->segment(3)) { ?>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/saldo/') ?>">
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Tanggal</label>
                  <input type="month" class="form-control" name="periode" value="<?= $this->uri->segment(3) ?>">
                </div>
                <div class="col-md-3 col-xs-12">
                  <button type="submit" class="btn btn-primary" style="margin-top: 28px;">Lihat</button>
                  <a type="button" href="<?= base_url('financial/closing/') ?>" class="btn btn-warning" style="margin-top: 28px;">Kembali</a>
                </div>
              </div>
            </form>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12 col-xs-12">

                <table id="datatable" class="table table-stripped" style="width:100%">
                  <thead>
                    <tr>
                      <th class="text-center">No. CoA</th>
                      <th class="text-center">Nama</th>
                      <th class="text-center">Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($coa) && is_iterable($coa)) :
                      $no = 1;
                      foreach ($coa as $c) :
                        $query = $this->cb->get_where('v_coa_all', ['no_sbb' => $c->no_sbb]);
                        $coa = $query->row(); ?>
                        <tr>
                          <td class="text-right"><?= htmlspecialchars($c->no_sbb) ?></td>
                          <td><?= htmlspecialchars($coa->nama_perkiraan) ?></td>
                          <td class="text-right"><?= number_format($c->saldo_awal) ?></td>
                        </tr>
                      <?php
                      endforeach;
                    else : ?>
                      <tr>
                        <td colspan="4" class="text-center">No data available</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php
          } else {
          ?>
            <div class="row">

              <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/saldo/') ?>">
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Bulan</label>
                  <input type="month" class="form-control" name="periode" value="">
                </div>
                <div class="col-md-1 col-xs-12">
                  <button type="submit" class="btn btn-primary" style="margin-top: 24px;">Lihat</button>
                </div>
              </form>
            </div>
          <?php
          } ?>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->