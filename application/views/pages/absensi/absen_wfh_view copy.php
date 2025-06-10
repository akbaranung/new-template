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
	<title>Kodesis | Bussines Development</title>
	<!-- Bootstrap -->
	<link href="<?php echo base_url(); ?>src/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- <link href="<?php echo base_url(); ?>login_lib/vendor/bootstrap/css/bootstrap-grid.css" rel="stylesheet"> -->
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
	<link href="<?php echo base_url(); ?>src/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
	<!-- Custom Theme Style -->
	<link href="<?php echo base_url(); ?>src/build/css/custom.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>src/build/css/owl.carousel.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>src/build/css/owl.theme.default.min.css" rel="stylesheet">

	<!-- <link rel="stylesheet" href="<?= base_url() ?>resources/assets/css/styles.css"> -->
	<script defer src="<?= base_url() ?>resources/assets/javascript/face_logics/face-api.min.js"></script>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
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


		.justify-content-center {
			display: flex;
			justify-content: center;
		}


		/*video*/
		canvas {
			position: absolute;

		}

		.video-container {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		#video {
			border-radius: 10px;
			box-shadow: #000;
		}
	</style>
</head>

<header class="header_area sticky-header">
	<!-- footer menu -->
	<div class="footer_panel">
		<div class="container-fluid text-center">
			<div class="row">
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/create_memo">
						<i class="la-i la-i-m la-i-home"></i>
						<div class="tag_">
							<font color="white">Create</font>
						</div>
					</a>
				</div>
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/inbox">
						<i class="la-i la-i-m la-i-order"></i>
						<div class="tag_">
							<font color="white">Inbox</font>
						</div>
					</a>
				</div>
				<div class="col-xs-3 btn_footer_panel">
					<a href="<?php echo base_url(); ?>app/send_memo">
						<i class="la-i la-i-m la-i-notif"></i>
						<div class="tag_">
							<font color="white">Outbox</font>
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
						<a href="<?php echo base_url(); ?>" class="site_title">
							<img src="<?= $this->session->userdata('icon') ?>" alt="..." width="60">
							<span><?= $this->session->userdata('nama_singkat') ?></span>
						</a>
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
									<li><a href="<?php echo base_url(); ?>login/logout"><i class="fa fa-sign-out pull-right"></i> Log
											Out</a></li>
								</ul>
							</li>

							<li role="presentation" class="dropdown">
								<!--a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false"-->
								<a href="<?php echo base_url() . "app/inbox"; ?>" class="dropdown-toggle info-number">
									<i class="fa fa-envelope-o"></i>
									<?php if ($count_inbox == 0) { ?>
										<span class="badge bg-green"><?php echo $count_inbox; ?></span>
									<?php } else { ?>
										<span class="badge bg-red"><?php echo $count_inbox; ?></span>
									<?php } ?>
								</a>
								<!--ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
									<li>
									<a>
										<span class="image"><img src="<?php echo base_url(); ?>src/images/img.jpg" alt="Profile Image" /></span>
										<span>
										<span>John Smith</span>
										<span class="time">3 mins ago</span>
										</span>
										<span class="message">
										Film festivals used to be do-or-die moments for movie makers. They were where...
										</span>
									</a>
									</li>
									<li>
									<a>
										<span class="image"><img src="<?php echo base_url(); ?>src/images/img.jpg" alt="Profile Image" /></span>
										<span>
										<span>John Smith</span>
										<span class="time">3 mins ago</span>
										</span>
										<span class="message">
										Film festivals used to be do-or-die moments for movie makers. They were where...
										</span>
									</a>
									</li>
									<li>
									<a>
										<span class="image"><img src="<?php echo base_url(); ?>src/images/img.jpg" alt="Profile Image" /></span>
										<span>
										<span>John Smith</span>
										<span class="time">3 mins ago</span>
										</span>
										<span class="message">
										Film festivals used to be do-or-die moments for movie makers. They were where...
										</span>
									</a>
									</li>
									<li>
									<a>
										<span class="image"><img src="<?php echo base_url(); ?>src/images/img.jpg" alt="Profile Image" /></span>
										<span>
										<span>John Smith</span>
										<span class="time">3 mins ago</span>
										</span>
										<span class="message">
										Film festivals used to be do-or-die moments for movie makers. They were where...
										</span>
									</a>
									</li>
									<li>
									<div class="text-center">
										<a>
										<strong>See All Alerts</strong>
										<i class="fa fa-angle-right"></i>
										</a>
									</div>
									</li>
								</ul-->
							</li>
							<?php include 'notif_tello.php' ?>

						</ul>
					</nav>
				</div>
			</div>
			<!-- /top navigation -->

			<!-- page content -->
			<div class="right_col" role="main">
				<div class="container">
					<div class="main--content">
						<div id="messageDiv" class="messageDiv" style="display:none;"> </div>
						<h5 id="lokasi_sekarang"></h5>
						<!-- <button class="btn" id="ShowUser" onclick="getLocation()">Tampilkan Posisi</button> -->
						<!-- <button class="btn" id="ShowUser" onclick="updateTable()">Tampilkan User</button> -->
						<div class="attendance-button">
							<button hidden id="startButton" class="add">Launch Facial Recognition</button>
							<button id="endButton" class="add" style="display:none">End Attendance Process</button>
							<button hidden id="endAttendance" class="add">END Attendance Taking</button>
						</div>

						<div class="video-container">
							<video id="video" class="video-class" width="320" height="240" autoplay muted></video>
							<canvas id="overlay"></canvas>
						</div>

						<div class="table-container">

							<div id="studentTableContainer">

							</div>

						</div>
						<p id="location"></p>
					</div>
				</div>

				<!-- /page content -->

				<!-- footer content -->

				<!-- /footer content -->
			</div>
		</div>

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

		<!-- Custom Theme Scripts -->
		<script src="<?php echo base_url(); ?>src/build/js/custom.min.js"></script>
		<script src="<?php echo base_url(); ?>src/build/js/owl.carousel.min.js"></script>

		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<script>
			$('.owl-carousel').owlCarousel({
				loop: true,
				margin: 10,
				responsiveClass: true,
				responsive: {
					0: {
						items: 1,
						nav: true
					},
					600: {
						items: 3,
						nav: false
					},
					1000: {
						items: 1,
						nav: true,
						loop: true,
						autoplay: true,
					}
				}
			})
		</script>
		<script>
			const locations = [{
					name: "Graha Dirgantara",
					latitude: -6.2559536,
					longitude: 106.8826187,
					radius: 0.5, // Radius in kilometers
				},
				{
					name: "Parkir Bandes",
					latitude: -6.2586284,
					longitude: 106.8820789,
					radius: 0.5, // Radius in kilometers
				},
				{
					name: "Mlejit",
					latitude: -6.2638584,
					longitude: 106.8856266,
					radius: 0.5, // Radius in kilometers
				},
			];

			function getLocation() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(showPosition, showError, {
						enableHighAccuracy: false
					});
				} else {
					Swal.fire('Error', 'Geolocation is not supported by this browser.', 'error');
				}
			}

			function showPosition(position) {
				const userLatitude = position.coords.latitude;
				const userLongitude = position.coords.longitude;

				let isWithinRange = false;
				let locationName = "";

				// Check each location
				for (const location of locations) {
					if (isWithinRadius(userLatitude, userLongitude, location.latitude, location.longitude, location.radius)) {
						isWithinRange = true;
						locationName = location.name;
						break;
					}
				}

				if (isWithinRange) {
					$('#lokasi_sekarang').text('Lokasi Sekarang ' + locationName);
					Swal.fire('Success', `You are within range of ${locationName}. Updating table...`, 'success');
					updateTable();
				} else {
					Swal.fire('Alert', 'You are not in the correct location.', 'warning');
				}
			}

			function showError(error) {
				switch (error.code) {
					case error.PERMISSION_DENIED:
						Swal.fire('Error', 'Permission to access location was denied.', 'error');
						break;
					case error.POSITION_UNAVAILABLE:
						Swal.fire('Error', 'Location information is unavailable.', 'error');
						break;
					case error.TIMEOUT:
						Swal.fire('Error', 'The request to get your location timed out.', 'error');
						break;
					case error.UNKNOWN_ERROR:
						Swal.fire('Error', 'An unknown error occurred.', 'error');
						break;
				}
			}

			// Function to calculate distance between two coordinates
			function isWithinRadius(lat1, lon1, lat2, lon2, radiusInKm) {
				const toRadians = (degrees) => degrees * (Math.PI / 180);
				const earthRadiusKm = 6371;

				const dLat = toRadians(lat2 - lat1);
				const dLon = toRadians(lon2 - lon1);
				const a =
					Math.sin(dLat / 2) * Math.sin(dLat / 2) +
					Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
					Math.sin(dLon / 2) * Math.sin(dLon / 2);
				const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

				const distance = earthRadiusKm * c;
				return distance <= radiusInKm;
			}

			function updateTable() {
				var xhr = new XMLHttpRequest();
				xhr.open("POST", "fetch_user", true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

				xhr.onreadystatechange = function() {
					if (xhr.readyState === 4 && xhr.status === 200) {
						var response = JSON.parse(xhr.responseText);
						if (response.status === "success") {
							students = response.data; // Store the student data
							labels = students.map(student => student.username);
							console.log(labels);
							updateOtherElements();

							document.getElementById("studentTableContainer").innerHTML = response.html;
						} else if (response.status === "No Picture") {
							Swal.fire('Alert', 'Picture Not Found, Please take Picture first', 'warning');

						} else {
							console.error("Error:", response.message);
						}
					}
				};

				xhr.send();
			}

			function markAttendance(detectedFaces) {
				document.querySelectorAll("#studentTableContainer tr").forEach((row) => {
					const username = row.cells[0].innerText.trim();
					if (detectedFaces.includes(username)) {
						row.cells[2].innerText = "present";
						Swal.fire('Success', `Anda Berhasil Melakukan Absensi`, 'success');
						sendAttendanceDataToServer();
						const videoContainer = document.querySelector(".video-container");
						videoContainer.style.display = "none";
						stopWebcam();
					}
				});
			}

			function updateOtherElements() {
				const video = document.getElementById("video");
				const videoContainer = document.querySelector(".video-container");
				const startButton = document.getElementById("startButton");
				let webcamStarted = false;
				let modelsLoaded = false;

				Promise.all([
						faceapi.nets.ssdMobilenetv1.loadFromUri("../models"),
						faceapi.nets.faceRecognitionNet.loadFromUri("../models"),
						faceapi.nets.faceLandmark68Net.loadFromUri("../models"),
					])
					.then(() => {
						modelsLoaded = true;
						console.log("models loaded successfully");
						videoContainer.style.display = "flex";
						if (!webcamStarted && modelsLoaded) {
							startWebcam();
							webcamStarted = true;
						}
					})
					.catch(() => {
						alert("models not loaded, please check your model folder location");
					});
				startButton.addEventListener("click", async () => {
					videoContainer.style.display = "flex";
					if (!webcamStarted && modelsLoaded) {
						startWebcam();
						webcamStarted = true;
					}
				});

				function startWebcam() {
					navigator.mediaDevices.getUserMedia({
						video: true,
						audio: false
					}).then((stream) => {
						video.srcObject = stream;
						videoStream = stream;
					}).catch((error) => {
						console.error("Error accessing webcam:", error);
						alert("Please allow webcam access.");
					});

				}

				async function getLabeledFaceDescriptions() {
					const labeledDescriptors = [];

					for (const label of labels) {
						console.log(labels);
						const descriptions = [];
						// Find the student matching the username (label)
						const student = students.find(s => s.username === label);

						if (student) {
							const nama = student.nama; // Get the student's first name
							const username = student.username; // Get the registration number
							for (let i = 1; i <= 5; i++) {
								try {
									const img = await faceapi.fetchImage(
										`../resources/labels/${label}/${i}.png`
									);
									const detections = await faceapi
										.detectSingleFace(img)
										.withFaceLandmarks()
										.withFaceDescriptor();

									if (detections) {
										descriptions.push(detections.descriptor);
									} else {
										console.log(`No face detected in ${label}/${i}.png`);
									}
								} catch (error) {
									console.error(`Error processing ${label}/${i}.png:`, error);
								}
							}

							if (descriptions.length > 0) {
								labeledDescriptors.push(
									new faceapi.LabeledFaceDescriptors(nama, descriptions) // Use nama here
								);
							}
						}
					}

					return labeledDescriptors;
				}

				video.addEventListener("play", async () => {
					const labeledFaceDescriptors = await getLabeledFaceDescriptions();
					const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

					const canvas = faceapi.createCanvasFromMedia(video);
					videoContainer.appendChild(canvas);

					const displaySize = {
						width: video.width,
						height: video.height
					};
					faceapi.matchDimensions(canvas, displaySize);

					setInterval(async () => {
						const detections = await faceapi
							.detectAllFaces(video)
							.withFaceLandmarks()
							.withFaceDescriptors();

						const resizedDetections = faceapi.resizeResults(detections, displaySize);

						canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

						const results = resizedDetections.map((d) => {
							return faceMatcher.findBestMatch(d.descriptor);
						});

						// Now map the results to include registration numbers
						detectedFaces = results.map((result) => {
							// We are returning the registration number instead of nama
							const student = students.find(s => s.nama === result.label);
							return student ? student.username : null;
						}).filter(Boolean); // Remove any null values

						console.log(detectedFaces); // Here you'll see the registration numbers
						markAttendance(detectedFaces);

						results.forEach((result, i) => {
							const box = resizedDetections[i].detection.box;
							const drawBox = new faceapi.draw.DrawBox(box, {
								label: result.label, // You can keep nama as label here for visual purposes
							});
							drawBox.draw(canvas);
						});
					}, 100);
				});

			}

			function sendAttendanceDataToServer() {
				const attendanceData = [];

				document
					.querySelectorAll("#studentTableContainer tr")
					.forEach((row, index) => {
						if (index === 0) return;
						const username = row.cells[0].innerText.trim();
						const nama = row.cells[1].innerText.trim();
						const attendanceStatus = row.cells[2].innerText.trim();

						attendanceData.push({
							username,
							nama,
							attendanceStatus
						});
					});

				const xhr = new XMLHttpRequest();
				xhr.open("POST", "recordAttendance", true);
				xhr.setRequestHeader("Content-Type", "application/json");

				xhr.onreadystatechange = function() {
					if (xhr.readyState === 4) {
						if (xhr.status === 200) {
							try {
								const response = JSON.parse(xhr.responseText);

								if (response.status === "success") {
									showMessage(
										response.message || "Attendance recorded successfully."
									);
								} else {
									showMessage(
										response.message ||
										"An error occurred while recording attendance."
									);
								}
							} catch (e) {
								showMessage("Error: Failed to parse the response from the server.");
								console.error(e);
							}
						} else {
							showMessage(
								"Error: Unable to record attendance. HTTP Status: " + xhr.status
							);
							console.error("HTTP Error", xhr.status, xhr.statusText);
						}
					}
				};

				xhr.send(JSON.stringify(attendanceData));
			}

			function showMessage(message) {
				var messageDiv = document.getElementById("messageDiv");
				messageDiv.style.display = "block";
				messageDiv.innerHTML = message;
				console.log(message);
				messageDiv.style.opacity = 1;
				setTimeout(function() {
					messageDiv.style.opacity = 0;
				}, 5000);
			}

			function stopWebcam() {
				if (videoStream) {
					const tracks = videoStream.getTracks();

					tracks.forEach((track) => {
						track.stop();
					});

					video.srcObject = null;
					videoStream = null;
				}
			}

			document.getElementById("endAttendance").addEventListener("click", function() {
				sendAttendanceDataToServer();
				const videoContainer = document.querySelector(".video-container");
				videoContainer.style.display = "none";
				stopWebcam();
			});

			const callOnceLocation = getLocation();

			callOnceLocation();
		</script>
		<script src='<?= base_url() ?>resources/assets/javascript/active_link.js'></script>
		<!-- <script src='<?= base_url() ?>resources/assets/javascript/face_logics/script.js'></script> -->
</body>

</html>