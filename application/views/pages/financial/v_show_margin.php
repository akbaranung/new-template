<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h1 class="page-title mb-3"><?= $title ?></h1>

			<!-- Header Info -->
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<h5 class="text-primary">📦 Detail Shipment</h5>
							<table class="table table-sm">
								<tr>
									<td width="150"><strong>SMU Number</strong></td>
									<td>: <?= $sales['no_smu'] ?></td>
								</tr>
								<tr>
									<td><strong>Tanggal Terbang</strong></td>
									<td>: <?= date('d/m/Y', strtotime($sales['tanggal_terbang'])) ?></td>
								</tr>
								<tr>
									<td><strong>Airline</strong></td>
									<td>: <?= $sales['airline_name'] ?></td>
								</tr>
								<tr>
									<td><strong>Flight Number</strong></td>
									<td>: <?= $sales['flight_number'] ?></td>
								</tr>
								<tr>
									<td><strong>Route</strong></td>
									<td>: <?= $sales['origin'] ?> → <?= $sales['destination'] ?></td>
								</tr>
								<tr>
									<td><strong>Agent</strong></td>
									<td>: <?= $sales['agent_name'] ?></td>
								</tr>
								<tr>
									<td><strong>Jenis Barang</strong></td>
									<td>: <?= $sales['jenis_barang'] ?></td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<h5 class="text-info">📊 Weight & Pricing</h5>
							<table class="table table-sm">
								<tr>
									<td width="150"><strong>Koli</strong></td>
									<td>: <?= $sales['koli'] ?> pcs</td>
								</tr>
								<tr>
									<td><strong>Gross Weight</strong></td>
									<td>: <?= number_format($sales['gross'], 2, ',', '.') ?> Kg</td>
								</tr>
								<tr>
									<td><strong>Chargeable Weight</strong></td>
									<td>: <?= number_format($sales['chargeable_weight'], 2, ',', '.') ?> Kg</td>
								</tr>
								<tr>
									<td><strong>Selling Price/Kg</strong></td>
									<td>: Rp <?= number_format($sales['selling_price'], 0, ',', '.') ?></td>
								</tr>
								<tr>
									<td><strong>Freight</strong></td>
									<td>: Rp <?= number_format($sales['freight'], 0, ',', '.') ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Revenue Breakdown -->
			<div class="card mt-3">
				<div class="card-header bg-primary">
					<h5 class="mb-0 text-white">💰 REVENUE BREAKDOWN</h5>
				</div>
				<div class="card-body">
					<table class="table table-bordered">
						<tr>
							<td width="250"><strong>Freight</strong></td>
							<td class="text-right">Rp <?= number_format($sales['freight'], 0, ',', '.') ?></td>
						</tr>
						<?php if ($sales['surcharge_nominal'] > 0): ?>
							<tr>
								<td><strong>Surcharge (<?= $sales['surcharge_percent'] ?>%)</strong></td>
								<td class="text-right">Rp <?= number_format($sales['surcharge_nominal'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['hht'] > 0): ?>
							<tr>
								<td><strong>HHT</strong></td>
								<td class="text-right">Rp <?= number_format($sales['hht'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['admin_fee'] > 0): ?>
							<tr>
								<td><strong>Admin Fee</strong></td>
								<td class="text-right">Rp <?= number_format($sales['admin_fee'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['ppn_smu'] > 0): ?>
							<tr>
								<td><strong>PPN SMU</strong></td>
								<td class="text-right">Rp <?= number_format($sales['ppn_smu'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['handling_charge'] > 0): ?>
							<tr>
								<td><strong>Handling Charge</strong></td>
								<td class="text-right">Rp <?= number_format($sales['handling_charge'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['asuransi'] > 0): ?>
							<tr>
								<td><strong>Asuransi</strong></td>
								<td class="text-right">Rp <?= number_format($sales['asuransi'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['extra_packing'] > 0): ?>
							<tr>
								<td><strong>Extra Packing</strong></td>
								<td class="text-right">Rp <?= number_format($sales['extra_packing'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['handling_dest'] > 0): ?>
							<tr>
								<td><strong>Handling Dest</strong></td>
								<td class="text-right">Rp <?= number_format($sales['handling_dest'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<?php if ($sales['other_charge'] > 0): ?>
							<tr>
								<td><strong>Other Charge</strong></td>
								<td class="text-right">Rp <?= number_format($sales['other_charge'], 0, ',', '.') ?></td>
							</tr>
						<?php endif; ?>
						<tr class="table-success">
							<td><strong>SUBTOTAL REVENUE</strong></td>
							<td class="text-right"><strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></td>
						</tr>
						<tr>
							<td><strong>VAT (<?= $sales['vat_percent'] ?>%)</strong></td>
							<td class="text-right">Rp <?= number_format($vat, 0, ',', '.') ?></td>
						</tr>
						<tr class="table-info">
							<td><strong>TOTAL TAGIHAN</strong></td>
							<td class="text-right"><strong>Rp <?= number_format($total_tagihan, 0, ',', '.') ?></strong></td>
						</tr>
					</table>
				</div>
			</div>

			<!-- Cost Breakdown -->
			<div class="card mt-3">
				<div class="card-header bg-pink">
					<h5 class="mb-0 text-white">📊 COST BREAKDOWN (HPP)</h5>
				</div>
				<div class="card-body">
					<?php if ($purchase): ?>
						<table class="table table-bordered">
							<?php if ($purchase['hhp_pusat'] > 0): ?>
								<tr>
									<td width="250"><strong>HPP Pusat</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hhp_pusat'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['ho_charge'] > 0): ?>
								<tr>
									<td><strong>HO Charge</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['ho_charge'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['hpp_jasa_gudang'] > 0): ?>
								<tr>
									<td><strong>HPP Jasa Gudang</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hpp_jasa_gudang'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['ra'] > 0): ?>
								<tr>
									<td><strong>RA (Regional Agent)</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['ra'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['handling_ra'] > 0): ?>
								<tr>
									<td><strong>Handling RA</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['handling_ra'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['hpp_packing'] > 0): ?>
								<tr>
									<td><strong>HPP Packing</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hpp_packing'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['hpp_handling'] > 0): ?>
								<tr>
									<td><strong>HPP Handling</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hpp_handling'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['hpp_handling_dest'] > 0): ?>
								<tr>
									<td><strong>HPP Handling Dest</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hpp_handling_dest'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['marketing_fee'] > 0): ?>
								<tr>
									<td><strong>Marketing Fee</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['marketing_fee'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['hpp_other_charge'] > 0): ?>
								<tr>
									<td><strong>HPP Other Charge</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['hpp_other_charge'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($purchase['asuransi'] > 0): ?>
								<tr>
									<td><strong>Asuransi HPP</strong></td>
									<td class="text-right">Rp <?= number_format($purchase['asuransi'], 0, ',', '.') ?></td>
								</tr>
							<?php endif; ?>
							<tr class="table-danger">
								<td><strong>TOTAL COST (HPP)</strong></td>
								<td class="text-right"><strong>Rp <?= number_format($total_hpp, 0, ',', '.') ?></strong></td>
							</tr>
						</table>
					<?php else: ?>
						<div class="alert alert-warning">
							<i class="fe fe-alert-triangle"></i> Data purchase untuk SMU ini tidak ditemukan!
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Profit Analysis -->
			<div class="card mt-3">
				<div class="card-header <?= $gross_profit >= 0 ? 'bg-primary' : 'bg-pink' ?>">
					<h5 class="mb-0 text-white">🎯 PROFIT ANALYSIS</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<div class="card bg-light">
								<div class="card-body text-center">
									<h6 class="text-muted">Subtotal Revenue</h6>
									<h4>Rp <?= number_format($subtotal, 0, ',', '.') ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card bg-light">
								<div class="card-body text-center">
									<h6 class="text-muted">Total Cost (HPP)</h6>
									<h4>Rp <?= number_format($total_hpp, 0, ',', '.') ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card <?= $gross_profit >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
								<div class="card-body text-center">
									<h6>Gross Profit</h6>
									<h4>Rp <?= number_format($gross_profit, 0, ',', '.') ?></h4>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card <?= $profit_margin >= 10 ? 'bg-success' : ($profit_margin >= 5 ? 'bg-warning' : 'bg-danger') ?> text-white">
								<div class="card-body text-center">
									<h6>Profit Margin</h6>
									<h4><?= number_format($profit_margin, 2, ',', '.') ?>%</h4>
								</div>
							</div>
						</div>
					</div>

					<div class="alert <?= $gross_profit >= 0 ? 'alert-success' : 'alert-danger' ?> mt-3">
						<h5 class="mb-2">
							<?php if ($gross_profit >= 0): ?>
								✅ <strong>PROFITABLE!</strong>
							<?php else: ?>
								⚠️ <strong>LOSS!</strong>
							<?php endif; ?>
						</h5>
						<p class="mb-0">
							<?php if ($profit_margin >= 10): ?>
								Margin sangat baik! (≥ 10%)
							<?php elseif ($profit_margin >= 5): ?>
								Margin cukup, tapi bisa ditingkatkan (5-10%)
							<?php elseif ($profit_margin > 0): ?>
								Margin terlalu tipis! Perlu review pricing (< 5%)
									<?php else: ?>
									RUGI! Perlu segera review pricing dan cost structure!
									<?php endif; ?>
									</p>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="card mt-3">
				<div class="card-body">
					<a href="javascript:window.close();" class="btn btn-secondary">
						<i class="fe fe-x"></i> Close
					</a>
					<button onclick="window.print();" class="btn btn-primary">
						<i class="fe fe-printer"></i> Print
					</button>
				</div>
			</div>

		</div>
	</div>
</div>

<style>
	@media print {

		.btn,
		.card-header {
			print-color-adjust: exact;
			-webkit-print-color-adjust: exact;
		}
	}
</style>