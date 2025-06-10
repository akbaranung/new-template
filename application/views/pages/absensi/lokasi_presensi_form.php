<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" href="<?= $this->session->userdata('icon') ?>" type="image/ico" />
	<title><?= $this->session->userdata('nama_singkat') ?> | Bussines Development</title>
	<title>Kodesis | Business Development</title>
	<!-- Bootstrap -->
	<link href="<?php echo base_url(); ?>src/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="<?php echo base_url(); ?>src/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- NProgress -->
	<link href="<?php echo base_url(); ?>src/vendors/nprogress/nprogress.css" rel="stylesheet">
	<!-- iCheck -->
	<link href="<?php echo base_url(); ?>src/vendors/iCheck/skins/flat/green.css" rel="stylesheet">

	<!-- bootstrap-progressbar -->
	<link href="<?php echo base_url(); ?>src/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
	<!-- JQVMap -->
	<link href="<?php echo base_url(); ?>src/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
	<!-- bootstrap-daterangepicker -->
	<link href="<?= base_url() ?>src/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>src/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
	<!-- Custom Theme Style -->
	<link href="<?php echo base_url(); ?>src/build/css/custom.min.css" rel="stylesheet">
	<!-- footer menu -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/header.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>src/css/mobile_menu/icons.css">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
</head>

