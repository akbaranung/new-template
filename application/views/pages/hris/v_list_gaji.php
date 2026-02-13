<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="page-header mb-4">
				<h1 class="page-title">Slip Gaji Karyawan</h1>
				<p class="text-muted">Cari dan download slip gaji karyawan berdasarkan bulan</p>
			</div>
		</div>
	</div>

	<!-- Search Filter Card -->
	<div class="row">
		<div class="col-lg-12 mb-4">
			<div class="card shadow-sm">
				<div class="card-header bg-primary text-white d-flex align-items-center">
					<i class="mdi mdi-calendar-search mr-2"></i>
					<strong class="text-white">Cari Slip Gaji</strong>
				</div>
				<div class="card-body">
					<?php echo form_open('hris/list_gaji', 'id="form_search_gaji"'); ?>

					<div class="row align-items-end">
						<div class="col-md-4 col-sm-12">
							<div class="form-group mb-0">
								<label for="date_pic" class="font-weight-bold">
									<i class="mdi mdi-calendar-month text-primary"></i> Bulan Gaji <span class="text-danger">*</span>
								</label>
								<div class="input-group">
									<input type="month" id="date_pic" name="date_pic" class="form-control" placeholder="YYYY-MM" value="<?= $this->input->post('date_pic') ?>" required>
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="mdi mdi-calendar"></i>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-8 col-sm-12">
							<div class="form-group mb-0">
								<button type="submit" class="btn btn-primary mr-2">
									<i class="mdi mdi-magnify"></i> Cari
								</button>
								<a href="<?= base_url('hris/list_gaji') ?>" class="btn btn-secondary mr-2">
									<i class="mdi mdi-refresh"></i> Reset
								</a>
								<a href="<?php echo base_url('hris/upload'); ?>" class="btn btn-outline-info">
									<i class="mdi mdi-arrow-left"></i> Kembali
								</a>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 col-sm-12">
							<small class="form-text text-muted mt-2">
								<i class="mdi mdi-information-outline"></i> Pilih bulan dan tahun gaji
							</small>
						</div>
					</div>

					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Results Card -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card shadow-sm">
				<div class="card-header bg-light d-flex justify-content-between align-items-center">
					<div>
						<h5 class="mb-0">
							<i class="mdi mdi-format-list-bulleted text-success"></i> Daftar Slip Gaji
						</h5>
					</div>
					<?php if (!empty($slip)): ?>
						<div>
							<span class="badge badge-primary badge-pill">
								<?php echo count($slip); ?> Data Ditemukan
							</span>
						</div>
					<?php endif; ?>
				</div>
				<div class="card-body">

					<?php if (empty($slip)): ?>
						<!-- Empty State -->
						<div class="text-center py-5">
							<i class="mdi mdi-file-document-outline" style="font-size: 80px; color: #ccc;"></i>
							<h5 class="mt-3 text-muted">Tidak Ada Data Ditemukan</h5>
							<p class="text-muted">Silakan pilih bulan gaji dan klik tombol "Cari Slip Gaji"</p>
						</div>
					<?php else: ?>

						<!-- Search Box -->
						<div class="row mb-3">
							<div class="col-md-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="mdi mdi-magnify"></i>
										</span>
									</div>
									<input type="text" id="searchInput" class="form-control" placeholder="Cari nama karyawan, NIP, atau jabatan...">
								</div>
							</div>
						</div>

						<!-- Table Responsive -->
						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="table-slip-gaji">
								<thead class="thead-dark">
									<tr>
										<th width="5%" class="text-center">No</th>
										<th width="20%">Nama Karyawan</th>
										<th width="15%">Jabatan</th>
										<th width="10%" class="text-center">Bulan Gaji</th>
										<th width="8%" class="text-center">Hari Kerja</th>
										<th width="10%" class="text-center">Periode</th>
										<th width="8%" class="text-center">Tidak Hadir</th>
										<th width="15%" class="text-right">Gaji Bersih</th>
										<th width="9%" class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody id="tableBody">
									<?php
									$no = 1;
									foreach ($slip as $data):
									?>
										<tr>
											<td class="text-center"><?php echo $no; ?></td>
											<td>
												<strong><?php echo $data->nama; ?></strong>
												<?php if (!empty($data->nip)): ?>
													<br><small class="text-muted">NIP: <?php echo $data->nip; ?></small>
												<?php endif; ?>
											</td>
											<td><?php echo $data->jabatan; ?></td>
											<td class="text-center">
												<span class="badge badge-info">
													<?php echo date("M Y", strtotime($data->bulan_gaji)); ?>
												</span>
											</td>
											<td class="text-center">
												<?php echo $data->hari_kerja; ?> hari
											</td>
											<td class="text-center">
												<?php
												if (!empty($data->periode_gaji)) {
													echo '<span class="badge badge-secondary">' . $data->periode_gaji . '</span>';
												} else {
													echo '<span class="text-muted">-</span>';
												}
												?>
											</td>
											<td class="text-center">
												<?php
												if ($data->tidak_hadir > 0) {
													echo '<span class="badge badge-warning">' . $data->tidak_hadir . '</span>';
												} else {
													echo '<span class="badge badge-success text-white">0</span>';
												}
												?>
											</td>
											<td class="text-right">
												<strong class="text-success">
													Rp <?php echo number_format($data->net_gaji, 0, ',', '.'); ?>
												</strong>
											</td>
											<td class="text-center">
												<a href="<?php echo base_url('hris/slip_gaji_pdf/' . $data->Id); ?>"
													class="btn btn-sm btn-primary"
													target="_blank"
													title="Download Slip Gaji">
													Cetak
												</a>
											</td>
										</tr>
									<?php
										$no++;
									endforeach;
									?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>

