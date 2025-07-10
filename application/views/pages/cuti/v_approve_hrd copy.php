<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/favicon.ico" type="image/ico" />
    <title>Mlejit Office | Business Development</title>


    <!-- Select2 -->
    <link href="<?php echo base_url(); ?>src/vendors/select2/dist/css/select2.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>src/vendors/select2/dist/css/select2.css" rel="stylesheet" />
    <!-- Bootstrap -->
    <link href="<?php echo base_url(); ?>src/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url(); ?>src/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo base_url(); ?>src/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?php echo base_url(); ?>src/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <link href="cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="<?= base_url() ?>src/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>src/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="<?php echo base_url(); ?>src/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="<?php echo base_url(); ?>src/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
    <!-- bootstrap-daterangepicker -->
    <link href="<?php echo base_url(); ?>src/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- bootstrap-datetimepicker -->
    <link href="<?php echo base_url(); ?>src/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="<?php echo base_url(); ?>src/build/css/custom.min.css" rel="stylesheet">
    <!-- footer menu -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/header.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet" />

    <style>
        .btn_on {
            position: fixed;
            bottom: 20px;
            right: 0;
            z-index: 1000;
        }

        .col-xs-3 {
            width: 25%;
            background-color: #008080;
        }

        .container-fluid {
            padding-right: 0px;
            padding-left: 0px
        }

        .btn_footer_panel .tag_ {
            padding-top: 37px;
        }

        .colored-toast.swal2-icon-error {
            background-color: #f27474 !important;
            color: white;
        }

        .colored-toast.swal2-icon-success {
            background-color: #a5dc86 !important;
            color: white;
        }

        .colored-toast.swal2-icon-info {
            background-color: #3fc3ee !important;
        }

        span.aksi {
            cursor: pointer;
        }

        .badge-success {
            background-color: green;
        }

        .badge-danger {
            background-color: red;
        }

        .badge-warning {
            background-color: orange;
        }

        th,
        td {
            padding: 5px;
        }
    </style>
</head>

<header class="header_area sticky-header">
    <!-- footer menu -->
    <div class="footer_panel">
        <div class="container-fluid text-center">
            <div class="row">
                <div class="col-xs-3 btn_footer_panel">
                    <a href="<?php echo base_url(); ?>app/antrian_input">
                        <i class="la-i la-i-m la-i-home"></i>
                        <div class="tag_">
                            <p color="white">Create</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-3 btn_footer_panel">
                    <a href="<?php echo base_url(); ?>app/antrian_panggil">
                        <i class="la-i la-i-m la-i-order"></i>
                        <div class="tag_">
                            <p color="white">Manage</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-3 btn_footer_panel">
                    <a href="<?php echo base_url(); ?>app/antrian_monitor">
                        <i class="la-i la-i-m la-i-notif">
                        </i>
                        <div class="tag_">
                            <p color="white">Monitor</p>
                        </div>
                    </a>
                </div>
                <div class="col-xs-3 btn_footer_panel">
                    <a href="<?php echo base_url(); ?>login/logout">
                        <i class="la-i la-i-m la-i-akun"></i>
                        <div class="tag_">
                            <font color="white">Logout</font>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <!-- footer menu -->
