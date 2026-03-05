<style>
	.pagination .page-item a,
	.pagination .page-item span {
		display: block;
		padding: 0.375rem 0.75rem;
		color: #007bff;
		background-color: #fff;
		border: 1px solid #dee2e6;
		text-decoration: none;
		line-height: 1.25;
	}

	.pagination .page-item a:hover {
		color: #0056b3;
		background-color: #e9ecef;
	}

	.pagination .page-item.active .page-link,
	.pagination .page-item.active a {
		color: #fff;
		background-color: #007bff;
		border-color: #007bff;
	}

	.pagination {
		display: flex;
		list-style: none;
		border-radius: 0.25rem;
		padding-left: 0;
		gap: 2px;
	}
</style>
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Arus Kas</h1>
			<div class="card shadow mb-4">
				<div class="card-body">

					<!-- Form Filter — selalu tampil -->
					<form method="GET" action="<?= base_url('financial/coa_report') ?>">
						<div class="row">
							<div class="col-md-3 col-xs-12">
								<label class="form-label">No. CoA</label>
								<select name="no_coa" id="no_coa" class="form-control select2">
									<option value="">:: Pilih nomor coa</option>
									<option <?= ($this->input->get('no_coa') == 'ALL') ? "selected" : "" ?> value="ALL">ALL COA</option>
									<?php foreach ($coas as $c) : ?>
										<option <?= ($this->input->get('no_coa') == $c->no_sbb) ? "selected" : "" ?> value="<?= $c->no_sbb ?>">
											<?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2 col-xs-12">
								<label class="form-label">Dari</label>
								<input type="date" class="form-control" name="tgl_dari" value="<?= $this->input->get('tgl_dari') ?>">
							</div>
							<div class="col-md-2 col-xs-12">
								<label class="form-label">Sampai</label>
								<input type="date" class="form-control" name="tgl_sampai" value="<?= $this->input->get('tgl_sampai') ?? date('Y-m-d') ?>">
							</div>
							<div class="col-md-3 col-xs-12">
								<label class="form-label">Keyword</label>
								<input type="text" name="keyword" class="form-control" placeholder="nomor coa/nominal/keterangan" value="<?= $this->input->get('keyword') ?>">
							</div>

							<div class="col-md-2 col-xs-12">
								<button type="submit" class="btn btn-primary btn-sm" style="margin-top: 30px;">Lihat</button>
								<a href="<?= base_url('financial/coa_report') ?>" class="btn btn-warning text-white btn-sm" style="margin-top: 30px;">Reset</a>
							</div>
						</div>
					</form>

					<?php if ($this->input->get('no_coa') || $this->input->get('keyword')) : ?>

						<div class="row mt-3">
							<div class="col-md-12 col-xs-12 table-responsive">

								<?php if ($this->input->get('no_coa') == "ALL" || (!$this->input->get('no_coa') && $this->input->get('keyword'))) : ?>

									<table class="table table-sm table-bordered">
										<thead class="thead-dark">
											<?php if ($this->input->get('keyword') !== "") : ?>
												<tr>
													<th class="text-right" colspan="3" style="background-color: #e91e63; font-weight: bolder;">Total:</th>
													<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_debit) ?></th>
													<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
													<th colspan="3" style="background-color: #e91e63;"></th>
												</tr>
											<?php endif; ?>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Tanggal</th>
												<th class="text-center">CoA</th>
												<th class="text-center">Debit</th>
												<th class="text-center">Kredit</th>
												<th class="text-center">Keterangan</th>
												<th class="text-center">File</th>
												<?php if ($this->session->userdata('nama_jabatan') == "Super Admin") : ?>
													<th class="text-center">Action</th>
												<?php endif; ?>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = ($offset ?? 0) + 1;
											if ($coa) :
												foreach ($coa as $a) :
													$coa_debit  = $this->M_coa->getCoa($a->akun_debit);
													$coa_kredit = $this->M_coa->getCoa($a->akun_kredit);
											?>
													<tr>
														<td rowspan=2><?= $no++ ?></td>
														<td><?= ($a->tanggal) ? format_indo($a->tanggal) : '-' ?></td>
														<td><?= $a->akun_debit ?> - <?= $coa_debit['nama_perkiraan'] ?></td>
														<td class="text-right"><?= rupiah($a->jumlah_debit) ?></td>
														<td class="text-right">0</td>
														<td style="white-space: pre-line;"><?= $a->keterangan ?></td>
														<td>
															<?php if ($a->file) : ?>
																<a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info btn-sm">
																	<i class="fa fa-download"></i> <?= $a->nama_file ?>
																</a>
															<?php else : ?>
																-
															<?php endif; ?>
														</td>
														<?php if ($this->session->userdata('nama_jabatan') == "Super Admin") : ?>
															<td class="text-center">
																<button class="btn btn-sm btn-warning text-white" onclick="onEdit_report_per_coa(<?= $a->id ?>)" type="button">Update</button>
															</td>
														<?php endif; ?>
													</tr>
													<tr>
														<!-- <td></td> -->
														<td><?= ($a->tanggal) ? format_indo($a->tanggal) : '-' ?></td>
														<td><?= $a->akun_kredit ?> - <?= $coa_kredit['nama_perkiraan'] ?></td>
														<td class="text-right">0</td>
														<td class="text-right"><?= rupiah($a->jumlah_kredit) ?></td>
														<td style="white-space: pre-line;"><?= $a->keterangan ?></td>
														<td>
															<?php if ($a->file) : ?>
																<a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info btn-sm">
																	<i class="fa fa-download"></i> <?= $a->nama_file ?>
																</a>
															<?php else : ?>
																-
															<?php endif; ?>
														</td>
														<?php if ($this->session->userdata('nama_jabatan') == "Super Admin") : ?>

															<td class="text-center"><button class="btn btn-sm btn-warning text-white" onclick="onEdit_report_per_coa(<?= $a->id ?>)" type="button">Update</button></td>
														<?php endif; ?>
													</tr>
												<?php
												endforeach;
											else : ?>
												<tr>
													<td colspan="8" class="text-center">Tidak ada transaksi pada periode yang dipilih</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>

								<?php else : ?>

									<table class="table table-sm table-bordered">
										<thead class="thead-dark">
											<tr>
												<th class="text-right" colspan="2" style="background-color: #e91e63; font-weight: bolder;">Total:</th>
												<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_debit) ?></th>
												<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
												<th style="background-color: #e91e63;" colspan="<?= ($this->session->userdata('nama_jabatan') == "Super Admin") ? '2' : '' ?>"></th>
											</tr>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Tanggal</th>
												<th class="text-center">Debit</th>
												<th class="text-center">Kredit</th>
												<th class="text-center">Keterangan</th>
												<?php if ($this->session->userdata('nama_jabatan') == "Super Admin") : ?>
													<th class="text-center">Action</th>
												<?php endif; ?>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = ($offset ?? 0) + 1;
											if ($coa) :
												foreach ($coa as $a) :
													$nama_coa_debit  = $this->M_coa->getCoa($a->akun_debit)['nama_perkiraan'];
													$nama_coa_kredit = $this->M_coa->getCoa($a->akun_kredit)['nama_perkiraan'];
											?>
													<tr>
														<td><?= $no++ ?></td>
														<td><?= ($a->tanggal) ? format_indo($a->tanggal) : '-' ?></td>
														<td class="<?= ($a->akun_debit == $detail_coa['no_sbb']) ? 'text-right' : 'text-center' ?>">
															<?= ($a->akun_debit == $detail_coa['no_sbb']) ? rupiah($a->jumlah_debit) : $a->akun_debit . ' - ' . $nama_coa_debit ?>
														</td>
														<td class="<?= ($a->akun_kredit == $detail_coa['no_sbb']) ? 'text-right' : 'text-center' ?>">
															<?= ($a->akun_kredit == $detail_coa['no_sbb']) ? rupiah($a->jumlah_kredit) : $a->akun_kredit . ' - ' . $nama_coa_kredit ?>
														</td>
														<td><?= $a->keterangan ?></td>
														<?php if ($this->session->userdata('nama_jabatan') == "Super Admin") : ?>

															<td class="text-center"><button class="btn btn-sm btn-warning text-white" onclick="onEdit_report_per_coa(<?= $a->id ?>)" type="button">Update</button></td>
														<?php endif; ?>
													</tr>
												<?php
												endforeach;
											else : ?>
												<tr>
													<td colspan="5" class="text-center">Tidak ada transaksi pada periode yang dipilih</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>

								<?php endif; ?>

								<?php if (isset($total_pages) && $total_pages > 1) : ?>
									<div class="d-flex justify-content-between align-items-center mt-3">
										<small class="text-muted">
											Menampilkan <strong><?= $offset + 1 ?>–<?= min($offset + $per_page, $total_rows) ?></strong>
											dari <strong><?= $total_rows ?></strong> data
										</small>
										<nav>
											<ul class="pagination" style="margin:0">
												<?php if ($current_page > 1) : ?>
													<li class="page-item">
														<a class="page-link" href="?<?= $query_string ?>&page=<?= $current_page - 1 ?>">«</a>
													</li>
												<?php endif; ?>

												<?php
												$start = max(1, $current_page - 2);
												$end   = min($total_pages, $current_page + 2);
												for ($i = $start; $i <= $end; $i++) : ?>
													<li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
														<a class="page-link" href="?<?= $query_string ?>&page=<?= $i ?>"><?= $i ?></a>
													</li>
												<?php endfor; ?>

												<?php if ($current_page < $total_pages) : ?>
													<li class="page-item">
														<a class="page-link" href="?<?= $query_string ?>&page=<?= $current_page + 1 ?>">»</a>
													</li>
												<?php endif; ?>
											</ul>
										</nav>
									</div>
								<?php endif; ?>

							</div>
						</div>

					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- Update COA Modal -->
