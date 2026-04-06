<!-- v_closing_form.php -->
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h1 class="page-title">Proses Closing Kasir</h1>
				<a href="<?= base_url('closing_nota') ?>" class="btn btn-secondary">
					<i class="fe fe-arrow-left"></i> Kembali
				</a>
			</div>

			<?php if ($total_transaksi == 0) : ?>
				<!-- No Transaction Alert -->
				<div class="alert alert-warning">
					<i class="fe fe-alert-circle"></i> <strong>Tidak ada transaksi!</strong>
					<p class="mb-0">Tidak ada nota yang perlu di-closing untuk tanggal <?= date('d F Y', strtotime($tanggal)) ?>.</p>
				</div>
				<a href="<?= base_url('nota/form') ?>" class="btn btn-primary">
					<i class="fe fe-plus"></i> Buat Nota Baru
				</a>
			<?php else : ?>

				<!-- Summary Cards -->
				<div class="row mb-4">
					<div class="col-md-4">
						<div class="card bg-info text-white shadow">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-0 text-white">Total Transaksi</h6>
										<h2 class="mb-0 text-white"><?= $total_transaksi ?></h2>
										<small>Nota</small>
									</div>
									<i class="fe fe-file-text fe-3x"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card bg-success text-white shadow">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-0 text-white">Total Penjualan</h6>
										<h4 class="mb-0 text-white">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></h4>
									</div>
									<i class="fe fe-dollar-sign fe-3x"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card bg-warning text-white shadow">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-0 text-white">Total HPP</h6>
										<h4 class="mb-0 text-white">Rp <?= number_format($total_hpp, 0, ',', '.') ?></h4>
									</div>
									<i class="fe fe-trending-down fe-3x"></i>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Detail Breakdown -->
				<div class="card shadow mb-4">
					<div class="card-header bg-info text-white">
						<h5 class="mb-0 text-white"><i class="fe fe-pie-chart"></i> Breakdown Penjualan</h5>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="card border-info">
									<div class="card-body">
										<h6 class="text-info"><i class="fe fe-dollar-sign"></i> Penjualan Cash</h6>
										<h3 class="text-info">Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?></h3>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="card border-success">
									<div class="card-body">
										<h6 class="text-success"><i class="fe fe-credit-card"></i> Penjualan Card</h6>
										<h3 class="text-success">Rp <?= number_format($total_penjualan_card, 0, ',', '.') ?></h3>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="card border-warning">
									<div class="card-body">
										<h6 class="text-warning"><i class="fe fe-credit-card"></i> Penjualan Qris</h6>
										<h3 class="text-warning">Rp <?= number_format($total_penjualan_qris, 0, ',', '.') ?></h3>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- List Nota yang akan di-closing -->
				<div class="card shadow mb-4">
					<div class="card-header bg-warning text-white">
						<h5 class="mb-0 text-white"><i class="fe fe-list"></i> Daftar Nota yang Akan Di-Closing</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-sm table-striped table-bordered">
								<thead class="thead-light">
									<tr>
										<th width="5%">No</th>
										<th>No. Nota</th>
										<th>Waktu</th>
										<th>Customer</th>
										<th class="text-right">Total</th>
										<th class="text-right">HPP</th>
										<?php if ($this->session->userdata('level_jabatan') == '99'): ?>
											<th class="text-right">Laba</th>
										<?php endif; ?>
										<th class="text-center">Metode</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$no = 1;
									foreach ($nota_belum_closing as $n) :
										$badge_metode = $n->metode_bayar == 'cash' ? 'badge-success' : 'badge-info';
									?>
										<tr>
											<td class="text-center"><?= $no++ ?></td>
											<td><?= $n->no_nota ?></td>
											<td><?= date('H:i', strtotime($n->tanggal)) ?></td>
											<td><?= $n->customer ?: '-' ?></td>
											<td class="text-right">Rp <?= number_format($n->total_penjualan, 0, ',', '.') ?></td>
											<td class="text-right">Rp <?= number_format($n->total_hpp, 0, ',', '.') ?></td>
											<?php if ($this->session->userdata('level_jabatan') == '99'): ?>
												<td class="text-right text-success">Rp <?= number_format($n->laba_kotor, 0, ',', '.') ?></td>
											<?php
											endif;	?>
											<td class="text-center">
												<span class="badge <?= $badge_metode ?>">
													<?= strtoupper($n->metode_bayar) ?>
												</span>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Form Pilih COA -->
				<div class="card shadow mb-4">
					<div class="card-header bg-primary text-white">
						<h5 class="mb-0 text-white"><i class="fe fe-book"></i> Pilih Chart of Account (COA)</h5>
					</div>
					<div class="card-body">
						<form id="formClosing">
							<input type="hidden" name="tanggal" value="<?= $tanggal ?>">

							<div class="alert alert-info">
								<i class="fe fe-info"></i> <strong>Informasi:</strong><br>
								Pilih COA Kas dan COA Pendapatan. COA Persediaan otomatis mengikuti setting per barang.
							</div>

							<?php if ($total_penjualan_cash > 0) : ?>
								<div class="form-group">
									<label for="coa_kas">COA Kas (Debit) <span class="text-danger">*</span></label>
									<select class="form-control" name="coa_kas" id="coa_kas" required>
										<option value="">-- Pilih COA Kas --</option>
										<?php foreach ($coa_list as $coa) : ?>
											<option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">
										Total kas masuk: Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?>
									</small>
								</div>
							<?php endif ?>

							<?php if ($total_penjualan_qris > 0) : ?>
								<div class="form-group">
									<label for="coa_qris">COA Qris (Debit) <span class="text-danger">*</span></label>
									<select class="form-control" name="coa_qris" id="coa_qris" required>
										<option value="">-- Pilih COA Qris --</option>
										<?php foreach ($coa_list as $coa) : ?>
											<option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">
										Total qris masuk: Rp <?= number_format($total_penjualan_qris, 0, ',', '.') ?>
									</small>
								</div>
							<?php endif ?>

							<?php if ($total_penjualan_card > 0) : ?>
								<div class="form-group">
									<label for="coa_card">COA Card (Debit) <span class="text-danger">*</span></label>
									<select class="form-control" name="coa_card" id="coa_card" required>
										<option value="">-- Pilih COA Card --</option>
										<?php foreach ($coa_list as $coa) : ?>
											<option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">
										Total card masuk : Rp <?= number_format($total_penjualan_card, 0, ',', '.') ?>
									</small>
								</div>
							<?php endif ?>

							<div class="form-group">
								<label for="coa_pendapatan">COA Pendapatan (Kredit) <span class="text-danger">*</span></label>
								<select class="form-control" name="coa_pendapatan" id="coa_pendapatan" required>
									<option value="">-- Pilih COA Pendapatan --</option>
									<?php foreach ($coa_list as $coa) : ?>
										<option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
									<?php endforeach; ?>
								</select>
								<small class="text-muted">
									Total pendapatan (laba): Rp <?= number_format($total_penjualan - $total_hpp, 0, ',', '.') ?>
								</small>
							</div>

							<hr>

							<div class="text-right">
								<a href="<?= base_url('closing_nota') ?>" class="btn btn-secondary">
									<i class="fe fe-x"></i> Batal
								</a>
								<button type="button" class="btn btn-info" id="btnPreview">
									<i class="fe fe-eye"></i> Preview Jurnal
								</button>
								<button type="submit" class="btn btn-primary" id="btnProses">
									<i class="fe fe-lock"></i> Proses Closing
								</button>
							</div>
						</form>
					</div>
				</div>

			<?php endif; ?>

		</div>
	</div>
