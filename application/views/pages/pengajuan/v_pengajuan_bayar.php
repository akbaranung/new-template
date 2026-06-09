<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan Biaya</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Bayar Pengajuan <?= $pengajuan->kode ?></strong></p>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <a href="<?= site_url('pengajuan/approval_keuangan') ?>" class="btn btn-warning">Kembali</a>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <form action="<?= site_url('pengajuan/update_bayar/') . $this->uri->segment(3) ?>" method="post">
                <table class="table table-sm table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th width="25px">No.</th>
                      <th width="400px">Uraian</th>
                      <th width="25px">Qty</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>COA</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($pengajuan_detail as $row) : ?>
                      <input type="hidden" name="id_item[]" value="<?= $row->Id ?>">
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row->item ?></td>
                        <td><?= $row->qty ?></td>
                        <td align="right"><?= rupiah($row->price) ?></td>
                        <input type="hidden" name="subtotal[]" value="<?= $row->total ?>">
                        <td align="right"><?= rupiah($row->total) ?></td>
                        <td>
                          <?php if ($pengajuan->metode_pembayaran == 1) { ?>
                            <select name="coa_debit[]" class="form-control coa_debit select2">
                              <option value="" selected> :: Pilih Coa Beban</option>
                              <?php foreach ($coa_debit as $c) : ?>
                                <option value="<?= $c->no_sbb ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                              <?php endforeach ?>
                            </select>
                            <select name="coa_credit[]" class="form-control coa_kredit select2">
                              <option value="" selected> :: Pilih Coa Kredit</option>
                              <?php foreach ($coa_kredit as $c) : ?>
                                <option value="<?= $c->no_sbb ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                              <?php endforeach ?>
                            </select>
                          <?php } else { ?>
                            <select name="coa_debit[]" class="form-control coa_debit select2">
                              <option value="" selected> :: Pilih Coa Uang Muka</option>
                              <?php foreach ($coa_debit as $c) : ?>
                                <option value="<?= $c->no_sbb ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                              <?php endforeach ?>
                            </select>
                            <select name="coa_credit[]" class="form-control coa_kredit select2">
                              <option value="" selected> :: Pilih Coa Kredit</option>
                              <?php foreach ($coa_kredit as $c) : ?>
                                <option value="<?= $c->no_sbb ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                              <?php endforeach ?>
                            </select>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php endforeach ?>
                    <tr>
                      <td colspan="4" align="right">Total</td>
                      <td align="right"><?= rupiah($pengajuan->total) ?></td>
                    </tr>
                  </tbody>
                </table>
                <?php if ($pengajuan->posisi == 'Diarahkan ke pembayaran' or $pengajuan->status == 3) { ?>
                  <hr>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                      <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                          <div class="form-group">
                            <label for="tanggal" class="form-label">Metode Pembayaran</label>
                            <input type="text" class="form-control" value="<?= $pengajuan->metode_pembayaran == 1 ? 'Reimburse' : 'Uang Muka' ?>" disabled>
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                          <div class="form-group">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                          <div class="form-group">
                            <label for="status" class="form-label">Bukti Bayar <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                                <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                              </svg></label>
                            <div class="div-file">
                              <input type="file" class="form-control-file" name="file" id="file">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div>
                        <button type="submit" class="btn btn-primary btn-submit-bayar">Simpan</button>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->