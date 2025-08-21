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
            <?php
            // if ($total_user <= 4) {
            ?>
            <!-- <a href="<?= base_url('perusahaan/add_user') ?>" class="btn btn-primary">Add User</a> -->
            <?php
            // } else {
            ?>
            <?php

            $this->db->from('users');
            $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
            $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
            $this->db->where('nama_jabatan !=', 'Super Admin');
            $total_user = $this->db->get()->num_rows(); // Get the number of rows

            $max_users_for_100_percent = 4; // Define your maximum limit


            // $max_users_for_100_percent = 5; // Define your maximum limit

            // $this->cb->from('v_coa_all');
            // $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
            // $cek_coa_cabang = $this->cb->get()->num_rows();

            $this->cb->from('v_coa_all');
            $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
            // Add the OR conditions
            $this->cb->group_start(); // Start a WHERE group for the OR conditions
            // $this->cb->where('no_sbb', '23014');
            // $this->cb->or_where('no_sbb', '23011');
            $this->cb->where_not_in('no_sbb', ['23014', '23011']);
            $this->cb->group_end(); // End the WHERE group
            $cek_coa_cabang = $this->cb->get()->num_rows();

            $i = $total_user;

            $this->cb->from('t_cabang');
            $this->cb->where('uid', $this->session->userdata('kode_cabang'));
            $cabang_now = $this->cb->get()->row();
            $cek_saldo_awal = $cabang_now->generate_sawal;

            // $this->cb->from('t_cabang');

            // if ($total_user < 4 || $cek_coa_cabang == 0) {
            if ($total_user > 4 || $cek_saldo_awal == 1) {
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
            <button onclick="onResetCutiAll()" class="btn btn-pink">
              Reset Cuti
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
              </svg>
            </button>
            <?php if ($this->session->userdata('is_premium')) { ?>
              <span style="float: right;">
                <b>
                  (Kuota User Tersisa <?= $limit_user - $total_user_perusahaan ?>)
                  <?=
                  $total_user_perusahaan . '/' . $limit_user;
                  ?>
                </b>
              </span>
            <?php } ?>
          </div>
          <div class="table-responsive">
            <table id="user-table" class="table table-sm table-striped table-bordered" style="width:100%">
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