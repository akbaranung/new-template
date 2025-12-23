<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h1 class="page-title mb-3"><?= $title ?></h1>

			<form id="form_invoice" method="POST" action="save_invoice_sales">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Detail Invoice</h5>

						<!-- Info Filter -->
						<div class="alert alert-info mb-4">
							<div class="row">
								<div class="col-md-4">
									<strong>Agent:</strong> <?= $this->input->post('agent_name') ?>
									<input type="hidden" name="agent_name" value="<?= $this->input->post('agent_name') ?>">
								</div>
								<div class="col-md-4">
									<strong>Period:</strong> <?= date('d/m/Y', strtotime($this->input->post('tanggal_dari'))) ?>
									- <?= date('d/m/Y', strtotime($this->input->post('tanggal_sampai'))) ?>
									<input type="hidden" name="tanggal_dari" value="<?= $this->input->post('tanggal_dari') ?>">
									<input type="hidden" name="tanggal_sampai"
										value="<?= $this->input->post('tanggal_sampai') ?>">
								</div>
								<div class="col-md-4">
									<strong>Total Items:</strong> <?= count($sales) ?> sales
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3 mb-3">
								<label for="tanggal_invoice" class="form-label">Tanggal Invoice <span
										class="text-danger">*</span></label>
								<input type="date" name="tanggal_invoice" id="tanggal_invoice" class="form-control"
									value="<?= date('Y-m-d') ?>" required>
							</div>
							<div class="col-md-3 mb-3">
								<label for="bill_to" class="form-label">Bill To <span class="text-danger">*</span></label>
								<select name="bill_to" id="bill_to" class="form-control" required>
									<option value="">:: Pilih Customer</option>
									<?php foreach ($customers as $customer): ?>
										<option value="<?= $customer->id ?>"><?= $customer->nama_customer ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-md-3 mb-3">
								<label class="form-label">Opsi PPh</label>
								<div class="form-check">
									<input type="checkbox" class="form-check-input" id="pph23_check" name="pph23_check"
										value="1">
									<label class="form-check-label" for="pph23_check">
										Gunakan PPh 23 (2%)
									</label>
								</div>
							</div>
							<div class="col-md-3 mb-3">
								<label class="form-label">Opsi Termin</label>
								<div class="form-check">
									<input type="checkbox" class="form-check-input" name="opsi_termin" value="1" checked>
									<label class="form-check-label" for="pph23_check">
										Termin
									</label>
								</div>
							</div>

							<div class="col-md-3 mb-3">
								<label for="bill_to" class="form-label">Coa Debit <span class="text-danger">*</span></label>
								<select name="coa_debit" id="coa_debit" class="form-control select2" style="width: 100%" required>
									<option value="">:: Pilih CoA Debit</option>
									<?php
									foreach ($pendapatan as $pd) :
									?>
										<option value="<?= $pd->no_sbb ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
									<?php
									endforeach; ?>
								</select>
							</div>

							<div class="col-md-3 mb-3">
								<label for="bill_to" class="form-label">Coa Kredit <span class="text-danger">*</span></label>
								<select name="coa_kredit" id="coa_kredit" class="form-control select2" style="width: 100%" required>
									<option value="">:: Pilih CoA Kredit</option>
									<?php
									foreach ($persediaan as $ps) :
									?>
										<option value="<?= $ps->no_sbb ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
									<?php
									endforeach; ?>
								</select>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">Keterangan</label>
								<input type="text" name="keterangan" class="form-control" placeholder="Optional notes...">
							</div>
						</div>

						<!-- Summary Cards -->
						<div class="row mb-4">
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-primary">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_subtotal">Rp 0</h4>
										<small class="text-white d-block">Subtotal</small>
										<input type="hidden" name="subtotal" id="subtotal" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-pink">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_vat">Rp 0</h4>
										<small class="text-white d-block">VAT</small>
										<input type="hidden" name="vat" id="vat" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-dark">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_pph23">Rp 0</h4>
										<small class="text-white d-block">PPh 23 (2%)</small>
										<input type="hidden" name="pph23" id="pph23" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-dark text-white">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_total_nonpph">Rp 0</h4>
										<small class="d-block text-white">Total (Non PPh)</small>
										<input type="hidden" name="total_nonpph" id="total_nonpph" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-primary text-white">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_total_denganpph">Rp 0</h4>
										<small class="d-block text-white">Total (PPh 2%)</small>
										<input type="hidden" name="total_denganpph" id="total_denganpph" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-pink">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_total_biaya">Rp 0</h4>
										<small class="d-block text-white">Total Biaya</small>
										<input type="hidden" name="total_biaya" id="total_biaya" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-pink text-white">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_nominal_bayar">Rp 0</h4>
										<small class="d-block text-white">Nominal Bayar</small>
										<input type="hidden" name="nominal_bayar" id="nominal_bayar" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-dark text-white">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_gross_profit">Rp 0</h4>
										<small class="d-block text-white">Gross Profit</small>
										<input type="hidden" name="gross_profit" id="gross_profit" value="0">
									</div>
								</div>
							</div>
							<div class="col-md-4 col-6 mb-3">
								<div class="card bg-primary text-white">
									<div class="card-body p-3">
										<h4 class="mb-0 text-white" id="display_profit_margin">0%</h4>
										<small class="d-block text-white">Profit Margin</small>
										<input type="hidden" name="profit_margin" id="profit_margin" value="0">
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<!-- Sales Items Table -->
				<div class="card mt-3">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="card-title mb-0">Sales Items</h5>
							<div>
								<button type="button" class="btn btn-sm btn-outline-primary" id="btn_expand_all">
									<i class="fe fe-arrow-expand-vertical"></i> Expand All
								</button>
								<button type="button" class="btn btn-sm btn-outline-secondary" id="btn_collapse_all">
									<i class="fe fe-arrow-collapse-vertical"></i> Collapse All
								</button>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table table-bordered table-hover table-striped align-middle table-sm"
								id="table_sales">
								<thead class="thead-dark">
									<tr>
										<th style="width: 40px;"></th>
										<th style="width: 90px;">Tanggal</th>
										<th style="width: 130px;">SMU</th>
										<th style="width: 80px;">Flight</th>
										<th style="width: 100px;">Route</th>
										<th style="width: 70px;">Koli</th>
										<th style="width: 80px;">Actual</th>
										<th style="width: 80px;">Chwt</th>
										<th style="width: 100px;">Selling</th>
										<th style="width: 100px;">Freight</th>
										<th style="width: 100px;">VAT</th>
										<th style="width: 120px;" class="text-right">Subtotal</th>
										<th style="width: 100px;" class="text-right">Total HPP</th>
										<th style="width: 100px;" class="text-right">Profit</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($sales as $index => $row): ?>
										<!-- Main Row -->
										<tr class="sales-row" data-index="<?= $index ?>" data-id="<?= $row['Id'] ?>">
											<td class="text-center">
												<button type="button" class="btn btn-sm btn-link toggle-detail p-0"
													data-target="detail_<?= $row['Id'] ?>">
													<i class="fe fe-chevron-down"></i>
												</button>
											</td>
											<td><small><?= date('d/m/Y', strtotime($row['tanggal_terbang'])) ?></small></td>
											<td><small><?= $row['no_smu'] ?></small></td>
											<td><small><?= $row['flight_number'] ?></small></td>
											<td><small><?= $row['origin'] ?> → <?= $row['destination'] ?></small></td>
											<td>
												<input type="text" name="koli[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-center calc-field"
													value="<?= $row['koli'] ?>">
											</td>
											<td>
												<input type="text" name="actual[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-right calc-field"
													value="<?= number_format($row['gross'], 2, ',', '.') ?>">
											</td>
											<td>
												<input type="text" name="chwt[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-right calc-field"
													value="<?= number_format($row['chargeable_weight'], 2, ',', '.') ?>">
											</td>
											<td>
												<input type="text" name="selling_price[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-right calc-field"
													value="<?= number_format($row['selling_price'], 2, ',', '.') ?>">
											</td>
											<td>
												<input type="text" name="freight[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-right freight-field"
													value="<?= number_format($row['freight'], 2, ',', '.') ?>" readonly>
											</td>
											<td>
												<input type="text" name="vat_nominal[<?= $row['Id'] ?>]"
													class="form-control form-control-sm text-right vat_nominal-field"
													value="<?= number_format($row['vat_nominal'], 2, ',', '.') ?>" readonly>
											</td>
											<td class="text-right">
												<strong class="row-subtotal">Rp
													<?= number_format($row['sub_total_tagihan'], 2, ',', '.') ?></strong>
												<input type="hidden" name="subtotal_row[<?= $row['Id'] ?>]"
													value="<?= $row['sub_total_tagihan'] ?>">
											</td>
											<td class="text-right">
												<strong class="row-total-hpp">Rp 0</strong>
												<input type="hidden" name="total_hpp_row[<?= $row['Id'] ?>]" value="0">
											</td>
											<td class="text-right">
												<strong class="row-profit">Rp 0</strong>
												<input type="hidden" name="profit_row[<?= $row['Id'] ?>]" value="0">
											</td>
										</tr>

										<!-- Detail Row (Hidden by default) -->
										<tr class="detail-row" id="detail_<?= $row['Id'] ?>" style="display: none;">
											<td colspan="13" class="bg-light">
												<div class="p-3">
													<h4 class="mb-3">Detail Charges & Fees</h4>
													<div class="row">
														<div class="col-md-2 mb-2">
															<label class="form-label small">Surcharge %</label>
															<div class="input-group input-group-sm">
																<input type="text" name="surcharge_percent[<?= $row['Id'] ?>]"
																	class="form-control calc-field"
																	value="<?= ($row['surcharge_percent']) ?>">
																<span class="input-group-text">%</span>
															</div>
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Surcharge Nominal</label>
															<input type="text" name="surcharge_nominal[<?= $row['Id'] ?>]"
																class="form-control form-control-sm surcharge-nominal-field"
																value="<?= number_format($row['surcharge_nominal'], 2, ',', '.') ?>"
																readonly>
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HHT</label>
															<input type="text" name="hht[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hht'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Admin Fee</label>
															<input type="text" name="admin_fee[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['admin_fee'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">PPN SMU</label>
															<input type="text" name="ppn_smu[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['ppn_smu'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Total Freight</label>
															<input type="text" name="total_freight[<?= $row['Id'] ?>]"
																class="form-control form-control-sm total-freight-field"
																value="<?= number_format($row['total_freight'], 2, ',', '.') ?>" readonly>
														</div>
													</div>
													<div class="row">
														<div class="col-md-2 mb-2">
															<label class="form-label small">Handling Charge</label>
															<input type="text" name="handling_charge[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['handling_charge'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Asuransi</label>
															<input type="text" name="asuransi[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['asuransi'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Extra Packing</label>
															<input type="text" name="extra_packing[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['extra_packing'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Handling Dest</label>
															<input type="text" name="handling_dest[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['handling_dest'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Other Charge</label>
															<input type="text" name="other_charge[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['other_charge'], 2, ',', '.') ?>">
														</div>
													</div>
													<h4 class="mb-3 mt-4 text-danger">Cost / HPP Breakdown</h4>
													<div class="row">
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Pusat</label>
															<input type="text" name="hpp_pusat[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_pusat'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HO Charge</label>
															<input type="text" name="ho_charge[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['ho_charge'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Program</label>
															<input type="text" name="hpp_program[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_program'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Jasa Gudang</label>
															<input type="text" name="hpp_jasa_gudang[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_jasa_gudang'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">RA</label>
															<input type="text" name="ra[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['ra'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Handling RA</label>
															<input type="text" name="handling_ra[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['handling_ra'], 2, ',', '.') ?>">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Packing</label>
															<input type="text" name="hpp_packing[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_packing'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Handling</label>
															<input type="text" name="hpp_handling[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_handling'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Handling Dest</label>
															<input type="text" name="hpp_handling_dest[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_handling_dest'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Marketing Fee</label>
															<input type="text" name="marketing_fee[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['marketing_fee'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">HPP Other</label>
															<input type="text" name="hpp_other[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['hpp_other'], 2, ',', '.') ?>">
														</div>
														<div class="col-md-2 mb-2">
															<label class="form-label small">Asuransi HPP</label>
															<input type="text" name="asuransi_hpp[<?= $row['Id'] ?>]"
																class="form-control form-control-sm calc-field"
																value="<?= number_format($row['asuransi_hpp'], 2, ',', '.') ?>">
														</div>
													</div>
												</div>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
								<tfoot class="table-light">
									<tr>
										<td colspan="13" class="text-right"><strong>Grand Total:</strong></td>
										<td class="text-right"><strong id="grand_total">Rp 0</strong></td>
									</tr>
								</tfoot>
							</table>
						</div>

						<div class="alert alert-info mt-3">
							<i class="fe fe-information-outline"></i>
							<strong>Note:</strong> Klik icon <i class="fe fe-chevron-down"></i> untuk melihat detail charges &
							fees setiap item.
							Field yang dapat diedit ditandai dengan border biru.
						</div>

					</div>
				</div>

				<!-- Action Buttons -->
				<div class="card mt-3">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<a href="<?= base_url('financial/sales') ?>" class="btn btn-secondary">
								<i class="fe fe-arrow-left"></i> Back
							</a>
							<div>
								<button type="submit" class="btn btn-primary" id="btn_save">
									<i class="fe fe-content-save"></i> Save Invoice
								</button>
							</div>
						</div>
					</div>
				</div>

			</form>

		</div>
	</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function() {

		// ========== HELPER FUNCTIONS ========== //

		/**
		 * Format angka ke format Indonesia: 10.800,00
		 */
		function formatNumber(num, decimals = 2) {
			if (num === '' || num === null || num === undefined) return '0' + (decimals > 0 ? ',' + '0'.repeat(decimals) : '');

			num = parseFloat(num);
			if (isNaN(num)) return '0' + (decimals > 0 ? ',' + '0'.repeat(decimals) : '');

			let parts = num.toFixed(decimals).split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

			return parts.join(',');
		}

		/**
		 * Parse format Indonesia ke angka: 10.800,00 → 10800.00
		 */
		function parseNumber(str) {
			if (str === '' || str === null || str === undefined) return 0;
			str = str.toString().replace(/\./g, '').replace(/,/g, '.');
			let num = parseFloat(str);
			return isNaN(num) ? 0 : num;
		}

		// ========== AUTO FORMAT INPUT ========== //

		/**
		 * Format input saat blur
		 */
		$(document).on('blur', '.calc-field', function() {
			let $input = $(this);
			let rawValue = parseNumber($input.val());

			// Tentukan jumlah desimal
			let decimals = 2;
			let fieldName = $input.attr('name');

			// Field dengan 2 desimal: actual, chwt, selling_price
			if (fieldName.includes('actual') || fieldName.includes('chwt') || fieldName.includes('selling_price')) {
				decimals = 2;
			}
			// Field tanpa desimal (currency): semua yang lain
			else {
				decimals = 0;
			}

			// Format dan update value
			$input.val(formatNumber(rawValue, decimals));

			// Trigger kalkulasi
			calculateRow($input);
		});

		/**
		 * Allow only numbers, comma, and dot
		 */
		$(document).on('keypress', '.calc-field', function(e) {
			let charCode = e.which ? e.which : e.keyCode;

			// Allow: backspace, delete, tab, escape, enter, dot, comma
			if (charCode === 8 || charCode === 9 || charCode === 27 || charCode === 13 ||
				charCode === 46 || charCode === 44) {
				return true;
			}

			// Allow: numbers
			if (charCode >= 48 && charCode <= 57) {
				return true;
			}

			return false;
		});

		/**
		 * Select all saat focus
		 */
		$(document).on('focus', '.calc-field', function() {
			$(this).select();
		});

		// ========== CALCULATION FUNCTIONS ========== //

		/**
		 * Kalkulasi per row
		 */
		function calculateRow($input) {
			let $row = $input.closest('tr.sales-row');
			let id = $row.data('id');
			let $detailRow = $row.next('tr.detail-row');

			// ===== STEP 1: Ambil nilai input utama ===== //
			let koli = parseNumber($row.find(`input[name="koli[${id}]"]`).val());
			let actual = parseNumber($row.find(`input[name="actual[${id}]"]`).val());
			let chwt = parseNumber($row.find(`input[name="chwt[${id}]"]`).val());
			let sellingPrice = parseNumber($row.find(`input[name="selling_price[${id}]"]`).val());

			// ===== STEP 2: Hitung FREIGHT = chwt * selling_price ===== //
			let freight = chwt * sellingPrice;
			$row.find(`input[name="freight[${id}]"]`).val(formatNumber(freight, 2));

			// ===== STEP 3: Hitung SURCHARGE ===== //
			let surchargePercent = parseNumber($detailRow.find(`input[name="surcharge_percent[${id}]"]`).val());
			let surchargeNominal = (freight * surchargePercent) / 100;
			$detailRow.find(`input[name="surcharge_nominal[${id}]"]`).val(formatNumber(surchargeNominal, 2));

			// ===== STEP 4: Ambil field lainnya ===== //
			let hht = parseNumber($detailRow.find(`input[name="hht[${id}]"]`).val());
			let adminFee = parseNumber($detailRow.find(`input[name="admin_fee[${id}]"]`).val());
			let ppnSmu = parseNumber($detailRow.find(`input[name="ppn_smu[${id}]"]`).val());

			// ===== STEP 5: Hitung TOTAL FREIGHT ===== //
			let totalFreight = freight + surchargeNominal + hht + adminFee + ppnSmu;
			$detailRow.find(`input[name="total_freight[${id}]"]`).val(formatNumber(totalFreight, 2));

			// ===== STEP 6: Ambil charges lainnya ===== //
			let handlingCharge = parseNumber($detailRow.find(`input[name="handling_charge[${id}]"]`).val());
			let asuransi = parseNumber($detailRow.find(`input[name="asuransi[${id}]"]`).val());
			let extraPacking = parseNumber($detailRow.find(`input[name="extra_packing[${id}]"]`).val());
			let handlingDest = parseNumber($detailRow.find(`input[name="handling_dest[${id}]"]`).val());
			let otherCharge = parseNumber($detailRow.find(`input[name="other_charge[${id}]"]`).val());

			// ===== STEP 7: Hitung SUB TOTAL TAGIHAN ===== //
			let subTotal = totalFreight + handlingCharge + asuransi + extraPacking + handlingDest + otherCharge;

			// Update subtotal display dan hidden input
			$row.find('.row-subtotal').text('Rp ' + formatNumber(subTotal, 2));
			$row.find(`input[name="subtotal_row[${id}]"]`).val(subTotal);

			// ===== STEP 8: Hitung TOTAL HPP ===== //
			let hppPusat = parseNumber($detailRow.find(`input[name="hpp_pusat[${id}]"]`).val());
			let hoCharge = parseNumber($detailRow.find(`input[name="ho_charge[${id}]"]`).val());
			let hppJasaGudang = parseNumber($detailRow.find(`input[name="hpp_jasa_gudang[${id}]"]`).val());
			let ra = parseNumber($detailRow.find(`input[name="ra[${id}]"]`).val());
			let handlingRa = parseNumber($detailRow.find(`input[name="handling_ra[${id}]"]`).val());
			let hppPacking = parseNumber($detailRow.find(`input[name="hpp_packing[${id}]"]`).val());
			let hppHandling = parseNumber($detailRow.find(`input[name="hpp_handling[${id}]"]`).val());
			let hppHandlingDest = parseNumber($detailRow.find(`input[name="hpp_handling_dest[${id}]"]`).val());
			let marketingFee = parseNumber($detailRow.find(`input[name="marketing_fee[${id}]"]`).val());
			let hppOther = parseNumber($detailRow.find(`input[name="hpp_other[${id}]"]`).val());
			let asuransiHpp = parseNumber($detailRow.find(`input[name="asuransi_hpp[${id}]"]`).val());

			// HAPUS hpp_program dari calculation karena duplicate!
			let totalHpp = hppPusat + hoCharge + hppJasaGudang + ra + handlingRa +
				hppPacking + hppHandling + hppHandlingDest + marketingFee + hppOther + asuransiHpp;

			// Update total HPP display
			$row.find('.row-total-hpp').text('Rp ' + formatNumber(totalHpp, 2));
			$row.find(`input[name="total_hpp_row[${id}]"]`).val(totalHpp);

			// ===== STEP 9: Hitung PROFIT per Row ===== //
			let profitRow = subTotal - totalHpp;
			$row.find('.row-profit').text('Rp ' + formatNumber(profitRow, 2));
			$row.find(`input[name="profit_row[${id}]"]`).val(profitRow);

			// ===== STEP 10: Update Grand Total & Summary ===== //
			calculateGrandTotal();
		}

		/**
		 * Kalkulasi Grand Total dan Summary Cards
		 */
		function calculateGrandTotal() {
			let grandTotal = 0;
			let totalVat = 0;

			// Sum semua subtotal_row
			$('input[name^="subtotal_row"]').each(function() {
				grandTotal += parseFloat($(this).val() || 0);
			});

			// Sum semua vat_nominal (dari database, readonly)
			$('input[name^="vat_nominal"]').each(function() {
				let vatValue = parseNumber($(this).val());
				totalVat += vatValue;
			});

			// Update Grand Total di tabel
			$('#grand_total').text('Rp ' + formatNumber(grandTotal, 2));

			// ===== SUMMARY CARDS CALCULATION ===== //

			// 1. Subtotal (sama dengan grand total)
			let subtotal = grandTotal;
			$('#display_subtotal').text('Rp ' + formatNumber(subtotal, 2));
			$('#subtotal').val(subtotal.toFixed(2));

			// 2. VAT (sum dari vat_nominal database)
			$('#display_vat').text('Rp ' + formatNumber(totalVat, 2));
			$('#vat').val(totalVat.toFixed(2));

			// 3. Total (Non PPh) = Subtotal + VAT
			let totalNonPph = subtotal + totalVat;
			$('#display_total_nonpph').text('Rp ' + formatNumber(totalNonPph, 2));
			$('#total_nonpph').val(totalNonPph.toFixed(2));

			// 4. PPh 23 (2% dari subtotal) - hanya jika checkbox dicentang
			let pph23 = 0;
			if ($('#pph23_check').is(':checked')) {
				pph23 = subtotal * 0.02;
			}
			$('#display_pph23').text('Rp ' + formatNumber(pph23, 2));
			$('#pph23').val(pph23.toFixed(2));

			// 5. Total (Dengan PPh) = Subtotal + VAT + PPh23
			let totalDenganPph = subtotal + totalVat + pph23;
			$('#display_total_denganpph').text('Rp ' + formatNumber(totalDenganPph, 2));
			$('#total_denganpph').val(totalDenganPph.toFixed(2));

			// 6. Hitung Total HPP All DULU sebelum dipake
			let totalHppAll = 0;
			$('input[name^="total_hpp_row"]').each(function() {
				totalHppAll += parseFloat($(this).val() || 0);
			});

			// 7. Total Biaya = Total HPP All
			$('#display_total_biaya').text('Rp ' + formatNumber(totalHppAll, 2));
			$('#total_biaya').val(totalHppAll.toFixed(2));

			// 8. Nominal Bayar = Total Dengan PPh
			let nominalBayar = totalDenganPph;
			$('#display_nominal_bayar').text('Rp ' + formatNumber(nominalBayar, 2));
			$('#nominal_bayar').val(nominalBayar.toFixed(2));

			// 9. Gross Profit = Subtotal - Total HPP
			let grossProfit = subtotal - totalHppAll;
			$('#display_gross_profit').text('Rp ' + formatNumber(grossProfit, 2));
			$('#gross_profit').val(grossProfit.toFixed(2));

			// 10. Profit Margin = (Gross Profit / Subtotal) * 100
			let profitMargin = subtotal > 0 ? (grossProfit / subtotal * 100) : 0;
			$('#display_profit_margin').text(profitMargin.toFixed(2) + '%');
			$('#profit_margin').val(profitMargin.toFixed(2));
		}

		// ========== TOGGLE DETAIL ROW ========== //

		$(document).on('click', '.toggle-detail', function() {
			let target = $(this).data('target');
			let $icon = $(this).find('i');

			$('#' + target).toggle();

			if ($icon.hasClass('fe-chevron-down')) {
				$icon.removeClass('fe-chevron-down').addClass('fe-chevron-up');
			} else {
				$icon.removeClass('fe-chevron-up').addClass('fe-chevron-down');
			}
		});

		// Expand all
		$('#btn_expand_all').on('click', function() {
			$('.detail-row').show();
			$('.toggle-detail i').removeClass('fe-chevron-down').addClass('fe-chevron-up');
		});

		// Collapse all
		$('#btn_collapse_all').on('click', function() {
			$('.detail-row').hide();
			$('.toggle-detail i').removeClass('fe-chevron-up').addClass('fe-chevron-down');
		});

		// ========== TRIGGER CALCULATION ========== //

		// Trigger kalkulasi saat input berubah
		$(document).on('input change blur', '.calc-field', function() {
			calculateRow($(this));
		});

		// Trigger kalkulasi saat checkbox PPh 23 berubah
		$('#pph23_check').on('change', function() {
			calculateGrandTotal();
		});

		// ========== INITIALIZE ON PAGE LOAD ========== //

		// Kalkulasi semua row saat pertama load
		$('.sales-row').each(function() {
			let $firstInput = $(this).find('.calc-field').first();
			if ($firstInput.length) {
				calculateRow($firstInput);
			}
		});

		// ========== FORM SUBMIT ========== //

		$('#form_invoice').on('submit', function(e) {
			e.preventDefault();

			// Validasi form
			let tanggalInvoice = $('#tanggal_invoice').val();
			let billTo = $('#bill_to').val();

			if (!tanggalInvoice) {
				Swal.fire('Peringatan', 'Tanggal Invoice wajib diisi!', 'warning');
				return false;
			}

			if (!billTo) {
				Swal.fire('Peringatan', 'Bill To wajib dipilih!', 'warning');
				return false;
			}

			// Convert semua format Indonesia ke angka sebelum submit
			$('.calc-field, .freight-field, .surcharge-nominal-field, .total-freight-field, input[name^="hpp_"], input[name^="ra"], input[name^="marketing_fee"]').each(function() {
				let rawValue = parseNumber($(this).val());
				$(this).val(rawValue);
			});

			// Konfirmasi sebelum submit
			Swal.fire({
				title: 'Konfirmasi',
				text: 'Apakah Anda yakin ingin menyimpan invoice ini?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'Ya, Simpan',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					// Submit form secara normal (tanpa AJAX)
					this.submit();
				}
			});

			return false;
		});

	});
