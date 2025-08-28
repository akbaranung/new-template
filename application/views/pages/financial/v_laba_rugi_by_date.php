<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Neraca L/R</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title"><strong>Neraca per tanggal <?= format_indo($per_tanggal) ?></strong></p>
        </div> -->
        <div class="card-body">
          <form method="POST" action="<?= base_url('financial/reportByDate') ?>">
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <h5>
                  Laba berjalan: <strong><?= rupiah($total_pendapatan) ?></strong>
                </h5>
              </div>
              <div class="col-md-2 col-xs-12">
                <div class="form-group">
                  <input type="date" name="per_tanggal" id="per_tanggal" class="form-control" value="<?= $per_tanggal ?>">
                </div>
              </div>
              <div class="col-md-4 col-xs-12">
                <div class="form-group ">
                  <select name="jenis_laporan" id="jenis_laporan" class="form-control">
                    <option <?= ($this->input->post('jenis_laporan') == "neraca") ? "selected" : "" ?> value="neraca">Neraca SBB</option>
                    <option <?= ($this->input->post('jenis_laporan') == "laba_rugi") ? "selected" : "" ?> value="laba_rugi">Laba Rugi SBB</option>
                    <option <?= ($this->input->post('jenis_laporan') == "laba_rugi_tanpa_sawal") ? "selected" : "" ?> value="laba_rugi_tanpa_sawal">Laba Rugi SBB Tanpa Sawal</option>
                    <option <?= ($this->input->post('jenis_laporan') == "neraca_bb") ? "selected" : "" ?> value="neraca_bb">Neraca BB</option>
                    <option <?= ($this->input->post('jenis_laporan') == "lr_bb") ? "selected" : "" ?> value="lr_bb">Laba Rugi BB</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-xs-12 text-right">
                <div class="form-group">
                  <button type="submit" name="button_sbm" class="btn btn-primary btn-sm" value="lihat">Lihat</button>
                  <button type="submit" name="button_sbm" class="btn btn-pink btn-sm" value="excel"><i class='fa fa-file'></i> Excel</button>
                </div>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="col-md-6 col-xs-12">
              <h2 class="text-center">Biaya</h2>
              <p class="text-right">Total: <strong><?= rupiah($sum_biaya) ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table table-sm" style="width:100%">
                  <thead class="thead-dark">
                    <tr>
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($biaya as $a) :
                      $coa = $this->M_coa->getCoa($a->no_sbb);

                      if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'AKTIVA') { ?>
                        <tr>
                          <td><button class="btn btn-primary arus_kas" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                          <td><?= $coa['nama_perkiraan'] ?></td>
                          <td class="text-right"><?= rupiah($a->saldo_awal) ?></td>
                        </tr>
                    <?php
                      }
                    endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6 col-xs-12">
              <h2 class="text-center">Pendapatan</h2>
              <p class="text-right">Total: <strong><?= rupiah($sum_pendapatan) ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table table-sm" style="width:100%;">
                  <thead class="thead-dark">
                    <tr>
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($pendapatan as $a) :
                      $coa = $this->M_coa->getCoa($a->no_sbb);

                      if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA') {

                        if ($a->saldo_awal == 0) {
                          continue;
                        }
                    ?>
                        <tr>
                          <td><button class="btn btn-primary arus_kas" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                          <td><?= $coa['nama_perkiraan'] ?></td>
                          <td class="text-right"><?= rupiah($a->saldo_awal) ?></td>
                        </tr>
                    <?php
                      }
                    endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" id="detailModal2" tabindex="-1" role="dialog" aria-labelledby="detailModal2" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="defaultModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>" target="_blank">
        <div class="modal-body">
          <div class="modal-body">
            <div class="row">
              <input type="hidden" class="form-control" name="no_coa">
              <div class="col-md-6 col-xs-12">
                <label for="tgl_dari" class="form-label">Dari</label>
                <input type="date" class="form-control" name="tgl_dari" value="<?= date('Y-m-01') ?>" required>
              </div>
              <div class="col-md-6 col-xs-12">
                <label for="tgl_sampai" class="form-label">Sampai</label>
                <input type="date" class="form-control" name="tgl_sampai" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Lihat</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>