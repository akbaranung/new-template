<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan Biaya</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Detail Pengajuan <?= $pengajuan->kode ?></strong></p>
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
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="25px">No.</th>
                      <th width="400px">Uraian</th>
                      <th width="25px">Qty</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>COA Credit</th>
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
                          <select name="coa_credit[]" class="form-control select2">
                            <option value=""> :: Pilih Coa Kredit</option>
                            <?php foreach ($coa as $c) : ?>
                              <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                            <?php endforeach ?>
                          </select>
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
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                          <div class="form-group">
                            <label for="status" class="form-label">Bukti Bayar</label>
                            <input type="file" class="form-control-file" name="file">
                          </div>
                        </div>
                      </div>
                      <div>
                        <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
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