<style>
	.pagination-next,
	.pagination-prev {
		padding: 6px 10px;
		color: #94a3b8;
		/* text-muted style */
		font-size: 14px;
	}

	.pagination-next:hover,
	.pagination-prev:hover {
		color: #fff;
		text-decoration: none;
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-12">

			<h1 class="page-title mb-3"><?= $title ?></h1>

			<div class="card">
				<div class="card-body">

					<div class="row align-items-end">
						<div class="col-md-8">
							<form method="GET" action="<?= base_url('financial/sales') ?>">

								<div class="form-row">

									<div class="col-md-3">
										<label class="label">Filter</label>
										<select name="keyword_opt" id="keyword_opt" class="form-control input select2">
											<option value="all">All keyword</option>
											<option value="airline_name" <?= $this->input->get('keyword_opt') == "airline_name" ? "selected" : "" ?>>Maskapai</option>
											<option value="jenis_barang" <?= $this->input->get('keyword_opt') == "jenis_barang" ? "selected" : "" ?>>Jenis barang</option>
											<option value="shipper_name" <?= $this->input->get('keyword_opt') == "shipper_name" ? "selected" : "" ?>>Shipper</option>
											<option value="agent_name" <?= $this->input->get('keyword_opt') == "agent_name" ? "selected" : "" ?>>Agent</option>
											<option value="no_smu" <?= $this->input->get('keyword_opt') == "no_smu" ? "selected" : "" ?>>AWB</option>
										</select>
									</div>

									<div class="col-md-5">
										<label class="label">Keyword</label>
										<input type="text" name="keyword" class="form-control input"
											placeholder="Masukkan kata kunci" value="<?= $this->input->get('keyword') ?>">
									</div>

									<div class="col-md-4">
										<label class="label d-block">&nbsp;</label>
										<div class="d-flex gap-2">
											<button type="submit" class="btn btn-primary mr-2">Cari</button>
											<a href="<?= base_url('financial/sales') ?>"
												class="btn btn-warning text-white">Reset</a>
										</div>
									</div>

								</div>

							</form>
						</div>

						<div class="col-md-4 text-right">
							<button class="btn btn-dark" data-toggle="modal" data-target="#upload_purchase">
								Upload purchase
							</button>
							<button class="btn btn-primary" data-toggle="modal" data-target="#upload_sales">
								Upload sales
							</button>
							<button class="btn btn-pink" data-toggle="modal" data-target="#create_invoice" <?= $agents ? '' : 'disabled' ?>>
								Create invoice
							</button>
						</div>
					</div>

					<div class="table-responsive mt-4">
						<table class="table">
							<thead class="thead-dark">
								<tr>
									<th>AWB</th>
									<th>Tanggal terbang</th>
									<th>Agent</th>
									<th>Origin</th>
									<th>Dest.</th>
									<th>Flight num.</th>
									<th>Stt. Billing</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($sales):
									foreach ($sales as $i): ?>
										<tr>
											<td><?= $i['no_smu'] ?></td>
											<td><?= format_indo($i['tanggal_terbang']) ?></td>
											<td><?= $i['agent_name'] ?></td>
											<td><?= $i['origin'] ?></td>
											<td><?= $i['destination'] ?></td>
											<td><?= $i['flight_number'] ?></td>
											<td class="text-center">
												<span class="badge <?= ($i['is_billing'] == '1') ? 'badge-success text-white' : 'badge-warning text-white' ?>">
													<?= ($i['is_billing'] == '1') ? 'Sudah' : 'Belum' ?>
												</span>
											</td>
											<td>
												<a href="<?= base_url('financial/show_margin/' . $i['Id']) ?>"
													class="badge badge-primary" target="_blank">Show Margin</a>

												<!-- <?php if ($i['status_sales'] == "0"): ?>
													<a href="<?= base_url('financial/edit_invoice/' . $i['Id']) ?>"
														class="badge badge-pink">Edit</a>
												<?php endif; ?> -->
											</td>
										</tr>
									<?php endforeach;
								else: ?>
									<tr>
										<td colspan="8" class="text-center">Tidak ada data</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<nav aria-label="Table Paging" class="mb-0 text-muted">
							<?= $this->pagination->create_links() ?>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- modal sales -->
<div class="modal fade" id="upload_sales">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content modal">

			<div class="modal-header">
				<h5 class="modal-title">Upload Sales</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form id="upload_excel_form" method="POST" enctype="multipart/form-data">
				<div class="modal-body">

					<label class="label">File Excel</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="inputExcel" name="file_excel" accept=".xls,.xlsx">
						<label class="custom-file-label">Choose file</label>
					</div>

					<div id="progress-wrapper" style="display:none;" class="mt-3">
						<label>Uploading...</label>
						<div class="progress progress">
							<div id="upload-progress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="btnUpload" class="btn btn-primary">Upload</button>
				</div>
			</form>

		</div>
	</div>
</div>

<!-- modal purchase -->
<div class="modal fade" id="upload_purchase">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content modal">

			<div class="modal-header">
				<h5 class="modal-title">Upload Purchase</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- ✅ FIX: Ubah ID form -->
			<form id="upload_excel_purchase_form" method="POST" enctype="multipart/form-data">
				<div class="modal-body">

					<label class="label">File Excel</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input" id="inputExcelPurchase" name="file_excel_purchase"
							accept=".xls,.xlsx">
						<label class="custom-file-label">Choose file</label>
					</div>

					<div id="progress-wrapper-purchase" style="display:none;" class="mt-3">
						<label>Uploading...</label>
						<div class="progress progress">
							<div id="upload-progress-purchase" class="progress-bar" role="progressbar" style="width:0%">0%
							</div>
						</div>
					</div>

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="btnUploadPurchase" class="btn btn-primary">Upload</button>
				</div>
			</form>

		</div>
	</div>
</div>
<div class="modal fade" id="create_invoice">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content modal">

			<div class="modal-header">
				<h5 class="modal-title">Create invoice</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form id="create_invoice_sales" method="POST" action="create_invoice_sales">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 col-12 mb-2">
							<label for="" class="form-label">Dari</label>
							<input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" required>
						</div>
						<div class="col-md-6 col-12 mb-2">
							<label for="" class="form-label">Sampai</label>
							<input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" required>
						</div>
						<div class="col-12 mb-2">
							<label for="" class="form-label">Agent</label>
							<select name="agent_name" id="agent_name" class="form-control" required>
								<option value="">::Pilih agent</option>
								<?php
								if ($agents) {
									foreach ($agents as $agent):
								?>
										<option value="<?= $agent->agent_name ?>"><?= $agent->agent_name ?></option>
								<?php
									endforeach;
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Process invoice</button>
				</div>
			</form>

		</div>
	</div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
	$(document).ready(function() {

		// Klik tombol upload → trigger submit
		$("#btnUpload").on("click", function() {
			$("#upload_excel_form").trigger("submit");
		});

		// Event ketika pilih file → tampilkan preview nama file
		$("#inputExcel").on("change", function() {
			if (this.files.length > 0) {
				let fileName = this.files[0].name;
				console.log("File selected:", fileName);

				// ✅ Update label dengan nama file
				$(this).next('.custom-file-label').text(fileName);
			}
		});

		// Submit Form Upload
		$("#upload_excel_form").on("submit", function(e) {
			e.preventDefault();

			let fileInput = $("#inputExcel")[0].files[0];
			if (!fileInput) {
				Swal.fire("Peringatan", "Silakan pilih file excel!", "warning");
				return;
			}

			let formData = new FormData();
			formData.append("file_excel", fileInput);

			// ✅ Reset progress bar (sesuaikan dengan ID di HTML)
			$("#upload-progress").css("width", "0%").text("0%");
			$("#progress-wrapper").show(); // ✅ Tampilkan progress wrapper
			$("#btnUpload").prop("disabled", true); // ✅ Disable tombol upload

			$.ajax({
				xhr: function() {
					let xhr = new XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(e) {
						if (e.lengthComputable) {
							let percent = Math.round((e.loaded / e.total) * 100);
							$("#upload-progress").css("width", percent + "%").text(percent + "%"); // ✅ Sesuaikan ID
						}
					});
					return xhr;
				},

				url: "<?= base_url('financial/upload_sales') ?>",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",

				success: function(res) {
					console.log("Parsed JSON:", res);

					// ✅ CEK DULU STATUS-nya
					if (res.status === "error") {
						if (res.error_type === "header_validation") {
							Swal.fire({
								icon: "error",
								title: "File Tidak Sesuai Template",
								html: `
									<div style="text-align: center; padding: 20px;">
										<div style="font-size: 48px; margin-bottom: 20px;">📋</div>
										<p style="font-size: 16px; margin-bottom: 10px;">
											File yang Anda upload memiliki <strong>${res.total_errors} kesalahan</strong> pada header kolom.
										</p>
										<p style="color: #666; font-size: 14px;">
											Pastikan Anda menggunakan template resmi untuk upload data sales.
										</p>
									</div>
							`,
								showDenyButton: true,
								showCancelButton: true,
								confirmButtonText: '📥 Download Template',
								denyButtonText: '🔍 Lihat Kesalahan',
								cancelButtonText: 'Tutup',
								confirmButtonColor: '#28a745',
								denyButtonColor: '#ffc107',
								width: '550px'
							}).then((result) => {
								if (result.isConfirmed) {
									window.location.href = '<?= base_url("assets/template_upload_sales.xlsx") ?>';
								} else if (result.isDenied) {
									showDetailErrors(res.error_details);
								}
							});
						}
						return;
					}

					// ✅ Untuk success (sampai sini kalau status = success)
					let message = `
						<div style="text-align: left;">
								<p><strong>Total baris:</strong> ${res.total_rows}</p>
								<p><strong>✅ Berhasil diupload:</strong> ${res.inserted_rows}</p>
								<p><strong>⚠️ Dilewati (duplikat):</strong> ${res.skipped_rows}</p>
						</div>
					`;

					// Jika ada data yang di-skip, tampilkan tombol download
					if (res.skipped_rows > 0) {
						message += `<hr><p><small>Klik tombol di bawah untuk download detail data yang dilewati</small></p>`;
					}

					Swal.fire({
						icon: "success",
						title: "Upload Selesai!",
						html: message,
						showConfirmButton: true,
						showCancelButton: res.skipped_rows > 0,
						confirmButtonText: 'OK',
						cancelButtonText: '📥 Download Detail',
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#6c757d'
					}).then((result) => {
						if (result.isDismissed && res.skipped_rows > 0) {
							// Download CSV detail skipped data
							downloadSkippedData(res.skipped);
						}

						// Reload halaman setelah success
						location.reload();
					});
				},

				error: function(xhr, status, error) {
					console.error("AJAX Error:", xhr.responseText);
					Swal.fire("Error", "Terjadi kesalahan pada server", "error");
					$("#btnUpload").prop("disabled", false);
					$("#progress-wrapper").hide();
				},

				complete: function() {
					// ✅ Sembunyikan progress setelah selesai
					$("#progress-wrapper").hide();
					$("#btnUpload").prop("disabled", false);
				}
			});
		});



		// ✅ Tombol upload purchase → trigger submit form
		$("#btnUploadPurchase").on("click", function() {
			$("#upload_excel_purchase_form").trigger("submit"); // ✅ Trigger form yang benar
		});

		// ✅ Preview nama file saat pilih file
		$("#inputExcelPurchase").on("change", function() {
			if (this.files.length > 0) {
				let fileName = this.files[0].name;
				console.log("File selected:", fileName);
				$(this).next('.custom-file-label').text(fileName);
			}
		});

		// ✅ Submit handler untuk FORM purchase (bukan input!)
		$("#upload_excel_purchase_form").on("submit", function(e) {
			e.preventDefault();

			let fileInput = $("#inputExcelPurchase")[0].files[0];
			if (!fileInput) {
				Swal.fire("Peringatan", "Silakan pilih file excel!", "warning");
				return;
			}

			let formData = new FormData();
			formData.append("file_excel_purchase", fileInput);

			// Reset progress bar
			$("#upload-progress-purchase").css("width", "0%").text("0%");
			$("#progress-wrapper-purchase").show();
			$("#btnUploadPurchase").prop("disabled", true);

			$.ajax({
				xhr: function() {
					let xhr = new XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(e) {
						if (e.lengthComputable) {
							let percent = Math.round((e.loaded / e.total) * 100);
							$("#upload-progress-purchase").css("width", percent + "%").text(percent + "%");
						}
					});
					return xhr;
				},

				url: "<?= base_url('financial/upload_purchase') ?>",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",

				success: function(res) {
					console.log("Parsed JSON:", res);

					// CEK STATUS ERROR
					if (res.status === "error") {
						if (res.error_type === "header_validation") {
							Swal.fire({
								icon: "error",
								title: "File Tidak Sesuai Template",
								html: `
								<div style="text-align: center; padding: 20px;">
									<div style="font-size: 48px; margin-bottom: 20px;">📋</div>
									<p style="font-size: 16px; margin-bottom: 10px;">
										File yang Anda upload memiliki <strong>${res.total_errors} kesalahan</strong> pada header kolom.
									</p>
									<p style="color: #666; font-size: 14px;">
										Pastikan Anda menggunakan template resmi untuk upload data purchase.
									</p>
								</div>
							`,
								showDenyButton: true,
								showCancelButton: true,
								confirmButtonText: '📥 Download Template',
								denyButtonText: '🔍 Lihat Kesalahan',
								cancelButtonText: 'Tutup',
								confirmButtonColor: '#28a745',
								denyButtonColor: '#ffc107',
								width: '550px'
							}).then((result) => {
								if (result.isConfirmed) {
									window.location.href = '<?= base_url("assets/template_upload_purchase.xlsx") ?>';
								} else if (result.isDenied) {
									showDetailErrors(res.error_details);
								}
							});
						} else {
							// Error lainnya
							Swal.fire("Error", res.message || "Terjadi kesalahan", "error");
						}
						return;
					}

					// SUCCESS
					let message = `
					<div style="text-align: left;">
						<p><strong>Total baris:</strong> ${res.total_rows}</p>
						<p><strong>✅ Berhasil diupload:</strong> ${res.inserted_rows}</p>
						<p><strong>⚠️ Dilewati (duplikat):</strong> ${res.skipped_rows}</p>
					</div>
				`;

					if (res.skipped_rows > 0) {
						message += `<hr><p><small>Klik tombol di bawah untuk download detail data yang dilewati</small></p>`;
					}

					Swal.fire({
						icon: "success",
						title: "Upload Selesai!",
						html: message,
						showConfirmButton: true,
						showCancelButton: res.skipped_rows > 0,
						confirmButtonText: 'OK',
						cancelButtonText: '📥 Download Detail',
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#6c757d'
					}).then((result) => {
						if (result.isDismissed && res.skipped_rows > 0) {
							downloadSkippedData(res.skipped);
						}

						// Close modal & reload
						$('#upload_purchase').modal('hide');
						location.reload();
					});
				},

				error: function(xhr, status, error) {
					console.error("AJAX Error:", xhr.responseText);
					Swal.fire("Error", "Terjadi kesalahan pada server: " + xhr.responseText, "error");
					$("#btnUploadPurchase").prop("disabled", false);
					$("#progress-wrapper-purchase").hide();
				},

				complete: function() {
					$("#progress-wrapper-purchase").hide();
					$("#btnUploadPurchase").prop("disabled", false);
				}
			});
		});
	});

	// Fungsi untuk download CSV
	function downloadSkippedData(skippedData) {
		let csv = "No SMU,Tanggal Upload Sebelumnya\n";
		skippedData.forEach(item => {
			csv += `${item.no_smu},${item.uploaded_at}\n`;
		});

		let blob = new Blob([csv], {
			type: 'text/csv'
		});
		let url = window.URL.createObjectURL(blob);
		let a = document.createElement('a');
		a.href = url;
		a.download = 'skipped_data_' + new Date().getTime() + '.csv';
		a.click();
	}

	function showDetailErrors(errors) {
		let errorList = '<ul style="text-align: left; max-height: 300px; overflow-y: auto;">';
		errors.forEach(err => {
			errorList += `<li style="margin-bottom: 8px;">${err}</li>`;
		});
		errorList += '</ul>';

		Swal.fire({
			icon: "warning",
			title: "Detail Kesalahan Header",
			html: errorList,
			confirmButtonText: 'OK',
			width: '700px'
		});
	}
</script>