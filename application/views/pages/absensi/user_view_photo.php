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

		body {}

		table,
		th,
		td {
			border: 0px solid black;
		}

		table.center {
			margin-left: auto;
			margin-right: auto;
		}

		.button1 {
			background-color: #4CAF50;
		}

		table,
		table {
			border-collapse: separate;
			border-spacing: 0 1em;
		}

		.image-box {
			position: relative;
			display: inline-block;
			margin-left: 20%;
			margin-top: 5px;
		}

		.image-box img {
			display: block;
			max-width: 100%;
			height: auto;
			border-radius: 5px;
		}

		.image-box:hover img {
			filter: blur(0.5px);
			cursor: pointer;
			box-shadow: 0px 0px 10px #5073fb;

		}

		.edit-icon {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			display: none;
			cursor: pointer;
			color: darkblue;
			font-size: 5rem;

		}

		.image-box:hover .edit-icon {
			display: block;
		}

		.image-box {
			position: relative;
			display: inline-block;
			height: 15rem;
			width: 15rem;
		}

		.image-box img {
			display: block;
			max-width: 100%;
			height: auto;
			border-radius: 5px;
		}

		.image-box:hover img {
			filter: blur(1.5px);
			cursor: pointer;
			transform: scale(1.1);
			box-shadow: 0px 0px 10px #5073fb;



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
							<?php if ($this->uri->segment(4) == 'e') {
								echo 'User Edit';
							} else {
								echo 'User View';
							} ?>
							<hr />
						</font>
					</div>
					<font style="font-size:14px;">
						<?php
						// Check the URI segments to determine the mode: view, add, or edit
						$mode = ($this->uri->segment(4) == 'e') ? 'edit' : (($this->uri->segment(3) == true) ? 'view' : 'add');
						?>

						</br>
						<?= $this->session->flashdata('msg') ?>

						<?php if ($mode == 'view') { ?>
							<!-- View mode: Display user details -->
							<table>
								<tr>
									<th width="200">Username</th>
									<td>: <?= $user->username ?></td>
								</tr>
								<tr>
									<th>Nama</th>
									<td>: <?= $user->nama ?></td>
								</tr>
								<tr>
									<div>
										<div class="form-title-image">
											<p>Take Pictures</p>
										</div>
										<div id="open_camera" class="image-box" onclick="takeMultipleImages()">
											<img src="<?= base_url() ?>resources/images/default.png" alt="Default Image">
										</div>
										<div id="multiple-images"></div>
									</div>
								</tr>
								<tr>
									<th>
										<a href="<?= base_url('app/user') ?>" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
									</th>
								</tr>
							</table>
							<br>

						<?php } else { ?>
							<!-- Add/Edit mode -->
							<form action="<?= ($mode == 'add') ? base_url('app/add_photo') : base_url('app/add_photo/' . $this->uri->segment(3)) ?>" method="POST">
								<input type="hidden" name="<?= $mode ?>" value="<?= $mode ?>">
								<input type="hidden" value="<?= $this->uri->segment(3) ?>" name="id">
								<table>
									<tr>
										<th width="300">Username</th>
										<td width="300">
											<input type="<?= ($mode == 'edit') ? 'text' : 'text' ?>" name="username" class="form-control" value="<?= ($mode == 'edit') ? $user->username : set_value('username') ?>" <?= ($mode == 'edit') ? 'readonly' : '' ?>>
										</td>
									</tr>
									<tr>
										<th width="200">Name</th>
										<td>
											<input type="text" name="nama" class="form-control" value="<?= ($mode == 'edit') ? $user->nama : '' ?>">
										</td>
									</tr>
									<?php if (!empty($user->userImage)) { ?>
										<tr>
											<div id="image-gallery" class="row">
												<?php
												$images = json_decode($user->userImage, true); // Decode the JSON array
												$imagePath = 'resources/labels/' . $user->username . '/';
												foreach ($images as $image) : ?>
													<div class="user-image col-md-2">
														<img src="<?= base_url($imagePath . $image) ?>" alt="User Image" style="width: 100px; margin: 5px;">
													</div>
												<?php endforeach; ?>
												<!-- <img src="<?= base_url() ?>resources/images/default.png" alt="Default Image"> -->
											</div>
										</tr>
									<?php } else { ?>
										<tr>
											<div>
												<div class="form-title-image">
													<p>Take Pictures</p>
												</div>
												<div id="open_camera" class="image-box" onclick="takeMultipleImages()">
													<img src="<?= base_url() ?>resources/images/default.png" alt="Default Image">
												</div>
												<div id="multiple-images"></div>
											</div>
										</tr>
									<?php } ?>
									<!-- Delete all images button -->
									<?php if (!empty($user->userImage)) { ?>
										<a id="delete-images" class="btn btn-danger" onclick="deleteUserImages('<?= $user->username ?>')">Delete All Images</a>
									<?php } ?>
									<tr>
										<th>
											<a href="<?= base_url('app/user') ?>" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
											<?php if ($mode != 'view') { ?>
												<button type="submit" class="btn btn-primary"><?= ($mode == 'add') ? 'Submit' : 'Update' ?></button>
											<?php } ?>
										</th>
									</tr>
								</table>
							</form>
							<br>
						<?php } ?>
					</font>
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

		function openCamera(buttonId) {
			navigator.mediaDevices
				.getUserMedia({
					video: true
				})
				.then((stream) => {
					const video = document.createElement("video");
					video.srcObject = stream;
					document.body.appendChild(video);

					video.play();

					setTimeout(() => {
						const capturedImage = captureImage(video);
						stream.getTracks().forEach((track) => track.stop());
						document.body.removeChild(video);

						const imgElement = document.getElementById(
							buttonId + "-captured-image"
						);
						imgElement.src = capturedImage;
						const hiddenInput = document.getElementById(
							buttonId + "-captured-image-input"
						);
						hiddenInput.value = capturedImage;
					}, 500);
				})
				.catch((error) => {
					console.error("Error accessing webcam:", error);
				});
		}
		const takeMultipleImages = async () => {
			document.getElementById("open_camera").style.display = "none";

			const images = document.getElementById("multiple-images");

			for (let i = 1; i <= 5; i++) {
				// Create the image box element
				const imageBox = document.createElement("div");
				imageBox.classList.add("image-box");

				const imgElement = document.createElement("img");
				imgElement.id = `image_${i}-captured-image`;

				const editIcon = document.createElement("div");
				editIcon.classList.add("edit-icon");

				const icon = document.createElement("i");
				icon.classList.add("fa", "fa-camera");
				icon.setAttribute("onclick", `openCamera("image_"+${i})`);

				const hiddenInput = document.createElement("input");
				hiddenInput.type = "hidden";
				hiddenInput.id = `image_${i}-captured-image-input`;
				hiddenInput.name = `capturedImage${i}`;

				editIcon.appendChild(icon);
				imageBox.appendChild(imgElement);
				imageBox.appendChild(editIcon);
				imageBox.appendChild(hiddenInput);
				images.appendChild(imageBox);
				await captureImageWithDelay(i);
			}
		};

		const captureImageWithDelay = async (i) => {
			try {
				// Get camera stream
				const stream = await navigator.mediaDevices.getUserMedia({
					video: true
				});
				const video = document.createElement("video");
				video.srcObject = stream;
				document.body.appendChild(video);
				video.play();

				// Wait for 500ms before capturing the image
				await new Promise((resolve) => setTimeout(resolve, 500));

				// Capture the image
				const capturedImage = captureImage(video);

				// Stop the video stream and remove the video element
				stream.getTracks().forEach((track) => track.stop());
				document.body.removeChild(video);

				// Update the image and hidden input
				const imgElement = document.getElementById(`image_${i}-captured-image`);
				imgElement.src = capturedImage;

				const hiddenInput = document.getElementById(
					`image_${i}-captured-image-input`
				);
				hiddenInput.value = capturedImage;
			} catch (err) {
				console.error("Error accessing camera: ", err);
			}
		};

		function captureImage(video) {
			const canvas = document.createElement("canvas");
			canvas.width = video.videoWidth;
			canvas.height = video.videoHeight;
			const context = canvas.getContext("2d");

			context.drawImage(video, 0, 0, canvas.width, canvas.height);

			return canvas.toDataURL("image/png");
		}

		function deleteUserImages(username) {
			if (!confirm("Are you sure you want to delete all images for this user?")) {
				return;
			}

			fetch('<?= base_url('app/delete_user_images') ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						username: username
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						alert(data.message);
						location.reload(); // Refresh the page to update the image gallery
					} else {
						alert("Error: " + data.message);
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Failed to delete images. Please try again.');
				});
		}
	</script>
</body>

</html>