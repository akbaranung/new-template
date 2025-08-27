<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">List Pengecualian Penyusutan</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="<?= site_url('asset/list_penyusutan') ?>" class="btn btn-warning">Kembali</a>
          <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#tambahPengecualian">Tambah Pengecualian</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <!-- <div style="min-width: 951px;"> -->
            <table class="table table-sm table-hover" style="width: 100% !important;" id="tablePenyusutanPengecualian">
              <thead style="background-color:#3498db;">
                <tr>
                  <th style="color: white;">No.</th>
                  <th style="color: white;">Nama Asset</th>
                  <th style="color: white;">Kode Asset</th>
                  <th style="color: white;">Spesifikasi</th>
                  <th style="color: white;">#</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
            <!-- </div> -->
          </div>

        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="tambahPengecualian">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Tambah Pengecualian
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('asset/tambah_pengecualian') ?>">
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12 mt-3">
              <label for="form-label">Nama Asset</label>
              <select name="asset_pengecualian[]" id="asset" class="form-control select2" multiple>
                <?php foreach ($asset_list as $al) { ?>
                  <option value="<?= $al->Id ?>"><?= $al->nama_asset . ' - ' . $al->kode ?></option>
                <?php } ?>
              </select>
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