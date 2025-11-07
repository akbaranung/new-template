<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
  .col-xs-3 {
    width: 25%;
    background-color: #004e81;
  }


  .btn_footer_panel .tag_ {
    padding-top: 37px;
  }

  tr>th {
    background-color: #004e81;
    color: white;
  }

  .col-centered {
    float: none;
    margin: 0 auto;
  }

  .dt-length label {
    margin-left: 8px;
    /* Adjust this value (e.g., 5px, 10px, 0.5em) as needed */
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Absensi List</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>List Absensi</strong></p>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-3">
              <button class="btn btn-primary btn-block" onclick="showUser()">User List</button>
            </div>
            <div class="col-md-3">
              <button class="btn btn-primary btn-block" onclick="showTeam()">Team List</button>
            </div>
            <?php
            if ($this->session->userdata('level_jabatan') >= 3) {
              $notif = '';
              if ($approval > 0) {
                $notif = '<span class="badge badge-pill badge-danger badge-counter ml-2">' . $approval . '</span>';
              }
            ?>
              <div class="col-md-3">
                <button class="btn btn-primary btn-block" onclick="showApproval()">Approval List <?= $notif ?></button>
              </div>
            <?php
            }

            if ($user->akses_export_absensi_list) {
            ?>
              <div class="col-md-3">
                <button class="btn btn-pink btn-block" onclick="showExport()"><i class="fa fa-file-excel-o"></i> Export List</button>
              </div>
            <?php
            }
            ?>
          </div>
        </div>
        <div class="card-body" id="user">
          <div class="table-responsive">
            <table id="user-table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nip</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Tanggal</th>
                  <th class="text-center">Waktu</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Lokasi</th>
                  <th class="text-center">Tipe</th>
                  <th class="text-center">Gambar</th>
                  <!-- <th class="text-center">Action</th> -->
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="card-body" id="team" style="display: none;">
          <div class="table-responsive">
            <table id="team-table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nip</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Tanggal</th>
                  <th class="text-center">Waktu</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Lokasi</th>
                  <th class="text-center">Tipe</th>
                  <th class="text-center">Gambar</th>
                  <!-- <th class="text-center">Action</th> -->
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="card-body" id="approval" style="display: none;">
          <div class="table-responsive">
            <table id="approval-table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nip</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Tanggal</th>
                  <th class="text-center">Waktu</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Lokasi</th>
                  <th class="text-center">Tipe</th>
                  <th class="text-center">Gambar</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="card-body" id="excel" style="display: none;">
          <div>
            <div class="content" style="cursor: pointer;  margin: 0;">
              <form class="form" id="form_export" action="<?= base_url('absensi/process_export_new') ?>" method="POST">
                <div class="row">
                  <div class="col-md-6">
                    <label for="" class="label">Tanggal Absensi</label>
                    <!-- <input type="text" class="form-control month-picker" name="tanggal" id="tanggal_export_absensi"> -->
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" value="<?= date('Y-m-d') ?>">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir" value="<?= date('Y-m-d') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <br>
                    <div class="form-group">
                      <label for="">Data</label>
                      <select class="form-control" name="data_absensi" id="data_absensi">
                        <option value="All" selected>All</option>
                        <option value="User">User</option>
                        <option value="Team">Team</option>
                        <option value="Cabang">Cabang</option>
                        <!-- <option value="Team">Team</option> -->
                      </select>
                    </div>

                  </div>
                </div>
                <br>
                <button class="btn btn-success rounded">Export</button>
              </form>
              <!-- <button class="btn btn-success rounded" onclick="proccess_export()">Export</button> -->
            </div>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->