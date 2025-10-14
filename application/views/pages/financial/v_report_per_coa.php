<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Arus Kas</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title"><strong>Arus Kas</strong></p>
        </div> -->
        <div class="card-body">
          <?php
          if ($this->input->post('no_coa')) { ?>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <label for="" class="form-label">No. CoA</label>
                  <select name="no_coa" id="no_coa" class="form-control select2">
                    <option value="">:: Pilih nomor coa</option>
                    <option <?= ($this->input->post('no_coa') == 'ALL') ? "selected" : "" ?> value="ALL">ALL COA</option>
                    <?php
                    foreach ($coas as $c) {
                    ?>
                      <option <?= ($this->input->post('no_coa') == $c->no_sbb) ? "selected" : "" ?> value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
                    <?php
                    } ?>
                  </select>
                </div>
                <div class="col-md-2 col-xs-12">
                  <label for="tgl_dari" class="form-label">Dari</label>
                  <input type="date" class="form-control" name="tgl_dari" value="<?= $this->input->post('tgl_dari') ?>">
                </div>
                <div class="col-md-2 col-xs-12">
                  <label for="tgl_sampai" class="form-label">Sampai</label>
                  <input type="date" class="form-control" name="tgl_sampai" value="<?= $this->input->post('tgl_sampai') ?>">
                </div>
                <div class="col-md-3 col-xs-12">
                  <label for="keyword" class="form-label">Keyword</label>
                  <input type="text" name="keyword" id="keyword" class="form-control" placeholder="nomor coa/nominal/keterangan" value="<?= $this->input->post('keyword') ?>">
                </div>
                <div class="col-md-2 col-xs-12">
                  <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 30px;">Lihat</button>
                  <a href="<?= base_url('financial/coa_report') ?>" class="btn btn-warning text-white btn-sm" style="margin-top: 30px;">Reset</a>
                </div>
              </div>
            </form>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12 col-xs-12 table-responsive">
                <?php
                if ($this->input->post('no_coa') == "ALL") {
                ?>
                  <table id="" class="table table-sm table-bordered" style="width:100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">CoA</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                        <!-- <th class="text-center">Saldo Akhir</th> -->
                        <th class="text-center">Keterangan</th>
                        <th class="text-center">File</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      if ($coa) {
                        foreach ($coa as $a) :
                          $nama_debit = $this->M_coa->getCoa($a->akun_debit)['nama_perkiraan'];
                          $nama_kredit = $this->M_coa->getCoa($a->akun_kredit)['nama_perkiraan']; ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= format_indo($a->tanggal) ?></td>
                            <td><?= $a->akun_debit ?> - <?= $nama_debit ?></td>
                            <td class="text-right"><?= rupiah($a->jumlah_debit) ?></td>
                            <td class="text-right"><?= '0' ?></td>
                            <!-- <td class="text-right"><?= rupiah($a->saldo_debit) ?></td> -->
                            <td style="white-space: pre-line;"><?= $a->keterangan ?></td>
                            <td style="white-space: pre-line;">
                              <?php if ($a->file): ?>
                                <a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info">
                                  <i class="fa fa-download"></i> <?= $a->nama_file ?>
                                </a>
                              <?php else: ?>
                                No Attachment
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= format_indo($a->tanggal) ?></td>
                            <td><?= $a->akun_kredit ?> - <?= $nama_kredit ?></td>
                            <td class="text-right"><?= '0' ?></td>
                            <td class="text-right"><?= rupiah($a->jumlah_kredit) ?></td>
                            <!-- <td class="text-right"><?= rupiah($a->saldo_kredit) ?></td> -->
                            <td style="white-space: pre-line;"><?= $a->keterangan ?></td>
                            <td style="white-space: pre-line;">
                              <?php if ($a->file): ?>
                                <a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info">
                                  <i class="fa fa-download"></i> <?= $a->nama_file ?>
                                </a>
                              <?php else: ?>
                                No Attachment
                              <?php endif; ?>
                            </td>
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
                  </table>
                <?php
                } else {
                ?>
                  <table id="" class="table table-sm table-bordered" style="width:100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-right" colspan="2" style="background-color: #e91e63; font-weight: bolder;">Total:</th>
                        <th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_debit) ?></th>
                        <th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
                        <!-- <th class="text-right" colspan="2">Saldo Awal: <?= rupiah($saldo_awal) ?></th> -->
                      </tr>
                      <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Debit</th>
                        <th class="text-center">Kredit</th>
                        <!-- <th class="text-center">Saldo Akhir</th> -->
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
                            <td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->jumlah_debit) ? rupiah($a->jumlah_debit) : '0') : '0' ?></td>
                            <!-- <td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->saldo_debit) ? rupiah($a->saldo_debit) : '0') : '0' ?></td> -->
                            <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->jumlah_kredit) ? rupiah($a->jumlah_kredit) : '0') : '0' ?></td>
                            <!-- <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? rupiah($a->saldo_kredit) : '0') : '0' ?></td> -->
                            <!-- <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? rupiah($a->saldo_kredit) :  '0') : (($a->saldo_debit) ? rupiah($a->saldo_debit) : '0') ?></td> -->
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
                    // $saldo = $saldo_awal;
                    $saldo = 0;
                    if ($coa) {
                      // foreach ($coa as $a) {
                      //   $posisi = $detail_coa["posisi"];
                      //   $no_sbb = $detail_coa["no_sbb"];

                      //   if ($posisi == "AKTIVA") {
                      //     if ($a->akun_debit == $no_sbb) {
                      //       $saldo += $a->jumlah_debit;
                      //     } else {
                      //       $saldo -= $a->jumlah_kredit;
                      //     }
                      //   } else { // PASIVA
                      //     if ($a->akun_kredit == $no_sbb) {
                      //       $saldo += $a->jumlah_kredit;
                      //     } else {
                      //       $saldo -= $a->jumlah_debit;
                      //     }
                      //   } 
                    ?>
                    <?php
                      // }
                    } else {
                    ?>
                    <?php
                    }
                    ?>
                  </table>
                <?php
                } ?>
              </div>
            </div>
          <?php
          } else {
          ?>
            <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <label for="" class="form-label">No. CoA </label>
                  <select name="no_coa" id="no_coa" class="form-control select2">
                    <option value="">:: Pilih nomor coa</option>
                    <option <?= ($this->input->post('no_coa') == 'ALL') ? "selected" : "" ?> value="ALL">ALL COA</option>
                    <?php
                    foreach ($coas as $c) {
                    ?>
                      <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
                    <?php
                    } ?>
                  </select>
                </div>
                <div class="col-md-2 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Dari</label>
                  <input type="date" class="form-control" name="tgl_dari" value="">
                </div>
                <div class="col-md-2 col-xs-12">
                  <label for="tgl_invoice" class="form-label">Sampai</label>
                  <input type="date" class="form-control" name="tgl_sampai" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4 col-xs-12">
                  <label for="keyword" class="form-label">Keyword</label>
                  <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Masukkan nomor coa/nominal/keterangan" value="<?= $this->input->post('keyword') ?>">
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