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
                  Laba berjalan: <strong>Rp <?= (isset($neraca)) ? number_format($neraca, 2) : 0 ?></strong>
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
            <div class="col-md-6 col-xs-12">
              <h2 class="text-center">Biaya</h2>
              <p class="text-right">Total: <strong><?= number_format($sum_biaya, 2) ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table" style="width:100%">
                  <thead>
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
                          <td class="text-right"><?= number_format($a->saldo_awal, 2) ?></td>
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
              <p class="text-right">Total: <strong><?= number_format($sum_pendapatan, 2) ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table" style="width:100%">
                  <thead>
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

                      if ($coa['table_source'] == "t_coalr_sbb" && $coa['posisi'] == 'PASIVA') { ?>
                        <tr>
                          <td><button class="btn btn-primary arus_kas" data-id="<?= $a->no_sbb ?>"><?= $a->no_sbb ?></td>
                          <td><?= $coa['nama_perkiraan'] ?></td>
                          <td class="text-right"><?= number_format($a->saldo_awal, 2) ?></td>
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