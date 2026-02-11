<!-- v_form_stok_masuk.php -->
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Input Stok Barang</h1>

			<div class="card shadow mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0 text-white"><i class="fe fe-package"></i> Form Input Stok</h5>
				</div>
				<div class="card-body">
					<form id="formStokMasuk">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="no_transaksi">No. Transaksi <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="no_transaksi" id="no_transaksi" value="<?= $no_transaksi ?>" readonly required>
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
									<label for="supplier">Supplier <span class="text-danger">*</span></label>
									<input type="text" class="form-control" name="supplier" id="supplier" placeholder="Nama Supplier" required>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
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
											<input class="form-check-input" type="radio" name="metode_bayar" id="metode_kredit" value="kredit">
											<label class="form-check-label" for="metode_kredit">
												<i class="fe fe-credit-card"></i> Kredit
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<hr>

						<h5 class="mb-3"><i class="fe fe-list"></i> Detail Item Barang</h5>

						<!-- Table Detail Items -->
						<div class="table-responsive">
							<table class="table table-bordered table-sm" id="tableItems">
								<thead class="thead-light">
									<tr>
										<th width="5%">No</th>
										<th width="35%">Barang <span class="text-danger">*</span></th>
										<th width="15%">Qty <span class="text-danger">*</span></th>
										<th width="20%">Harga Modal <span class="text-danger">*</span></th>
										<th width="20%">Subtotal</th>
										<th width="5%">
											<button type="button" class="btn btn-sm btn-success" id="btnAddRow">
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
										<td>
											<input type="text" class="form-control form-control-sm text-right qty" name="qty[]" placeholder="0" required>
										</td>
										<td>
											<input type="text" class="form-control form-control-sm text-right harga-modal format-rupiah" name="harga_modal[]" placeholder="0" required>
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
									<tr class="table-info">
										<td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
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
								<li>Harga modal akan otomatis update ke master barang (last price)</li>
								<li>Stok akan otomatis bertambah setelah disimpan</li>
							</ul>
						</div>

						<hr>

						<div class="row">
							<div class="col-md-12 text-right">
								<a href="<?= base_url('stok_masuk') ?>" class="btn btn-secondary">
									<i class="fe fe-x"></i> Batal
								</a>
								<button type="button" class="btn btn-primary" id="btnSimpan">
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

