<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="<?= base_url('assets/') ?>progress-bar-dashboard.css">

<style>
  .col-xs-3 {
    width: 25%;
    background-color: #1b68ff;
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

  .dt-length label {
    margin-left: 8px;
    /* Adjust this value (e.g., 5px, 10px, 0.5em) as needed */
  }

  .triangle-right-success {
    margin-left: 4px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    /* border-left: 12px solid #3ad29f; */
    border-left: 12px solid #1b68ff;
    /* Green for success */
    border-bottom: 8px solid transparent;
  }

  .triangle-right-primary {
    margin-left: 4px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    /* border-left: 12px solid #1b68ff; */
    border-left: 12px solid #e81f63;

    /* Blue for primary */
    border-bottom: 8px solid transparent;
  }

  .triangle-right-secondary {
    margin-left: 4px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-left: 12px solid #6c757d;
    /* Grey for secondary */
    border-bottom: 8px solid transparent;
  }

  table.dataTable>thead>tr>th {
    padding: 0 5px 0 5px;
    height: 30px;
  }

  table.dataTable>tbody>tr>td {
    padding: 1px 5px 1px 5px;
  }


  .btn-di-td {
    padding: 0.125rem 0.25rem;
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error!</strong> <?= $this->session->flashdata('error'); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <!-- <strong><?= $this->session->flashdata('error'); ?>!</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"> -->
            <span aria-hidden="true">x</span>
          </button>
        </div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Success!</strong> <?= $this->session->flashdata('success'); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
      <?php endif; ?>
      <h1 class="page-title">User List</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title mb-0"><strong>List User</strong></p>
        </div> -->
        <div class="card-body" id="user">
          <!-- <div class="d-flex justify-content-end align-items-center"> -->
          <!-- <div class="d-flex align-items-center"> -->
          <div>

            <a href="#" id="addUserBtn" class="btn btn-primary">
              Add Nasabah
              <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
              </svg> -->
            </a>

            <!-- <button onclick="onResetCutiAll()" class="btn btn-pink">
              Reset Cuti
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
              </svg>
            </button> -->
          </div>
          <div class="table-responsive">
            <table id="user-table" class="table table-sm table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>nama</th>
                  <th>Alamat</th>
                  <th>No Ktp</th>
                  <th>No Telp</th>
                  <th>Ahli Waris</th>
                  <th>Kode Pos</th>
                  <th>Nama Ibu Kandung</th>
                  <th>Pekerjaan</th>
                  <th>Kode AO</th>
                  <th>Nama Panggilan</th>
                  <th>Tgl Lahir</th>
                  <th>Tempat Lahir</th>
                  <th>Kota</th>
                  <th>Tgl Pendaftaran</th>
                  <th>Tipe Nasabah</th>
                  <th>Nama Segmen</th>
                  <th>Warga Negara</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Modal Hapus Buku -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="resetCuti">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Reset Cuti
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('cuti/resetCutiAll') ?>">
        <div class="modal-body">
          <p><strong>Masukan password anda terlebih dahulu untuk melakukan reset cuti!</strong></p>
          <div class="form-group row">
            <div class="col-12">
              <label for="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-reset-cuti">
            Proses
          </button>
        </div>
      </form>
    </div>
  </div>
</div>