<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Arus Kas</h1>
			<div class="card shadow mb-4">
				<!-- <div class="card-header">
          <p class="card-title"><strong>Arus Kas</strong></p>
        </div> -->
				<div class="card-body">
					<?php
					if ($this->input->post('no_coa') || $this->input->post('keyword')) { ?>
						<form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
							<div class="row">
								<div class="col-md-3 col-xs-12">
									<label for="" class="form-label">No. CoA</label>
									<select name="no_coa" id="no_coa" class="form-control select2">
										<option value="">:: Pilih nomor coa</option>
										<option <?= ($this->input->post('no_coa') == 'ALL') ? "selected" : "" ?> value="ALL">ALL COA</option>
										<?php
										foreach ($coas as $c) {
										?>
											<option <?= ($this->input->post('no_coa') == $c->no_sbb) ? "selected" : "" ?> value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
										<?php
										} ?>
									</select>
								</div>
								<div class="col-md-2 col-xs-12">
									<label for="tgl_dari" class="form-label">Dari</label>
									<input type="date" class="form-control" name="tgl_dari" value="<?= $this->input->post('tgl_dari') ?>">
								</div>
								<div class="col-md-2 col-xs-12">
									<label for="tgl_sampai" class="form-label">Sampai</label>
									<input type="date" class="form-control" name="tgl_sampai" value="<?= $this->input->post('tgl_sampai') ?>">
								</div>
								<div class="col-md-3 col-xs-12">
									<label for="keyword" class="form-label">Keyword</label>
									<input type="text" name="keyword" id="keyword" class="form-control" placeholder="nomor coa/nominal/keterangan" value="<?= $this->input->post('keyword') ?>">
								</div>
								<div class="col-md-2 col-xs-12">
									<button type="submit" class="btn btn-primary btn-sm" style="margin-top: 30px;">Lihat</button>
									<a href="<?= base_url('financial/coa_report') ?>" class="btn btn-warning text-white btn-sm" style="margin-top: 30px;">Reset</a>
								</div>
							</div>
						</form>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12 col-xs-12 table-responsive">
								<?php
								if ($this->input->post('no_coa') == "ALL" || (!$this->input->post('no_coa') && $this->input->post('keyword'))) {
								?>
									<table id="" class="table table-sm table-bordered" style="width:100%">
										<thead class="thead-dark">
											<?php
											if ($this->input->post('keyword') !== "") {
											?><tr>
													<th class="text-right" colspan="3" style="background-color: #e91e63; font-weight: bolder;">Total:</th>
													<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
													<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
													<!-- <th class="text-right" colspan="2">Saldo Awal: <?= rupiah($saldo_awal) ?></th> -->
												</tr>
											<?php
											} ?>


											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Tanggal</th>
												<th class="text-center">CoA</th>
												<th class="text-center">Debit</th>
												<th class="text-center">Kredit</th>
												<!-- <th class="text-center">Saldo Akhir</th> -->
												<th class="text-center">Keterangan</th>
												<th class="text-center">File</th>
												<?php
												if ($this->session->userdata('nama_jabatan') == "Super Admin") {
												?>
													<th class="text-center">Action</th>
												<?php
												}
												?>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = 1;
											if ($coa) {
												foreach ($coa as $a) :
													$coa_debit = $this->M_coa->getCoa($a->akun_debit);
													$coa_kredit = $this->M_coa->getCoa($a->akun_kredit); ?>
													<tr>
														<td><?= $no++ ?></td>
														<!-- <td><?= format_indo($a->tanggal) ?></td> -->
														<td><?= date('d/m/Y', strtotime($a->tanggal)) ?></td>
														<td><?= $a->akun_debit ?> - <?= $coa_debit['nama_perkiraan'] ?></td>
														<td class="text-right"><?= rupiah($a->jumlah_debit) ?></td>
														<td class="text-right"><?= '0' ?></td>
														<!-- <td class="text-right"><?= rupiah($a->saldo_debit) ?></td> -->
														<td style="white-space: pre-line;"><?= $a->keterangan ?></td>
														<td style="white-space: pre-line;">
															<?php if ($a->file): ?>
																<a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info">
																	<i class="fa fa-download"></i> <?= $a->nama_file ?>
																</a>
															<?php else: ?>
																<!-- No Attachment -->
																-
															<?php endif; ?>
														</td>
														<?php
														if ($this->session->userdata('nama_jabatan') == "Super Admin") {
														?>
															<td class="text-center"><button class="btn btn-sm btn-warning text-white" onclick="onEdit_report_per_coa(<?= $a->id ?>)" type="button">Update</button></td>
														<?php
														}
														?>
													</tr>
													<tr>
														<td><?= $no++ ?></td>
														<!-- <td><?= format_indo($a->tanggal) ?></td> -->
														<td><?= date('d/m/Y', strtotime($a->tanggal)) ?></td>
														<td><?= $a->akun_kredit ?> - <?= $coa_kredit['nama_perkiraan'] ?></td>
														<td class="text-right"><?= '0' ?></td>
														<td class="text-right"><?= rupiah($a->jumlah_kredit) ?></td>
														<!-- <td class="text-right"><?= rupiah($a->saldo_kredit) ?></td> -->
														<td style="white-space: pre-line;"><?= $a->keterangan ?></td>
														<td style="white-space: pre-line;">
															<?php if ($a->file): ?>
																<a href="<?= site_url('financial/download_file/' . $a->id) ?>" class="btn btn-info">
																	<i class="fa fa-download"></i> <?= $a->nama_file ?>
																</a>
															<?php else: ?>
																<!-- No Attachment -->
																-
															<?php endif; ?>
														</td>
														<?php
														if ($this->session->userdata('nama_jabatan') == "Super Admin") {
														?>
															<td class="text-center"><button class="btn btn-sm btn-warning text-white" onclick="onEdit_report_per_coa(<?= $a->id ?>)" type="button">Update</button></td>
														<?php
														}
														?>
													</tr>
												<?php
												endforeach;
											} else {
												?>
												<tr>
													<td colspan="8">Tidak ada transaksi pada periode yang dipilih</td>
												</tr>
											<?php
											} ?>
										</tbody>
									</table>
								<?php
								} else {
								?>
									<table id="" class="table table-sm table-bordered" style="width:100%">
										<thead class="thead-dark">
											<tr>
												<th class="text-right" colspan="2" style="background-color: #e91e63; font-weight: bolder;">Total:</th>
												<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_debit) ?></th>
												<th class="text-right" style="background-color: #e91e63; font-weight: bolder;"><?= rupiah($sum_kredit) ?></th>
												<!-- <th class="text-right" colspan="2">Saldo Awal: <?= rupiah($saldo_awal) ?></th> -->
											</tr>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Tanggal</th>
												<th class="text-center">Debit</th>
												<th class="text-center">Kredit</th>
												<!-- <th class="text-center">Saldo Akhir</th> -->
												<th class="text-center">Keterangan</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = 1;
											if ($coa) {

												foreach ($coa as $a) :
											?>
													<tr>
														<td><?= $no++ ?></td>
														<td><?= format_indo($a->tanggal) ?></td>
														<!-- <td><?= ($a->akun_debit == $detail_coa['no_sbb']) ? $a->akun_debit : $a->akun_kredit ?></td> -->
														<td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->jumlah_debit) ? rupiah($a->jumlah_debit) : '0') : '0' ?></td>
														<!-- <td class="text-right"><?= ($a->akun_debit == $detail_coa['no_sbb']) ? (($a->saldo_debit) ? rupiah($a->saldo_debit) : '0') : '0' ?></td> -->
														<td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->jumlah_kredit) ? rupiah($a->jumlah_kredit) : '0') : '0' ?></td>
														<!-- <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? rupiah($a->saldo_kredit) : '0') : '0' ?></td> -->
														<!-- <td class="text-right"><?= ($a->akun_kredit == $detail_coa['no_sbb']) ? (($a->saldo_kredit) ? rupiah($a->saldo_kredit) :  '0') : (($a->saldo_debit) ? rupiah($a->saldo_debit) : '0') ?></td> -->
														<td><?= $a->keterangan ?></td>
													</tr>
												<?php
												endforeach;
											} else {
												?>
												<tr>
													<td colspan="6">Tidak ada transaksi pada periode yang dipilih</td>
												</tr>
											<?php
											} ?>
										</tbody>

										<?php
										$no = 1;
										// $saldo = $saldo_awal;
										$saldo = 0;
										if ($coa) {
											// foreach ($coa as $a) {
											//   $posisi = $detail_coa["posisi"];
											//   $no_sbb = $detail_coa["no_sbb"];

											//   if ($posisi == "AKTIVA") {
											//     if ($a->akun_debit == $no_sbb) {
											//       $saldo += $a->jumlah_debit;
											//     } else {
											//       $saldo -= $a->jumlah_kredit;
											//     }
											//   } else { // PASIVA
											//     if ($a->akun_kredit == $no_sbb) {
											//       $saldo += $a->jumlah_kredit;
											//     } else {
											//       $saldo -= $a->jumlah_debit;
											//     }
											//   } 
										?>
										<?php
											// }
										} else {
										?>
										<?php
										}
										?>
									</table>
								<?php
								} ?>
							</div>
						</div>
					<?php
					} else {
					?>
						<form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/coa_report') ?>">
							<div class="row">
								<div class="col-md-3 col-xs-12">
									<label for="" class="form-label">No. CoA </label>
									<select name="no_coa" id="no_coa" class="form-control select2">
										<option value="">:: Pilih nomor coa</option>
										<option <?= ($this->input->post('no_coa') == 'ALL') ? "selected" : "" ?> value="ALL">ALL COA</option>
										<?php
										foreach ($coas as $c) {
										?>
											<option value="<?= $c->no_sbb ?>"><?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?></option>
										<?php
										} ?>
									</select>
								</div>
								<div class="col-md-2 col-xs-12">
									<label for="tgl_invoice" class="form-label">Dari</label>
									<input type="date" class="form-control" name="tgl_dari" value="">
								</div>
								<div class="col-md-2 col-xs-12">
									<label for="tgl_invoice" class="form-label">Sampai</label>
									<input type="date" class="form-control" name="tgl_sampai" value="<?= date('Y-m-d') ?>">
								</div>
								<div class="col-md-4 col-xs-12">
									<label for="keyword" class="form-label">Keyword</label>
									<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Masukkan nomor coa/nominal/keterangan" value="<?= $this->input->post('keyword') ?>">
								</div>
								<div class="col-md-1 col-xs-12">
									<button type="submit" class="btn btn-primary" style="margin-top: 24px;">Lihat</button>
								</div>
							</div>
						</form>
					<?php
					} ?>
				</div>
			</div>
		</div> <!-- .col-12 -->
	</div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Update COA Modal -->
