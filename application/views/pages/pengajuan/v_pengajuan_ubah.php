<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Form Ubah Pengajuan</strong></p>
        </div>
        <div class="card-body">
          <form action="<?= site_url('pengajuan/update/') . $pengajuan->kode ?>" method="post" enctype="multipart/form-data">
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="tanggal" class="form-label">Tanggal</label>
                  <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= $pengajuan->tanggal ?>">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="rekening" class="form-label">No. Rekening</label>
                  <input type="text" class="form-control" name="rekening" id="rekening" value="<?= $pengajuan->no_rekening ?>">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="metode" class="form-label">Metode Pembayaran</label>
                  <select name="metode" id="metode" class="form-control">
                    <option value=""> :: Pilih Metode Pembayaran</option>
                    <option value="1" <?= $pengajuan->metode_pembayaran == 'Reimburse' ? "selected" : '' ?>>Reimburse</option>
                    <option value="2" <?= $pengajuan->metode_pembayaran == 'Transfer' ? "selected" : '' ?>>Transfer</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="file" class="form-label">File Pengajuan</label>
                  <input type="file" class="form-control-file" name="file" id="file">
                  <span>File : <a href="<?= base_url('uploads/pengajuan/') . $pengajuan->bukti_pengajuan ?>"><?= $pengajuan->bukti_pengajuan ?></a></span>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="catatan" class="form-label">Catatan</label>
                  <textarea name="catatan" id="catatan" class="form-control"><?= $pengajuan->catatan ?></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Uraian</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody id="uraian-pengajuan">
                    <?php foreach ($pengajuan_detail as $detail) : ?>
                      <tr id="clone">
                        <td><textarea name="uraian[]" id="uraian" class="form-control"><?= $detail->item ?></textarea></td>
                        <td width="80px"><input type="text" class="form-control qty" name="qty[]" id="qty" value="<?= $detail->qty ?>"></td>
                        <td><input type="text" class="form-control price" name="price[]" id="price" value="<?= str_replace('.', ',', $detail->price) ?>"></td>
                        <td><input type="text" class="form-control subtotal" name="subtotal[]" id="subtotal" readonly></td>
                        <td>
                          <span class="btn btn-success add-row btn-sm"><i class="fe fe-plus-square fe-12"></i></span>
                          <span class="btn btn-danger hapus-row btn-sm"><i class="fe fe-trash-2 fe-12"></i></span>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" style="text-align: end;"><span>TOTAL</span></td>
                      <td><input type="text" class="form-control total" name="total" id="total" readonly></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <a href="<?= site_url('pengajuan/list') ?>" class="btn btn-warning">Kembali</a>
                    <button class="btn btn-primary btn-submit" type="submit">Simpan</button>
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