<header class="header_area sticky-header">
	<!-- footer menu -->
	<!--div class="footer_panel">
		<div class="container-fluid text-center">
			<div class="row">

				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/antrian_input">
					<i class="la-i la-i-m la-i-home"></i>
					<div class="tag_"><font color="white">Create</font></div></a>
				</div>
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/antrian_panggil">
					<i class="la-i la-i-m la-i-order"></i>
					<div class="tag_"><font color="white">Manage</font></div></a>
				</div>
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/antrian_monitor">
					<i class="la-i la-i-m la-i-notif">
					</i>
					<div class="tag_">
						<font color="white">Monitor</font>
					</div>
					</a>
				</div>
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>login/logout">
					<i class="la-i la-i-m la-i-akun"></i>
					<div class="tag_"><font color="white">Logout</font></div></a>
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
						<a href="<?php echo base_url(); ?>" class="site_title"><img src="<?= $this->session->userdata('icon') ?>" alt="..." width="60">
							<span><?= $this->session->userdata('nama_singkat') ?></span></a>
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

			<!-- Start content-->
			<div class="right_col" role="main">
				<div class="clearfix"></div>

				<div class="x_panel card">
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
					<div class="form">
						<div style="margin-bottom: 10px;">
							<div id="map"></div>
						</div>
						<?php
						if ($this->uri->segment(3) == null) { ?>
							<form method="POST" action="<?= base_url('app/proses_tambah_lokasi_presensi') ?>">
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
							<form method="POST" action="<?= base_url('app/proses_update_lokasi_presensi') ?>">
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
									<select class="form-control" name="zona_waktu" id="zona_waktu">
										<option <?php if ($detail->zona_waktu == "WIB") "Selected" ?> value="WIB">WIB</option>
										<option <?php if ($detail->zona_waktu == "WIT") "Selected" ?> value="WIT">WIT</option>
										<option <?php if ($detail->zona_waktu == "WITA") "Selected" ?> value="WITA">WITA</option>
									</select>
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
						<form action="">

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Finish content-->


	<!-- /page content -->

	<!-- footer content -->

	<!-- /footer content --></br>


	<!-- jQuery -->
	<script src="<?php echo base_url(); ?>src/vendors/jquery/dist/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>src/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
	<!-- FastClick -->
	<script src="<?php echo base_url(); ?>src/vendors/fastclick/lib/fastclick.js"></script>
	<!-- NProgress -->
	<script src="<?php echo base_url(); ?>src/vendors/nprogress/nprogress.js"></script>
	<!-- Chart.js -->
	<script src="<?php echo base_url(); ?>src/vendors/Chart.js/dist/Chart.min.js"></script>
	<!-- gauge.js -->
	<script src="<?php echo base_url(); ?>src/vendors/gauge.js/dist/gauge.min.js"></script>
	<!-- bootstrap-progressbar -->
	<script src="<?php echo base_url(); ?>src/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js">
	</script>
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
	<script src="<?= base_url() ?>src/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
	<!-- Custom Theme Scripts -->
	<script src="<?php echo base_url(); ?>src/build/js/custom.min.js"></script>
	<!-- Select2 -->
	<link rel="stylesheet" href="<?= base_url() ?>src/select2/css/select2.min.css">
	<script type="text/javascript" src="<?= base_url() ?>src/select2/js/select2.min.js"></script>

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

	<script>
		$(document).ready(function() {
			$('select.js-example-basic-multiple').select2();
			$('div#myDatepicker2').datetimepicker({
				format: 'YYYY-MM-DD',
				maxDate: Date.now() + 90000000
			});
		});

		window.setTimeout(function() {
			$(".alert-success").fadeTo(500, 0).slideUp(500, function() {
				$(this).remove();
			});
		}, 3000);

		window.setTimeout(function() {
			$(".alert-danger").fadeTo(500, 0).slideUp(500, function() {
				$(this).remove();
			});
		}, 3000);

		// Initialize the map
		<?php
		if ($this->uri->segment(3) == Null) {
		?>
			const map = L.map('map').setView([-6.2568425826630625, 106.88298401638922], 13); // Centered on Jakarta, Indonesia
			const marker = L.marker([-6.2568425826630625, 106.88298401638922], {
				draggable: true
			}).addTo(map);
		<?php
		} else {
		?>
			const map = L.map('map').setView([<?= $detail->latitude ?>, <?= $detail->longitude ?>], 13); // Centered on a specific location
			const marker = L.marker([<?= $detail->latitude ?>, <?= $detail->longitude ?>], {
				draggable: true
			}).addTo(map);
		<?php
		}
		?>

		// Add OpenStreetMap tiles
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '© OpenStreetMap contributors'
		}).addTo(map);

		// Default radius (can be changed based on user input)
		let radius = parseInt(document.getElementById('radius_lokasi').value) || 500; // Default to 500 if empty

		// Create the circle with initial radius
		const circle = L.circle(marker.getLatLng(), {
			color: 'blue',
			fillColor: '#30f',
			fillOpacity: 0.2,
			radius: radius
		}).addTo(map);

		// Event listener for marker drag
		marker.on('dragend', () => {
			const latLng = marker.getLatLng();
			updateLocation(latLng.lat, latLng.lng);

			// Update circle position
			circle.setLatLng(latLng);
		});

		// Function to update location fields
		function updateLocation(lat, lng) {
			document.getElementById('latitude_lokasi').value = lat;
			document.getElementById('longitude_lokasi').value = lng;

			// Fetch address using Nominatim API
			fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
				.then(response => response.json())
				.then(data => {
					const address = data.display_name || "Unknown Address";
					const name = data.address.road || "Unknown Location";

					document.getElementById('nama_lokasi').value = name;
					document.getElementById('alamat_lokasi').value = address;
				})
				.catch(error => console.error("Error fetching address:", error));
		}

		// Event listener for radius input change
		document.getElementById('radius_lokasi').addEventListener('input', (event) => {
			// Update the radius based on input value
			const newRadius = parseInt(event.target.value);
			if (!isNaN(newRadius) && newRadius > 0) {
				radius = newRadius / 1000; // Update radius variable
				circle.setRadius(radius); // Update the circle's radius
			}
		});

		// Event listener for longitude and latitude input change (for both longitude and latitude)
		document.getElementById('longitude_lokasi').addEventListener('input', (event) => {
			const lat = parseFloat(document.getElementById('latitude_lokasi').value);
			const lng = parseFloat(event.target.value);

			if (!isNaN(lat) && !isNaN(lng)) {
				marker.setLatLng([lat, lng]); // Update marker position
				circle.setLatLng([lat, lng]); // Update circle position

				// Update location fields with the new values
				updateLocation(lat, lng);
			}
		});

		document.getElementById('latitude_lokasi').addEventListener('input', (event) => {
			const lat = parseFloat(event.target.value);
			const lng = parseFloat(document.getElementById('longitude_lokasi').value);

			if (!isNaN(lat) && !isNaN(lng)) {
				marker.setLatLng([lat, lng]); // Update marker position
				circle.setLatLng([lat, lng]); // Update circle position

				// Update location fields with the new values
				updateLocation(lat, lng);
			}
		});
	</script>

</body>

</html>