<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12">

			<div class="d-flex align-items-center justify-content-between mb-4">
				<h1 class="page-title mb-0">Buat Invoice</h1>
				<a href="<?= base_url('financial/invoice') ?>" class="btn btn-sm btn-warning text-white">
					<i class="fe fe-arrow-left me-1"></i> Back
				</a>
			</div>

			<form method="POST" action="<?= base_url('financial/store_invoice/khusus') ?>" id="formInvoice">
				<div class="row g-4 align-items-start">

					<!-- ══════════════════════════════ FORM (kiri) ══════════════════════════════ -->
					<div class="col-lg-8">
						<div class="card shadow-sm">
							<div class="card-body p-4">

								<!-- ── Identitas Dokumen ── -->
								<p class="section-label">
									<i class="fe fe-file-text me-1"></i> Identitas Dokumen
								</p>
								<div class="row g-3">
									<div class="col-md-3">
										<label class="form-label">Tgl. Invoice</label>
										<input type="date" class="form-control" name="tgl_invoice" value="<?= date('Y-m-d') ?>" required>
									</div>
									<div class="col-md-3">
										<label class="form-label">
											No. PO
											<span class="badge bg-secondary ms-1 fw-normal text-white" style="font-size:0.65rem;">opsional</span>
										</label>
										<input type="text" class="form-control uppercase" name="no_po" placeholder="—">
									</div>
									<div class="col-md-3">
										<label class="form-label">
											Tgl. PO
											<span class="badge bg-secondary ms-1 fw-normal text-white" style="font-size:0.65rem;">opsional</span>
										</label>
										<input type="date" class="form-control" name="tgl_po">
									</div>
									<div class="col-md-3">
										<label class="form-label">PPN</label>
										<select name="ppn" id="ppn" class="form-control">
											<option value="0">0%</option>
											<option value="<?= $this->session->userdata('ppn') ?>"><?= $this->session->userdata('nama_ppn') ?></option>
										</select>
									</div>
								</div>
								<div class="row g-3 mt-1">
									<div class="col-12">
										<label class="form-label">Bill To</label>
										<select name="customer" id="customer" class="form-control select2" style="width:100%" required>
											<option value="">:: Pilih customer</option>
											<?php foreach ($customers as $c) : ?>
												<option value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="row g-3 mt-1">
									<div class="col-12">
										<label class="form-label">Notes</label>
										<textarea name="keterangan" id="keterangan" class="form-control uppercase" rows="2" placeholder="Keterangan invoice..." required></textarea>
									</div>
								</div>

								<hr class="my-4">

								<!-- ── Item Invoice ── -->
								<p class="section-label">
									<i class="fe fe-list me-1"></i> Item Invoice
								</p>
								<table class="table table-sm table-bordered mb-0" id="tabelItem">
									<thead class="table-light">
										<tr>
											<th style="width:42%; color:#6c757d;">Keterangan</th>
											<th style="width:13%; color:#6c757d;">Jumlah</th>
											<th style="width:18%; color:#6c757d;">Nominal</th>
											<th style="width:18%; color:#6c757d;">Amount</th>
											<th style="width:9%; color:#6c757d; text-align:center;">#</th>
										</tr>
									</thead>
									<tbody id="tbody">
										<tr class="baris">
											<td><input type="text" class="form-control form-control-sm uppercase" name="item[]"></td>
											<td><input type="text" class="form-control form-control-sm" name="jumlah[]" value="0"></td>
											<td><input type="text" class="form-control form-control-sm" name="total[]" value="0"></td>
											<td><input type="text" class="form-control form-control-sm" name="total_amount[]" value="0" readonly></td>
											<td class="text-center align-middle">
												<button type="button" class="btn btn-danger btn-sm hapusRow d-none">
													<i class="fe fe-trash"></i>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
								<button type="button" id="addRow" class="btn btn-sm btn-outline-secondary mt-2">
									<i class="fe fe-plus me-1"></i> Tambah baris
								</button>

								<hr class="my-4">

								<!-- ── Konfigurasi ── -->
								<p class="section-label">
									<i class="fe fe-settings me-1"></i> Konfigurasi
								</p>
								<div class="row g-3">
									<div class="col-md-4">
										<label class="form-label">CoA Debit</label>
										<select name="coa_debit" id="coa_debit" class="form-control select2 coa_debit" style="width:100%" required>
											<option value="">:: Pilih CoA Debit</option>
											<?php foreach ($pendapatan as $pd) : ?>
												<option value="<?= $pd->no_sbb ?>" data-posisi="<?= $pd->posisi ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-4">
										<label class="form-label">CoA Kredit</label>
										<select name="coa_kredit" id="coa_kredit" class="form-control select2 coa_kredit" style="width:100%" required>
											<option value="">:: Pilih CoA Kredit</option>
											<?php foreach ($persediaan as $ps) : ?>
												<option value="<?= $ps->no_sbb ?>" data-posisi="<?= $ps->posisi ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-4">
										<label class="form-label">Metode Bayar</label>
										<select name="metode_bayar" id="metode_bayar" class="form-control" required>
											<option value="">:: Pilih Metode</option>
											<option value="cod">Cash on Delivery (CoD)</option>
											<option value="tempo">Tempo</option>
										</select>
									</div>
								</div>
								<div class="row g-3 mt-1 align-items-end">
									<div class="col-md-4">
										<label class="form-label">Tgl. Jatuh Tempo</label>
										<input type="date" class="form-control" name="tgl_jatuh_tempo" value="<?= date('Y-m-d') ?>" required>
									</div>
									<div class="col-md-4">
										<label class="form-label d-block mb-2">Opsi</label>
										<div class="d-flex gap-4">
											<div class="form-check mb-0">
												<input type="checkbox" class="form-check-input" name="opsi_termin" id="opsi_termin" value="1" checked>
												<label class="form-check-label" for="opsi_termin">Termin</label>
											</div>
											<div class="form-check mb-0">
												<input type="checkbox" class="form-check-input" name="opsi_pph" id="opsi_pph" value="1">
												<label class="form-check-label" for="opsi_pph">PPh 23</label>
											</div>
										</div>
									</div>
								</div>

								<!-- Hidden fields kalkulasi untuk dikirim ke server -->
								<input type="hidden" name="nominal" id="nominal" value="0">
								<input type="hidden" name="besaran_ppn" id="besaran_ppn" value="0">
								<input type="hidden" name="besaran_pph" id="besaran_pph" value="0">
								<input type="hidden" name="total_nonpph" id="total_nonpph" value="0">
								<input type="hidden" name="total_denganpph" id="total_denganpph" value="0">
								<input type="hidden" name="nominal_pendapatan" id="nominal_pendapatan" value="0">
								<input type="hidden" name="nominal_bayar" id="nominal_bayar" value="0">

							</div><!-- .card-body -->
						</div><!-- .card -->
					</div><!-- col-lg-8 -->

					<!-- ══════════════════════════════ INFO CARD (kanan) ══════════════════════════════ -->
					<div class="col-lg-4">
						<div class="card shadow-sm" style="position: sticky; top: 80px;">
							<div class="card-body p-4">

								<p class="section-label mb-3">
									<i class="fe fe-file me-1"></i> Ringkasan Invoice
								</p>

								<!-- Bill To -->
								<div class="rounded p-3 mb-3" style="background: #f8f9fa;">
									<p style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.06em; color:#6c757d; margin-bottom:2px;">Bill To</p>
									<p class="fw-semibold mb-0" id="ic-customer">—</p>
								</div>

								<!-- Kalkulasi -->
								<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
									<span class="text-muted" style="font-size:0.85rem;">Subtotal</span>
									<span class="fw-semibold" id="ic-subtotal">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
									<span class="text-muted" style="font-size:0.85rem;">PPN (<span id="ic-ppn-pct">0</span>%)</span>
									<span class="fw-semibold" id="ic-ppn">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between align-items-center py-2 border-bottom" id="row-pph" style="display:none !important;">
									<span class="text-muted" style="font-size:0.85rem;">PPh 23 (2%)</span>
									<span class="fw-semibold" id="ic-pph">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
									<span style="font-size:0.85rem;">Total <small class="text-muted">(non PPh)</small></span>
									<span class="fw-semibold" id="ic-total-nonpph">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between align-items-center py-2 border-bottom">
									<span style="font-size:0.85rem;">Total <small class="text-muted">(w/ PPh)</small></span>
									<span class="fw-semibold" id="ic-total-pph">Rp 0</span>
								</div>
								<div class="d-flex justify-content-between align-items-center py-2 rounded px-2 mt-2" style="background:#e8f5e9;">
									<span class="fw-semibold text-success" style="font-size:0.85rem;">Pendapatan</span>
									<span class="fw-bold text-success" id="ic-pendapatan">Rp 0</span>
								</div>

								<div class="mt-4">
									<button type="submit" class="btn btn-primary w-100">
										<i class="fe fe-save me-1"></i> Simpan Invoice
									</button>
								</div>

							</div><!-- .card-body -->
						</div><!-- .card -->
					</div><!-- col-lg-4 -->

				</div><!-- .row -->
			</form>

		</div>
	</div>
