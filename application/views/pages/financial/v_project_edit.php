<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Edit Project</h1>
			<div class="card shadow mb-4">
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-12">
							<a href="<?= site_url('financial/project') ?>" class="btn btn-secondary btn-sm">
								<i class="fe fe-arrow-left"></i> Kembali
							</a>
						</div>
					</div>

					<form class="form-label-left" method="POST" action="<?= site_url('financial/process_update_project/' . $project['id']) ?>" enctype="multipart/form-data">
						<div class="row">
							<!-- No Project (readonly) -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">No. Project</label>
								<input type="text" class="form-control" value="<?= $project['no_project'] ?>" readonly>
							</div>

							<!-- Tanggal -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">Tanggal</label>
								<input type="date" name="tanggal" class="form-control" value="<?= $project['tanggal'] ?>" required>
							</div>

							<!-- Keterangan -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">Keterangan</label>
								<input type="text" name="keterangan" class="form-control" value="<?= $project['keterangan'] ?>" oninput="this.value = this.value.toUpperCase()" required>
							</div>

							<!-- Upload File -->
							<div class="col-md-6 col-xs-12 form-group">
								<label class="form-label">Attachment</label>
								<?php if ($project['nama_file']) : ?>
									<div class="mb-1">
										<a href="<?= base_url('financial/download_project_file/' . $project['id']) ?>" class="btn btn-info btn-sm">
											<i class="fe fe-download"></i> <?= $project['nama_file'] ?>
										</a>
									</div>
								<?php endif; ?>
								<input type="file" name="file_upload" class="form-control-file">
								<small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
							</div>
						</div>

						<!-- Tabel Entry Detail -->
						<div class="table-responsive mt-3">
							<table class="table table-sm table-bordered" id="project-detail-table">
								<thead class="thead-dark">
									<tr>
										<th style="width: 40%">CoA</th>
										<th style="width: 25%">Nominal</th>
										<th style="width: 20%">Posisi</th>
										<th style="width: 10%" class="text-center">Aksi</th>
									</tr>
								</thead>
								<tbody id="detail-rows">
									<?php foreach ($project_detail as $d) : ?>
										<tr class="detail-row">
											<td>
												<select name="no_coa[]" class="form-control select2-coa" style="width:100%" required>
													<option value="">:: Pilih CoA</option>
													<?php foreach ($coa as $c) : ?>
														<option value="<?= $c->no_sbb ?>" <?= ($c->no_sbb == $d['no_coa']) ? 'selected' : '' ?>>
															<?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?>
														</option>
													<?php endforeach; ?>
												</select>
											</td>
											<td>
												<input type="text" name="nominal[]" class="form-control nominal-input"
													value="<?= number_format($d['nominal'], 0, ',', '.') ?>" required>
											</td>
											<td>
												<select name="posisi[]" class="form-control" required>
													<option value="DEBIT" <?= ($d['posisi'] == 'DEBIT') ? 'selected' : '' ?>>DEBIT</option>
													<option value="KREDIT" <?= ($d['posisi'] == 'KREDIT') ? 'selected' : '' ?>>KREDIT</option>
												</select>
											</td>
											<td class="text-center">
												<button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus baris">
													<i class="fe fe-trash"></i>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="4">
											<button type="button" class="btn btn-secondary btn-sm" id="btn-add-row">
												<i class="fe fe-plus"></i> Tambah Baris
											</button>
										</td>
									</tr>
									<tr style="background-color: #f8f9fa; font-weight: bold;">
										<td class="text-right">Total</td>
										<td colspan="3">
											<span class="text-success">Debit: <span id="total-debit">Rp0</span></span>
											&nbsp;&nbsp;|&nbsp;&nbsp;
											<span class="text-danger">Kredit: <span id="total-kredit">Rp0</span></span>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>

						<div class="row mt-3">
							<div class="col-12">
								<button type="submit" class="btn btn-primary btn-sm">
									<i class="fe fe-save"></i> Update
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Template row (hidden, untuk clone) -->
<template id="row-template">
	<tr class="detail-row">
		<td>
			<select name="no_coa[]" class="form-control select2-coa" style="width:100%" required>
				<option value="">:: Pilih CoA</option>
				<?php foreach ($coa as $c) : ?>
					<option value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="text" name="nominal[]" class="form-control nominal-input" placeholder="Nominal" required>
		</td>
		<td>
			<select name="posisi[]" class="form-control" required>
				<option value="DEBIT">DEBIT</option>
				<option value="KREDIT">KREDIT</option>
			</select>
		</td>
		<td class="text-center">
			<button type="button" class="btn btn-danger btn-sm btn-remove-row" title="Hapus baris">
				<i class="fe fe-trash"></i>
			</button>
		</td>
	</tr>
</template>

<script>
	function formatRupiah(angka) {
		let num = parseInt(angka.toString().replace(/[^0-9]/g, '')) || 0;
		return 'Rp' + num.toLocaleString('id-ID');
	}

	function getRawNominal(val) {
		return parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
	}

	function hitungTotal() {
		let totalDebit = 0;
		let totalKredit = 0;

		$('#detail-rows .detail-row').each(function() {
			let nominal = getRawNominal($(this).find('.nominal-input').val());
			let posisi = $(this).find('select[name="posisi[]"]').val();

			if (posisi === 'DEBIT') {
				totalDebit += nominal;
			} else {
				totalKredit += nominal;
			}
		});

		$('#total-debit').text(formatRupiah(totalDebit));
		$('#total-kredit').text(formatRupiah(totalKredit));
	}

	$(document).on('input', '.nominal-input', function() {
		let raw = getRawNominal($(this).val());
		$(this).val(raw > 0 ? raw.toLocaleString('id-ID') : '');
		hitungTotal();
	});

	$(document).on('change', 'select[name="posisi[]"]', function() {
		hitungTotal();
	});

	$('#btn-add-row').on('click', function() {
		let template = document.getElementById('row-template');
		let clone = document.importNode(template.content, true);
		$('#detail-rows').append(clone);

		$('#detail-rows .detail-row:last-child .select2-coa').select2({
			placeholder: ':: Pilih CoA',
			width: '100%'
		});
	});

	$(document).on('click', '.btn-remove-row', function() {
		if ($('#detail-rows .detail-row').length > 1) {
			$(this).closest('tr').remove();
			hitungTotal();
		} else {
			alert('Minimal harus ada 1 baris.');
		}
	});

	$(document).ready(function() {
		$('.select2-coa').select2({
			placeholder: ':: Pilih CoA',
			width: '100%'
		});

		// Hitung total dari data existing
		hitungTotal();
	});
</script>