<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12">

			<div class="d-flex align-items-center justify-content-between mb-4">
				<h1 class="page-title mb-0">Edit Invoice</h1>
				<a href="<?= base_url('financial/invoice') ?>" class="btn btn-sm btn-warning">
					<i class="bi bi-arrow-return-left"></i> Back
				</a>
			</div>

			<form method="POST" action="<?= base_url('financial/update_invoice/' . $inv['Id']) ?>">
				<div class="card shadow">
					<div class="card-body p-4">

						<!-- ── Identitas Dokumen ── -->
						<p class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="bi bi-file-earmark-text me-1"></i> Identitas Dokumen
						</p>
						<div class="row g-3">
							<div class="col-md-2">
								<label class="form-label">No. Invoice</label>
								<input type="text" class="form-control" name="no_invoice" value="<?= $inv['no_invoice'] ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label">Tgl. Invoice</label>
								<input type="date" class="form-control" name="tgl_invoice" value="<?= $inv['tanggal_invoice'] ?>" required>
							</div>
							<div class="col-md-2">
								<label class="form-label">
									No. PO
									<span class="badge bg-secondary text-white ms-1" style="font-size: 0.65rem; font-weight: 400;">opsional</span>
								</label>
								<input type="text" class="form-control uppercase" name="no_po" value="<?= $inv['no_po'] ?? '' ?>" placeholder="—">
							</div>
							<div class="col-md-2">
								<label class="form-label">
									Tgl. PO
									<span class="badge bg-secondary text-white ms-1" style="font-size: 0.65rem; font-weight: 400;">opsional</span>
								</label>
								<input type="date" class="form-control" name="tgl_po" value="<?= $inv['tgl_po'] ?? '' ?>">
							</div>
							<div class="col-md-2">
								<label class="form-label">PPN</label>
								<select name="ppn" id="ppn" class="form-control">
									<option <?= ($inv['ppn'] == '0.000') ? 'selected' : '' ?> value="0.000">0%</option>
									<option <?= ($inv['ppn'] == '0.110') ? 'selected' : '' ?> value="0.110">11%</option>
								</select>
							</div>
							<div class="col-md-2">
								<label class="form-label">Bill To</label>
								<select name="customer" id="customer" class="form-control select2" style="width:100%" required>
									<option value="">:: Pilih customer</option>
									<?php foreach ($customers as $c) : ?>
										<option <?= ($inv['id_customer'] == $c->id) ? 'selected' : '' ?> value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="row g-3 mt-3">
							<div class="col-12">
								<label class="form-label">Notes</label>
								<textarea name="keterangan" id="keterangan" class="form-control uppercase" placeholder="Keterangan invoice..." required><?= $inv['keterangan'] ?></textarea>
							</div>
						</div>

						<hr class="my-4">

						<!-- ── Item Invoice ── -->
						<p class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="bi bi-list-ul me-1"></i> Item Invoice
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
								<?php if ($details) : ?>
									<?php foreach ($details as $d) : ?>
										<tr class="baris">
											<td><input type="text" class="form-control form-control-sm uppercase" name="item[]" value="<?= $d->item ?>"></td>
											<td><input type="text" class="form-control form-control-sm" name="jumlah[]" value="<?= number_format($d->qty, 0, ',', ',') ?>"></td>
											<td><input type="text" class="form-control form-control-sm total" name="total[]" value="<?= number_format($d->total, 0, ',', ',') ?>"></td>
											<td><input type="text" class="form-control form-control-sm" name="total_amount[]" value="<?= number_format($d->total_amount, 0, ',', ',') ?>" readonly></td>
											<td class="text-center">
												<button type="button" class="btn btn-danger btn-sm hapusRow d-none"><i class="bi bi-trash"></i></button>
												<br class="brRow d-none">
												<button type="button" class="btn btn-outline-secondary btn-sm" id="addRow"><i class="fe fe-plus"></i></button>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr class="baris">
										<td><input type="text" class="form-control form-control-sm uppercase" name="item[]"></td>
										<td><input type="text" class="form-control form-control-sm" name="jumlah[]" value="0"></td>
										<td><input type="text" class="form-control form-control-sm total" name="total[]" value="0"></td>
										<td><input type="text" class="form-control form-control-sm" name="total_amount[]" value="0" readonly></td>
										<td class="text-center">
											<button type="button" class="btn btn-danger btn-sm hapusRow d-none"><i class="bi bi-trash"></i></button>
											<br class="brRow d-none">
											<button type="button" class="btn btn-outline-secondary btn-sm" id="addRow"><i class="fe fe-plus"></i></button>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>

						<hr class="my-4">

						<!-- ── Kalkulasi ── -->
						<p class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="bi bi-calculator me-1"></i> Kalkulasi
						</p>
						<div class="row g-3">
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">Subtotal</label>
								<input type="text" class="form-control form-control-sm bg-light" name="nominal" id="nominal" value="<?= number_format($inv['subtotal'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">Besaran PPN</label>
								<input type="text" class="form-control form-control-sm bg-light" name="besaran_ppn" id="besaran_ppn" value="<?= number_format($inv['besaran_ppn'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">PPh 23</label>
								<input type="text" class="form-control form-control-sm bg-light" name="besaran_pph" id="besaran_pph" value="<?= number_format($inv['besaran_pph'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">Total (non PPh)</label>
								<input type="text" class="form-control form-control-sm bg-light" name="total_nonpph" id="total_nonpph" value="<?= number_format($inv['total_nonpph'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">Total (w/ PPh)</label>
								<input type="text" class="form-control form-control-sm bg-light fw-semibold" name="total_denganpph" id="total_denganpph" value="<?= number_format($inv['total_denganpph'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-2">
								<label class="form-label text-muted" style="font-size:0.8rem;">Pendapatan</label>
								<input type="text" class="form-control form-control-sm bg-light fw-semibold" name="nominal_pendapatan" id="nominal_pendapatan" value="<?= number_format($inv['nominal_pendapatan'], 0, ',', ',') ?>" readonly>
							</div>
						</div>

						<hr class="my-4">

						<!-- ── Konfigurasi & Aksi ── -->
						<p class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em;">
							<i class="bi bi-gear me-1"></i> Konfigurasi
						</p>
						<div class="row g-3 align-items-end">
							<div class="col-md-2">
								<label class="form-label">Nominal Bayar</label>
								<input type="text" class="form-control" name="nominal_bayar" id="nominal_bayar" value="<?= number_format($inv['nominal_bayar'], 0, ',', ',') ?>" readonly>
							</div>
							<div class="col-md-3">
								<label class="form-label">CoA Debit</label>
								<select name="coa_debit" id="coa_debit" class="form-control select2 coa_debit" style="width:100%" required>
									<option value="">:: Pilih CoA Debit</option>
									<?php foreach ($pendapatan as $pd) : ?>
										<option <?= ($pd->no_sbb == $inv['coa_debit']) ? 'selected' : '' ?> value="<?= $pd->no_sbb ?>" data-posisi="<?= $pd->posisi ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-3">
								<label class="form-label">CoA Kredit</label>
								<select name="coa_kredit" id="coa_kredit" class="form-control select2 coa_kredit" style="width:100%" required>
									<option value="">:: Pilih CoA Kredit</option>
									<?php foreach ($persediaan as $ps) : ?>
										<option <?= ($ps->no_sbb == $inv['coa_kredit']) ? 'selected' : '' ?> value="<?= $ps->no_sbb ?>" data-posisi="<?= $ps->posisi ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<label class="form-label d-block mb-2">Opsi</label>
								<div class="d-flex gap-3">
									<div class="form-check mb-0" style="margin-right: 5px;">
										<input type="checkbox" class="form-check-input icheckbox_flat-green" name="opsi_termin" id="opsi_termin" value="1" <?= ($inv['opsi_termin'] == '1') ? 'checked' : '' ?>>
										<label class="form-check-label" for="opsi_termin">Termin</label>
									</div>
									<div class="form-check mb-0">
										<input type="checkbox" class="form-check-input icheckbox_flat-green" name="opsi_pph" id="opsi_pph" value="1" <?= ($inv['opsi_pph23'] == '1') ? 'checked' : '' ?>>
										<label class="form-check-label" for="opsi_pph">PPh 23</label>
									</div>
								</div>
							</div>
							<div class="col-md-2 text-end">
								<button type="submit" class="btn btn-primary w-100">
									<i class="bi bi-save me-1"></i> Update Invoice
								</button>
							</div>
						</div>

					</div><!-- .card-body -->
				</div><!-- .card -->
			</form>

		</div>
	</div>
</div>