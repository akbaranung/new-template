<!-- bootstrap-daterangepicker -->
<link href="<?php echo base_url(); ?>src/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<!-- Custom Theme Style -->
<link href="<?php echo base_url(); ?>src/build/css/custom.min.css" rel="stylesheet">

<!-- DataTables -->
<link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<link href="<?= base_url() ?>src/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>src/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>src/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>src/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>src/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

<!-- footer menu -->
<link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/header.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/icons.css">

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
    background-color: #004e81;
    color: white;
  }

  .col-centered {
    float: none;
    margin: 0 auto;
  }
</style>

<!-- Start content-->
<div class="right_col" role="main">
  <div class="x_panel card">
    <div class="row text-center">
      <div class="col-md-3">
        <button class="btn btn-primary btn-block" onclick="showUser()">User List</button>
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary btn-block" onclick="showTeam()">Team List</button>
      </div>
      <?php
      if ($this->session->userdata('level_jabatan') >= 3) {
      ?>
        <div class="col-md-3">
          <button class="btn btn-primary btn-block" onclick="showApproval()">Approval List</button>
        </div>
      <?php
      }
      ?>
      <div class="col-md-3">
        <button class="btn btn-success btn-block" onclick="showExport()"><i class="fa fa-file-excel-o"></i> Export List</button>
      </div>
    </div>
  </div>


  <div class="x_panel card" id="user">
    <?php if ($this->session->flashdata('success_reset')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_reset'); ?>
      </div>
    <?php } ?>
    <?php if ($this->session->flashdata('msg')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
      </div>
    <?php } ?>


    <!--div class="alert alert-info">Daftar Surat Kuasa </div-->
    <div align="center">
      <font color="brown">Absensi List User</font><br><br>
    </div>

    <div class="table-responsive">
      <table id="table1" class="table table-striped" style="width: 100%;">
        <thead>
          <tr>
            <th bgcolor="#34495e">
              <font color="white">No.</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nip</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nama</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tanggal</font>
            </th>

            <th bgcolor="#34495e">
              <font color="white">Waktu</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Status</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Lokasi</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tipe</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Gambar</font>
            </th>
            <!--th bgcolor="#004e81"><font color="white">Status</font></th-->
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- Finish content-->

  <!-- Start content 2-->
  <!-- <div class="" id="team" role="main"> -->

  <div class="x_panel card" id="team" style="display: none;">
    <?php if ($this->session->flashdata('success_reset')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_reset'); ?>
      </div>
    <?php } ?>
    <?php if ($this->session->flashdata('msg')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
      </div>
    <?php } ?>


    <!--div class="alert alert-info">Daftar Surat Kuasa </div-->
    <div align="center">

      <font color="brown">Absensi List Team</font><br><br>
    </div>


    <div class="table-responsive">
      <table id="table2" class="table table-striped" style="width: 100%;">
        <thead>
          <tr>
            <th bgcolor="#34495e">
              <font color="white">No.</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nip</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nama</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tanggal</font>
            </th>

            <th bgcolor="#34495e">
              <font color="white">Waktu</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Status</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Lokasi</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tipe</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Gambar</font>
            </th>
            <!--th bgcolor="#004e81"><font color="white">Status</font></th-->
          </tr>
        </thead>

      </table>
    </div>
  </div>

  <!-- Finish content 2-->


  <!-- Start content 3-->
  <!-- <div class="" id="approval" role="main"> -->

  <div class="x_panel card" id="approval" style="display: none;">
    <?php if ($this->session->flashdata('success_reset')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_reset'); ?>
      </div>
    <?php } ?>
    <?php if ($this->session->flashdata('msg')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
      </div>
    <?php } ?>


    <!--div class="alert alert-info">Daftar Surat Kuasa </div-->
    <div align="center">

      <font color="brown">Absensi List Approval</font><br><br>
    </div>


    <div class="table-responsive">
      <table id="table3" class="table table-striped" style="width: 100%;">
        <thead>
          <tr>
            <th bgcolor="#34495e">
              <font color="white">No.</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nip</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Nama</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tanggal</font>
            </th>

            <th bgcolor="#34495e">
              <font color="white">Waktu</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Status</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Lokasi</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Tipe</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Gambar</font>
            </th>
            <th bgcolor="#34495e">
              <font color="white">Approval</font>
            </th>
            <!--th bgcolor="#004e81"><font color="white">Status</font></th-->
          </tr>
        </thead>

      </table>
    </div>
  </div>

  <div class="x_panel card" id="excel" style="display: none;">
    <?php if ($this->session->flashdata('success_reset')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_reset'); ?>
      </div>
    <?php } ?>
    <?php if ($this->session->flashdata('msg')) { ?>
      <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong>Success!</strong> <?php echo $this->session->flashdata('msg'); ?>
      </div>
    <?php } ?>


    <!--div class="alert alert-info">Daftar Surat Kuasa </div-->
    <div align="center">

      <font color="brown">Absensi List Approval</font><br><br>
    </div>


    <div>
      <div class="content" style="cursor: pointer;  margin: 0;">
        <form class="form" id="form_export" action="<?= base_url('absensi/process_export') ?>" method="POST">
          <div class="row">
            <div class="col-md-6">
              <label for="" class="label">Tanggal Absensi</label>
              <input type="text" class="form-control month-picker" name="tanggal" id="tanggal_export_absensi">
            </div>
            <div class="col-md-6">
              <label for="" class="label">Data</label>
              <select class="form-control" name="data_absensi" id="data_absensi">
                <option value="All" selected>All</option>
                <option value="User">User</option>
                <option value="Team">Team</option>
                <!-- <option value="Team">Team</option> -->
              </select>

            </div>
          </div>
          <br>
          <button class="btn btn-success rounded">Export</button>
        </form>
        <!-- <button class="btn btn-success rounded" onclick="proccess_export()">Export</button> -->
      </div>
    </div>
  </div>
  <!-- Finish content 3-->