<!-- Modal Pilih COA -->
<div class="modal fade" id="modalCOA" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white">
					<i class="fe fe-book"></i> Pilih Chart of Account (COA)
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalCOABody">
				<div class="text-center">
					<div class="spinner-border text-primary" role="status">
						<span class="sr-only">Loading...</span>
					</div>
					<p>Memuat COA...</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
	$(document).ready(function() {
		let rowCount = 1;
		let selectedCOA = {
			persediaan: null,
			kas_utang: null
		};

		// Initialize Select2 untuk row pertama
		initSelect2($('.select-item'));

		// Add Row Button
		$('#btnAddRow').on('click', function() {
			addRow();
		});

		// Remove Row
		$(document).on('click', '.btn-remove-row', function() {
			if ($('#itemRows tr').length > 1) {
				$(this).closest('tr').remove();
				updateRowNumbers();
				calculateGrandTotal();
			} else {
				alert('Minimal harus ada 1 item!');
			}
		});

		// Enter key untuk add row
		$(document).on('keydown', '#tableItems input, #tableItems select', function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
				addRow();
			}
		});

		// Calculate subtotal saat qty atau harga modal berubah
		$(document).on('keyup', '.qty, .harga-modal', function() {
			calculateSubtotal($(this).closest('tr'));
			calculateGrandTotal();
		});

		// Format Rupiah
		$(document).on('keyup', '.format-rupiah', function() {
			let value = $(this).val().replace(/\./g, '');
			if (!isNaN(value) && value !== '') {
				$(this).val(formatRupiah(value));
			}
			calculateSubtotal($(this).closest('tr'));
			calculateGrandTotal();
		});

		// Button Simpan
		$('#btnSimpan').on('click', function() {
			// Validasi items
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

			// Validasi form
			if (!$('#formStokMasuk')[0].checkValidity()) {
				$('#formStokMasuk')[0].reportValidity();
				return;
			}

			// Get metode bayar
			const metodeBayar = $('input[name="metode_bayar"]:checked').val();

			// Load modal COA
			$('#modalCOABody').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Memuat COA...</p>
                </div>
            `);
			$('#modalCOA').modal('show');

			$.ajax({
				url: '<?= base_url("stok_masuk/modal_coa") ?>',
				type: 'GET',
				data: {
					metode: metodeBayar
				},
				success: function(response) {
					$('#modalCOABody').html(response);
				},
				error: function() {
					$('#modalCOABody').html(`
                        <div class="alert alert-danger">
                            <i class="fe fe-alert-circle"></i> Gagal memuat COA!
                        </div>
                    `);
				}
			});
		});

		// Confirm COA dan Submit
		$(document).on('click', '#btnConfirmCOA', function() {
			selectedCOA.persediaan = $('#coa_persediaan').val();
			selectedCOA.kas_utang = $('#coa_kas_utang').val();

			if (!selectedCOA.persediaan || !selectedCOA.kas_utang) {
				alert('Pilih semua COA yang diperlukan!');
				return;
			}

			$('#modalCOA').modal('hide');
			submitForm();
		});

		// Function Add Row
		function addRow() {
			rowCount++;
			const newRow = `
                <tr class="item-row">
                    <td class="text-center row-number">${rowCount}</td>
                    <td>
                        <select class="form-control form-control-sm select-item" 
                                name="id_item[]" required>
                            <option value="">-- Pilih Barang --</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right qty" 
                               name="qty[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right harga-modal format-rupiah" 
                               name="harga_modal[]" placeholder="0" required>
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

			// Initialize Select2 untuk row baru
			const newSelect = $('#itemRows tr:last .select-item');
			initSelect2(newSelect);

			// Focus ke select2 yang baru
			newSelect.select2('open');
		}

		// Initialize Select2
		function initSelect2(element) {
			element.select2({
				placeholder: '-- Pilih Barang --',
				allowClear: true,
				width: '100%',
				ajax: {
					url: '<?= base_url("items/get_items") ?>',
					dataType: 'json',
					delay: 250,
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

			// Saat item dipilih, autofill harga modal
			element.on('select2:select', function(e) {
				const data = e.params.data;
				const row = $(this).closest('tr');

				if (data.harga_modal) {
					// Pastikan data.harga_modal adalah number, bukan string dengan desimal
					let hargaModal = parseFloat(data.harga_modal);
					row.find('.harga-modal').val(formatRupiah(hargaModal));
				}

				// Focus ke qty
				row.find('.qty').focus();
			});
		}

		// Calculate Subtotal
		function calculateSubtotal(row) {
			const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;
			const hargaModal = parseFloat(row.find('.harga-modal').val().replace(/\./g, '')) || 0;
			const subtotal = qty * hargaModal;

			row.find('.subtotal').val(formatRupiah(Math.floor(subtotal)));
		}

		// Calculate Grand Total
		function calculateGrandTotal() {
			let total = 0;
			$('.subtotal').each(function() {
				const value = parseFloat($(this).val().replace(/\./g, '')) || 0;
				total += value;
			});
			$('#grandTotal').val(formatRupiah(Math.floor(total)));
		}

		// Update Row Numbers
		function updateRowNumbers() {
			$('#itemRows tr').each(function(index) {
				$(this).find('.row-number').text(index + 1);
			});
			rowCount = $('#itemRows tr').length;
		}

		// Format Rupiah - Buang desimal
		function formatRupiah(angka) {
			// Konversi ke number, buang desimal untuk konsistensi
			let number = Math.floor(parseFloat(angka));

			// Handle NaN atau undefined
			if (isNaN(number) || number < 0) number = 0;

			return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		}

		// Submit Form
		function submitForm() {
			$('#btnSimpan').prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

			// Append COA to form
			$('<input>').attr({
				type: 'hidden',
				name: 'coa_persediaan',
				value: selectedCOA.persediaan
			}).appendTo('#formStokMasuk');

			$('<input>').attr({
				type: 'hidden',
				name: 'coa_kas_utang',
				value: selectedCOA.kas_utang
			}).appendTo('#formStokMasuk');

			$.ajax({
				url: '<?= base_url("stok_masuk/save") ?>',
				type: 'POST',
				data: $('#formStokMasuk').serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						alert(response.message);
						window.location.href = response.redirect;
					} else {
						alert(response.message);
						$('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan');
					}
				},
				error: function() {
					alert('Terjadi kesalahan saat menyimpan data!');
					$('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan');
				}
			});
		}
	});
</script>