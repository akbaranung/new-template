<!-- invoice penjualan item dari DB -->
<!-- file: pages/financial/v_create_invoice_item.php -->
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12">

			<div class="d-flex align-items-center justify-content-between mb-4">
				<h1 class="page-title mb-0">Buat Invoice (Item)</h1>
				<a href="<?= base_url('financial/invoice') ?>" class="btn btn-sm btn-warning text-white">
					<i class="fe fe-arrow-left me-1"></i> Back
				</a>
			</div>

			<form method="POST" action="<?= base_url('financial/store_invoice_penjualan') ?>" id="formInvoice">
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
								<div class="table-responsive">
									<table class="table table-sm table-bordered mb-0" id="tabelItem">
										<thead class="table-light">
											<tr>
												<th style="width:35%; color:#6c757d;">Item</th>
												<th style="width:8%; color:#6c757d; text-align:center;">Stok</th>
												<th style="width:12%; color:#6c757d;">Qty</th>
												<th style="width:18%; color:#6c757d;">Harga Jual</th>
												<th style="width:18%; color:#6c757d;">Amount</th>
												<th style="width:9%; color:#6c757d; text-align:center;">#</th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr class="baris">
												<td>
													<select class="form-control form-control-sm select-item" name="id_item[]" required>
														<option value="">-- Pilih Item --</option>
													</select>
													<!-- hidden fields per baris -->
													<input type="hidden" name="item[]" class="nama-item">
													<input type="hidden" name="total[]" class="harga-satuan" value="0">
												</td>
												<td class="text-center align-middle">
													<span class="badge badge-info stok-display">0</span>
												</td>
												<td>
													<input type="text" class="form-control form-control-sm jumlah" name="jumlah[]" value="0">
												</td>
												<td>
													<input type="text" class="form-control form-control-sm harga-jual-display" value="0" readonly>
												</td>
												<td>
													<input type="text" class="form-control form-control-sm" name="total_amount[]" value="0" readonly>
												</td>
												<td class="text-center align-middle">
													<button type="button" class="btn btn-danger btn-sm hapusRow d-none">
														<i class="fe fe-trash"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
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
										<select name="coa_debit" id="coa_debit" class="form-control select2" style="width:100%" required>
											<option value="">:: Pilih CoA Debit</option>
											<?php foreach ($pendapatan as $pd) : ?>
												<option value="<?= $pd->no_sbb ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-4">
										<label class="form-label">CoA Kredit</label>
										<select name="coa_kredit" id="coa_kredit" class="form-control select2" style="width:100%" required>
											<option value="">:: Pilih CoA Kredit</option>
											<?php foreach ($persediaan as $ps) : ?>
												<option value="<?= $ps->no_sbb ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
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

								<!-- Hidden fields kalkulasi -->
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

	.stok-danger {
		background-color: #dc3545 !important;
		color: #fff !important;
	}

	.stok-warning {
		background-color: #ffc107 !important;
		color: #fff !important;
	}
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	function formatNumber(number) {
		let parts = number.toString().split(",");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(",");
	}

	function formatRupiah(angka) {
		let number = Math.floor(parseFloat(angka));
		if (isNaN(number)) number = 0;
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	$(document).ready(function() {

		/* ── Sync customer ke info card ── */
		$('#customer').on('change', function() {
			var label = $(this).find('option:selected').text();
			$('#ic-customer').text($(this).val() ? label : '—');
		});

		/* ── Update info card ── */
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

		/* ── Hitung amount per baris ── */
		function hitungAmountBaris(row) {
			var jumlah = parseFloat(row.find('.jumlah').val().replace(/\,/g, '')) || 0;
			var harga = parseFloat(row.find('.harga-satuan').val().replace(/\,/g, '')) || 0;
			var amount = jumlah * harga;
			row.find('input[name="total_amount[]"]').val(formatNumber(amount.toFixed(0)));
		}

		/* ── Update subtotal semua baris → #nominal ── */
		function updateTotalBelanja() {
			var total_pos_fix = 0;
			$('.baris').each(function() {
				var v = parseFloat($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, ''));
				if (!isNaN(v)) total_pos_fix += v;
			});
			$('#nominal').val(formatNumber(total_pos_fix));
		}

		/* ── Kalkulasi utama (dari existing JS) ── */
		function updateTotal() {
			var ppn = parseFloat($('#ppn').val()) || 0;
			var pph = 0.02;
			var besaranpph = 0;

			var subtotal = 0;
			$('.baris').each(function() {
				subtotal += parseInt($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, '') || 0);
			});

			if ($('#opsi_pph').is(':checked')) {
				besaranpph = subtotal * pph;
			}

			var besaranppn = subtotal * ppn;
			var total_nonpph = subtotal + besaranppn;
			var total_denganpph = subtotal + besaranppn - besaranpph;
			var pendapatan = subtotal - besaranpph;
			var nominal_bayar = subtotal + besaranppn - besaranpph;

			$('#besaran_ppn').val(formatNumber(besaranppn.toFixed(0)));
			$('#besaran_pph').val(formatNumber(besaranpph.toFixed(0)));
			$('#total_nonpph').val(formatNumber(total_nonpph.toFixed(0)));
			$('#total_denganpph').val(formatNumber(total_denganpph.toFixed(0)));
			$('#nominal_pendapatan').val(formatNumber(pendapatan.toFixed(0)));
			$('#nominal_bayar').val(formatNumber(nominal_bayar.toFixed(0)));

			updateInfoCard();
		}

		/* ── Event: input qty ── */
		$(document).on('change click keyup input paste', 'input.jumlah', function() {
			$(this).val(function(i, v) {
				return v.replace(/(?!\.)\D/g, "")
					.replace(/(?<=\..*)\./g, "")
					.replace(/(?<=\.\d\d).*/g, "")
					.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});
			var row = $(this).closest('.baris');
			hitungAmountBaris(row);
			updateTotalBelanja();
			updateTotal();
		});

		/* ── Event: PPN & PPh toggle ── */
		$('#ppn').on('change', function() {
			updateTotal();
		});
		$('#opsi_pph').on('change', function() {
			updateTotal();
		});

		/* ── Init Select2 AJAX per baris ── */
		function initSelectItem(selectEl) {
			selectEl.select2({
				placeholder: '-- Cari Item --',
				allowClear: true,
				width: '100%',
				minimumInputLength: 3,
				language: {
					inputTooShort: function() {
						return 'Ketik minimal 3 karakter...';
					}
				},
				ajax: {
					url: '<?= base_url("items/get_items") ?>',
					dataType: 'json',
					delay: 400,
					data: function(params) {
						return {
							q: params.term
						};
					},
					processResults: function(data) {
						return {
							results: data.results
						};
					},
					cache: true
				}
			});

			/* Saat item dipilih → isi harga & stok */
			selectEl.on('select2:select', function(e) {
				var data = e.params.data;
				var row = $(this).closest('.baris');

				var stok = parseFloat(data.stok) || 0;
				var harga = parseFloat(data.harga_jual) || 0;
				var namaItem = data.text;

				/* Isi hidden & display fields */
				row.find('.nama-item').val(namaItem);
				row.find('.harga-satuan').val(harga);
				row.find('.harga-jual-display').val(formatRupiah(harga));
				row.find('.stok-display').text(stok.toFixed(2))
					.removeClass('stok-danger stok-warning badge-secondary')
					.addClass(stok <= 0 ? 'stok-danger' : (stok <= 5 ? 'stok-warning' : 'badge-info'));

				/* Simpan stok ke data attribute untuk validasi qty */
				row.data('stok-db', stok);
				row.data('id-item', data.id);

				/* Reset qty, fokus */
				row.find('.jumlah').val('1').focus().select();
				hitungAmountBaris(row);
				updateTotalBelanja();
				updateTotal();
			});

			/* Clear → reset baris */
			selectEl.on('select2:clear', function() {
				var row = $(this).closest('.baris');
				row.find('.nama-item').val('');
				row.find('.harga-satuan').val('0');
				row.find('.harga-jual-display').val('0');
				row.find('.stok-display').text('0').removeClass('stok-danger stok-warning').addClass('badge-info');
				row.find('.jumlah').val('0');
				row.find('input[name="total_amount[]"]').val('0');
				row.removeData('stok-db').removeData('id-item');
				updateTotalBelanja();
				updateTotal();
			});
		}

		/* Init baris pertama */
		initSelectItem($('.baris:first .select-item'));

		/* ── Tambah baris ── */
		$('#addRow').on('click', function() {
			var previousRow = $('.baris').last();

			/* Validasi baris sebelumnya sudah terisi */
			var itemVal = previousRow.find('.select-item').val();
			var qtyVal = parseFloat(previousRow.find('.jumlah').val().replace(/\,/g, '')) || 0;

			if (!itemVal) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Pilih item pada baris sebelumnya terlebih dahulu!'
				});
				return;
			}
			if (qtyVal <= 0) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Isi qty pada baris sebelumnya terlebih dahulu!'
				});
				return;
			}

			var newRow = previousRow.clone(false); /* clone tanpa event */
			/* Reset isi baris baru */
			newRow.find('.select-item').val(null);
			newRow.find('.nama-item').val('');
			newRow.find('.harga-satuan').val('0');
			newRow.find('.harga-jual-display').val('0');
			newRow.find('.stok-display').text('0').removeClass('stok-danger stok-warning').addClass('badge-info');
			newRow.find('.jumlah').val('0');
			newRow.find('input[name="total_amount[]"]').val('0');
			newRow.find('.hapusRow').removeClass('d-none');
			newRow.removeData('stok-db').removeData('id-item');

			$('#tbody').append(newRow);

			/* Re-init select2 pada baris baru */
			initSelectItem(newRow.find('.select-item'));
		});

		/* ── Hapus baris ── */
		$(document).on('click', '.hapusRow', function() {
			$(this).closest('.baris').remove();
			updateTotalBelanja();
			updateTotal();
		});

		/* ── Validasi stok sebelum submit ── */
		$('#formInvoice').on('submit', function(e) {
			var stokError = [];

			$('.baris').each(function() {
				var itemId = $(this).data('id-item');
				if (!itemId) return;

				var stokDB = parseFloat($(this).data('stok-db')) || 0;
				var qty = parseFloat($(this).find('.jumlah').val().replace(/\,/g, '')) || 0;
				var namaItem = $(this).find('.nama-item').val();

				if (qty > stokDB) {
					stokError.push(namaItem + ' (Stok: ' + stokDB.toFixed(2) + ', Diminta: ' + qty.toFixed(2) + ')');
				}
			});

			if (stokError.length > 0) {
				e.preventDefault();
				Swal.fire({
					icon: 'error',
					title: 'Stok Tidak Cukup',
					html: '<ul class="text-left">' + stokError.map(function(s) {
						return '<li>' + s + '</li>';
					}).join('') + '</ul>'
				});
				return false;
			}
		});

		/* Init kalkulasi */
		updateTotal();
	});
</script>
