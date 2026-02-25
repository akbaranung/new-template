<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
			<h1 class="page-title">Project</h1>
			<div class="card shadow mb-4">
				<div class="card-body">
					<div class="row mb-3 align-items-center">
						<div class="col-lg-2 col-md-2 col-sm-12">
							<a href="<?= site_url('financial/create_project') ?>" class="btn btn-primary btn-sm">
								<i class="fe fe-plus"></i> Buat Project
							</a>
						</div>
						<form class="form-horizontal form-label-left col-lg-10 col-md-10 col-sm-12" method="POST" action="<?= base_url('financial/project') ?>">
							<div class="row align-items-center">
								<div class="col-md-7 col-xs-12">
									<input type="text" name="keyword" id="keyword" class="form-control form-control-sm" placeholder="Cari nomor project / keterangan / user..." value="<?= $keyword ?>">
								</div>
								<div class="col-md-5 col-xs-12">
									<button type="submit" class="btn btn-primary btn-sm">Cari</button>
									<a href="<?= base_url('financial/project') ?>" class="btn btn-warning text-white btn-sm">Reset</a>
								</div>
							</div>
						</form>
					</div>



					<div class="table-responsive">
						<table class="table table-sm table-striped table-bordered" style="width:100%">
							<thead class="thead-dark">
								<tr>
									<th class="text-center">No.</th>
									<th class="text-center">No. Project</th>
									<th class="text-center">Tanggal</th>
									<th class="text-center">Keterangan</th>
									<th class="text-center">Total Debit</th>
									<th class="text-center">Total Kredit</th>
									<th class="text-center">Dibuat Oleh</th>
									<th class="text-center">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($projects) :
									$no = ($page) + 1;
									foreach ($projects as $p) : ?>
										<tr>
											<td class="text-center"><?= $no++ ?></td>
											<td><?= $p['no_project'] ?></td>
											<td><?= format_indo($p['tanggal']) ?></td>
											<td><?= $p['keterangan'] ?></td>
											<td class="text-right"><?= rupiah($p['total_debit'] ?? 0) ?></td>
											<td class="text-right"><?= rupiah($p['total_kredit'] ?? 0) ?></td>
											<td><?= $p['created_by'] ?></td>
											<td class="text-center">
												<a href="<?= base_url('financial/print_project/' . $p['id']) ?>" class="badge badge-pill badge-primary" target="_blank">
													<i class="fa fa-print"></i> Print
												</a>
												<a href="<?= base_url('financial/edit_project/' . $p['id']) ?>" class="badge badge-pill badge-pink">
													<i class="fa fa-edit"></i> Edit
												</a>
												<a href="#" class="badge badge-pill badge-danger"
													onclick="confirmDelete(<?= $p['id'] ?>, '<?= $p['no_project'] ?>')">
													<i class="fa fa-trash"></i> Hapus
												</a>
											</td>
										</tr>
									<?php
									endforeach;
								else : ?>
									<tr>
										<td colspan="8" class="text-center">Tidak ada data project</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>

					<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-6 text-right">
							<?= $this->pagination->create_links() ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Konfirmasi Hapus</h5>
			</div>
			<div class="modal-body">
				<p>Apakah Anda yakin ingin menghapus project <strong id="deleteLabel"></strong>?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
				<a href="#" id="deleteBtn" class="btn btn-danger btn-sm">Hapus</a>
			</div>
		</div>
	</div>
</div>

<script>
	function confirmDelete(id, no_project) {
		document.getElementById('deleteLabel').innerText = no_project;
		document.getElementById('deleteBtn').href = '<?= base_url("financial/delete_project/") ?>' + id;
		$('#deleteModal').modal('show');
	}
</script>