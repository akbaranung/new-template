<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-header mb-4">
				<h1 class="page-title">Upload Gaji</h1>
				<p class="text-muted">Upload file Excel untuk data gaji karyawan (Monthly & Daily)</p>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Upload Gaji Monthly -->
		<div class="col-lg-6 col-md-12 mb-4">
			<div class="card shadow-sm h-100">
				<div class="card-header bg-primary text-white d-flex align-items-center">
					<i class="mdi mdi-calendar-month mr-2"></i>
					<strong class="text-white">Upload Gaji (Monthly)</strong>
				</div>
				<div class="card-body">
					<?php echo $this->session->flashdata('notif') ?>

					<form method="POST" id="form_input" name="form_input" action="<?php echo base_url() ?>hris/upload_gaji_monthly" enctype="multipart/form-data">

						<!-- Info Box -->
						<div class="alert alert-info alert-dismissible fade show" role="alert">
							<i class="mdi mdi-information-outline mr-2"></i>
							<strong>Informasi:</strong> Upload file Excel (.xlsx) untuk gaji bulanan karyawan
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<!-- File Input with Custom Style -->
						<div class="form-group">
							<label for="userfile_monthly" class="font-weight-bold">
								<i class="mdi mdi-file-excel text-success"></i> Pilih File Excel
							</label>
							<div class="custom-file">
								<input type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="userfile" class="custom-file-input" id="userfile_monthly" required>
								<label class="custom-file-label" for="userfile_monthly">Pilih file...</label>
							</div>
							<small class="form-text text-muted">
								<i class="mdi mdi-alert-circle-outline"></i> Format: .xlsx | Max size: 5MB
							</small>
						</div>

						<!-- Upload Button -->
						<div class="form-group mb-0">
							<button name="btn-submit" id="btn-submit" type="button" class="btn btn-success btn-block text-white">
								<i class="mdi mdi-upload"></i> Upload File Monthly
							</button>
						</div>

					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-6 col-md-12 mb-4">
			<div class="card shadow-sm">
				<div class="card-body bg-light">
					<div class="d-flex align-items-center justify-content-between">
						<div>
							<h5 class="mb-1"><i class="mdi mdi-download text-primary"></i> Download Template</h5>
							<p class="text-muted mb-0">Unduh template Excel untuk memudahkan proses upload</p>
						</div>
						<div>
							<a href="<?php echo base_url() ?>assets/template_upload_gajian_monthly.xlsx" class="btn btn-outline-primary mr-2" download>
								<i class="fe fe-file mr-2"></i> Template Monthly
							</a>
							<!-- <a href="<?php echo base_url() ?>assets/templates/template_upload_gaji_daily.xlsx" class="btn btn-outline-info disabled">
								<i class="mdi mdi-file-download"></i> Template Daily
							</a> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div> <!-- .container-fluid -->

<!-- Scripts -->
<script src="<?php echo base_url(); ?>assets/vendors/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/vendors/sweetalert2/sweetalert2.all.min.js"></script>

<script type="text/javascript">
	// Custom file input - show filename
	$('.custom-file-input').on('change', function() {
		let fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').addClass("selected").html(fileName);
	});

	// Validation file size (5MB max)
	function validateFileSize(input) {
		const fileSize = input.files[0].size / 1024 / 1024; // in MB
		if (fileSize > 5) {
			Swal.fire({
				icon: 'error',
				title: 'File Terlalu Besar!',
				text: 'Ukuran file maksimal 5MB',
				confirmButtonColor: '#d33'
			});
			$(input).val('');
			$(input).next('.custom-file-label').removeClass("selected").html('Pilih file...');
			return false;
		}
		return true;
	}

	// File validation on change
	$('#userfile_monthly, #userfile_daily').on('change', function() {
		validateFileSize(this);
	});

	// Sweet alert untuk upload gaji monthly
	$('#btn-submit').on('click', function(e) {
		e.preventDefault();
		var form = $(this).parents('form');
		var fileInput = $('#userfile_monthly');

		if (!fileInput.val()) {
			Swal.fire({
				icon: 'warning',
				title: 'Perhatian!',
				text: 'Silakan pilih file terlebih dahulu',
				confirmButtonColor: '#ffc107'
			});
			return false;
		}

		Swal.fire({
			icon: 'question',
			title: 'Konfirmasi Upload',
			text: 'Data gaji bulanan akan diupload. Lanjutkan?',
			showCancelButton: true,
			confirmButtonColor: '#28a745',
			cancelButtonColor: '#6c757d',
			confirmButtonText: '<i class="mdi mdi-check"></i> Ya, Upload!',
			cancelButtonText: '<i class="mdi mdi-close"></i> Batal',
			reverseButtons: true,
			showLoaderOnConfirm: true,
			allowOutsideClick: false,
			preConfirm: () => {
				return new Promise((resolve) => {
					form.submit();
					resolve();
				});
			}
		});
	});

	// Sweet alert untuk upload gaji daily
	$('#btn-submit2').on('click', function(e) {
		e.preventDefault();
		var form = $(this).parents('form');
		var fileInput = $('#userfile_daily');

		if (!fileInput.val()) {
			Swal.fire({
				icon: 'warning',
				title: 'Perhatian!',
				text: 'Silakan pilih file terlebih dahulu',
				confirmButtonColor: '#ffc107'
			});
			return false;
		}

		Swal.fire({
			icon: 'question',
			title: 'Konfirmasi Upload',
			text: 'Data gaji harian akan diupload. Lanjutkan?',
			showCancelButton: true,
			confirmButtonColor: '#17a2b8',
			cancelButtonColor: '#6c757d',
			confirmButtonText: '<i class="mdi mdi-check"></i> Ya, Upload!',
			cancelButtonText: '<i class="mdi mdi-close"></i> Batal',
			reverseButtons: true,
			showLoaderOnConfirm: true,
			allowOutsideClick: false,
			preConfirm: () => {
				return new Promise((resolve) => {
					form.submit();
					resolve();
				});
			}
		});
	});
</script>
<style>
	/* Custom styling untuk enhancement */
	.page-header {
		border-bottom: 2px solid #f0f0f0;
		padding-bottom: 15px;
	}

	.card {
		border: none;
		transition: transform 0.2s;
	}

	.card:hover {
		transform: translateY(-5px);
	}

	.custom-file-label::after {
		content: "Browse";
	}

	.btn-block {
		padding: 12px;
		font-weight: 600;
	}

	.alert {
		border-left: 4px solid;
	}

	.alert-info {
		border-left-color: #17a2b8;
	}

	.alert-warning {
		border-left-color: #ffc107;
	}
</style>