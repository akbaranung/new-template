<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Arus Kas</strong></p>
        </div>
        <div class="card-body">
          <?php
          if ($this->input->post('no_coa')) { ?>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
              <div class="row">
                <div class="col-md-5 col-xs-12">
                  <label for="" class="form-label">No. CoA</label>
                  <select name="no_coa" id="no_coa" class="form-control select2">
                    <option value="">:: Pilih nomor coa</option>
                    <?php
                    foreach ($coas as $c) {
                    ?>
                      <option <?= ($this->input->post('no_coa') == $c->no_sbb) ? "selected" : "" ?> value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
                    <?php
                    } ?>
                  </select>
                </div>
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_dari" class="form-label">Dari</label>
                  <input type="date" class="form-control" name="tgl_dari" value="<?= $this->input->post('tgl_dari') ?>">
                </div>
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_sampai" class="form-label">Sampai</label>
                  <input type="date" class="form-control" name="tgl_sampai" value="<?= $this->input->post('tgl_sampai') ?>">
                </div>
                <div class="col-md-1 col-xs-12">
                  <button type="submit" class="btn btn-primary" style="margin-top: 24px;">Lihat</button>
                </div>
              </div>
            </form>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12 col-xs-12 table-responsive">
                <table id="datatable" class="table table-bordered" style="width:100%">
                  <thead class="thead-dark">
                    <tr>
                      <th class="text-right" colspan="2">Total:</th>
                      <th class="text-right"><?= number_format($sum_debit, 2) ?></th>
                      <th class="text-right"><?= number_format($sum_kredit, 2) ?></th>
                      <!-- <th class="text-right" colspan="2">Saldo Awal: <?= number_format($saldo_awal, 2) ?></th> -->
                    </tr>
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">Tanggal</th>
                      <th class="text-center">Debit</th>
                      <th class="text-center">Kredit</th>
                      <th class="text-center">Saldo Akhir</th>
                      <th class="text-center">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    if ($coa) {

                      foreach ($coa as $a) :
                    ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= format_indo($a->tanggal) ?></td>
                          <!-- <td><?= ($a->akun_debit == $detail_coa['no_sbb']) ? $a->akun_debit : $a->akun_kredit ?></td> -->
                          <td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->jumlah_debit) ? number_format($a->jumlah_debit) : '0') : '0' ?></td>
                          <!-- <td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->saldo_debit) ? number_format($a->saldo_debit) : '0') : '0' ?></td> -->
                          <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->jumlah_kredit) ? number_format($a->jumlah_kredit) : '0') : '0' ?></td>
                          <!-- <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? number_format($a->saldo_kredit) : '0') : '0' ?></td> -->
                          <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? number_format($a->saldo_kredit) :  '0') : (($a->saldo_debit) ? number_format($a->saldo_debit) : '0') ?></td>
                          <td><?= $a->keterangan ?></td>
                        </tr>
                      <?php
                      endforeach;
                    } else {
                      ?>
                      <tr>
                        <td colspan="6">Tidak ada transaksi pada periode yang dipilih</td>
                      </tr>
                    <?php
                    } ?>
                  </tbody>

                  <?php
                  $no = 1;
                  $saldo = $saldo_awal;
                  if ($coa) {
                    foreach ($coa as $a) {
                      $posisi = $detail_coa["posisi"];
                      $no_sbb = $detail_coa["no_sbb"];

                      if ($posisi == "AKTIVA") {
                        if ($a->akun_debit == $no_sbb) {
                          $saldo += $a->jumlah_debit;
                        } else {
                          $saldo -= $a->jumlah_kredit;
                        }
                      } else { // PASIVA
                        if ($a->akun_kredit == $no_sbb) {
                          $saldo += $a->jumlah_kredit;
                        } else {
                          $saldo -= $a->jumlah_debit;
                        }
                      } ?>
                    <?php
                    }
                  } else {
                    ?>
                  <?php
                  }
                  ?>
                </table>
              </div>
            </div>
          <?php
          } else {
          ?>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
              <div class="row">
                <div class="col-md-5 col-xs-12">
                  <label for="" class="form-label">No. CoA </label>
                  <select name="no_coa" id="no_coa" class="form-control select2">
                    <option value="">:: Pilih nomor coa</option>
                    <?php
                    foreach ($coas as $c) {
                    ?>
                      <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
                    <?php
                    } ?>
                  </select>
                </div>
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Dari</label>
                  <input type="date" class="form-control" name="tgl_dari" value="">
                </div>
                <div class="col-md-3 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Sampai</label>
                  <input type="date" class="form-control" name="tgl_sampai" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-1 col-xs-12">
                  <button type="submit" class="btn btn-primary" style="margin-top: 24px;">Lihat</button>
                </div>
              </div>
            </form>
          <?php
          } ?>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->