</div>

<!-- DataTables -->
<script src="<?= base_url() ?>src/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="<?= base_url() ?>src/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>

<!-- DatePicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tanggal_export_absensi').datepicker({
      format: "mm/yyyy",
      startView: "months",
      minViewMode: "months",
      autoclose: true
    });
    $("a[id='button-reset-cuti']").click(function(e) {
      if (!confirm('Apakah anda yakin ingin mereset cuti?')) {
        e.preventDefault();
      }

    });

    <?php if ($this->session->flashdata('error')) { ?>
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?= $this->session->flashdata('error') ?>',
      })
    <?php } ?>

    $("button[id='btn-hapus-tgl-libur']").click(function(e) {
      if (!confirm('Apakah anda yakin ingin menghapus tanggal libur tersebut?')) {
        e.preventDefault();
      }
    });


    $('#table1').dataTable({
      // responsive: true,
      rowReorder: {
        selector: 'td:nth-child(2)'
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo site_url('absensi/ajax_list') ?>",
        type: "POST"
      },
      order: [],
      iDisplayLength: 10,
      columnDefs: [{
        // targets: 8,
        orderable: false
      }]
    });

    $('#table2').dataTable({
      // responsive: true,
      rowReorder: {
        selector: 'td:nth-child(3)'
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo site_url('absensi/ajax_list2') ?>",
        type: "POST"
      },
      order: [],
      iDisplayLength: 10,
      columnDefs: [{
        // targets: 8,
        orderable: false
      }]
    });

    $('#table3').dataTable({
      // responsive: true,
      rowReorder: {
        selector: 'td:nth-child(3)'
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo site_url('absensi/ajax_list3') ?>",
        type: "POST"
      },
      order: [],
      iDisplayLength: 10,
      columnDefs: [{
        targets: 8,
        orderable: false
      }]
    });

  })

  function showUser() {
    document.getElementById('user').style.display = 'flex';
    document.getElementById('team').style.display = 'none';
    document.getElementById('excel').style.display = 'none';
    document.getElementById('approval').style.display = 'none';
  }

  function showTeam() {
    document.getElementById('user').style.display = 'none';
    document.getElementById('team').style.display = 'flex';
    document.getElementById('excel').style.display = 'none';
    document.getElementById('approval').style.display = 'none';
  }

  function showApproval() {
    document.getElementById('user').style.display = 'none';
    document.getElementById('team').style.display = 'none';
    document.getElementById('excel').style.display = 'none';
    document.getElementById('approval').style.display = 'flex';
  }

  function showExport() {

    document.getElementById('user').style.display = 'none';
    document.getElementById('team').style.display = 'none';
    document.getElementById('approval').style.display = 'none';
    document.getElementById('excel').style.display = 'flex';

  }
</script>

</body>

</html>