<div class="modal fade" id="updateCoaModal" tabindex="-1" aria-labelledby="updateCoaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateCoaModalLabel">Update COA Entry</h5>
				<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
			</div>
			<form id="updateCoaForm" action="<?= site_url('financial/update_report_per_coa') ?>" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="id" id="update_id">
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Debit</label>
							<select name="neraca_debit" id="update_neraca_debit" class="form-control" style="width: 100%;" required>
								<option value="">-- Pilih pos neraca debit</option>
								<?php foreach ($daftar_coa as $c) : ?>
									<option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>">
										<?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Kredit</label>
							<select name="neraca_kredit" id="update_neraca_kredit" class="form-control" style="width: 100%;" required>
								<option value="">-- Pilih pos neraca kredit</option>
								<?php foreach ($daftar_coa as $c) : ?>
									<option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>">
										<?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-12 col-xs-12 form-group has-feedback">
							<div id="warningMessage" class="validation-error-alert"></div>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Nominal</label>
							<input type="text" class="form-control format_angka" name="input_nominal" id="update_input_nominal" placeholder="Nominal" required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Keterangan</label>
							<input type="text" class="form-control" name="input_keterangan" id="update_input_keterangan" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Tanggal</label>
							<input type="date" name="tanggal" id="update_tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label class="form-label">Attachment (Image/Excel/Word)</label>
							<input type="file" class="form-control-file" name="file" id="file">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger" onclick="onDeleteArusKas()">Delete</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	function formatAngka(input) {
		let raw = input.value;

		// Pisahkan bagian integer dan desimal (koma sebagai separator)
		let parts = raw.split(',');
		let intPart = parts[0].replace(/\./g, '').replace(/[^0-9]/g, '');
		let decPart = parts.length > 1 ? parts[1].replace(/[^0-9]/g, '').substring(0, 2) : null;

		// Format ribuan dengan titik
		let formatted = intPart ? parseInt(intPart).toLocaleString('id-ID') : '';

		// Tambah desimal jika ada
		if (decPart !== null) {
			formatted += ',' + decPart;
		}

		input.value = formatted;
	}

	function getRawAngka(val) {
		if (!val) return 0;
		// Format Indonesia: titik = ribuan, koma = desimal
		let cleaned = val.toString().replace(/\./g, '').replace(',', '.');
		return parseFloat(cleaned) || 0;
	}

	$(document).ready(function() {

		// Auto-bind ke semua elemen .format_angka
		$(document).on('input', '.format_angka', function() {
			formatAngka(this);
		});

		// Blokir karakter selain angka dan koma
		$(document).on('keypress', '.format_angka', function(e) {
			let char = String.fromCharCode(e.which);
			let val = $(this).val();

			// Hanya izinkan angka dan koma
			if (!/[0-9,]/.test(char)) return false;

			// Hanya boleh 1 koma
			if (char === ',' && val.indexOf(',') !== -1) return false;

			// Max 2 digit setelah koma
			if (val.indexOf(',') !== -1) {
				let decPart = val.split(',')[1];
				if (decPart && decPart.length >= 2) return false;
			}
		});

		// Sebelum form submit → convert balik ke format raw (titik desimal)
		// agar nilai yang dikirim ke server adalah angka murni
		$('form').on('submit', function() {
			$(this).find('.format_angka').each(function() {
				$(this).val(getRawAngka($(this).val()));
			});
		});

	});
</script>