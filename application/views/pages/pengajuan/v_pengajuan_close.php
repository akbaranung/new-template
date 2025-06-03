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
              <form action="<?= site_url('pengajuan/update_close/') . $this->uri->segment(3) ?>" method="post">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="400px">Uraian</th>
                      <th>Total</th>
                      <th>COA Beban</th>
                      <th>Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($pengajuan_detail as $row) : ?>
                      <input type="hidden" name="id_item[]" value="<?= $row->Id ?>">
                      <tr>
                        <td><?= $row->item ?></td>
                        <input type="hidden" name="subtotal[]" value="<?= $row->total ?>">
                        <td align="right"><?= rupiah($row->total) ?></td>
                        <td>
                          <select name="coa_beban[]" class="form-control select2">
                            <option value=""> :: Pilih Coa Beban</option>
                            <?php foreach ($coa as $c) : ?>
                              <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                            <?php endforeach ?>
                          </select>
                        </td>
                        <td><input type="text" class="form-control" name="realisasi[]"></td>
                      </tr>
                    <?php endforeach ?>
                    <tr>
                      <td colspan="2" align="right">Total</td>
                      <td align="right"><?= rupiah($pengajuan->total) ?></td>
                    </tr>
                  </tbody>
                </table>
                <?php if ($pengajuan->posisi == 'Sudah dibayar' or $pengajuan->status == 4) { ?>
                  <hr>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                      <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                          <div class="form-group">
                            <label for="tanggal" class="form-label">Tanggal Close</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
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