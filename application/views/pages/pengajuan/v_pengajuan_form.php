<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Form Pengajuan</strong></p>
          <span style="float:right; ">
            <b>
              (Kuota Pengajuan Biaya Tersisa <?= $limit_pengajuan - $total_pengajuan ?>)
              <?=
              $total_pengajuan . '/' . $limit_pengajuan;
              ?>
            </b>
          </span>
        </div>
        <div class="card-body">
          <form action="<?= site_url('pengajuan/insert') ?>" method="post" enctype="multipart/form-data">
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="rekening" class="form-label">No. Rekening</label>
                  <input type="text" class="form-control" name="rekening" id="rekening">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="metode" class="form-label">Metode Pembayaran</label>
                  <select name="metode" id="metode" class="form-control">
                    <option value=""> :: Pilih Metode Pembayaran</option>
                    <option value="1">Reimburse</option>
                    <option value="2">Uang Muka</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="file" class="form-label">File Pengajuan <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                      <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                    </svg></label>
                  <div class="div-file">
                    <input type="file" class="form-control-file" name="file" id="file">
                  </div>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="catatan" class="form-label">Catatan</label>
                  <textarea name="catatan" id="catatan" class="form-control"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive">
                  <table class="table table-sm" style="min-width: 952px;">
                    <thead class="thead-dark">
                      <tr>
                        <th>Uraian</th>
                        <th width="80px">Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>#</th>
                      </tr>
                    </thead>
                    <tbody id="uraian-pengajuan">
                      <tr id="clone">
                        <td><textarea name="uraian[]" id="uraian" class="form-control"></textarea></td>
                        <td><input type="text" class="form-control qty" name="qty[]" id="qty"></td>
                        <td><input type="text" class="form-control price" name="price[]" id="price"></td>
                        <td><input type="text" class="form-control subtotal" name="subtotal[]" id="subtotal" readonly></td>
                        <td>
                          <span class="btn btn-success add-row btn-sm"><i class="fe fe-plus-square fe-12"></i></span>
                          <span class="btn btn-danger hapus-row btn-sm"><i class="fe fe-trash-2 fe-12"></i></span>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="3" style="text-align: end;"><span>TOTAL</span></td>
                        <td><input type="text" class="form-control total" name="total" id="total" readonly></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <a href="<?= site_url('pengajuan/list') ?>" class="btn btn-warning">Kembali</a>
                    <button class="btn btn-primary btn-submit" type="submit">Ajukan</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->