</div> <!-- .container-fluid -->

<script type="text/javascript">
	// Reset Form Function
	function resetForm() {
		$('#date_pic').val('');
	}

	// Form validation
	$('#form_search_gaji').on('submit', function(e) {
		var date_value = $('#date_pic').val();

		if (!date_value) {
			e.preventDefault();
			Swal.fire({
				icon: 'warning',
				title: 'Perhatian!',
				text: 'Silakan pilih bulan gaji terlebih dahulu',
				confirmButtonColor: '#ffc107'
			});
			return false;
		}
	});

	<?php if (!empty($slip)): ?>
		// Simple Search Function
		$('#searchInput').on('keyup', function() {
			var value = $(this).val().toLowerCase();
			var count = 0;

			$('#tableBody tr').filter(function() {
				var isVisible = $(this).text().toLowerCase().indexOf(value) > -1;
				$(this).toggle(isVisible);
				if (isVisible) count++;
			});

			$('#displayCount').text(count);

			// Show no results message
			if (count === 0) {
				if ($('#noResultsRow').length === 0) {
					$('#tableBody').append('<tr id="noResultsRow"><td colspan="9" class="text-center text-muted py-4"><i class="mdi mdi-magnify"></i> Tidak ada data yang cocok dengan pencarian</td></tr>');
				}
			} else {
				$('#noResultsRow').remove();
			}
		});
	<?php endif; ?>
</script>

<style>
	/* Custom Styling */
	.page-header {
		border-bottom: 2px solid #f0f0f0;
		padding-bottom: 15px;
	}

	.card {
		border: none;
		transition: transform 0.2s;
	}

	.table-hover tbody tr:hover {
		background-color: #f8f9fa;
	}

	/* Empty state styling */
	.text-center i.mdi-file-document-outline {
		animation: pulse 2s infinite;
	}

	@keyframes pulse {

		0%,
		100% {
			opacity: 0.3;
		}

		50% {
			opacity: 0.6;
		}
	}

	/* Badge styling */
	.badge {
		font-size: 0.85rem;
		padding: 0.35em 0.65em;
	}

	/* Search input styling */
	#searchInput {
		border-radius: 0.25rem;
	}

	#searchInput:focus {
		border-color: #007bff;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
	}

	/* Responsive table improvements */
	@media (max-width: 768px) {
		.table-responsive {
			font-size: 0.85rem;
		}

		.btn-sm {
			padding: 0.25rem 0.5rem;
			font-size: 0.75rem;
		}

		.btn-group {
			display: flex;
			flex-direction: column;
			width: 100%;
		}

		.btn-group .btn {
			margin-bottom: 5px;
		}
	}

	/* Print styles */
	@media print {

		.card-header,
		.btn,
		.form-group,
		#searchInput {
			display: none !important;
		}
	}
</style>