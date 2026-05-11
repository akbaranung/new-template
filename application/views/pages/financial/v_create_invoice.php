<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12">

			<div class="d-flex align-items-center justify-content-between mb-4">
				<h1 class="page-title mb-0">Buat Invoice</h1>
				<a href="<?= base_url('financial/invoice') ?>" class="btn btn-sm btn-warning text-white">
					<i class="fe fe-arrow-return-left"></i> Back
				</a>
			</div>

			<form method="POST" action="<?= base_url('financial/store_invoice/khusus') ?>">
				<div class="card shadow">
					<div class="card-body p-4">

						<!-- ── Identitas Dokumen ── -->
						<p class="text-uppercase fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="fe fe-file-earmark-text me-1"></i> Identitas Dokumen
						</p>
						<div class="row g-3">
							<div class="col-md-2">
								<label class="form-label">Tgl. Invoice</label>
								<input type="date" class="form-control" name="tgl_invoice" value="<?= date('Y-m-d') ?>" required>
							</div>
							<div class="col-md-2">
								<label class="form-label">
									No. PO
									<span class="badge bg-secondary ms-1 text-white" style="font-size: 0.65rem; font-weight: 400;">opsional</span>
								</label>
								<input type="text" class="form-control uppercase" name="no_po" placeholder="—">
							</div>
							<div class="col-md-2">
								<label class="form-label">
									Tgl. PO
									<span class="badge bg-secondary ms-1 text-white" style="font-size: 0.65rem; font-weight: 400;">opsional</span>
								</label>
								<input type="date" class="form-control" name="tgl_po">
							</div>
							<div class="col-md-4">
								<label class="form-label">Bill To</label>
								<select name="customer" id="customer" class="form-control select2" style="width:100%" required>
									<option value="">:: Pilih customer</option>
									<?php foreach ($customers as $c) : ?>
										<option value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<label class="form-label">PPN</label>
								<select name="ppn" id="ppn" class="form-control">
									<option value="0">0%</option>
									<option value="<?= $this->session->userdata('ppn') ?>"><?= $this->session->userdata('nama_ppn') ?></option>
								</select>
							</div>
						</div>
						<div class="row g-3 mt-3">
							<div class="col-12">
								<label class="form-label">Notes</label>
								<textarea name="keterangan" id="keterangan" class="form-control uppercase" placeholder="Keterangan invoice..." required></textarea>
							</div>
						</div>

						<hr class="my-4">

						<!-- ── Item Invoice ── -->
						<p class="text-uppercase fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="fe fe-list-ul me-1"></i> Item Invoice
						</p>
						<table class="table table-sm table-bordered mb-0">
							<thead class="table-light">
								<tr>
									<th style="color:#6c757d; width:44%;">Keterangan</th>
									<th style="color:#6c757d; width:14%;">Jumlah</th>
									<th style="color:#6c757d; width:18%;">Nominal</th>
									<th style="color:#6c757d; width:18%;">Amount</th>
									<th style="color:#6c757d; width:6%; text-align:center;">#</th>
								</tr>
							</thead>
							<tbody>
								<tr class="baris">
									<td><input type="text" class="form-control form-control-sm uppercase" name="item[]"></td>
									<td><input type="text" class="form-control form-control-sm" name="jumlah[]" value="0"></td>
									<td><input type="text" class="form-control form-control-sm total" name="total[]" value="0"></td>
									<td><input type="text" class="form-control form-control-sm" name="total_amount[]" value="0" readonly></td>
									<td class="text-center">
										<button type="button" class="btn btn-danger btn-sm hapusRow d-none"><i class="fe fe-trash"></i></button>
										<br class="brRow d-none">
										<button type="button" class="btn btn-outline-secondary btn-sm" id="addRow"><i class="fe fe-plus"></i></button>
									</td>
								</tr>
							</tbody>
						</table>

						<hr class="my-4">

						<!-- ── Kalkulasi ── -->
						<p class="text-uppercase fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="fe fe-calculator me-1"></i> Kalkulasi
						</p>
						<div class="row g-3">
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">Subtotal</label>
								<input type="text" class="form-control form-control-sm bg-light" name="nominal" id="nominal" value="0" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">Besaran PPN</label>
								<input type="text" class="form-control form-control-sm bg-light" name="besaran_ppn" id="besaran_ppn" value="0" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">PPh 23</label>
								<input type="text" class="form-control form-control-sm bg-light" name="besaran_pph" id="besaran_pph" value="0" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">Total (non PPh)</label>
								<input type="text" class="form-control form-control-sm bg-light" name="total_nonpph" id="total_nonpph" value="0" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">Total (w/ PPh)</label>
								<input type="text" class="form-control form-control-sm bg-light fw-semibold" name="total_denganpph" id="total_denganpph" value="0" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label" style="font-size:0.8rem;">Pendapatan</label>
								<input type="text" class="form-control form-control-sm bg-light fw-semibold" name="nominal_pendapatan" id="nominal_pendapatan" value="0" readonly>
							</div>
						</div>

						<hr class="my-4">

						<!-- ── Konfigurasi & Aksi ── -->
						<p class="text-uppercase fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="fe fe-gear me-1"></i> Konfigurasi
						</p>
						<div class="row g-3 align-items-end">
							<div class="col-md-2">
								<label class="form-label">Nominal Bayar</label>
								<input type="text" class="form-control" name="nominal_bayar" id="nominal_bayar" value="0" readonly>
							</div>
							<div class="col-md-3">
								<label class="form-label">CoA Debit</label>
								<select name="coa_debit" id="coa_debit" class="form-control select2" style="width:100%" required>
									<option value="">:: Pilih CoA Debit</option>
									<?php foreach ($pendapatan as $pd) : ?>
										<option value="<?= $pd->no_sbb ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-3">
								<label class="form-label">CoA Kredit</label>
								<select name="coa_kredit" id="coa_kredit" class="form-control select2" style="width:100%" required>
									<option value="">:: Pilih CoA Kredit</option>
									<?php foreach ($persediaan as $ps) : ?>
										<option value="<?= $ps->no_sbb ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<label class="form-label">Metode bayar</label>
								<select name="metode_bayar" id="metode_bayar" class="form-control" style="width:100%" required>
									<option value="">:: Pilih Metode bayar</option>
									<option value="cod">Cash on Delivery (CoD)</option>
									<option value="tempo">Tempo</option>
								</select>
							</div>
							<div class="col-md-2">
								<label class="form-label">Tgl. Jatuh Tempo</label>
								<input type="date" class="form-control" name="tgl_jatuh_tempo" value="<?= date('Y-m-d') ?>" required>
							</div>
							<div class="col-md-2 mt-3">
								<label class="form-label d-block mb-2">Opsi</label>
								<div class="d-flex gap-3">
									<div class="form-check mb-0" style="margin-right: 5px;">
										<input type="checkbox" class="form-check-input icheckbox_flat-green" name="opsi_termin" id="opsi_termin" value="1" checked>
										<label class="form-check-label" for="opsi_termin">Termin</label>
									</div>
									<div class="form-check mb-0">
										<input type="checkbox" class="form-check-input icheckbox_flat-green" name="opsi_pph" id="opsi_pph" value="1">
										<label class="form-check-label" for="opsi_pph">PPh 23</label>
									</div>
								</div>
							</div>
							<div class="col-md-2 text-end">
								<button type="submit" class="btn btn-primary w-100">
									<i class="fe fe-save me-1"></i> Save Invoice
								</button>
							</div>
						</div>

					</div><!-- .card-body -->
				</div><!-- .card -->
			</form>

		</div>
	</div>
</div>
