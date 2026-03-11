<!-- nota/v_index.php -->
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h1 class="page-title">Nota Penjualan</h1>

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

			<?php
			$nota_belum_closing_today = $this->M_nota->get_belum_closing(date('Y-m-d'));
			if (!empty($nota_belum_closing_today)) :
			?>
				<div class="alert alert-warning alert-dismissible fade show">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<i class="fe fe-alert-circle"></i>
					<strong>Perhatian!</strong>
					Ada <strong><?= count($nota_belum_closing_today) ?> nota</strong> hari ini yang belum di-closing.
					<a href="<?= base_url('closing_nota/form?tanggal=' . date('Y-m-d')) ?>" class="btn btn-sm btn-warning ml-2 text-white">
						<i class="fe fe-lock"></i> Proses Closing Sekarang
					</a>
				</div>
			<?php endif; ?>

			<div class="card shadow mb-4">
				<div class="card-body">

					<!-- Search & Filter -->
					<form method="GET" action="<?= base_url('nota') ?>">
						<div class="row align-items-center mb-3">

							<div class="col-3 col-lg-2 mb-2">
								<input type="date" name="tanggal_dari" class="form-control form-control-sm"
									value="<?= htmlspecialchars($tanggal_dari ?? '') ?>">
							</div>

							<div class="col-3 col-lg-2 mb-2">
								<input type="date" name="tanggal_sampai" class="form-control form-control-sm"
									value="<?= htmlspecialchars($tanggal_sampai ?? '') ?>">
							</div>

							<div class="col-6 col-lg-4 mb-2">
								<input type="text" name="search" id="search" class="form-control form-control-sm"
									placeholder="Cari No. Nota atau Customer..."
									value="<?= htmlspecialchars($search ?? '') ?>">
							</div>

							<div class="col-12 col-lg-4 mb-2">
								<!-- Desktop ≥992px -->
								<div class="d-none d-lg-flex" style="gap:4px;">
									<button type="submit" class="btn btn-sm btn-dark">
										<i class="fe fe-search"></i> Cari
									</button>
									<a href="<?= base_url('nota') ?>" class="btn btn-sm btn-pink text-white">
										<i class="fe fe-refresh-cw"></i> Reset
									</a>
									<a href="<?= base_url('nota/form') ?>" class="btn btn-sm btn-primary text-white">
										<i class="fe fe-plus"></i> New
									</a>
									<button type="button" class="btn btn-sm btn-secondary"
										data-toggle="modal" data-target="#modalSettingStruk">
										<i class="fe fe-settings"></i> Settings
									</button>
								</div>

								<!-- Mobile/Tablet <992px -->
								<div class="d-flex d-lg-none" style="gap:4px;">
									<button type="submit" class="btn btn-sm btn-dark flex-fill">
										<i class="fe fe-search"></i> Cari
									</button>
									<a href="<?= base_url('nota') ?>" class="btn btn-sm btn-pink text-white flex-fill">
										<i class="fe fe-refresh-cw"></i> Reset
									</a>
									<a href="<?= base_url('nota/form') ?>" class="btn btn-sm btn-primary text-white flex-fill">
										<i class="fe fe-plus"></i> New
									</a>
									<button type="button" class="btn btn-sm btn-secondary px-3 flex-shrink-0"
										data-toggle="modal" data-target="#modalSettingStruk">
										<i class="fe fe-settings"></i> Settings
									</button>
								</div>
							</div>

						</div>
					</form>

					<!-- Table -->
					<div class="table-responsive">
						<table class="table table-sm table-striped table-bordered table-hover w-100">
							<thead class="thead-dark">
								<tr>
									<th width="4%">No</th>
									<th width="13%">No. Nota</th>
									<th width="11%">Tanggal</th>
									<th>Customer</th>
									<th width="6%" class="text-center">Item</th>
									<th width="13%" class="text-right">Total</th>
									<th width="11%" class="text-right">Laba</th>
									<th width="9%" class="text-center">Metode</th>
									<th width="7%" class="text-center">Status</th>
									<th width="8%" class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($nota)) : ?>
									<?php
									$no = (isset($_GET['per_page']) ? $_GET['per_page'] : 0) + 1;
									foreach ($nota as $n) :
										$badge_metode = $n->metode_bayar == 'cash' ? 'badge-primary' : 'badge-info';
										$badge_status = $n->is_closed == 1 ? 'badge-secondary' : 'badge-pink';
										$status_text  = $n->is_closed == 1 ? 'CLOSED' : 'OPEN';
									?>
										<tr>
											<td class="text-center"><?= $no++ ?></td>
											<td><strong><?= $n->no_nota ?></strong></td>
											<td><?= date('d/m/Y H:i', strtotime($n->tanggal)) ?></td>
											<td><?= $n->customer ?: '-' ?></td>
											<td class="text-center">
												<span class="badge badge-primary"><?= $n->total_item ?></span>
											</td>
											<td class="text-right">
												<strong>Rp <?= number_format($n->total_penjualan, 0, ',', '.') ?></strong>
											</td>
											<td class="text-right">
												<span class="text-primary">Rp <?= number_format($n->laba_kotor, 0, ',', '.') ?></span>
											</td>
											<td class="text-center">
												<span class="badge <?= $badge_metode ?>"><?= strtoupper($n->metode_bayar) ?></span>
											</td>
											<td class="text-center">
												<span class="badge <?= $badge_status ?>"><?= $status_text ?></span>
											</td>
											<td class="text-center">
												<a href="<?= base_url('nota/detail/' . $n->id) ?>" class="btn btn-sm btn-primary" title="Detail">
													<i class="fe fe-eye"></i>
												</a>
												<a href="<?= base_url('nota/print_nota/' . $n->id) ?>" class="btn btn-sm btn-pink"
													title="Print nota" target="_blank"
													onclick="return window.open(this.href,'_blank','width=400,height=600'), false;">
													<i class="fe fe-printer"></i>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr>
										<td colspan="10" class="text-center">
											<div class="alert alert-info mb-0">
												<i class="fe fe-info"></i> Tidak ada data nota penjualan
											</div>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<?php if (!empty($nota)) : ?>
						<div class="row mt-3">
							<div class="col-md-6">
								<p class="text-muted small">Menampilkan data nota penjualan</p>
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

