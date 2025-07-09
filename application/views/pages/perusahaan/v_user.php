<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

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

  tr>th {
    /* background-color: #e91f62; */
    background-color: #3e51b4;
    color: white;
  }

  .col-centered {
    float: none;
    margin: 0 auto;
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">User List</h1>
      <?php
      if ($total_user < 5) {
      ?>
        <div class="card shadow mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <!-- <p class="card-title mb-0"><strong>Input 4 User Role (Staff, Supervisor, Comptroller, Direktur) untuk membuka akses menu! (<?= $total_user . '/' . $max_users_for_100_percent ?>)</strong></p> -->
            <p class="card-title mb-0"><strong>Tambahkan 4 User Role (Staff, Manager, Comptroller, Direktur) untuk Memulai Hidup Baru! (<?= $total_user . '/' . $max_users_for_100_percent ?>)</strong></p>

          </div>
          <div class="card-body">
            <div class="progress">
              <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"><?= $percentage ?>%</div>
            </div>
          </div>
        </div>

      <?php
      }
      ?>
      <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title mb-0"><strong>List User</strong></p>
          <?php
          if ($total_user < 5) {
          ?>
            <a href="<?= base_url('perusahaan/add_user') ?>" class="btn btn-primary">Add User</a>
          <?php
          } else {
          ?>
            <a href="#" id="addUserBtn" class="btn btn-primary">
              Add User
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
              </svg>
            </a>
          <?php
          }
          ?>
        </div>
        <div class="card-body" id="user">
          <div class="table-responsive">
            <table id="user-table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Nip</th>
                  <th class="text-center">Nama Jabatan</th>
                  <!-- <th class="text-center">User Access</th> -->
                  <th class="text-center">Status</th>
                  <th class="text-center">#</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->