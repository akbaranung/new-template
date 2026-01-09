<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Closing / Saldo Awal</h1>
			<div class="card shadow mb-4">
				<!-- <div class="card-header">
          <p class="card-title"><strong>Closing / Saldo Awal</strong></p>
        </div> -->
				<div class="card-body">
					<!-- ✅ LAYOUT SIMPLE - Responsive Desktop & Mobile -->
					<div class="row mb-4">
						<!-- Button Closing EoM -->
						<div class="col-lg-6 col-md-12 mb-3">
							<div class="text-center p-4 border rounded bg-light">
								<h5 class="mb-3">
									<i class="fa fa-calendar-check-o text-primary"></i> Closing Akhir Bulan
								</h5>
								<a href="#" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#proses-closing">
									<i class="fa fa-lock"></i> Closing EoM
								</a>
								<p class="mt-3 mb-0 text-muted">
									<small><strong><i class="fa fa-info-circle"></i> Penting!</strong><br>
										Closing harus dilakukan setiap akhir bulan untuk membentuk saldo awal bulan berikutnya</small>
								</p>
							</div>
						</div>

						<!-- Button Proses Penihilan -->
						<div class="col-lg-6 col-md-12 mb-3">
							<div class="text-center p-4 border rounded bg-light">
								<h5 class="mb-3">
									<i class="fa fa-refresh text-danger"></i> Penihilan Akun
								</h5>
								<a href="#" class="btn btn-danger btn-lg btn-block" data-toggle="modal" data-target="#proses-penihilan">
									<i class="fa fa-refresh"></i> Proses Penihilan
								</a>
								<p class="mt-3 mb-0 text-muted">
									<small><strong><i class="fa fa-info-circle"></i> Penting!</strong><br>
										Penihilan dilakukan untuk mengosongkan akun pendapatan dan beban ke laba ditahan.</small>
								</p>
							</div>
						</div>
					</div>
					<div class="table-responsive mt-3">
						<table id="" class="table table-sm table-stripped" style="width:100%">
							<thead class="thead-dark">
								<tr>
									<th class="text-center">No.</th>
									<th class="text-center">Closing Periode</th>
									<th class="text-center">Keterangan</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (($saldo)) {
									$no = 1;
									foreach ($saldo as $c) : ?>
										<tr>
											<td class="text-right"><?= $no++ ?>.</td>
											<td><?= format_indo($c->periode) ?></td>
											<td><?= $c->keterangan ?></td>
											<td class="text-center"><a href="<?= base_url('financial/closing/' . $c->periode) ?>" class="btn btn-primary btn-sm">Detail</a></td>
										</tr>
									<?php
									endforeach;
								} else { ?>
									<tr>
										<td colspan="4" class="text-center">No data available</td>
									</tr>
								<?php
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div> <!-- .col-12 -->
	</div> <!-- .row -->
</div> <!-- .container-fluid -->


<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="proses-closing">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">
					Proses Closing EoM
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/save_saldo_awal') ?>">
				<div class="modal-body">
					<p><strong>Masukan periode dan password anda terlebih dahulu untuk memproses closing EoM</strong></p>
					<div class="form-group row">
						<div class="col-12">
							<label for="form-label">Periode</label>
							<input type="month" class="form-control" name="periode" value="<?= date('Y-m') ?>">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-12">
							<label for="form-label">Password</label>
							<input type="password" name="password" id="password" class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary btn-submit">
						Proses
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- ✅ MODAL BARU - Proses Penihilan -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="proses-penihilan">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-danger">
				<h4 class="modal-title text-white">
					<i class="fa fa-exclamation-triangle"></i> Proses Penihilan
				</h4>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/proses_penihilan') ?>">
				<div class="modal-body">
					<div class="alert alert-warning">
						<strong><i class="fa fa-info-circle"></i> Perhatian!</strong>
						<p class="mb-0">Proses penihilan akan mengosongkan semua akun <strong>Pendapatan</strong> dan <strong>Beban</strong> ke akun <strong>Laba Ditahan (32010)</strong>.</p>
					</div>

					<div class="form-group row">
						<div class="col-12">
							<label for="tanggal_transaksi">Tanggal Penihilan <span class="text-danger">*</span></label>
							<input type="date"
								class="form-control"
								id="tanggal_transaksi"
								name="tanggal_transaksi"
								value="<?= date('Y-m-d') ?>"
								required>
							<small class="form-text text-muted">Tanggal pencatatan jurnal penihilan</small>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-12">
							<label for="password_penihilan">Konfirmasi Password <span class="text-danger">*</span></label>
							<input type="password"
								name="password"
								id="password_penihilan"
								class="form-control"
								placeholder="Masukkan password anda"
								required>
							<small class="form-text text-muted">Password diperlukan untuk otorisasi proses penihilan</small>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-times"></i> Batal
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="fa fa-check"></i> Proses Penihilan
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {
		// Konfirmasi sebelum submit penihilan
		$('#proses-penihilan form').on('submit', function(e) {
			const password = $('#password_penihilan').val();

			if (!password) {
				e.preventDefault();
				alert('Password harus diisi!');
				return false;
			}

			if (!confirm('⚠️ PERHATIAN!\n\nProses penihilan akan mengosongkan semua akun Pendapatan dan Beban.\n\nApakah Anda yakin ingin melanjutkan?')) {
				e.preventDefault();
				return false;
			}

			// Disable button untuk prevent double submit
			$(this).find('.btn-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
		});
	});
</script>