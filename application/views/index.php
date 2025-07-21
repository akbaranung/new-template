<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <!-- <link rel="icon" href="favicon.ico"> -->
  <link rel="icon" type="image/png" href="<?php echo ($this->session->userdata('icon')) ? $this->session->userdata('icon') : $utility['logo']; ?>">
  <!-- <title>Bariskode - <?= $title ?></title> -->
  <title><?php echo ($this->session->userdata('nama_perusahaan')) ? $this->session->userdata('nama_perusahaan') : $utility['nama_perusahaan']; ?> - <?= $title ?></title>

  <!-- Simple bar CSS -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/simplebar.css">
  <!-- Fonts CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/feather.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/select2.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/dropzone.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/uppy.min.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/jquery.steps.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/jquery.timepicker.css">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/quill.snow.css">
  <!-- Date Range Picker CSS -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/daterangepicker.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/app-light.css" id="lightTheme">
  <link rel="stylesheet" href="<?= base_url('assets') ?>/css/app-dark.css" id="darkTheme" disabled>
  <!-- Sweetalert2 -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/sweetalert2/css/sweetalert2.min.css">
  <!-- Datatables -->
  <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/vendors/bootstrap/dist/css/bootstrap.min.css"> -->

  <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css"> -->
  <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css"> -->
  <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css"> -->
  <link rel="stylesheet" href="<?= base_url('assets') ?>/dataTables/css/datatables.min.css">
  <link rel="manifest" href="<?= base_url() ?>assets/_manifest.json" />

</head>