<!-- Modal Setting Struk -->
<div class="modal fade" id="modalSettingStruk" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fe fe-settings"></i> Pengaturan Struk Nota</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form id="formSettingStruk">
					<div class="form-group">
						<label class="font-weight-bold">Ukuran Kertas</label>
						<div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="struk_lebar_kertas" value="80"
									<?= ($struk_cabang['lebar_kertas'] ?? '80') == '80' ? 'checked' : '' ?>>
								<label class="form-check-label">80mm</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="struk_lebar_kertas" value="58"
									<?= ($struk_cabang['lebar_kertas'] ?? '80') == '58' ? 'checked' : '' ?>>
								<label class="form-check-label">58mm</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Nama Toko di Struk</label>
						<input type="text" class="form-control" name="struk_nama_toko"
							value="<?= htmlspecialchars($struk_cabang['nama_toko'] ?? '') ?>"
							placeholder="Kosongkan untuk pakai nama perusahaan">
						<small class="text-muted">Kosongkan untuk otomatis pakai nama perusahaan dari utility</small>
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Teks Footer</label>
						<input type="text" class="form-control mb-1" name="struk_footer_1"
							value="<?= htmlspecialchars($struk_cabang['footer_1'] ?? 'Terima kasih atas kunjungan Anda') ?>"
							placeholder="Baris 1">
						<input type="text" class="form-control mb-1" name="struk_footer_2"
							value="<?= htmlspecialchars($struk_cabang['footer_2'] ?? 'Barang yang sudah dibeli') ?>"
							placeholder="Baris 2">
						<input type="text" class="form-control" name="struk_footer_3"
							value="<?= htmlspecialchars($struk_cabang['footer_3'] ?? 'tidak dapat dikembalikan') ?>"
							placeholder="Baris 3">
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Tampilan</label>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="struk_show_kasir" value="1" id="showKasir"
								<?= ($struk_cabang['show_kasir'] ?? 1) ? 'checked' : '' ?>>
							<label class="form-check-label" for="showKasir">Tampilkan nama kasir</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="struk_show_harga_satuan" value="1" id="showHargaSatuan"
								<?= ($struk_cabang['show_harga_satuan'] ?? 1) ? 'checked' : '' ?>>
							<label class="form-check-label" for="showHargaSatuan">Tampilkan harga satuan (qty x harga)</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="struk_auto_print" value="1" id="autoPrint"
								<?= ($struk_cabang['auto_print'] ?? 1) ? 'checked' : '' ?>>
							<label class="form-check-label" for="autoPrint">Auto print saat struk dibuka</label>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btnSaveSetting">
					<i class="fe fe-save"></i> Simpan Pengaturan
				</button>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
	$('#btnSaveSetting').on('click', function() {
		const btn = $(this);
		btn.prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

		$.ajax({
			url: '<?= base_url("nota/setting_struk") ?>',
			type: 'POST',
			data: $('#formSettingStruk').serialize(),
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					Swal.fire({
						icon: 'success',
						title: 'Berhasil!',
						text: response.message,
						timer: 1500,
						showConfirmButton: false
					}).then(function() {
						$('#modalSettingStruk').modal('hide');
						location.reload();
					});
				} else {
					Swal.fire('Gagal', response.message, 'error');
				}
				btn.prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Pengaturan');
			},
			error: function() {
				Swal.fire('Error', 'Terjadi kesalahan!', 'error');
				btn.prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Pengaturan');
			}
		});
	});
</script>
