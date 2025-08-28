<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet" />

<style>
	.col-xs-3 {
		width: 25%;
		background-color: #004e81;
	}

	.btn_footer_panel .tag_ {
		padding-top: 37px;
	}

	tr>th {
		/* background-color: #e91f62; */
		background-color: #3498db;
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
</style>

<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Cuti List</h1>
			<div class="card shadow mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<p class="card-title"><strong>List Cuti</strong></p>

					<div>
						<?php
						$nipSession = $this->session->userdata('nip');
						$id_perusahaan = $this->session->userdata('user_perusahaan_id');
						$user = $this->db->get_where('users', ['nip' => $nipSession])->row();

						$sql = ("SELECT COUNT(nip) as total FROM cuti WHERE status_atasan is null AND atasan = '$nipSession' AND nip != '$nipSession' AND id_perusahaan = '$id_perusahaan'
						");
						$countSupervisi = $this->db->query($sql)->row();


						$sql = ("SELECT COUNT(nip) as total FROM cuti WHERE status_atasan is null AND atasan = '$nipSession' AND nip != '$nipSession' AND 
						CASE 
						    WHEN cuti.jenis = 2 THEN status_hrd = 'Disetujui' AND cuti.atasan != cuti.dirsdm AND cuti.atasan != cuti.dirut
							ELSE status_hrd = 'Disetujui'
						END
						");
						$countAtasan = $this->db->query($sql)->row();

						// $sql = ("SELECT COUNT(nip) as total FROM cuti WHERE status_hrd is null AND nip != '$nipSession' AND id_perusahaan = '$id_perusahaan'");
						$sql = ("SELECT COUNT(nip) as total FROM cuti WHERE status_hrd is null AND nip != '$nipSession' AND status_atasan = 'Disetujui' AND id_perusahaan = '$id_perusahaan'");
						$countHrd = $this->db->query($sql)->row();

						$sql = ("SELECT COUNT(nip) as total FROM cuti WHERE cuti.jenis = 2 AND cuti.status_dirsdm is null AND
						CASE 
							WHEN cuti.atasan = cuti.dirsdm THEN (cuti.status_hrd = 'Disetujui' OR cuti.status_atasan = 'Disetujui') 
							WHEN cuti.atasan = cuti.dirut THEN(cuti.status_hrd = 'Disetujui' OR cuti.status_atasan = 'Disetujui') 
							ELSE (cuti.status_hrd = 'Disetujui' AND cuti.status_atasan = 'Disetujui') END");
						$countDirsdm = $this->db->query($sql)->row();

						$sql = ("SELECT COUNT(nip) as total FROM cuti WHERE cuti.jenis = 2 AND cuti.status_dirut is null AND cuti.status_dirsdm = 'Disetujui' AND
						CASE 
							WHEN cuti.atasan = cuti.dirsdm THEN (cuti.status_hrd = 'Disetujui' OR cuti.status_atasan = 'Disetujui') 
							WHEN cuti.atasan = cuti.dirut THEN(cuti.status_hrd = 'Disetujui' OR cuti.status_atasan = 'Disetujui') 
							ELSE (cuti.status_hrd = 'Disetujui' AND cuti.status_atasan = 'Disetujui') END
						");

						$countDirut = $this->db->query($sql)->row();

						if ($user->level_jabatan == '5' && $user->bagian == '11') {
							$count = $countDirsdm->total;
						}

						if ($user->level_jabatan == '6') {
							$count = $countDirut->total;
						}

						?>

						<button class="btn btn-primary" data-toggle="modal" data-target="#cutiModal">
							<i class="fa fa-file-text-o" aria-hidden="true"></i> Form Cuti
						</button>

						<!-- <?php if ($user->level_jabatan == '2') { ?>
							<a href="<?= base_url('cuti/data_approve_supervisi_view') ?>" class="btn btn-pink"><i class="fa fa-list-ul" aria-hidden="true"></i> Approval Supervisi <span class="badge bg-primary"><?= $countSupervisi->total > 0 ? $countSupervisi->total : "" ?></span></a>
						<?php } ?>
						<?php if ($user->level_jabatan == '3') { ?>
							<a href="<?= base_url('cuti/data_approve_hrd_view') ?>" class="btn btn-pink"><i class="fa fa-list-ul" aria-hidden="true"></i> Approval Hrd <span class="badge bg-primary"><?= $countHrd->total > 0 ? $countHrd->total : "" ?></span></a>
						<?php } ?> -->
						<?php if ($user->level_jabatan >= '3') { ?>
							<a href="<?= base_url('cuti/data_approve_atasan_view') ?>" class="btn btn-pink"><i class="fa fa-list-ul" aria-hidden="true"></i> Approval Atasan <span class="badge bg-red"><?= $countAtasan->total > 0 ? $countAtasan->total : "" ?></span></a>
						<?php } ?>
						<?php if (($user->level_jabatan == '5' && $user->bagian == '11') || $user->level_jabatan == '6') { ?>
							<a href="<?= base_url('cuti/data_approve_direksi_view') ?>" class="btn btn-pink"><i class="fa fa-list-ul" aria-hidden="true"></i> Approval Direksi <span class="badge bg-red"><?= $count > 0 ? $count : "" ?></span></a>
						<?php } ?>
						<?php if ($user->level_jabatan == '4') { ?>
							<a href="<?= base_url('cuti/cuti_all') ?>" class="btn btn-pink"><i class="fa fa-list-ul" aria-hidden="true"></i> List Cuti</a>
						<?php } ?>
					</div>
					<!-- <a href="#" id="addCabangBtn" class="btn btn-primary">
						Add Cabang
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
							<path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
						</svg>
					</a> -->

				</div>
				<div class="card-body" id="all">
					<div class="table-responsive">
						<table id="table-all" class="table table-striped table-bordered" style="width:100%">
							<thead>
								<tr>
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
						</table>
					</div>
				</div>
			</div>
		</div> <!-- .col-12 -->
	</div> <!-- .row -->
</div> <!-- .container-fluid -->
<!-- Modal Form Cuti User -->
<div class="modal fade " id="cutiModal">
	<div class="modal-dialog modal-centered">
		<div class="modal-content">
			<!-- header-->
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Form Pengajuan Cuti Online</h4>

				<button class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<!--body-->
			<div class="modal-body">
				<form action="" id="formCuti" method="post">
					<div class="form-group">
						<label for="nama">Nama Karyawan</label>
						<input type="text" readonly class="form-control" placeholder="Nama Karyawan" id="nama" name="nama" value="<?= $this->session->userdata('nama') ?>">
					</div>
					<div class="form-group" id="error_jenis">
						<label for="jenisCuti">Jenis Cuti</label>
						<select class="form-control select2" id="jenisCuti" name="jenisCuti" style="width:100%;">
							<option value="">-- Pilih Jenis Cuti --</option>
							<?php foreach ($jenis_cuti as $row) : ?>
								<option value="<?= $row['Id'] ?>"><?= $row['nama_jenis'] ?></option>
							<?php endforeach ?>
						</select>
						<span id="err_jenis" class="text-danger"></span>
					</div>
					<div class="form-group error_detail" id="selectDetail">
						<label for="detailCuti">Detail Cuti</label>
						<select class="form-control select2" id="detailCuti" name="detailCuti" style="width: 100%;">
							<option value="">-- Detail Cuti --</option>
						</select>
						<span id="err_detail" class="text-danger"></span>
					</div>
					<div class="form-group" id="filePendukung">
						<label for="file">Dokumen Pendukung</label>
						<input type="file" class="form-control" id="file" name="file">
						<span id="err_file" class="text-danger"></span>
					</div>
					<div class="form-group">
						<label for="alamat">Alamat Cuti</label>
						<textarea name="alamat" id="alamat" class="form-control"></textarea>
						<span id="err_alamat" class="text-danger"></span>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group" id="error_mulai">
								<label for="mulaiCuti">Dari</label>
								<div class="input-group date">
									<input type="text" class="form-control" placeholder="Mulai Cuti" id="mulaiCuti" name="mulaiCuti" onkeydown="event.preventDefault()" autocomplete="off">
									<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
								</div>
								<span id="err_mulai" class="text-danger"></span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group" id="error_akhir">
								<label for="akhirCuti">Sampai</label>
								<div class="input-group date">
									<input type="text" class="form-control" placeholder="Akhir Cuti" id="akhirCuti" name="akhirCuti" onkeydown="event.preventDefault()" autocomplete="off">
									<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
								</div>
								<span id="err_akhir" class="text-danger"></span>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group" id="error_jumlah">
								<label for="jumlahCuti">Jumlah Cuti</label>
								<input type="text" class="form-control" placeholder="Jumlah Cuti" id="jumlahCuti" name="jumlahCuti" readonly>
								<span id="err_jumlah" class="text-danger"></span>
							</div>
						</div>
					</div>
					<div class="form-group" id="error_alasan">
						<label for="alasan">Alasan Cuti</label>
						<input type="text" class="form-control" placeholder="Alasan Cuti" id="alasan" name="alasan">
						<span id="err_alasan" class="text-danger"></span>
					</div>
					<!-- <div class="form-group" id="error_tujuan">
						<label for="jenisCuti">Tujuan (Contoh Bagian : HRD) </label>
						<select class="form-control select2" id="tujuanCuti" name="tujuanCuti" style="width:100%;">
							<option value="">-- Pilih Jenis Cuti --</option>
							<?php foreach ($bagian as $row) : ?>
								<option value="<?= $row->Id ?>"><?= $row->kode_nama . ' - ' . $row->nama ?></option>
							<?php endforeach ?>
						</select>
						<span id="err_tujuan" class="text-danger"></span>
					</div> -->
					<div class="form-group">
						<label for="sisaCuti">Sisa Cuti Reguler</label>
						<?php $cuti = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array() ?>
						<input type="text" class="form-control" placeholder="Sisa cuti" id="sisaCuti" name="sisaCuti" value="<?= $cuti['cuti'] ?>" readonly>
					</div>
					<div class="form-group">
						<label for="atasan">Nama Atasan</label>
						<?php
						$nip_user = $this->session->userdata('nip');
						$user = $this->db->get_where('users', ['nip' => $nip_user])->row();
						$atasan = $this->db->get_where('users', ['nip' => $user->supervisi])->row();
						$nama_atasan = 'Tidak Ada Atasan';
						$nip_atasan = 'Tidak Ada Atasan';
						if ($atasan) {
							$nama_atasan = $atasan->nama;
							$nip_atasan = $atasan->nip;
						}
						?>

						<input type="text" class="form-control" placeholder="Nama Atasan" id="atasan" name="atasan" value="<?= $nama_atasan ?>" readonly>
						<input type="hidden" class="form-control" placeholder="Nama Atasan" id="nipAtasan" name="nipAtasan" value="<?= $nip_atasan ?>" readonly>
					</div>
					<input type="hidden" name="nip" id="nip" value="<?= $this->session->userdata("nip") ?>">
					<!--footer-->
					<div class="modal-footer">
						<button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
						<button type="button" class="btn btn-primary" id="btnSubmit"><i class="fa fa-paper-plane" aria-hidden="true"></i> Kirim</button>
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
				<h4 class="modal-title" id="myModalLabel">Detail Cuti</h4>
				<button class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<!--body-->
			<div class="modal-body">
				<table class="table" width="100%" id="detail-cuti-byID">

				</table>
			</div>
		</div>
	</div>
</div>