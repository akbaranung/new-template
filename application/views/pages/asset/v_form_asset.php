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


            <div id="image-preview" class="form-group row d-none mt-1">
              <label class="col-sm-3 col-form-label">Preview</label>
              <div class="col-sm-6 div-foto">
                <img id="preview-image" src="#" alt="Image Preview" style="display: none; max-width: 450px; height: auto;">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Foto <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                  <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                </svg></label>
              <div class="col-sm-6 div-foto">
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
              <label for="tujuan" class="col-sm-3 col-form-label">Nilai Bukau <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control uang" name="harga" id="harga">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Sisa Umur (Bulan) <strong>(*)</strong></label>
              <div class="col-sm-6">
                <input type="text" class="form-control" name="umur" id="umur">
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">COA Aset <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="coa_aset" id="coa_aset" class="form-control select2">
                  <option value="">Pilih Coa Aset</option>
                  <?php foreach ($coa_asset as $c) : ?>
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
                  <?php foreach ($coa_beban as $c) : ?>
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
                  <?php foreach ($coa_kas as $c) : ?>
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
                  <?php foreach ($coa_penyusutan as $c) : ?>
                    <option value="<?= $c->no_sbb ?>"><?= $c->nama_perkiraan ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="tujuan" class="col-sm-3 col-form-label">Penjurnalan <strong>(*)</strong></label>
              <div class="col-sm-6">
                <select name="penjurnalan" id="penjurnalan" class="form-control select2">
                  <option value="">Pilih Penjurnalan</option>
                  <option value="1">YA</option>
                  <option value="0">TIDAK</option>
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