</script>

<style>
	.table-responsive {
		max-height: 600px;
		overflow-y: auto;
	}

	.sales-row:hover {
		background-color: #f8f9fa;
	}

	.calc-field {
		border-color: #007bff;
	}

	.calc-field:focus {
		border-color: #0056b3;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
	}

	input[readonly] {
		background-color: #e9ecef;
		cursor: not-allowed;
	}

	.card {
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
	}

	.table-sm td,
	.table-sm th {
		padding: 0.3rem;
		font-size: 0.875rem;
		vertical-align: middle;
	}

	thead th {
		position: sticky;
		top: 0;
		background: #343a40;
		color: white;
		z-index: 10;
	}

	.detail-row td {
		border-top: none !important;
	}

	.toggle-detail {
		color: #007bff;
		text-decoration: none;
	}

	.toggle-detail:hover {
		color: #0056b3;
	}

	.form-label.small {
		margin-bottom: 0.25rem;
		font-size: 0.8rem;
		font-weight: 600;
		color: #6c757d;
	}

	.detail-row .form-control-sm {
		font-size: 0.8rem;
		padding: 0.25rem 0.5rem;
	}

	/* Right align number inputs */
	.text-right {
		text-align: right;
	}

	/* Highlight summary cards */
	.card.bg-light .card-body {
		min-height: 80px;
	}

	.card.bg-info,
	.card.bg-success,
	.card.bg-warning,
	.card.bg-primary {
		transition: transform 0.2s;
	}

	.card.bg-info:hover,
	.card.bg-success:hover,
	.card.bg-warning:hover,
	.card.bg-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	}
