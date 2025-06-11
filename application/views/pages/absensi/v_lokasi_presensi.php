<style>

</style>
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Lokasi Presensi</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>List Lokasi Presensi</strong></p>
        </div>
        <div class="card-body">
          <a class="btn btn-primary mb-5" href="<?= base_url('absensi/add_lokasi_presensi') ?>">Tambah Lokasi Presensi</a>
          <!-- <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
            <div class="row mb-4">
              <div class="col-md-3 col-xs-12">
                <input type="month" class="form-control" name="periode" value="<?= $this->input->post('periode') ?>">
              </div>
              <div class="col-md-3 col-xs-12">
                <button type="submit" class="btn btn-primary">Closing EoM</button>
              </div>
            </div>
          </form> -->
          <div class="table-responsive">
            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nama Lokasi</th>
                  <th class="text-center">Alamat Lokasi</th>
                  <th class="text-center">Tipe Lokasi</th>
                  <th class="text-center">Latitude</th>
                  <th class="text-center">Longitude</th>
                  <th class="text-center">Radius</th>
                  <th class="text-center">Jam Masuk</th>
                  <th class="text-center">Jam Pulang</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->