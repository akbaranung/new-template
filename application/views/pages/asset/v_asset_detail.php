<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Detail Aset <?= $data_asset->nama_asset . ' (' . $data_asset->kode . ')' ?></h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="<?= site_url('asset') ?>" class="btn btn-warning">Kembali</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12 col-12">
              <table class="table table-sm">
                <tr>
                  <th>Image</th>
                  <td>:</td>
                  <td>
                    <div class="shadow">
                      <img src="<?= base_url('upload/asset/' . $data_asset->pic) ?>" alt="" style="width: 100%;">
                    </div>
                  </td>
                </tr>
                <tr>
                  <th width="20%">Kode Aset</th>
                  <td width="5px"> : </td>
                  <td><?= $data_asset->kode ?></td>
                </tr>
                <tr>
                  <th width="20%">Nama Aset</th>
                  <td width="5px"> : </td>
                  <td><?= $data_asset->nama_asset ?></td>
                </tr>
                <tr>
                  <th width="20%">Spesifikasi</th>
                  <td width="5px"> : </td>
                  <td><?= $data_asset->spesifikasi ?></td>
                </tr>
                <tr>
                  <th width="20%">Lokasi</th>
                  <td width="5px"> : </td>
                  <td><?php $lokasi = $this->cb->select('nama_cabang')->from('t_cabang')->where('uid', $data_asset->lokasi)->get()->row();
                      echo $lokasi->nama_cabang ?></td>
                </tr>
                <tr>
                  <th width="20%">Ruangan</th>
                  <td width="5px"> : </td>
                  <td><?= $data_asset->nama_ruangan ?></td>
                </tr>
                <tr>
                  <th width="20%">Tanggal Perolehan</th>
                  <td width="5px"> : </td>
                  <td><?= tgl_indo($data_asset->tgl_perolehan) ?></td>
                </tr>
                <tr>
                  <th width="20%">Update Terakhir</th>
                  <td width="5px"> : </td>
                  <td><?= tgl_indo($data_asset->last_update) ?></td>
                </tr>
                <tr>
                  <th width="20%">Status</th>
                  <td width="5px"> : </td>
                  <td>
                    <?php if ($data_asset->kondisi == 1) {
                      echo 'Baik';
                    } else if ($data_asset->kondisi == 2) {
                      echo 'Rusak';
                    } else if ($data_asset->kondisi == 3) {
                      echo "Dalam Perbaikan";
                    } else {
                      echo "Hapus Buku";
                    } ?>
                  </td>
                </tr>
                <tr>
                  <th>Nilai Buku (Harga Perolehan)</th>
                  <td>:</td>
                  <td>Rp.<?= number_format($data_asset->harga) ?></td>
                </tr>
                <tr>
                  <th>Nilai Buku (Sekarang)</th>
                  <td>:</td>
                  <td>Rp.<?= number_format($data_asset->nilai_buku) ?></td>
                </tr>
                <tr>
                  <th colspan="3"><button class="btn btn-success" data-toggle="modal" data-target="#updateAset">Update</button></th>
                </tr>
              </table>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12 col-12">
              <div class="mb-2 card p-2 shadow">
                <img src="<?php echo base_url(); ?>asset/qrcode_view/<?php echo $data_asset->Id; ?>" alt="qr-code" style="width:100%">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-12">
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th>No.</th>
                      <th>Ruangan</th>
                      <th>Lokasi</th>
                      <th>Tanggal</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($asset_history as $history) :
                      $lokasi = $this->cb->select('nama_cabang')->from('t_cabang')->where('uid', $history->lokasi)->get()->row();
                    ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $history->nama_ruangan ?></td>
                        <td><?= $lokasi->nama_cabang ?></td>
                        <td><?= tgl_indo($history->tanggal) ?></td>
                        <td><?= $history->remark ?? '-' ?></td>
                      </tr>
                    <?php endforeach ?>
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

