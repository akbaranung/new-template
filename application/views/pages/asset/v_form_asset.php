<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Tambah Aset Baru</h1>
      <div class="card shadow mb-4">
        <div class="card-body">
          <form method="post" action="<?= site_url('asset/store_asset') ?>" enctype="multipart/form-data">
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Kode <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="kode" id="kode">
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Tanggal Perolehan <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="date" class="form-control" name="tgl" id="tgl">
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Jenis Aset <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="jenis" id="jenis" class="form-control select2">
                  <option value="">Pilih Jenis Aset</option>
                  <?php foreach ($jenis_aset as $jenis) : ?>
                    <option value="<?= $jenis->Id ?>"><?= $jenis->nama_jenis ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Nama Aset <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="nama" id="nama">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Foto</label>
              <div class="col-sm-6">
                <input type="file" class="form-control-file" name="foto" id="foto">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Spesifikasi <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="spesifikasi" id="spesifikasi">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Ruangan <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="ruangan" id="ruangan" class="form-control select2">
                  <option value="">Pilih Ruangan</option>
                  <?php foreach ($ruangan as $r) : ?>
                    <option value="<?= $r->Id ?>"><?= $r->keterangan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Kondisi <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="kondisi" id="kondisi" class="form-control">
                  <option value="1">Baik</option>
                  <option value="2">Rusak</option>
                  <option value="3">Dalam Perbaikan</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Harga <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control uang" name="harga" id="harga">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Umur (Bulan) <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="umur" id="umur">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">COA Aset <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="coa_aset" id="coa_aset" class="form-control select2">
                  <option value="">Pilih Coa Aset</option>
                  <?php foreach ($coa as $c) : ?>
                    <option value="<?= $c->no_sbb ?>"><?= $c->nama_perkiraan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">COA Beban <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="coa_beban" id="coa_beban" class="form-control select2">
                  <option value="">Pilih Coa Beban</option>
                  <?php foreach ($coa as $c) : ?>
                    <option value="<?= $c->no_sbb ?>"><?= $c->nama_perkiraan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">COA Kas <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="coa_kas" id="coa_kas" class="form-control select2">
                  <option value="">Pilih Coa Kas</option>
                  <?php foreach ($coa as $c) : ?>
                    <option value="<?= $c->no_sbb ?>"><?= $c->nama_perkiraan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">COA Penyusutan <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="coa_penyusutan" id="coa_penyusutan" class="form-control select2">
                  <option value="">Pilih Coa Penyusutan</option>
                  <?php foreach ($coa as $c) : ?>
                    <option value="<?= $c->no_sbb ?>"><?= $c->nama_perkiraan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group mb-2">
              <a href="<?= site_url('asset') ?>" class="btn btn-warning">Kembali</a>
              <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->