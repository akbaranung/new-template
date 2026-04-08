<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h1 class="page-title">Detail Nota Penjualan</h1>
				<a href="<?= base_url('nota') ?>" class="btn btn-secondary">
					<i class="fe fe-arrow-left"></i> Kembali
				</a>
			</div>

			<?php if ($this->session->flashdata('success')) : ?>
				<div class="alert alert-success alert-dismissible fade show">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?= $this->session->flashdata('success') ?>
				</div>
			<?php endif; ?>

			<?php if ($this->session->flashdata('error')) : ?>
				<div class="alert alert-danger alert-dismissible fade show">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?= $this->session->flashdata('error') ?>
				</div>
			<?php endif; ?>

			<!-- Header Information -->
			<div class="card shadow mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0 text-white"><i class="fe fe-file-text"></i> Informasi Nota</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<table class="table table-borderless table-sm">
								<tr>
									<td width="40%"><strong>No. Nota</strong></td>
									<td width="5%">:</td>
									<td>
										<span class="badge badge-primary badge-lg">
											<?= $nota->no_nota ?>
										</span>
									</td>
								</tr>
								<tr>
									<td><strong>Tanggal</strong></td>
									<td>:</td>
									<td><?= date('d F Y H:i', strtotime($nota->tanggal)) ?></td>
								</tr>
								<tr>
									<td><strong>Customer</strong></td>
									<td>:</td>
									<td><?= $nota->customer ?: '-' ?></td>
								</tr>
								<tr>
									<td><strong>Metode Pembayaran</strong></td>
									<td>:</td>
									<td>
										<?php
										$badge_metode = $nota->metode_bayar == 'cash' ? 'badge-primary' : 'badge-info';
										?>
										<span class="badge <?= $badge_metode ?>">
											<?= strtoupper($nota->metode_bayar) ?>
										</span>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<table class="table table-borderless table-sm">
								<tr>
									<td width="40%"><strong>Total Penjualan</strong></td>
									<td width="5%">:</td>
									<td>
										<h5 class="mb-0 text-primary">
											Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?>
										</h5>
									</td>
								</tr>
								<?php
								if ($is_admin_nota) {
								?>
									<tr>
										<td><strong>Total HPP</strong></td>
										<td>:</td>
										<td>
											<h6 class="mb-0 text-pink">
												Rp <?= number_format($nota->total_hpp, 0, ',', '.') ?>
											</h6>
										</td>
									</tr>

									<tr>
										<td><strong>Laba Kotor</strong></td>
										<td>:</td>
										<td>
											<h5 class="mb-0 text-primary">
												Rp <?= number_format($nota->laba_kotor, 0, ',', '.') ?>
											</h5>
										</td>
									</tr>
								<?php
								} ?>
								<tr>
									<td><strong>Status</strong></td>
									<td>:</td>
									<td>
										<?php
										$badge_status = $nota->is_closed == 1 ? 'badge-primary' : 'badge-pink';
										$status_text = $nota->is_closed == 1 ? 'CLOSED' : 'OPEN (Belum Closing)';
										?>
										<span class="badge <?= $badge_status ?>">
											<?= $status_text ?>
										</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<small class="text-muted">
								<strong>Dibuat oleh:</strong> <?= $nota->created_by ?> |
								<?= date('d/m/Y H:i', strtotime($nota->created_at)) ?>
							</small>
						</div>
					</div>
				</div>
			</div>

			<!-- Summary Card -->
			<div class="row mb-4">
				<div class="col-md-4">
					<div class="card bg-primary text-white shadow">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="mb-0 text-white">Total Penjualan</h6>
									<h3 class="mb-0 text-white">
										Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?>
									</h3>
								</div>
								<i class="fe fe-dollar-sign fe-3x"></i>
							</div>
						</div>
					</div>
				</div>
				<?php
				if ($is_admin_nota): ?>
					<div class="col-md-4">
						<div class="card bg-pink text-white shadow">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-0 text-white">Total HPP</h6>
										<h3 class="mb-0 text-white">
											Rp <?= number_format($nota->total_hpp, 0, ',', '.') ?>
										</h3>
									</div>
									<i class="fe fe-trending-down fe-3x"></i>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="card bg-dark text-white shadow">
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h6 class="mb-0 text-white">Laba Kotor</h6>
										<h3 class="mb-0 text-white">
											Rp <?= number_format($nota->laba_kotor, 0, ',', '.') ?>
										</h3>
									</div>
									<i class="fe fe-trending-up fe-3x"></i>
								</div>
							</div>
						</div>
					</div>
				<?php
				endif; ?>
			</div>

			<!-- Detail Items -->
			<div class="card shadow mb-4">
				<div class="card-header bg-pink text-white">
					<h5 class="mb-0 text-white"><i class="fe fe-package"></i> Detail Item Barang</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-sm">
							<thead class="thead-light">
								<tr>
									<th width="5%" class="text-center">No</th>
									<th width="12%">Kode Barang</th>
									<th>Nama Barang</th>
									<th width="8%" class="text-center">Satuan</th>
									<th width="10%" class="text-right">Qty</th>
									<th width="13%" class="text-right">Harga Jual</th>
									<?php if ($is_admin_nota): ?>
										<th width="13%" class="text-right">HPP</th>
									<?php endif; ?>
									<th width="15%" class="text-right">Subtotal</th>
									<?php if ($is_admin_nota): ?>
										<th width="12%" class="text-right">Laba</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($detail)) {
									$no = 1;
									$total_qty = 0;
									foreach ($detail as $d) :
										$total_qty += $d->qty;
										$laba_item = $d->subtotal_jual - $d->subtotal_hpp;
								?>
										<tr>
											<td class="text-center"><?= $no++ ?></td>
											<td><strong><?= $d->kode_item ?></strong></td>
											<td><?= $d->nama_item ?></td>
											<td class="text-center"><?= $d->satuan ?></td>
											<td class="text-right">
												<?= number_format($d->qty, 2, ',', '.') ?>
											</td>
											<td class="text-right">
												Rp <?= number_format($d->harga_jual, 0, ',', '.') ?>
											</td>
											<?php if ($is_admin_nota): ?>
												<td class="text-right">
													Rp <?= number_format($d->harga_modal, 0, ',', '.') ?>
												</td>
											<?php endif; ?>
											<td class="text-right">
												<strong>Rp <?= number_format($d->subtotal_jual, 0, ',', '.') ?></strong>
											</td>
											<?php if ($is_admin_nota): ?>
												<td class="text-right">
													<span class="text-success">
														Rp <?= number_format($laba_item, 0, ',', '.') ?>
													</span>
												</td>
											<?php endif; ?>
										</tr>
									<?php
									endforeach;
									?>
									<tr class="table-light">
										<td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
										<td class="text-right">
											<strong><?= number_format($total_qty, 2, ',', '.') ?></strong>
										</td>

										<td colspan="<?= $is_admin_nota ? '2' : '' ?>"></td>
										<td class="text-right">
											<h5 class="mb-0 text-success">
												<strong>Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?></strong>
											</h5>
										</td>
										<?php if ($is_admin_nota): ?>
											<td class="text-right">
												<h5 class="mb-0 text-primary">
													<strong>Rp <?= number_format($nota->laba_kotor, 0, ',', '.') ?></strong>
												</h5>
											</td>
										<?php endif; ?>
									</tr>
								<?php
								} else {
								?>
									<tr>
										<td colspan="9" class="text-center">
											<div class="alert alert-warning mb-0">
												<i class="fe fe-alert-triangle"></i> Tidak ada detail item
											</div>
										</td>
									</tr>
								<?php
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="row mb-4">
				<div class="col-md-12 text-right">
					<a href="<?= base_url('nota') ?>" class="btn btn-secondary">
						<i class="fe fe-arrow-left"></i> Kembali ke Daftar
					</a>
					<a href="<?= base_url('nota/print_nota/' . $nota->id) ?>" target="_blank" class="btn btn-primary">
						<i class="fe fe-printer"></i> Print Nota
					</a>
				</div>
			</div>

		</div>
	</div>
</div>

<!-- Print Styles -->
<style>
	@media print {

		.btn,
		.card-header,
		.page-title,
		.alert {
			display: none !important;
		}

		.card {
			box-shadow: none !important;
			border: 1px solid #dee2e6 !important;
			page-break-inside: avoid;
		}

		body {
			font-size: 11px;
		}

		h5,
		h6 {
			font-size: 13px;
		}

		.table-sm td,
		.table-sm th {
			padding: 0.2rem;
		}
	}
</style>