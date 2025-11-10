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
                        <h3><b>Data Approval Direksi</b></h3>
                    </div>
                    <div>
                        <button onclick="history.back()" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</button>
                    </div>
                    <div class="table-responsive" style="margin-top: 2em;">
                        <table id="table-approve-atasan" class="table table-striped jambo_table bulk_action" width="100%">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title">No.</th>
                                    <th class="column-title">Nama</th>
                                    <th class="column-title">Jenis</th>
                                    <th class="column-title">Alasan</th>
                                    <th class="column-title">Tanggal</th>
                                    <th class="column-title">Mulai</th>
                                    <th class="column-title">Jumlah</th>
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
    <div class="modal fade" id="modal-update-cuti-direksi">
        <div class="modal-dialog modal-centered">
            <div class="modal-content">
                <!-- header-->
                <div class="modal-header">
                    <button class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Cuti</h4>
                </div>
                <!--body-->
                <div class="modal-body">
                    <form action="<?= base_url('cuti/update_cuti_direksi/') ?>" id="form-update-cuti-direksi" method="post">
                        <div class="form-group">
                            <input type="hidden" readonly class="form-control" id="update_direksi" name="update_direksi">
                            <input type="hidden" readonly class="form-control" id="id_cuti_direksi" name="id_cuti">
                        </div>
                        <div class="form-group" id="error_jenis">
                            <label for="status_cuti">Status</label>
                            <select class="form-control select2" id="status_cuti" name="status_cuti" style="width:100%;">
                                <option value=""> -- Pilih Status --</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                            <span id="err_status_cuti" class="text-danger"></span>
                        </div>
                        <div class="form-group" id="select-pengganti">
                            <label for="pengganti">Pengganti</label>
                            <select class="form-control select2" id="pengganti" name="pengganti" style="width: 100%;">
                                <option value="">-- Pilih Pengganti --</option>
                            </select>
                            <span class="text-danger" id="err_pengganti"></span>
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan (Opsional)</label>
                            <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                        </div>
                        <!--footer-->
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btn-update-cuti-direksi"><i class="fa fa-paper-plane" aria-hidden="true"></i> Update</button>
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
    <script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

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

            var myTable = $('#table-approve-atasan').DataTable({
                "ajax": {
                    type: "POST",
                    url: "<?= base_url('cuti/data_approve_direksi') ?>",
                    data: function(d) {

                    }
                },
            })

            $("#select-pengganti").hide();
            $("#status_cuti").change(function() {
                var value = $(this).val();
                var id = $("#id_cuti_direksi").val();
                var direksi = $("#update_direksi").val();
                $.ajax({
                    url: "<?= base_url('cuti/approveAtasan/') ?>" + id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(res) {
                        if (direksi == 'dirsdm') {
                            if (res.cuti.atasan == res.cuti.dirsdm) {
                                $("#pengganti").html(res.option);
                                if (value == "Disetujui") {
                                    $("#select-pengganti").show();
                                } else {
                                    $("#select-pengganti").hide();
                                }
                            }
                        } else {
                            if (res.cuti.atasan == res.cuti.dirut) {
                                $("#pengganti").html(res.option);
                                if (value == "Disetujui") {
                                    $("#select-pengganti").show();
                                } else {
                                    $("#select-pengganti").hide();
                                }
                            }
                        }
                    }
                })
            })

            $("#btn-update-cuti-direksi").click(function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: "Apakah anda yakin data cuti sudah sesuai dan dapat dipertanggung jawabkan?",
                    showCancelButton: true,
                    cancelButtonText: "Batal",
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = $("#form-update-cuti-direksi").attr('action');
                        var id = $("#id_cuti_direksi").val();
                        var direksi = $("#update_direksi").val();
                        var status_cuti = $("#status_cuti").val();
                        var catatan = $("#catatan").val();
                        var pengganti = $('#pengganti').val();

                        $.ajax({
                            url: url + id,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                status_cuti: status_cuti,
                                catatan: catatan,
                                direksi: direksi,
                                pengganti: pengganti
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
                                console.log(res);
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
                                            res.err_pengganti ?
                                                $("span#err_pengganti").html(
                                                    res.err_pengganti
                                                ) :
                                                $("span#err_pengganti").html("");
                                        }, 1500)
                                    );
                                }
                            }
                        })
                    }
                })
            })

        })

        function historyCuti(nip) {
            location.href = "<?= site_url('cuti/historyCuti/') ?>" + nip;
        }

        function confirmDirsdm(id) {
            $('#modal-update-cuti-direksi').modal('show');
            $('#id_cuti_direksi').val(id);
            $('#update_direksi').val('dirsdm')
        }

        function confirmDirut(id) {
            $('#modal-update-cuti-direksi').modal('show');
            $('#id_cuti_direksi').val(id);
            $('#update_direksi').val('dirut')
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

        function topFunction() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        }
    </script>
</body>

</html>