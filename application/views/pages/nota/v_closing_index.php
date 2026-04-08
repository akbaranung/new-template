<!-- v_closing_index.php -->
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Closing Kasir</h1>

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

			<!-- Check Nota Belum Closing Hari Ini -->
			<?php
			$nota_hari_ini = $this->M_nota->get_belum_closing(date('Y-m-d'));
			$jumlah_nota = count($nota_hari_ini);
			?>

			<!-- Big Button Proses Closing Hari Ini -->
			<div class="card shadow mb-4 border-primary">
				<div class="card-body text-center py-5">
					<?php if ($jumlah_nota > 0) : ?>
						<i class="fe fe-alert-circle fe-5x text-warning mb-3"></i>
						<h3 class="text-dark">Ada <strong class="text-danger"><?= $jumlah_nota ?> Nota</strong> Belum Closing Hari Ini</h3>
						<p class="text-muted mb-4">Tanggal: <?= date('d F Y') ?></p>
						<a href="<?= base_url('closing_nota/form?tanggal=' . date('Y-m-d')) ?>" class="btn btn-warning btn-lg text-white">
							<i class="fe fe-lock"></i> Proses Closing Hari Ini
						</a>
					<?php else : ?>
						<i class="fe fe-check-circle fe-5x text-success mb-3"></i>
						<h3 class="text-dark">Semua Nota Hari Ini Sudah Di-Closing</h3>
						<p class="text-muted mb-4">Tanggal: <?= date('d F Y') ?></p>
						<a href="<?= base_url('nota/form') ?>" class="btn btn-primary">
							<i class="fe fe-plus"></i> Buat Nota Baru
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- History Closing -->
			<div class="card shadow mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0 text-white"><i class="fe fe-list"></i> History Closing Kasir</h5>
				</div>
				<div class="card-body">
					<!-- Search & Filter -->
					<form method="GET" action="<?= base_url('closing_nota') ?>">
						<div class="row mb-3">
							<div class="col-md-4">
								<div class="form-group">
									<input type="date" name="tanggal_dari" class="form-control" placeholder="Tanggal Dari" value="<?= htmlspecialchars($tanggal_dari ?? '') ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="date" name="tanggal_sampai" class="form-control" placeholder="Tanggal Sampai" value="<?= htmlspecialchars($tanggal_sampai ?? '') ?>">
								</div>
							</div>
							<div class="col-md-4">
								<button type="submit" class="btn btn-primary">
									<i class="fe fe-search"></i> Cari
								</button>
								<a href="<?= base_url('closing_nota') ?>" class="btn btn-warning text-white">
									<i class="fe fe-refresh-cw"></i> Reset
								</a>
							</div>
						</div>
					</form>

					<!-- Table -->
					<div class="table-responsive">
						<table class="table table-sm table-striped table-bordered table-hover" style="width:100%">
							<thead class="thead-dark">
								<tr>
									<th width="5%">No</th>
									<th width="12%">Tanggal</th>
									<th width="10%" class="text-center">Total Nota</th>
									<th width="15%" class="text-right">Penjualan Cash</th>
									<th width="15%" class="text-right">Penjualan Qris</th>
									<th width="15%" class="text-right">Penjualan Card</th>
									<th width="13%" class="text-right">Total Penjualan</th>
									<th width="10%" class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($closing)) {
									$no = (isset($_GET['per_page']) ? $_GET['per_page'] : 0) + 1;
									foreach ($closing as $c) :
										$notas = $this->cb->select('total_penjualan,metode_bayar')->from('nota')->where('id_closing', $c->id)->get()->result();
										$nota_cash = 0;
										$nota_qris = 0;
										$nota_card = 0;

										foreach ($notas as $nota) {
											if ($nota->metode_bayar == 'cash') {
												$nota_cash += $nota->total_penjualan;
											}

											if ($nota->metode_bayar == 'qris') {
												$nota_qris += $nota->total_penjualan;
											}

											if ($nota->metode_bayar == 'card') {
												$nota_card += $nota->total_penjualan;
											}
										}
								?>
										<tr>
											<td class="text-center"><?= $no++ ?></td>
											<td>
												<strong><?= date('d/m/Y', strtotime($c->tanggal)) ?></strong>
												<br>
												<small class="text-muted">
													<?= date('H:i', strtotime($c->created_at)) ?>
												</small>
											</td>
											<td class="text-center">
												<span class="badge badge-info badge-lg">
													<?= $c->total_transaksi ?> nota
												</span>
											</td>
											<td class="text-right">
												Rp <?= number_format($nota_cash, 0, ',', '.') ?>
											</td>
											<td class="text-right">
												Rp <?= number_format($nota_qris, 0, ',', '.') ?>
											</td>
											<td class="text-right">
												Rp <?= number_format($nota_card, 0, ',', '.') ?>
											</td>
											<td class="text-right">
												<strong>Rp <?= number_format($c->total_penjualan, 0, ',', '.') ?></strong>
											</td>
											<td class="text-center">
												<a href="<?= base_url('closing_nota/detail/' . $c->id) ?>" class="btn btn-sm btn-info" title="Detail">
													<i class="fe fe-eye"></i>
												</a>
											</td>
										</tr>
									<?php
									endforeach;
								} else {
									?>
									<tr>
										<td colspan="8" class="text-center">
											<div class="alert alert-info mb-0">
												<i class="fe fe-info"></i> Belum ada data closing kasir
											</div>
										</td>
									</tr>
								<?php
								} ?>
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<?php if (!empty($closing)) : ?>
						<div class="row mt-3">
							<div class="col-md-6">
								<p class="text-muted">
									Menampilkan data history closing kasir
								</p>
							</div>
							<div class="col-md-6 text-right">
								<?= $pagination ?>
							</div>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.fe-5x {
		font-size: 5rem;
	}

	.badge-lg {
		font-size: 1rem;
		padding: 0.5rem 0.75rem;
	}
</style>