<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Create Project</h1>
			<div class="card shadow mb-4">
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-12">
							<a href="<?= site_url('financial/project') ?>" class="btn btn-secondary btn-sm">
								<i class="fe fe-arrow-left"></i> Kembali
							</a>
						</div>
					</div>

					<form class="form-label-left" method="POST" action="<?= site_url('financial/process_save_project') ?>" enctype="multipart/form-data">
						<div class="row">
							<!-- No Project (readonly, auto-generate) -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">No. Project</label>
								<input type="text" name="no_project" class="form-control" value="<?= $no_project ?>" readonly>
							</div>

							<!-- Tanggal -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">Tanggal</label>
								<input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
							</div>

							<!-- Keterangan -->
							<div class="col-md-4 col-xs-12 form-group">
								<label class="form-label">Keterangan</label>
								<input type="text" name="keterangan" class="form-control" placeholder="Keterangan project" oninput="this.value = this.value.toUpperCase()" required>
							</div>

							<!-- Upload File -->
							<div class="col-md-6 col-xs-12 form-group">
								<label class="form-label">Attachment (Opsional)</label>
								<input type="file" name="file_upload" class="form-control-file">
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
								<button type="reset" class="btn btn-secondary btn-sm">Reset</button>
								<button type="submit" class="btn btn-primary btn-sm">
									<i class="fe fe-save"></i> Simpan
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {

		// Init select2 pada baris pertama
		$('.select2-coa').select2({
			placeholder: ':: Pilih CoA',
			width: '100%'
		});

		// =============================================
		// Format Rupiah dengan desimal
		// =============================================
		function formatRupiah(angka) {
			if (!angka && angka !== 0) return 'Rp0';
			let num = parseFloat(angka) || 0;
			return 'Rp' + num.toLocaleString('id-ID', {
				minimumFractionDigits: 0,
				maximumFractionDigits: 2
			});
		}

		function getRawNominal(val) {
			if (!val) return 0;
			// Hapus semua karakter selain angka, koma, titik
			// Format Indonesia: titik = pemisah ribuan, koma = desimal
			let cleaned = val.toString().replace(/\./g, '').replace(',', '.');
			return parseFloat(cleaned) || 0;
		}

		function formatInput(val) {
			if (!val) return '';

			// Pisahkan bagian integer dan desimal
			let parts = val.toString().split(',');
			let intPart = parts[0].replace(/\./g, ''); // hapus titik ribuan lama
			let decPart = parts[1] !== undefined ? parts[1] : null;

			// Hanya angka
			intPart = intPart.replace(/[^0-9]/g, '');

			// Format ribuan dengan titik
			let formatted = parseInt(intPart || '0').toLocaleString('id-ID');

			// Tambah bagian desimal jika ada (max 2 digit)
			if (decPart !== null) {
				decPart = decPart.replace(/[^0-9]/g, '').substring(0, 2);
				formatted += ',' + decPart;
			}

			return formatted;
		}

		// =============================================
		// Hitung total debit & kredit
		// =============================================
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

		// =============================================
		// Format nominal input saat ketik
		// Izinkan koma sebagai desimal
		// =============================================
		$(document).on('keypress', '.nominal-input', function(e) {
			let char = String.fromCharCode(e.which);
			let val = $(this).val();

			// Izinkan angka, titik (ribuan), dan koma (desimal)
			if (!/[0-9,]/.test(char)) return false;

			// Hanya boleh 1 koma
			if (char === ',' && val.indexOf(',') !== -1) return false;

			// Max 2 digit setelah koma
			if (val.indexOf(',') !== -1) {
				let decPart = val.split(',')[1];
				if (decPart && decPart.length >= 2 && char !== ',') return false;
			}
		});

		$(document).on('input', '.nominal-input', function() {
			let cursorPos = this.selectionStart;
			let oldVal = $(this).val();
			let newVal = formatInput(oldVal);
			$(this).val(newVal);

			// Pertahankan posisi kursor (approximate)
			try {
				this.setSelectionRange(cursorPos, cursorPos);
			} catch (e) {}

			hitungTotal();
		});

		// =============================================
		// Update total saat posisi berubah
		// =============================================
		$(document).on('change', 'select[name="posisi[]"]', function() {
			hitungTotal();
		});

		// =============================================
		// Tambah baris
		// =============================================
		$('#btn-add-row').on('click', function() {
			let template = document.getElementById('row-template');
			let clone = document.importNode(template.content, true);
			$('#detail-rows').append(clone);

			$('#detail-rows .detail-row:last-child .select2-coa').select2({
				placeholder: ':: Pilih CoA',
				width: '100%'
			});
		});

		// =============================================
		// Hapus baris (minimal 1 baris)
		// =============================================
		$(document).on('click', '.btn-remove-row', function() {
			if ($('#detail-rows .detail-row').length > 1) {
				$(this).closest('tr').remove();
				hitungTotal();
			} else {
				alert('Minimal harus ada 1 baris.');
			}
		});

		// Hitung total awal (untuk edit page yang sudah ada data)
		hitungTotal();
	});
</script>