</div>

<style>
	.section-label {
		font-size: 0.72rem;
		letter-spacing: 0.08em;
		text-transform: uppercase;
		font-weight: 600;
		color: #6c757d;
		margin-bottom: 0.75rem;
	}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	function formatNumber(number) {
		let parts = number.toString().split(",");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(",");
	}

	$(document).ready(function() {

		/* ── [BARU] Sync customer ke info card ── */
		$('#customer').on('change', function() {
			var label = $(this).find('option:selected').text();
			$('#ic-customer').text($(this).val() ? label : '—');
		});

		/* ── [BARU] Update tampilan info card ── */
		function updateInfoCard() {
			var fmt = function(val) {
				return 'Rp ' + Math.round(val).toLocaleString('id-ID');
			};

			var subtotal = parseInt($('#nominal').val().replace(/\,/g, '') || 0);
			var ppnPct = parseFloat($('#ppn').val()) || 0;
			var besaranPpn = parseInt($('#besaran_ppn').val().replace(/\,/g, '') || 0);
			var besaranPph = parseInt($('#besaran_pph').val().replace(/\,/g, '') || 0);
			var totalNonPph = parseInt($('#total_nonpph').val().replace(/\,/g, '') || 0);
			var totalDenganPph = parseInt($('#total_denganpph').val().replace(/\,/g, '') || 0);
			var pendapatan = parseInt($('#nominal_pendapatan').val().replace(/\,/g, '') || 0);
			var usePph = $('#opsi_pph').is(':checked');

			$('#ic-subtotal').text(fmt(subtotal));
			$('#ic-ppn-pct').text(ppnPct);
			$('#ic-ppn').text(fmt(besaranPpn));
			$('#ic-pph').text(fmt(besaranPph));
			$('#ic-total-nonpph').text(fmt(totalNonPph));
			$('#ic-total-pph').text(fmt(totalDenganPph));
			$('#ic-pendapatan').text(fmt(pendapatan));

			if (usePph) {
				$('#row-pph').css('display', 'flex');
			} else {
				$('#row-pph').hide();
			}
		}

		/* ── Existing: format input jumlah & total saat diketik ── */
		$(document).on('change click keyup input paste', 'input[name="jumlah[]"], input[name="total[]"]', function(event) {
			$(this).val(function(index, value) {
				return value.replace(/(?!\.)\D/g, "")
					.replace(/(?<=\..*)\./g, "")
					.replace(/(?<=\.\d\d).*/g, "")
					.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});

			var row = $(this).closest('.baris');
			hitungTotal(row);
			updateTotalBelanja();
			updateTotal();
		});

		function hitungTotal(row) {
			var total = row.find('input[name="total[]"]').val().replace(/\,/g, '');
			var jumlah = row.find('input[name="jumlah[]"]').val().replace(/\,/g, '');

			total = (total) || 0;
			jumlah = (jumlah) || 0;

			var total_amount = Number(total) * Number(jumlah);
			row.find('input[name="total_amount[]"]').val(formatNumber(total_amount.toFixed(0)));
			updateTotalBelanja();
		}

		function updateTotalBelanja() {
			var total_pos_fix = 0;
			$(".baris").each(function() {
				var total = parseFloat($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, ''));
				if (!isNaN(total)) {
					total_pos_fix += total;
				}
			});
			$('#nominal').val(formatNumber(total_pos_fix));
		}

		/* ── Existing: hapus row ── */
		$(document).on('click', '.hapusRow', function() {
			$(this).closest('.baris').remove();
			updateTotalBelanja();
			updateTotal();
		});

		/* ── Existing: trigger updateTotal ── */
		$('#ppn').on('change', function() {
			updateTotal();
		});
		$('#opsi_pph').on('change', function() {
			updateTotal();
		});

		/* ── Existing: kalkulasi utama ── */
		function updateTotal() {
			var ppn = parseFloat($('#ppn').val());
			var pph = 0.02;
			var besaranpph = parseFloat($('#besaran_pph').val()) || 0;

			var subtotal = 0;
			$('.baris').each(function() {
				var totalBaris = parseInt($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, '') || 0);
				subtotal += totalBaris;
			});

			var total = subtotal;

			if ($('#opsi_pph').is(':checked')) {
				besaranpph = total * pph;
			} else {
				besaranpph = 0;
			}

			var besaranppn = total * ppn;
			var total_nonpph = total + besaranppn;
			var total_denganpph = total + besaranppn - besaranpph;
			var pendapatan = total - besaranpph;
			var nominal_bayar = total + besaranppn - besaranpph;

			$('#besaran_ppn').val(formatNumber(besaranppn.toFixed(0)));
			$('#besaran_pph').val(formatNumber(besaranpph.toFixed(0)));
			$('#total_nonpph').val(formatNumber(total_nonpph.toFixed(0)));
			$('#total_denganpph').val(formatNumber(total_denganpph.toFixed(0)));
			$('#nominal_pendapatan').val(formatNumber(pendapatan.toFixed(0)));
			$('#nominal_bayar').val(formatNumber(nominal_bayar.toFixed(0)));

			/* [BARU] Setelah hidden fields diupdate, refresh info card */
			updateInfoCard();
		}

		/* ── Existing: tambah baris (dengan validasi Swal dari existing) ── */
		var rowCount = 1;

		$('#addRow').on('click', function() {
			var previousRow = $('.baris').last();
			var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
			var isEmpty = false;

			inputs.each(function() {
				if ($(this).val().trim() === '' && $(this).attr('name') !== 'total_amount[]') {
					isEmpty = true;
					return false;
				}
			});

			if (isEmpty) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
				});
				return;
			}

			var newRow = previousRow.clone();
			newRow.find('input').val('');
			newRow.find('input[name="total[]"]').val('0');
			newRow.find('input[name="jumlah[]"]').val('0');
			newRow.find('input[name="total_amount[]"]').val('0');
			newRow.find('input[type="checkbox"]').prop('checked', false);
			newRow.find('.hapusRow').removeClass('d-none');

			previousRow.after(newRow);
			rowCount++;
		});

		/* ── Existing: hapus row (class .hapusRow dari clone) ── */
		$(document).on('click', '.hapusRow', function() {
			$(this).closest('.baris').remove();
			updateTotalBelanja();
			updateTotal();
		});

		/* Init */
		updateTotal();
	});
</script>