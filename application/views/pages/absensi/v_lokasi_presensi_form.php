<!-- Leaflet  -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">

<style>
  .col-xs-3 {
    width: 25%;
    background-color: #004e81;
  }

  .row {
    margin-left: 0px;
  }

  .container-fluid {
    padding-right: 0px;
    padding-left: 0px
  }

  .btn_footer_panel .tag_ {
    padding-top: 37px;
  }

  #map {
    height: 400px;
    width: 100%;
  }

  /* Green */
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Lokasi Presensi</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title">
            <strong>Form Lokasi Presensi</strong>
          </p>
        </div>
        <div class="card-body">
          <div align="center">
            <font style="font-size:17px;">
              <?php
              if ($this->uri->segment(3) == null) { ?>
                Tambah
              <?php
              } else {
              ?>
                Ubah
              <?php
              }
              ?>
              Lokasi Presensi
              <hr />

            </font>
          </div>
          <div style="margin-bottom: 10px;">
            <div id="map"></div>
          </div>
          <?php
          if ($this->uri->segment(3) == null) { ?>
            <form method="POST" action="<?= base_url('absensi/proses_tambah_lokasi_presensi') ?>">
              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-12" style="margin-bottom: 10px;">
                  <label for="nama_lokasi">Nama Lokasi</label>
                  <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi">
                </div>
                <div class="col-md-12" style="margin-bottom: 10px;">
                  <label for="alamat_lokasi">Alamat Lokasi</label>
                  <textarea class="form-control" name="alamat_lokasi" id="alamat_lokasi"></textarea>
                </div>
              </div>

              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-3">
                  <label for="tipe_lokasi">Tipe Lokasi</label>
                  <input type="text" class="form-control" name="tipe_lokasi" id="tipe_lokasi">
                </div>
                <div class="col-md-3">
                  <label for="latitude_lokasi">Latitude Lokasi</label>
                  <input type="number" step="any" class="form-control" name="latitude_lokasi" id="latitude_lokasi">
                </div>
                <div class="col-md-3">
                  <label for="longitude_lokasi">Longitude Lokasi</label>
                  <input type="number" step="any" class="form-control" name="longitude_lokasi" id="longitude_lokasi">
                </div>
                <div class="col-md-3">
                  <label for="radius_lokasi">Radius Lokasi (Meter)</label>
                  <input type="number" min="1" class="form-control" name="radius_lokasi" id="radius_lokasi" value="100">
                </div>
              </div>

              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-4">
                  <label for="zona_waktu">Zona Waktu</label>
                  <select class="form-control" name="zona_waktu" id="zona_waktu">
                    <option value="WIB">WIB</option>
                    <option value="WIT">WIT</option>
                    <option value="WITA">WITA</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="jam_masuk">Jam Masuk</label>
                  <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" value="09:00:00">
                </div>
                <div class="col-md-4">
                  <label for="jam_pulang">Jam Pulang</label>
                  <input type="time" class="form-control" name="jam_pulang" id="jam_pulang" value="17:00:00">
                </div>
              </div>

              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-warning">Reset</button>
            </form>
          <?php
          } else {
          ?>
            <form method="POST" action="<?= base_url('absensi/proses_update_lokasi_presensi') ?>">
              <input type="hidden" name="id_lokasi" id="id_lokasi" value="<?= $detail->id ?>">
              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-12">
                  <label for="nama_lokasi">Nama Lokasi</label>
                  <input type="text" class="form-control" name="nama_lokasi" id="nama_lokasi" value="<?= $detail->nama_lokasi ?>">
                </div>
                <div class="col-md-12">
                  <label for="alamat_lokasi">Alamat Lokasi</label>
                  <textarea class="form-control" name="alamat_lokasi" id="alamat_lokasi"><?= $detail->alamat_lokasi ?></textarea>
                </div>
              </div>

              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-3">
                  <label for="tipe_lokasi">Tipe Lokasi</label>
                  <input type="text" class="form-control" name="tipe_lokasi" id="tipe_lokasi" value="<?= $detail->tipe_lokasi ?>">
                </div>
                <div class="col-md-3">
                  <label for="latitude_lokasi">Latitude Lokasi</label>
                  <input type="number" step="any" class="form-control" name="latitude_lokasi" id="latitude_lokasi" value="<?= $detail->latitude ?>">
                </div>
                <div class="col-md-3">
                  <label for="longitude_lokasi">Longitude Lokasi</label>
                  <input type="number" step="any" class="form-control" name="longitude_lokasi" id="longitude_lokasi" value="<?= $detail->longitude ?>">
                </div>
                <div class="col-md-3">
                  <label for="radius_lokasi">Radius Lokasi (Meter)</label>
                  <input type="number" min="0" class="form-control" name="radius_lokasi" id="radius_lokasi" value="<?= $detail->radius * 1000 ?>">
                </div>
              </div>

              <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-4">
                  <label for="zona_waktu">Zona Waktu</label>
                  <select class="form-control" name="zona_waktu" id="zona_waktu">
                    <option <?php if ($detail->zona_waktu == "WIB") "Selected" ?> value="WIB">WIB</option>
                    <option <?php if ($detail->zona_waktu == "WIT") "Selected" ?> value="WIT">WIT</option>
                    <option <?php if ($detail->zona_waktu == "WITA") "Selected" ?> value="WITA">WITA</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="jam_masuk">Jam Masuk</label>
                  <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" value="<?= $detail->jam_masuk ?>">
                </div>
                <div class="col-md-4">
                  <label for="jam_pulang">Jam Pulang</label>
                  <input type="time" class="form-control" name="jam_pulang" id="jam_pulang" value="<?= $detail->jam_pulang ?>">
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="reset" class="btn btn-warning">Reset</button>
            </form>
          <?php
          }
          ?>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->