</style>
<style>
	.table-responsive {
		max-height: 600px;
		overflow-y: auto;
	}

	.sales-row:hover {
		background-color: #f8f9fa;
	}

	.calc-field {
		border-color: #007bff;
	}

	.calc-field:focus {
		border-color: #0056b3;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
	}

	input[readonly] {
		background-color: #e9ecef;
		cursor: not-allowed;
	}

	.card {
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
	}

	.table-sm td,
	.table-sm th {
		padding: 0.3rem;
		font-size: 0.875rem;
		vertical-align: middle;
	}

	thead th {
		position: sticky;
		top: 0;
		background: #343a40;
		color: white;
		z-index: 10;
	}

	.detail-row td {
		border-top: none !important;
	}

	.toggle-detail {
		color: #007bff;
		text-decoration: none;
	}

	.toggle-detail:hover {
		color: #0056b3;
	}

	.form-label.small {
		margin-bottom: 0.25rem;
		font-size: 0.8rem;
		font-weight: 600;
		color: #6c757d;
	}

	.detail-row .form-control-sm {
		font-size: 0.8rem;
		padding: 0.25rem 0.5rem;
	}

	/* Right align number inputs */
	input[type="number"].text-right,
	input[type="number"].calc-field {
		text-align: right;
	}
</style>