<div class="modal fade" id="updateCoaModal" tabindex="-1" aria-labelledby="updateCoaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="updateCoaModalLabel">Update COA Entry</h5>
				<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
			</div>
			<form id="updateCoaForm" action="<?php echo site_url('financial/update_report_per_coa'); ?>" method="POST" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="id" id="update_id">
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label for="" class="form-label">Debit</label>
							<select name="neraca_debit" id="update_neraca_debit" class="form-control" style="width: 100%;" required>
								<option value="">-- Pilih pos neraca debit</option>
								<?php
								foreach ($daftar_coa as $c) :
								?>
									<option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
								<?php
								endforeach; ?>
							</select>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label for="" class="form-label">Kredit</label>
							<select name="neraca_kredit" id="update_neraca_kredit" class="form-control" style="width: 100%;" required>
								<option value="">-- Pilih pos neraca kredit</option>
								<?php
								foreach ($daftar_coa as $c) :
								?>
									<option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?> </option>
								<?php
								endforeach; ?>
							</select>
						</div>
						<div class="col-md-12 col-xs-12 form-group has-feedback">
							<div id="warningMessage" class="validation-error-alert">

							</div>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label for="" class="form-label">Nominal</label>
							<!-- <input type="text" class="form-control" name="input_nominal" id="input_nominal" placeholder="Nominal" oninput="format_angka()" onkeypress="return onlyNumberKey(event)" autofocus required> -->
							<input type="text" class="form-control uang" name="input_nominal" id="update_input_nominal" placeholder="Nominal" autofocus required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label for="" class="form-label">Keterangan</label>
							<input type="text" class="form-control" name="input_keterangan" id="update_input_keterangan" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<label for="" class="form-label">Tanggal</label>
							<input type="date" name="tanggal" id="update_tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
						</div>
						<div class="col-md-6 col-xs-12 form-group has-feedback">
							<div class="form-group">
								<label for="file" class="form-label">Attachment (Image/Excel/Word) <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
										<path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
									</svg></label>
								<div class="div-file">
									<input type="file" class="form-control-file" name="file" id="file">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger" onclick="onDeleteArusKas()">Delete</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>