<body class="vertical  light  ">
  <div class="wrapper">
    <!-- Navbar -->
    <?php $this->load->view('layouts/navbar.php') ?>
    <!-- Sidebar -->
    <?php $this->load->view('layouts/sidebar.php') ?>
    <!-- Main Content -->
    <main role="main" class="main-content">
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

      if ($total_user < 4 || $cek_coa_cabang != 0) {
        $this->load->view('layouts/tutorial.php');
      }
      ?>
      <?php if (isset($pages)) $this->load->view($pages); ?>
    </main> <!-- main -->
  </div> <!-- .wrapper -->
  <script src="<?= base_url('assets') ?>/js/jquery.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/popper.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/moment.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/bootstrap.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/simplebar.min.js"></script>
  <script src='<?= base_url('assets') ?>/js/daterangepicker.js'></script>
  <script src='<?= base_url('assets') ?>/js/jquery.stickOnScroll.js'></script>
  <script src="<?= base_url('assets') ?>/js/tinycolor-min.js"></script>
  <script src="<?= base_url('assets') ?>/js/config.js"></script>
  <script src="<?= base_url('assets') ?>/js/d3.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/topojson.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/datamaps.all.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/datamaps-zoomto.js"></script>
  <script src="<?= base_url('assets') ?>/js/datamaps.custom.js"></script>
  <script src="<?= base_url('assets') ?>/js/Chart.min.js"></script>
  <script>
    /* defind global options */
    // Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
    // Chart.defaults.global.defaultFontColor = colors_dashboard.mutedColor;
  </script>
  <script src="<?= base_url('assets') ?>/js/gauge.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/jquery.sparkline.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/apexcharts.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/apexcharts.custom.js"></script>
  <script src='<?= base_url('assets') ?>/js/jquery.mask.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/select2.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/jquery.steps.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/jquery.validate.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/jquery.timepicker.js'></script>
  <script src='<?= base_url('assets') ?>/js/dropzone.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/uppy.min.js'></script>
  <script src='<?= base_url('assets') ?>/js/quill.min.js'></script>
  <!-- CKEditor -->
  <script type="text/javascript" src="<?= base_url(); ?>/assets/ckeditor/ckeditor.js"></script>
  <!-- Sweetalert -->
  <script src="<?= base_url('assets') ?>/sweetalert2/js/sweetalert2.all.min.js"></script>
  <!-- Cleave JS -->
  <script src="<?= base_url('assets') ?>/js/cleave.min.js"></script>
  <!-- DataTables -->
  <script src="<?= base_url('assets') ?>/dataTables/js/datatables.min.js"></script>
  <script>
    const progress = document.getElementById("progress");
    const circles = document.querySelectorAll(".circle");

    let currentActive = <?= ($i) ? $i : 1 ?>;

    next.addEventListener("click", () => {
      currentActive++;
      if (currentActive > circles.length) currentActive = circles.length;
      update();
    });

    prev.addEventListener("click", () => {
      currentActive--;
      if (currentActive < 1) currentActive = 1;
      update();
    });

    const update = () => {
      circles.forEach((circle, index) => {
        if (index < currentActive) circle.classList.add("active");
        else circle.classList.remove("active");
      });
      const actives = document.querySelectorAll(".active");
      progress.style.width =
        ((actives.length - 1) / (circles.length - 1)) * 100 + "%";
      if (currentActive === 1) prev.disabled = true;
      else if (currentActive === circles.length) next.disabled = true;
      else {
        prev.disabled = false;
        next.disabled = false;
      }
    };
  </script>
  <script>
    $('.select2').select2({
      theme: 'bootstrap4',
      width: '100%'
    });
    $('.select2-multi').select2({
      theme: 'bootstrap4',
    });
    $('.drgpicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      showDropdowns: true,
      locale: {
        format: 'MM/DD/YYYY'
      }
    });
    $('.time-input').timepicker({
      'scrollDefault': 'now',
      'zindex': '9999' /* fix modal open */
    });
    /** date range picker */
    if ($('.datetimes').length) {
      $('.datetimes').daterangepicker({
        timePicker: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
          format: 'M/DD hh:mm A'
        }
      });
    }
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    $('#reportrange').daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, cb);
    cb(start, end);
    $('.input-placeholder').mask("00/00/0000", {
      placeholder: "__/__/____"
    });
    $('.input-zip').mask('00000-000', {
      placeholder: "____-___"
    });
    $('.input-money').mask("#.##0,00", {
      reverse: true
    });
    $('.input-phoneus').mask('(000) 000-0000');
    $('.input-mixed').mask('AAA 000-S0S');
    $('.input-ip').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
      translation: {
        'Z': {
          pattern: /[0-9]/,
          optional: true
        }
      },
      placeholder: "___.___.___.___"
    });
    // editor
    var editor = document.getElementById('editor');
    if (editor) {
      var toolbarOptions = [
        [{
          'font': []
        }],
        [{
          'header': [1, 2, 3, 4, 5, 6, false]
        }],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{
            'header': 1
          },
          {
            'header': 2
          }
        ],
        [{
            'list': 'ordered'
          },
          {
            'list': 'bullet'
          }
        ],
        [{
            'script': 'sub'
          },
          {
            'script': 'super'
          }
        ],
        [{
            'indent': '-1'
          },
          {
            'indent': '+1'
          }
        ], // outdent/indent
        [{
          'direction': 'rtl'
        }], // text direction
        [{
            'color': []
          },
          {
            'background': []
          }
        ], // dropdown with defaults from theme
        [{
          'align': []
        }],
        ['clean'] // remove formatting button
      ];
      var quill = new Quill(editor, {
        modules: {
          toolbar: toolbarOptions
        },
        theme: 'snow'
      });
    }
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>
  <script src="<?= base_url('assets') ?>/js/apps.js"></script>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');
  </script>

  <!-- My Script -->
  <?php if (isset($pages_script)) $this->load->view($pages_script); ?>

  <script>
    const chartColors_dashboard = ["#5E72E4", "#e91e63"];
    const colors_dashboard = {
      mutedColor: "#8898aa",
      borderColor: "#e3e3e3",
      chartTheme: "light",
    };
    const base_dashboard = {
      defaultFontFamily: "inherit",
    };

    var areaChartOptions = {
      series: [{
          name: "Pendapatan",
          data: <?= $json_pendapatan ?>
        },
        {
          name: "Biaya",
          data: <?= $json_biaya ?>
        }
      ],
      chart: {
        type: "area",
        height: 350,
        stacked: false,
        toolbar: {
          show: true
        },
        zoom: {
          enabled: false
        },
        background: "transparent"
      },
      theme: {
        mode: colors_dashboard.chartTheme
      },
      xaxis: {
        categories: <?= $json_categories ?>,
        labels: {
          style: {
            colors: colors_dashboard.mutedColor,
            cssClass: "text-muted",
            fontFamily: base_dashboard.defaultFontFamily,
          }
        },
        axisBorder: {
          show: true
        },
        tooltip: {
          enabled: true
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: colors_dashboard.mutedColor,
            cssClass: "text-muted",
            fontFamily: base_dashboard.defaultFontFamily,
          }
        }
      },
      stroke: {
        curve: "smooth",
        lineCap: "round",
        width: 2
      },
      fill: {
        type: "solid",
        opacity: 0.3
      },
      markers: {
        size: 4,
        strokeColors: "#fff",
        strokeWidth: 2,
      },
      colors: chartColors_dashboard,
      dataLabels: {
        enabled: false
      },
      grid: {
        borderColor: colors_dashboard.borderColor,
        yaxis: {
          lines: {
            show: true
          }
        },
        xaxis: {
          lines: {
            show: false
          }
        }
      },
      legend: {
        position: "top",
        labels: {
          colors: colors_dashboard.mutedColor
        }
      },
      tooltip: {
        y: {
          formatter: val => new Intl.NumberFormat('id-ID').format(val)
        }
      }
    };

    var areaChart = new ApexCharts(document.querySelector("#areaChart"), areaChartOptions);
    areaChart.render();
  </script>
  <script>
    function upgrade_premium() {
      // In your HTML/JavaScript view file
      const is_premium = <?= (int)$this->session->userdata('is_premium'); ?>;

      // This will guarantee 'is_premium' is either the number 1 or 0 in your script.
      if (is_premium === 1) {
        console.log('User is premium.');
      }

      if (is_premium == 0) {

        Swal.fire({
          title: 'Siap Menjadi Raja <?= '<img src="' . base_url() . 'assets/icons/sword_gray.png" alt="Sword Icon" width="32" height="32">' ?>', // New title: "Ready to Become King?"
          html: 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda.', // New text with HTML for emphasis
          icon: 'warning', // IMPORTANT: Set icon to undefined or remove it if you're using iconHtml
          iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>',
          confirmButtonText: 'Ambil Mahkota Sekarang!', // New confirm button text: "Take the Crown Now!"
          showCancelButton: true,
          cancelButtonText: 'Nanti Saja, Belum Siap Jadi Raja', // New cancel button text: "Later, Not Ready to Be King Yet"
          customClass: {
            confirmButton: 'btn btn-primary', // Optional: Use your custom btn-pink class for the confirm button
            cancelButton: 'btn btn-pink' // Optional: Style the cancel button differently
          },
          buttonsStyling: false // Important if you use customClass for buttons
        }).then((result) => {
          if (result.isConfirmed) {
            // Optional: Redirect to an upgrade page if 'Ambil Mahkota Sekarang!' is clicked
            window.location.href = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed
          }
        });
      }
    }
  </script>

</body>

</html>