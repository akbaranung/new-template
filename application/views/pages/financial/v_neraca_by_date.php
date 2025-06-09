<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Neraca per tanggal <?= format_indo($per_tanggal) ?></strong></p>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= base_url('financial/reportByDate') ?>">
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <h5>
                  Neraca: <strong>Rp <?= (isset($neraca)) ? number_format($neraca, 2) : 0 ?></strong>
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
                    <option <?= ($this->input->post('jenis_laporan') == "neraca_bb") ? "selected" : "" ?> value="neraca_bb">Neraca BB</option>
                    <option <?= ($this->input->post('jenis_laporan') == "lr_bb") ? "selected" : "" ?> value="lr_bb">Laba Rugi BB</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-xs-12 text-right">
                <div class="form-group">
                  <button type="submit" name="button_sbm" class="btn btn-primary btn-sm" value="lihat">Lihat</button>
                  <button type="submit" name="button_sbm" class="btn btn-success btn-sm" value="excel"><i class='fa fa-file'></i> Excel</button>
                </div>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="col-lg-6 col-md-6 col-xs-12">
              <h2 class="text-center">Activa</h2>
              <p class="text-right">Total: <strong><?= (isset($sum_activa)) ? number_format($sum_activa, 2) : 0 ?></strong></p>
              <div class="table-responsive">
                <table class="table" style="width:100%">
                  <thead class="thead-dark">
                    <tr>
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($activa)) :
                      foreach ($activa as $a) :
                        $coa = $this->M_coa->getCoa($a->no_sbb);

                        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'AKTIVA' && $a->saldo_awal != '0') : ?>
                          <tr>
                            <td><button class="btn btn-primary arus_kas btn-sm" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></button></td>
                            <td><?= $coa['nama_perkiraan'] ?></td>
                            <td class="text-right"><?= number_format($a->saldo_awal, 2) ?></td>
                          </tr>
                      <?php
                        endif;
                      endforeach;
                    else : ?>
                      <tr>
                        <td colspan="3">Tidak ada activa yang ditampilkan</td>
                      </tr>
                    <?php
                    endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6 col-xs-12">
              <h2 class="text-center">Pasiva</h2>
              <p class="text-right">Total: <strong><?= (isset($sum_pasiva)) ? number_format($sum_pasiva, 2) : 0 ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table" style="width:100%">
                  <thead class="thead-dark">
                    <tr>
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($pasiva)) :
                      foreach ($pasiva as $a) :
                        $coa = $this->M_coa->getCoa($a->no_sbb);

                        if ($coa['table_source'] == "t_coa_sbb" && $coa['posisi'] == 'PASIVA' && $a->saldo_awal != '0') : ?>
                          <tr>
                            <td><button class="btn btn-primary arus_kas btn-sm" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                            <td><?= $coa['nama_perkiraan'] ?></td>
                            <td class="text-right"><?= number_format($a->saldo_awal, 2) ?></td>
                          </tr>
                      <?php
                        endif;
                      endforeach; ?>
                      <tr>
                        <td>31030</td>
                        <td>LABA TAHUN BERJALAN</td>
                        <td class="text-right"><?= number_format($laba, 2) ?></td>
                      </tr>
                    <?php
                    else : ?>
                      <tr>
                        <td colspan="3">Tidak ada pasiva yang ditampilkan</td>
                      </tr>
                    <?php
                    endif; ?>
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
                <input type="date" class="form-control" name="tgl_dari" required>
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

<!-- <div class="modal fade" id="detailModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title" id="myModalLabel2">Lacak arus kas</h4>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>" target="_blank">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" class="form-control" name="no_coa">
            <div class="col-md-6 col-xs-12">
              <label for="tgl_dari" class="form-label">Dari</label>
              <input type="date" class="form-control" name="tgl_dari" required>
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
      </form>
    </div>
  </div>
</div> -->