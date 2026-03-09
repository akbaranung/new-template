<!-- nota/v_form.php -->
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Buat Nota Penjualan</h1>

			<div class="card shadow mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0 text-white"><i class="fe fe-shopping-cart"></i> Form Nota Penjualan</h5>
				</div>
				<div class="card-body">
					<form id="formNota">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="no_nota">No. Nota <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="no_nota" id="no_nota" value="<?= $no_nota ?>" readonly required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="tanggal">Tanggal <span class="text-danger">*</span></label>
									<input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="customer">Customer</label>
									<input type="text" class="form-control" name="customer" id="customer" placeholder="Nama Customer (Opsional)">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Metode Pembayaran <span class="text-danger">*</span></label>
									<div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="metode_bayar" id="metode_cash" value="cash" checked>
											<label class="form-check-label" for="metode_cash">
												<i class="fe fe-dollar-sign"></i> Cash
											</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="metode_bayar" id="metode_qris" value="qris">
											<label class="form-check-label" for="metode_qris">
												<i class="fe fe-smartphone"></i> QRIS
											</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="metode_bayar" id="metode_card" value="card">
											<label class="form-check-label" for="metode_card">
												<i class="fe fe-credit-card"></i> Card
											</label>
										</div>
									</div>
								</div>
							</div>
							<!-- Input no kartu — muncul hanya saat CARD dipilih -->
							<div class="col-md-6" id="wrapper_no_kartu" style="display:none;">
								<div class="form-group">
									<label for="no_kartu">No. Kartu <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="no_kartu" id="no_kartu" placeholder="Masukkan nomor kartu">
									<small class="text-muted">Untuk keperluan rekonsiliasi</small>
								</div>
							</div>
						</div>

						<hr>

						<h5 class="mb-3"><i class="fe fe-list"></i> Detail Item Barang</h5>

						<div class="table-responsive">
							<table class="table table-bordered table-sm" id="tableItems">
								<thead class="thead-light">
									<tr>
										<th width="5%">No</th>
										<th width="33%">Barang <span class="text-danger">*</span></th>
										<th width="10%" class="text-center">Stok</th>
										<th width="12%">Qty <span class="text-danger">*</span></th>
										<th width="18%">Harga Jual <span class="text-danger">*</span></th>
										<th width="15%">Subtotal</th>
										<th width="5%">
											<button type="button" class="btn btn-sm btn-primary" id="btnAddRow">
												<i class="fe fe-plus"></i>
											</button>
										</th>
									</tr>
								</thead>
								<tbody id="itemRows">
									<tr class="item-row">
										<td class="text-center row-number">1</td>
										<td>
											<select class="form-control form-control-sm select-item" name="id_item[]" required>
												<option value="">-- Pilih Barang --</option>
											</select>
										</td>
										<td class="text-center">
											<span class="badge badge-info stok-display">0</span>
										</td>
										<td>
											<input type="text" class="form-control form-control-sm text-right qty" name="qty[]" placeholder="0" required>
										</td>
										<td>
											<input type="text" class="form-control form-control-sm text-right harga-jual format-rupiah" name="harga_jual[]" placeholder="0" required>
										</td>
										<td>
											<input type="text" class="form-control form-control-sm text-right subtotal" readonly value="0">
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-sm btn-danger btn-remove-row">
												<i class="fe fe-trash-2"></i>
											</button>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr class="bg-primary">
										<td colspan="5" class="text-right"><strong class="text-white">TOTAL PENJUALAN:</strong></td>
										<td>
											<input type="text" class="form-control form-control-sm text-right font-weight-bold" id="grandTotal" readonly value="0">
										</td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>

						<div class="alert alert-warning">
							<i class="fe fe-info"></i> <strong>Petunjuk:</strong>
							<ul class="mb-0 pl-3">
								<li>Tekan <strong>Enter</strong> untuk menambah baris baru</li>
								<li>Pilih barang dari dropdown (ketik untuk mencari)</li>
								<li>Stok akan otomatis berkurang setelah disimpan</li>
								<li>Jurnal akan dibuat saat <strong>Closing Kasir</strong></li>
							</ul>
						</div>

						<hr>

						<div class="row">
							<div class="col-md-12 text-right">
								<a href="<?= base_url('nota') ?>" class="btn btn-secondary">
									<i class="fe fe-x"></i> Batal
								</a>
								<button type="submit" class="btn btn-primary" id="btnSimpan">
									<i class="fe fe-save"></i> Simpan Nota
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<style>
		.stok-warning {
			background-color: #ffc107 !important;
			color: #fff !important;
		}

		.stok-danger {
			background-color: #dc3545 !important;
			color: #fff !important;
		}

		.qty-error {
			border: 2px solid #dc3545 !important;
		}

		input.qty:disabled {
			background-color: #e9ecef !important;
			cursor: not-allowed !important;
			opacity: 0.6;
		}

		input.qty.near-max {
			background-color: #fff3cd;
		}

		.select2-results__option {
			padding: 6px 12px !important;
			cursor: pointer !important;
		}

		.select2-results__option--highlighted {
			background-color: #007bff !important;
			color: #fff !important;
		}

		.select2-results__option:hover {
			background-color: #e9f0ff !important;
			color: #333 !important;
		}
	</style>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
	$(document).ready(function() {
		let rowCount = 1;

		initSelect2($('.select-item'));

		// Show/hide no_kartu input sesuai metode bayar
		$('input[name="metode_bayar"]').on('change', function() {
			if ($(this).val() === 'card') {
				$('#wrapper_no_kartu').show();
				$('#no_kartu').attr('required', true);
			} else {
				$('#wrapper_no_kartu').hide();
				$('#no_kartu').removeAttr('required').val('');
			}
		});

		$('#btnAddRow').on('click', function() {
			addRow();
		});

		$(document).on('click', '.btn-remove-row', function() {
			if ($('#itemRows tr').length > 1) {
				$(this).closest('tr').remove();
				updateRowNumbers();
				calculateTotal();
			} else {
				alert('Minimal harus ada 1 item!');
			}
		});

		$(document).on('keydown', '#tableItems input, #tableItems select', function(e) {
			if (e.keyCode === 13) {

				if ($('.select2-results__options').is(':visible')) {
					return;
				}
				e.preventDefault();

				const currentRow = $(this).closest('tr');
				const itemId = currentRow.find('.select-item').val();
				const qty = parseFloat(currentRow.find('.qty').val().replace(',', '.')) || 0;

				if (!itemId) {
					showToast('Pilih barang pada baris ini sebelum menambah baris baru!', 'warning');
					currentRow.find('.select-item').select2('open');
					return;
				}

				if (qty <= 0) {
					showToast('Isi jumlah qty pada baris ini sebelum menambah baris baru!', 'warning');
					currentRow.find('.qty').focus();
					return;
				}

				addRow();
			}
		});

		function showToast(message, type = 'warning') {
			const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 2500,
				timerProgressBar: true,
				didOpen: function(toast) {
					toast.addEventListener('mouseenter', Swal.stopTimer);
					toast.addEventListener('mouseleave', Swal.resumeTimer);
				}
			});

			Toast.fire({
				icon: type,
				title: message
			});
		}

		console.log('Form Nota siap digunakan');

		$(document).on('keyup change', '.qty, .harga-jual', function() {
			const row = $(this).closest('tr');
			const currentItemId = row.data('item-id');

			if ($(this).hasClass('qty') && currentItemId) {
				const qty = parseFloat($(this).val().replace(',', '.')) || 0;
				const maxStok = parseFloat($(this).attr('max')) || 0;

				if (qty > maxStok && maxStok > 0) {
					$(this).val(maxStok.toString().replace('.', ','));
					showToast('Qty melebihi stok tersedia!', 'warning');
				}

				const stokDB = parseFloat(row.data('stok-db')) || 0;

				// Hitung total qty terpakai dari SEMUA row item sama
				let totalTerpakai = 0;
				$('#itemRows tr.item-row').each(function() {
					if ($(this).data('item-id') == currentItemId) {
						totalTerpakai += parseFloat($(this).find('.qty').val().replace(',', '.')) || 0;
					}
				});

				const sisaGlobal = Math.max(0, stokDB - totalTerpakai);

				// Semua row item sama tampilkan sisa global yang sama
				$('#itemRows tr.item-row').each(function() {
					if ($(this).data('item-id') == currentItemId) {
						$(this).find('.stok-display').text(sisaGlobal.toFixed(2));
						$(this).find('.qty').attr('max', sisaGlobal + (parseFloat($(this).find('.qty').val().replace(',', '.')) || 0));
					}
				});
			}

			calculateSubtotal(row);
			calculateTotal();
		});

		$(document).on('blur', '.qty', function() {
			const row = $(this).closest('tr');
			const qty = parseFloat($(this).val().replace(',', '.')) || 0;
			const maxStok = parseFloat($(this).attr('max')) || 0;

			if (qty > maxStok && maxStok > 0) {
				$(this).val(maxStok.toString().replace('.', ','));
				calculateSubtotal(row);
				calculateTotal();
			}
		});

		$(document).on('keyup', '.format-rupiah', function() {
			let value = $(this).val().replace(/\./g, '');
			if (!isNaN(value) && value !== '') {
				$(this).val(formatRupiah(value));
			}
			calculateSubtotal($(this).closest('tr'));
			calculateTotal();
		});

		// Submit Form
		$(document).on('submit', '#formNota', function(e) {
			e.preventDefault();

			let hasItem = false;
			$('.select-item').each(function() {
				if ($(this).val() !== '') {
					hasItem = true;
					return false;
				}
			});

			if (!hasItem) {
				alert('Pilih minimal 1 item barang!');
				return;
			}

			// Validasi no_kartu kalau metode card
			const metode = $('input[name="metode_bayar"]:checked').val();
			if (metode === 'card' && $('#no_kartu').val().trim() === '') {
				alert('No. kartu harus diisi!');
				$('#no_kartu').focus();
				return;
			}

			let stokCukup = true;
			let pesanError = '';

			$('.item-row').each(function() {
				const row = $(this);
				const itemId = row.find('.select-item').val();
				if (!itemId) return;

				const namaBarang = row.find('.select-item option:selected').text();
				const stok = parseFloat(row.find('.stok-display').text()) || 0;
				const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;

				if (qty <= 0) {
					stokCukup = false;
					pesanError += '- ' + namaBarang + ': Qty harus lebih dari 0\n';
					return;
				}

				if (stok <= 0) {
					stokCukup = false;
					pesanError += '- ' + namaBarang + ': Stok habis!\n';
					return;
				}

				if (qty > stok) {
					stokCukup = false;
					pesanError += '- ' + namaBarang + ': Stok tidak cukup (Tersedia: ' + stok.toFixed(2) + ', Diminta: ' + qty.toFixed(2) + ')\n';
					return;
				}
			});

			if (!stokCukup) {
				alert('Stok tidak mencukupi untuk:\n' + pesanError);
				return;
			}

			if (!$('#formNota')[0].checkValidity()) {
				$('#formNota')[0].reportValidity();
				return;
			}

			$('#btnSimpan').prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

			$.ajax({
				url: '<?= base_url("nota/save") ?>',
				type: 'POST',
				data: $(this).serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						alert(response.message);
						const printUrl = '<?= base_url("nota/print_nota/") ?>' + response.id_nota;
						window.open(printUrl, '_blank', 'width=800,height=600');
						window.location.href = response.redirect;
					} else {
						alert(response.message);
						$('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Nota');
					}
				},
				error: function() {
					alert('Terjadi kesalahan saat menyimpan data!');
					$('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Nota');
				}
			});
		});

		function addRow() {
			rowCount++;
			const newRow = `
				<tr class="item-row">
					<td class="text-center row-number">${rowCount}</td>
					<td>
						<select class="form-control form-control-sm select-item" name="id_item[]" required>
							<option value="">-- Pilih Barang --</option>
						</select>
					</td>
					<td class="text-center">
						<span class="badge badge-info stok-display">0</span>
					</td>
					<td>
						<input type="text" class="form-control form-control-sm text-right qty"
							name="qty[]" placeholder="0" required>
					</td>
					<td>
						<input type="text" class="form-control form-control-sm text-right harga-jual format-rupiah"
							name="harga_jual[]" placeholder="0" required>
					</td>
					<td>
						<input type="text" class="form-control form-control-sm text-right subtotal"
							readonly value="0">
					</td>
					<td class="text-center">
						<button type="button" class="btn btn-sm btn-danger btn-remove-row">
							<i class="fe fe-trash-2"></i>
						</button>
					</td>
				</tr>
			`;
			$('#itemRows').append(newRow);
			const newSelect = $('#itemRows tr:last .select-item');
			initSelect2(newSelect);
			newSelect.select2('open');
		}

		function initSelect2(element) {
			element.select2({
				placeholder: '-- Pilih Barang --',
				allowClear: true,
				width: '100%',
				minimumInputLength: 3,
				language: {
					inputTooShort: function() {
						return 'Ketik minimal 3 karakter untuk mencari barang...';
					}
				},
				ajax: {
					url: '<?= base_url("items/get_items") ?>',
					dataType: 'json',
					delay: 1000,
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

			element.on('select2:select', function(e) {
				const data = e.params.data;
				const row = $(this).closest('tr');
				const qtyInput = row.find('.qty');
				const itemId = data.id;
				const stokDB = parseFloat(data.stok) || 0; // ← pindah ke sini, sebelum dipakai

				// Reset state row
				qtyInput.prop('disabled', false).val('').removeClass('qty-error');
				row.find('.stok-display').removeClass('stok-danger stok-warning').text('0');
				row.find('.stock-warning').remove();
				row.find('.harga-jual').val('');
				row.find('.subtotal').val('0');

				// Simpan ke data row
				row.data('stok-db', stokDB);
				row.data('item-id', itemId);

				// Hitung stok terpakai di row lain yang item-nya sama
				let stokTerpakai = 0;
				$('#itemRows tr.item-row').not(row).each(function() {
					if ($(this).data('item-id') == itemId) {
						stokTerpakai += parseFloat($(this).find('.qty').val().replace(',', '.')) || 0;
					}
				});

				const stokTersedia = Math.max(0, stokDB - stokTerpakai);

				row.find('.stok-display').text(stokTersedia.toFixed(2));

				if (data.harga_jual) {
					row.find('.harga-jual').val(formatRupiah(data.harga_jual));
				}

				if (stokTersedia <= 0) {
					qtyInput.prop('disabled', true).val('').attr('placeholder', 'Stok Habis');
					row.find('.stok-display').addClass('stok-danger');
					setTimeout(function() {
						if (!row.find('.stock-warning').length) {
							qtyInput.after('<small class="text-danger d-block stock-warning mt-1">⚠️ Stok habis</small>');
						}
					}, 100);
					return;
				}

				qtyInput.prop('disabled', false)
					.attr('max', stokTersedia)
					.attr('placeholder', 'Max: ' + stokTersedia.toFixed(2));
				qtyInput.focus();
			});
		}

		function calculateSubtotal(row) {
			const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;
			const hargaJual = parseFloat(row.find('.harga-jual').val().replace(/\./g, '')) || 0;
			const stok = parseFloat(row.find('.stok-display').text()) || 0;

			row.find('.subtotal').val(formatRupiah(Math.floor(qty * hargaJual)));

			const stokDisplay = row.find('.stok-display');
			const qtyInput = row.find('.qty');

			stokDisplay.removeClass('stok-warning stok-danger');
			qtyInput.removeClass('qty-error');

			if (qty > 0) {
				if (stok <= 0) {
					stokDisplay.addClass('stok-danger');
					qtyInput.addClass('qty-error');
				} else if (qty > stok) {
					stokDisplay.addClass('stok-warning');
					qtyInput.addClass('qty-error');
				} else if (stok <= 5) {
					stokDisplay.addClass('stok-warning');
				}
			}
		}

		// Hanya hitung total penjualan
		function calculateTotal() {
			let total = 0;
			$('.item-row').each(function() {
				const qty = parseFloat($(this).find('.qty').val().replace(',', '.')) || 0;
				const hargaJual = parseFloat($(this).find('.harga-jual').val().replace(/\./g, '')) || 0;
				total += (qty * hargaJual);
			});
			$('#grandTotal').val(formatRupiah(Math.floor(total)));
		}

		function updateRowNumbers() {
			$('#itemRows tr').each(function(index) {
				$(this).find('.row-number').text(index + 1);
			});
			rowCount = $('#itemRows tr').length;
		}

		function formatRupiah(angka) {
			let number = Math.floor(parseFloat(angka));
			if (isNaN(number)) number = 0;
			return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}
	});
</script>