</header>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="<?php echo base_url(); ?>" class="site_title"><img src="<?php echo base_url(); ?>img/boc_logo.png" alt="..." height="42" width="60"><span>
                                Mlejit Office</span></a>
                    </div>

                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="<?php echo base_url(); ?>src/images/img.jpg" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span>Welcome,</span>
                            <h2><?php echo $this->session->userdata('nama'); ?></h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <?php $this->load->view('side_menu.php'); ?>
                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->

                    <!-- /menu footer buttons -->
                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo base_url(); ?>src/images/img.jpg" alt=""><?php echo $this->session->userdata('nama'); ?>
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li><a href="javascript:;"> Profile</a></li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="badge bg-red pull-right">50%</span>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li><a href="javascript:;">Help</a></li>
                                    <li><a href="<?php echo base_url(); ?>login/logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                </ul>
                            </li>
                            <li role="presentation" class="dropdown">
                                <a href="<?php echo base_url() . "app/inbox"; ?>" class="dropdown-toggle info-number">
                                    <i class="fa fa-envelope-o"></i>
                                    <?php if ($count_inbox == 0) { ?>
                                        <span class="badge bg-green"><?php echo $count_inbox; ?></span>
                                    <?php } else { ?>
                                        <span class="badge bg-red"><?php echo $count_inbox; ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <?php include 'notif_tello.php' ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top navigation -->

            <!-- page content -->
            <div class="right_col" role="main">
                <div class="x_panel card">
                    <div class="title">
                        <h3><b>Data Approval HRD</b></h3>
                    </div>
                    <div style="display: flex;">
                        <a href="<?= site_url('cuti/view') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</a>
                        <a href="<?= base_url('cuti/export_cuti/' . $this->input->get('filter')) ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#cutiModalHrd"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Cuti Manual</button>
                    </div>
                    <div style="margin-top: 2em;">
                        <form action="" method="get">
                            <div class='input-group date' id='myDatepicker2' style="width: 40%;">
                                <input type='text' id='filter' name='filter' class="form-control" placeholder="yyyy-mm" value="<?= $this->input->get('filter') ?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Search</button>
                        </form>
                    </div>
                    <div class="table-responsive" style="margin-top: 2em;">
                        <table id="table-approve-hrd" class="table table-striped jambo_table bulk_action" width="100%">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title">No.</th>
                                    <th class="column-title">Nama</th>
                                    <th class="column-title">Jenis Cuti</th>
                                    <th class="column-title">Alasan Cuti</th>
                                    <th class="column-title">Tanggal Pengajuan</th>
                                    <th class="column-title">Mulai Cuti</th>
                                    <th class="column-title">Jumlah Cuti</th>
                                    <th class="column-title">Atasan</th>
                                    <th class="column-title">Status</th>
                                    <th class="column-title">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Cuti Manual HRD -->
    <div class="modal fade " id="cutiModalHrd">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <!-- header-->
                <div class="modal-header">
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Form Pengajuan Cuti Online</h4>
                </div>
                <!--body-->
                <div class="modal-body">
                    <form action="" id="formCutiHrd" method="post">
                        <div class="form-group">
                            <label for="nama_karyawan">Nama Karyawan</label>
                            <select name="nama_karyawan" id="nama_karyawan" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Karyawan --</option>
                                <?php foreach ($karyawan as $k) : ?>
                                    <option value="<?= $k->nip ?>"><?= $k->nama ?> [<?= $k->nip ?>]</option>
                                <?php endforeach ?>
                            </select>
                            <span id="err_namakar" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="pengganti_cuti">Pengganti</label>
                            <select name="pengganti_cuti" id="pengganti_cuti" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Pengganti --</option>
                            </select>
                            <span id="err_namapeng" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="jenis_cuti">Jenis Cuti</label>
                            <select class="form-control select2" id="jenis_cuti" name="jenis_cuti" style="width:100%;">
                                <option value="">-- Pilih Jenis Cuti --</option>
                                <?php foreach ($all_jenis as $row) : ?>
                                    <option value="<?= $row['Id'] ?>"><?= $row['nama_jenis'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <span id="err_jenis_cuti" class="text-danger"></span>
                        </div>
                        <div class="form-group" id="select_detail">
                            <label for="detail_cuti">Detail Cuti</label>
                            <select class="form-control select2" id="detail_cuti" name="detail_cuti" style="width: 100%;">
                                <option value="">-- Detail Cuti --</option>
                            </select>
                            <span id="err_detail_detail" class="text-danger"></span>
                        </div>
                        <div class="form-group" id="file_pendukung_form">
                            <label for="file_pendukung">Dokumen Pendukung</label>
                            <input type="file" class="form-control" id="file_pendukung" name="file_pendukung">
                            <span id="err_file_pendukung" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="alamat_cuti">Alamat Cuti</label>
                            <textarea name="alamat_cuti" id="alamat_cuti" class="form-control"></textarea>
                            <span id="err_alamat_cuti" class="text-danger"></span>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" id="error_mulai">
                                    <label for="mulai_cuti">Dari</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" placeholder="Mulai Cuti" id="mulai_cuti" name="mulai_cuti">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                    <span id="err_mulai_cuti" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="error_akhir">
                                    <label for="akhir_cuti">Sampai</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" placeholder="Akhir Cuti" id="akhir_cuti" name="akhir_cuti">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                    <span id="err_akhir_cuti" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group" id="error_jumlah">
                                    <label for="jumlah_cuti">Jumlah Cuti</label>
                                    <input type="text" class="form-control" placeholder="Jumlah Cuti" id="jumlah_cuti" name="jumlah_cuti" readonly>
                                    <span id="err_jumlah" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="error_alasan">
                            <label for="alasan_cuti">Alasan Cuti</label>
                            <input type="text" class="form-control" placeholder="Alasan Cuti" id="alasan_cuti" name="alasan_cuti">
                            <span id="err_alasan_cuti" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="sisa_cuti">Sisa Cuti Reguler</label>
                            <input type="text" class="form-control" placeholder="Sisa cuti" id="sisa_cuti" name="sisa_cuti" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_atasan">Atasan</label>
                            <input type="hidden" readonly class="form-control" placeholder="Nip Atasan" id="nip_atasan" name="nip_atasan">
                            <input type="text" readonly class="form-control" placeholder="Nama Atasan" id="nama_atasan" name="nama_atasan">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
                            <button type="button" class="btn btn-primary" id="btn-form-cuti-hrd"><i class="fa fa-paper-plane" aria-hidden="true"></i> Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Cuti -->
    <div class="modal fade " id="detail-cuti">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <!-- header-->
                <div class="modal-header">
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Detail Cuti</h4>
                </div>
                <!--body-->
                <div class="modal-body">
                    <table class="table" width="100%" id="detail-cuti-byID">

                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Update Cuti -->
    <div class="modal fade" id="modal-update-cuti-hrd">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <!-- header-->
                <div class="modal-header">
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Cuti</h4>
                </div>
                <!--body-->
                <div class="modal-body">
                    <form action="<?= base_url('cuti/update_cuti_hrd/') ?>" id="form-update-cuti-hrd" method="post">
                        <div class="form-group">
                            <input type="hidden" readonly class="form-control" id="id_cuti" name="id_cuti">
                        </div>
                        <div class="form-group" id="error_jenis">
                            <label for="status_cuti">Status</label>
                            <select class="form-control select2" id="status_cuti" name="status_cuti" style="width:100%;">
                                <option value=""> -- Pilih Status Cuti --</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                            <span id="err_status_cuti" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan (Opsional)</label>
                            <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                        </div>
                        <!--footer-->
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btn-update-cuti"><i class="fa fa-paper-plane" aria-hidden="true"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>src/vendors/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/jquery/dist/jquery.mask.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo base_url(); ?>src/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url(); ?>src/vendors/select2/dist/js/select2.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url(); ?>src/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?php echo base_url(); ?>src/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="<?php echo base_url(); ?>src/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="<?php echo base_url(); ?>src/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="<?php echo base_url(); ?>src/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="<?php echo base_url(); ?>src/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="<?php echo base_url(); ?>src/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    <script src="<?php echo base_url(); ?>src/vendors/Flot/jquery.flot.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/Flot/jquery.flot.time.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/Flot/jquery.flot.resize.js"></script>
    <!-- Flot plugins -->
    <script src="<?php echo base_url(); ?>src/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/flot.curvedlines/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="<?php echo base_url(); ?>src/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url(); ?>src/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="<?php echo base_url(); ?>src/vendors/moment/min/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-datetimepicker -->
    <script src="<?php echo base_url(); ?>src/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

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
    <!-- Custom Theme Scripts -->
    <script src="<?php echo base_url(); ?>src/build/js/custom.min.js"></script>
    <script src="<?php echo base_url(); ?>src/vendors/validator/validator.js"></script>
    <!-- Sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(() => {
            $('.select2').select2();

            $('#myDatepicker2').datetimepicker({
                format: 'YYYY-MM',
            });

            var myTable = $('#table-approve-hrd').DataTable({
                "ajax": {
                    type: "POST",
                    url: "<?= base_url('cuti/data_approve_hrd?filter=' . $this->input->get('filter')) ?>",
                    data: function(d) {

                    }
                },
            })

            $("#nama_karyawan").change(() => {
                var nip = $('#nama_karyawan').val()
                $.ajax({
                    url: "<?= base_url('cuti/get_data_karyawan') ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        nip: nip
                    },
                    success: (res) => {
                        console.log(res)
                        $('#nama_atasan').val(res.atasan.nama);
                        $('#nip_atasan').val(res.atasan.nip);
                        $('#sisa_cuti').val(res.sisa_cuti);
                        $('#pengganti_cuti').html(res.pengganti);
                    }
                })
            })

            $('#select_detail').hide();
            $('#file_pendukung_form').hide();
            $('#jenis_cuti').change(function() {
                var value = $(this).val();
                if (value > 0) {
                    $.ajax({
                        url: "<?= base_url('cuti/ambilDataDetail') ?>",
                        type: "post",
                        dataType: "json",
                        data: {
                            id: value,
                            cuti: 'hrd'
                        },
                        success: (res) => {
                            if (res.jenis.file_pendukung == 1) {
                                $("#file_pendukung_form").show();
                                if (res.detail == 0) {
                                    $("#select_detail").hide();
                                } else {
                                    $("#select_detail").show();
                                    $("#detail_cuti").html(res.detail);
                                }
                            } else {
                                $("#file_pendukung_form").hide();
                                if (res.detail == 0) {
                                    $("#select_detail").hide();
                                } else {
                                    $("#select_detail").show();
                                    $("#detail_cuti").html(res.detail);
                                }
                            }
                        }
                    })
                }
            })

            $("#btn-update-cuti").click(function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: "Apakah anda yakin data cuti sudah sesuai dan dapat dipertanggung jawabkan?",
                    showCancelButton: true,
                    cancelButtonText: "Batal",
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = $("#form-update-cuti-hrd").attr('action');
                        var id = $("#id_cuti").val();
                        var status_cuti = $("#status_cuti").val();
                        var catatan = $("#catatan").val();

                        $.ajax({
                            url: url + id,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                status_cuti: status_cuti,
                                catatan: catatan
                            },
                            beforeSend: () => {
                                Swal.fire({
                                    title: 'Loading...',
                                    timerProgressBar: true,
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    },
                                })
                            },
                            success: function(res) {
                                if (!res.error) {
                                    Swal.fire({
                                            type: "success",
                                            icon: "success",
                                            title: `${res.msg}`,
                                            showConfirmButton: false,
                                            allowOutsideClick: false,
                                        },
                                        setTimeout(function() {
                                            window.location.reload();
                                        }, 1500)
                                    );
                                } else {
                                    Swal.fire({
                                            icon: "error",
                                            title: `${res.msg}`,
                                            showConfirmButton: false,
                                            allowOutsideClick: false,
                                        },
                                        setTimeout(function() {
                                            Swal.close();
                                            res.err_status ?
                                                $("span#err_status_cuti").html(
                                                    res.err_status
                                                ) :
                                                $("span#err_status_cuti").html("");
                                        }, 1500)
                                    );
                                }
                            }
                        })
                    }
                })
            })
        })

        $('#mulai_cuti').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        })

        $('#akhir_cuti').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        })

        $('#akhir_cuti').change(function() {
            if ($("#jenis_cuti").val() == 2 || $("#jenis_cuti").val() == 3 || $("#jenis_cuti").val() == 4 || $("#jenis_cuti").val() == 5) {
                var awal = $('#mulai_cuti').val()
                awal = new Date(awal.split('/')[2], awal.split('/')[1] - 1, awal.split('/')[0])
                var akhir = $('#akhir_cuti').val()
                akhir = new Date(akhir.split('/')[2], akhir.split('/')[1] - 1, akhir.split('/')[0])
                var time = akhir.getTime() - awal.getTime();
                var hari = (time / (1000 * 3600 * 24)) + 1;
                $('#jumlah_cuti').val(hari);
            } else {
                var firstDate = $('#mulai_cuti').val();
                firstDate = new Date(firstDate.split('/')[2], firstDate.split('/')[1] - 1, firstDate.split('/')[0])
                var secondDate = $('#akhir_cuti').val();
                secondDate = new Date(secondDate.split('/')[2], secondDate.split('/')[1] - 1, secondDate.split('/')[0])
                const daysWithOutWeekEnd = [];
                for (var currentDate = new Date(firstDate); currentDate <= secondDate; currentDate.setDate(currentDate.getDate() + 1)) {
                    // console.log(currentDate);
                    if (currentDate.getDay() != 0 && currentDate.getDay() != 6) {
                        daysWithOutWeekEnd.push(new Date(currentDate));
                    }
                }
                $('#jumlah_cuti').val(daysWithOutWeekEnd.length);
            }
        })

        $('#btn-form-cuti-hrd').on('click', function() {
            var formData = new FormData($('#formCutiHrd')[0]);
            $.ajax({
                url: "<?= base_url('cuti/cuti_manual') ?>",
                type: "post",
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    Swal.fire({
                        title: 'Sending...',
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                    })
                    $('#btn-form-cuti-hrd').attr('disabled', true)
                },
                complete: () => {
                    $('#btn-form-cuti-hrd').attr('disabled', false)
                },
                success: function(res) {
                    if (!res.error) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg,
                            showConfirmButton: false,
                        }, setTimeout(() => {
                            location.reload()
                        }, 1500))
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.msg,
                            showConfirmButton: false,
                        }, setTimeout(function() {
                            res.err_namakar != "" ? $("#err_namakar").html(res.err_namakar) : $("#err_namakar").html("");
                            res.err_namapeng != "" ? $("#err_namapeng").html(res.err_namapeng) : $("#err_namapeng").html("");
                            res.err_jenis != "" ? $("#err_jenis_cuti").html(res.err_jenis) : $("#err_jenis_cuti").html("");
                            res.err_detail != "" ? $("#err_detail_cuti").html(res.err_detail) : $("#err_detail_cuti").html("");
                            res.err_mulai != "" ? $("#err_mulai_cuti").html(res.err_mulai) : $("#err_mulai_cuti").html("");
                            res.err_akhir != "" ? $("#err_akhir_cuti").html(res.err_akhir) : $("#err_akhir_cuti").html("");
                            res.err_jumlah != "" ? $("#err_jumlah_cuti").html(res.err_jumlah) : $("#err_jumlah_cuti").html("");
                            res.err_alasan != "" ? $("#err_alasan_cuti").html(res.err_alasan) : $("#err_alasan_cuti").html("");
                            res.err_alamat != "" ? $("#err_alamat_cuti").html(res.err_alamat) : $("#err_alamat_cuti").html("");
                            res.err_file != "" ? $("#err_alamat_cuti").html(res.err_alamat) : $("#err_alamat_cuti").html("");
                            Swal.close()
                        }, 2000))
                    }
                }
            })
        })

        function update_cuti_hrd(id) {
            $('#modal-update-cuti-hrd').modal('show');
            $('#id_cuti').val(id);
        }

        function detailCuti(id) {
            $("#detail-cuti").modal('show');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?= base_url('cuti/detailCuti/') ?>" + id,
                success: (res) => {
                    $("#detail-cuti-byID").html(res);
                }
            })
        }

        function historyCuti(nip) {
            location.href = "<?= site_url('cuti/historyCuti/') ?>" + nip;
        }

        function topFunction() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        }
    </script>
</body>

</html>