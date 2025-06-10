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
						<?php if ($this->uri->segment(4) != 'e' && $this->uri->segment(3) == true) { ?>
							</br>
							<table>
								<tr>
									<th width="200">Usernamee</th>
									<td>: <?= $user->username ?></td>
								</tr>
								<tr>
									<th>Nama</th>
									<td>: <?= $user->nama ?></td>

								</tr>
								<tr>
									<th>Level</th>
									<td>: <?= $user->level ?></td>
								</tr>
								<tr>
									<th>Status</th>
									<td>:<?php if ($user->status == 1) { ?>
										<span style="cursor: default;" class="btn btn-primary">Active</span>
									<?php } else { ?>
										<span style="cursor: default;" class="btn btn-danger">Not Active</span>
									<?php } ?>
									</td>
								</tr>
								<tr>
									<th>Email</th>
									<td>: <?= $user->email ?></td>
								</tr>
								<tr>
									<th>Phone</th>
									<td>: <?= $user->phone ?></td>
								</tr>
								<tr>
									<th>Code Agent</th>
									<td>: <?= $user->kd_agent ?></td>
								</tr>
								<tr>
									<th>Nip</th>
									<td>: <?= $user->nip ?></td>
								</tr>
								<tr>
									<th>Level</th>
									<td>: <?= $user->level_jabatan ?></td>
								</tr>
								<tr>
									<th>Bagian</th>
									<td>: <?= $user->bagian ?></td>
								</tr>
								<tr>
									<th>TMT</th>
									<td>: <?= date('d F Y', strtotime($user->tmt)) ?></td>
								</tr>
								<tr>
									<th>Cuti Reguler</th>
									<td>: <?= $user->cuti ?></td>
								</tr>
								<tr>
									<th>Nama Jabatan</th>
									<td>: <?= $user->nama_jabatan ?></td>
								</tr>
								<tr>
									<th>Nama Jabatan</th>
									<td>: <?= $user->nama_jabatan ?></td>
								</tr>

								<tr>
									<th>Lokasi Presensi</th>
									<?php

									$lokasi = $this->db->get_where('lokasi_presensi', ['id' => $user->id_lokasi_presensi])->row();									?>
									<td>: <?= $lokasi->nama_lokasi ?></td>
								</tr>
								<tr>
									<th>Jam Masuk</th>
									<td>: <?= $user->jam_masuk ?></td>
								</tr>
								<tr>
									<th>Jam Keluar</th>
									<td>: <?= $user->jam_keluar ?></td>
								</tr>
								<tr>
									<th>Supervisi</th>
									<td>:
										<?php
										if ($user->supervisi != null || $user->supervisi != "") {
											$sv = $this->db->get_where('users', ['nip' => $user->supervisi])->row();
											echo $sv->nama_jabatan;
										} else {
											echo "";
										}
										// if ($user->level_jabatan < 3 && $user->bagian != null) {
										// 	$sv = $this->db->get_where('users', [
										// 		'bagian' => $user->bagian,
										// 		'level_jabatan' => 3
										// 	])->row();
										// 	echo $sv->nama_jabatan;
										// } else {
										// 	echo "";
										// }

										// if ($user->level_jabatan >= 3) {
										// 	$sv = $this->db->get_where('users', [
										// 		'nip' => $user->supervisi
										// 	])->row();
										// 	echo $sv->nama_jabatan;
										// }
										?>
									</td>
								</tr>
								<tr>
									<th><a href="<?= base_url('app/user') ?>" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a></th>
								</tr>
							</table>
							<br>
						<?php } elseif ($this->uri->segment(3) == false) { ?> <!-- add user -->
							<?= $this->session->flashdata('msg') ?>
							<form action="<?= base_url('app/add_user') ?>" method="POST">
								<input type="hidden" value="add" name="add">
								<input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
								<table>
									<tr>
										<th width="300">Username</th>
										<td width="300"> <input type="text" value="<?php echo set_value('username'); ?>" name="username" class="form-control"></td>
									</tr>
									<tr>
										<th width="300">Password</th>
										<td width="300"> <input type="text" name="password" class="form-control"></td>
									</tr>
									<tr>
										<th width="300">Password Confirmation</th>
										<td width="300"> <input type="text" name="password_confirmation" class="form-control"></td>
									</tr>
									<tr>
										<th width="200">Name</th>
										<td> <input type="text" name="nama" class="form-control">
										</td>
									</tr>
									<tr>
										<th width="200">Date of birth</th>
										<td>
											<div class='input-group date' id='myDatepicker2'>
												<input type='text' id='date_pic' name='tgl_lahir' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<th width="200">Level</th>
										<td>
											<select class="form-control js-example-basic-multiple" name="level[]" multiple="multiple">
												<?php
												$level_x = explode(',', $user->level);
												$x = $this->db->get('menu')->result();
												foreach ($x as $k) {
													// foreach($level_x as $o) {
													if (strpos($user->level, $k->level) !== false) {
												?>
														<option selected="selected" value="<?= $k->level ?>"><?= $k->nama ?>
														</option>
													<?php } else { ?>
														<option value="<?= $k->level ?>"><?= $k->nama ?></option>

												<?php }
													//}
												} ?>
											</select>
										</td>
									</tr>

									<tr>
										<th>Status</th>
										<td>
											<input name="status" value="1" type="radio" id="active">
											<label for="active">Active</label>
											<input name="status" value="0" type="radio" id="noactive">
											<label for="noactive">Not Active</label>
										</td>
									</tr>
									<tr>
										<th width="200">Email</th>
										<td> <input type="text" name="email" class="form-control"></td>
									</tr>
									<tr>
										<th>Phone</th>
										<td><input type="text" name="phone" class="form-control"></td>
									</tr>
									<tr>
										<th>Code Agent</th>
										<td><input type="text" name="kd_agent" class="form-control"></td>
									</tr>
									<tr>
										<th>Nip</th>
										<td><input type="text" name="nip" class="form-control"></td>
									</tr>
									<tr>
										<th>Level Jabatan</th>
										<td>
											<select name="level_jabatan" id="" class="form-control">
												<option value="">Pilih Jabatan</option>
												<option value="1">Staff</option>
												<option value="2">Supervisor</option>
												<option value="3">Manajer</option>
												<option value="4">General Manajer</option>
												<option value="5">Direktur</option>
												<option value="6">Direktur Utama</option>

											</select>
										</td>
									</tr>
									<tr>
										<th>Bagian</th>
										<td>
											<select name="bagian" class="form-control" id="">
												<?php $xx = $this->db->get('bagian')->result();
												foreach ($xx as $k) {
													if (!empty($user)) {
												?>
														<option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->nama ?></option>
													<?php } else { ?>
														<option value="<?= $k->Id ?>"><?= $k->nama ?></option>
												<?php }
												} ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>Nama Jabatan</th>
										<td><input type="text" name="nama_jabatan" class="form-control"></td>
									</tr>
									<tr>
										<th>Supervisi</th>
										<td>
											<select name="supervisi" id="" class="form-control js-example-basic-multiple">
												<?php
												$supervisi = $this->db->get_where('users', ['level_jabatan >=' => 3])->result();
												foreach ($supervisi as $data) { ?>
													<option value="<?= $data->nip ?>"><?= $data->nama_jabatan ?></option>
												<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>TMT</th>
										<td>
											<div class='input-group date' id='myDatepicker2'>
												<input type='text' name='tmt' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</span>
											</div>
										</td>
									</tr>
									<tr>
										<th>Cuti Reguler</th>
										<td><input type="number" name="cuti" class="form-control"></td>
									</tr>
									<tr>
										<th>
											Lokasi Presensi
										</th>
										<td>
											<select name="lokasi_presensi" class="form-control js-example-basic-multiple">
												<option value=""> -- Pilih Lokasi Presensi --</option>
												<?php
												$lokasi = $this->db->get('lokasi_presensi')->result();
												foreach ($lokasi as $data) {
												?>
													<option value="<?= $data->id ?>"><?= $data->nama_lokasi ?></option>
												<?php } ?>
											</select>
										</td>
									</tr>
									<tr>
										<th>Jam Masuk</th>
										<td><input type="time" name="jam_masuk" class="form-control"></td>
									</tr>
									<tr>
										<th>Jam Keluar</th>
										<td><input type="time" name="jam_keluar" class="form-control"></td>
									</tr>
									<tr>
										<th>
											<a class="btn btn-warning" href="<?= base_url('app/user') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
										</th>
										<td><button type="submit" class="btn btn-primary">Submit</button></td>
									</tr>
								</table>
							</form>
						<?php  } else if ($this->uri->segment(4) == 'e') { ?>
							</br>
							<?= $this->session->flashdata('msg') ?>
							<form action="<?= base_url('app/user_edit/' . $this->uri->segment('3')) ?>" method="POST">
								<input type="hidden" value="edit" name="edit">
								<input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Username</label>
											<input readonly type="text" name="username" class="form-control" value="<?= $user->username ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Name</label>
											<input type="text" name="nama" class="form-control" value="<?= $user->nama ?>">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Level</label>
											<select class="form-control js-example-basic-multiple" name="level[]" multiple="multiple">
												<?php
												$level_x = explode(',', $user->level);
												$x = $this->db->get('menu')->result();
												foreach ($x as $k) {
													// foreach($level_x as $o) {
													if (strpos($user->level, $k->level) !== false) {
												?>
														<option selected="selected" value="<?= $k->level ?>"><?= $k->nama ?>
														</option>
													<?php } else { ?>
														<option value="<?= $k->level ?>"><?= $k->nama ?></option>

												<?php }
													//}
												} ?>
											</select>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-check">
											<label>Status</label>
											<br>
											<input <?= $user->status ? 'checked' : '' ?> name="status" type="radio" value="1" id="active" class="form-check-input">
											<label class="form-check-label" for="active">Active</label>
											<br>
											<input <?= $user->status ? '' : 'checked' ?> name="status" type="radio" value="0" id="noactive" class="form-check-input">
											<label class="form-check-label" for="noactive">Not Active</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Email</label>
											<input type="text" name="email" class="form-control" value="<?= $user->email ?>">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Phone</label>
											<input type="text" name="phone" class="form-control" value="<?= $user->phone ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Code Agent</label>
											<input type="text" name="kd_agent" class="form-control" value="<?= $user->kd_agent ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Nip</label>
											<input readonly type="text" name="nip" class="form-control" value="<?= $user->nip ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Level Jabatan</label>
											<input type="text" name="level_jabatan" class="form-control" value="<?= $user->level_jabatan ?>">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>TMT</label>
											<input type="date" name="tmt" class="form-control" value="<?= $user->tmt ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Bagian</label>
											<select name="bagian" class="form-control js-example-basic-multiple" id="">
												<option value=""> -- Pilih Bagian --</option>
												<?php $xx = $this->db->get('bagian')->result();
												foreach ($xx as $k) { ?>
													<option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->nama ?></option>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Nama Jabatan</label>
											<input type="text" name="nama_jabatan" class="form-control" value="<?= $user->nama_jabatan ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Supervisi</label>
											<select name="supervisi" class="form-control js-example-basic-multiple">
												<option value=""> -- Pilih Supervisi --</option>
												<?php
												$supervisi = $this->db->get_where('users', ['level_jabatan >=' => 3])->result();
												foreach ($supervisi as $data) {
													if ($user->supervisi != null || $user->supervisi != "") {
														$super_visi = $this->db->get_where('users', ['nip' => $user->supervisi])->row();
														$selected = $super_visi->nip == $data->nip ? "selected" : "";
													} else {
														$selected = "";
													}
												?>
													<option <?= $selected ?> value="<?= $data->nip ?>"><?= $data->nama_jabatan ?></option>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Cuti</label>
											<input type="number" name="cuti" class="form-control" value="<?= $user->cuti ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Lokasi Presensi</label>
											<select name="lokasi_presensi" class="form-control js-example-basic-multiple">
												<option value=""> -- Pilih Lokasi Presensi --</option>
												<?php
												$lokasi = $this->db->get('lokasi_presensi')->result();
												foreach ($lokasi as $data) {
													if ($user->id_lokasi_presensi != null || $user->id_lokasi_presensi != "") {
														$selected = $user->id_lokasi_presensi == $data->id ? "selected" : "";
													} else {
														$selected = "";
													}
												?>
													<option <?= $selected ?> value="<?= $data->id ?>"><?= $data->nama_lokasi ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Jam Masuk</label>
											<input type="time" name="jam_masuk" class="form-control" value="<?= $user->jam_masuk ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Jam Keluar</label>
											<input type="time" name="jam_keluar" class="form-control" value="<?= $user->jam_keluar ?>">
										</div>
									</div>

									<div class="col-12">
										<div class="form-group">
											<a class="btn btn-warning" href="<?= base_url('app/user') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
											<button type="submit" class="btn btn-primary">Update</button>
										</div>
									</div>

								</div>
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
	</script>
</body>

</html>