<!-- Modal update aset -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="updateAset">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Update Aset <?= $data_asset->nama_asset . ' - ' . $data_asset->kode ?>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('asset/update_asset/' . $data_asset->Id) ?>">
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12 mt-2">
              <label for="nama" class="form-label">Nama Aset *</label>
              <input type="text" name="nama" id="nama" class="form-control uppercase" value="<?= $data_asset->nama_asset; ?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12 mt-2">
              <label for="foto" class="form-label">Foto *</label>
              <input type="file" name="foto" id="foto" class="form-control-file">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12 mt-2">
              <label for="foto" class="form-label">Foto View</label>
              <img src="<?= base_url('upload/asset/' . $data_asset->pic) ?>" alt="foto aset" width="100%">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="spesifikasi" class="form-label">Spesifikasi *</label>
              <input type="text" name="spesifikasi" id="spesifikasi" class="form-control uppercase" value="<?= $data_asset->spesifikasi; ?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="ruangan" class="form-label">Ruangan *</label>
              <select name="ruangan" id="ruangan" class="form-control select2">
                <?php foreach ($ruangan as $r) : ?>
                  <option value="<?= $r->Id ?>" <?= $r->Id == $data_asset->ruangan ? 'selected' : '' ?>><?= $r->keterangan ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="kondisi" class="form-label">Kondisi *</label>
              <select name="kondisi" id="kondisi" class="form-control">
                <option value="1" <?= $data_asset->kondisi == 1 ? 'selected' : '' ?>>Baik</option>
                <option value="2" <?= $data_asset->kondisi == 2 ? 'selected' : '' ?>>Rusak</option>
                <option value="3" <?= $data_asset->kondisi == 3 ? 'selected' : '' ?>>Dalam Perbaikan</option>
              </select>
            </div>
          </div>
          <?php
          if ($data_asset->t_penyusutan > 0) {
            $disabled = 'disabled';
          } else {
            $disabled = '';
          }
          ?>

          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">Nilai Buku <strong>(*)</strong></label>
              <input type="text" class="form-control uang" name="harga" id="harga" value="<?= $data_asset->nilai_buku ?>" <?= $disabled ?>>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">Sisa Umur (Bulan) <strong>(*)</strong></label>
              <input type="text" class="form-control" name="umur" id="umur" value="<?= $data_asset->sisa_umur ?>" <?= $disabled ?>>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">COA Aset <strong>(*)</strong></label>
              <select name="coa_aset" id="coa_aset" class="form-control select2" <?= $disabled ?>>
                <option value="">Pilih Coa Aset</option>
                <?php foreach ($coa_asset as $c) : ?>
                  <option value="<?= $c->no_sbb ?>" <?= $data_asset->coa_asset == $c->no_sbb ? 'selected' : '' ?>><?= $c->nama_perkiraan ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">COA Beban <strong>(*)</strong></label>
              <select name="coa_beban" id="coa_beban" class="form-control select2" <?= $disabled ?>>
                <option value="">Pilih Coa Beban</option>
                <?php foreach ($coa_beban as $c) : ?>
                  <option value="<?= $c->no_sbb ?>" <?= $data_asset->coa_beban == $c->no_sbb ? 'selected' : '' ?>><?= $c->nama_perkiraan ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">COA Kas <strong>(*)</strong></label>
              <select name="coa_kas" id="coa_kas" class="form-control select2" <?= $disabled ?>>
                <option value="">Pilih Coa Kas</option>
                <?php foreach ($coa_kas as $c) : ?>
                  <option value="<?= $c->no_sbb ?>" <?= $data_asset->coa_kas == $c->no_sbb ? 'selected' : '' ?>><?= $c->nama_perkiraan ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="tujuan" class="form-label">COA Penyusutan <strong>(*)</strong></label>
              <select name="coa_penyusutan" id="coa_penyusutan" class="form-control select2" <?= $disabled ?>>
                <option value="">Pilih Coa Penyusutan</option>
                <?php foreach ($coa_penyusutan as $c) : ?>
                  <option value="<?= $c->no_sbb ?>" <?= $data_asset->coa_penyusutan == $c->no_sbb ? 'selected' : '' ?>><?= $c->nama_perkiraan ?></option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label for="kondisi" class="form-label">Detail Perubahan *</label>
              <textarea name="detail" id="detail" class="form-control"><?= $data_asset->keterangan ?></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-submit">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>