</div>

<!-- Modal Preview Jurnal -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white">
					<i class="fe fe-book"></i> Preview Jurnal Closing
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body" id="previewContent">
				<!-- Content will be loaded here -->
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
	$(document).ready(function() {

		initCOASelect2('coa_kas', '-- Pilih COA Kas --');
		initCOASelect2('coa_qris', '-- Pilih COA Qris --');
		initCOASelect2('coa_card', '-- Pilih COA Card --');
		initCOASelect2('coa_pendapatan', '-- Pilih COA Pendapatan --');

		function initCOASelect2(id, placeholder) {
			$('#' + id).select2({
				placeholder: placeholder,
				allowClear: true,
				width: '100%'
			});
		}

		function validateCOA() {
			if (!$('#coa_kas').val()) {
				alert('Pilih COA Kas terlebih dahulu!');
				$('#coa_kas').select2('open');
				return false;
			}
			if (!$('#coa_pendapatan').val()) {
				alert('Pilih COA Pendapatan terlebih dahulu!');
				$('#coa_pendapatan').select2('open');
				return false;
			}
			return true;
		}

		// Preview — tampilkan summary jurnal (tanpa laba detail)
		$('#btnPreview').on('click', function() {
			if (!validateCOA()) return;

			const coa_kas = $('#coa_kas option:selected').text();
			const coa_qris = $('#coa_qris option:selected').text();
			const coa_card = $('#coa_card option:selected').text();
			const coa_pendapatan = $('#coa_pendapatan option:selected').text();

			const html = `
            <div class="alert alert-info">
                <i class="fe fe-info"></i> <strong>Jurnal yang akan dibuat:</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="50%">Akun</th>
                            <th width="25%" class="text-right">Debit</th>
                            <th width="25%" class="text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td colspan="3"><strong>Jurnal HPP (per COA Persediaan item)</strong></td>
                        </tr>
						<?php if ($total_penjualan_cash > 0) : ?>
                        <tr>
                            <td>${coa_kas}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_hpp_cash, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr>
                            <td><em>COA Persediaan (otomatis per item)</em></td>
                            <td class="text-right">-</td>
                            <td class="text-right text-danger"><strong>Rp <?= number_format($total_hpp_cash, 0, ',', '.') ?></strong></td>
                        </tr>
						<?php endif ?>
						<?php if ($total_penjualan_qris > 0) : ?>
                        <tr>
                            <td>${coa_qris}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_hpp_qris, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr>
                            <td><em>COA Persediaan (otomatis per item)</em></td>
                            <td class="text-right">-</td>
                            <td class="text-right text-danger"><strong>Rp <?= number_format($total_hpp_qris, 0, ',', '.') ?></strong></td>
                        </tr>
						<?php endif ?>
						<?php if ($total_penjualan_card > 0) : ?>
                        <tr>
                            <td>${coa_card}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_hpp_card, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr>
                            <td><em>COA Persediaan (otomatis per item)</em></td>
                            <td class="text-right">-</td>
                            <td class="text-right text-danger"><strong>Rp <?= number_format($total_hpp_card, 0, ',', '.') ?></strong></td>
                        </tr>
						<?php endif ?>

                        <tr class="table-success">
                            <td colspan="3"><strong>Jurnal Pendapatan</strong></td>
                        </tr>
						<?php if ($total_penjualan_cash > 0) : ?>
                        <tr>
                            <td>${coa_kas}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_penjualan_cash - $total_hpp_cash, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
						<?php endif ?>

						<?php if ($total_penjualan_qris > 0) : ?>
                        <tr>
                            <td>${coa_qris}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_penjualan_qris - $total_hpp_qris, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
						<?php endif ?>
						<?php if ($total_penjualan_card > 0) : ?>
                        <tr>
                            <td>${coa_card}</td>
                            <td class="text-right text-success"><strong>Rp <?= number_format($total_penjualan_card - $total_hpp_card, 0, ',', '.') ?></strong></td>
                            <td class="text-right">-</td>
                        </tr>
						<?php endif ?>
                        <tr>
                            <td>${coa_pendapatan}</td>
                            <td class="text-right">-</td>
                            <td class="text-right text-danger"><strong>Rp <?= number_format($total_penjualan - $total_hpp, 0, ',', '.') ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-warning mt-2">
                <i class="fe fe-info"></i> Jurnal HPP akan di-split otomatis per COA Persediaan masing-masing item.
            </div>
        `;

			$('#previewContent').html(html);
			$('#modalPreview').modal('show');
		});

		// Submit
		$('#formClosing').on('submit', function(e) {
			e.preventDefault();
			if (!validateCOA()) return;

			if (!confirm('Proses closing? Nota tidak dapat diubah setelah ini!')) return;

			$('#btnProses').prop('disabled', true).html('<i class="fe fe-loader"></i> Memproses...');

			$.ajax({
				url: '<?= base_url("closing_nota/proses") ?>',
				type: 'POST',
				data: $(this).serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						alert(response.message);
						window.location.href = response.redirect;
					} else {
						alert(response.message);
						$('#btnProses').prop('disabled', false).html('<i class="fe fe-lock"></i> Proses Closing');
					}
				},
				error: function() {
					alert('Terjadi kesalahan!');
					$('#btnProses').prop('disabled', false).html('<i class="fe fe-lock"></i> Proses Closing');
				}
			});
		});
	});
</script>

<style>
	.fe-3x {
		font-size: 3rem;
	}
</style>