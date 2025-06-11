<!-- Start content-->
<div class="right_col" role="main">
	<div class="clearfix"></div>

	<div class="x_panel card">
		<div align="center">
			<font style="font-size:17px;">
				Lokasi Presensi
				<br>
				<a href="<?= base_url('app/add_lokasi_presensi') ?>" class="btn btn-primary">Tambah Lokasi Presensi</a>
				<hr />

			</font>
		</div>
		<div class="table-responsive">
			<table id="table1" class="table table-striped" style="width: 100%;">
				<thead>
					<tr>
						<th bgcolor="#004e81">
							<font color="white">No.</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Nama Lokasi</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Alamat Lokasi</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Tipe Lokasi</font>
						</th>

						<th bgcolor="#004e81">
							<font color="white">Latitude</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Longitude</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Radius</font>
						</th>
						<!-- <th bgcolor="#004e81">
										<font color="white">Zona Waktu</font>
									</th> -->
						<th bgcolor="#004e81">
							<font color="white">Jam Masuk</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Jam Pulang</font>
						</th>
						<th bgcolor="#004e81">
							<font color="white">Action</font>
						</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
</div>
</div>

<script>
	$(document).ready(function() {
		$('select.js-example-basic-multiple').select2();
		$('div#myDatepicker2').datetimepicker({
			format: 'YYYY-MM-DD',
			maxDate: Date.now() + 90000000
		});
	});

	window.setTimeout(function() {
		$(".alert-success").fadeTo(500, 0).slideUp(500, function() {
			$(this).remove();
		});
	}, 3000);

	window.setTimeout(function() {
		$(".alert-danger").fadeTo(500, 0).slideUp(500, function() {
			$(this).remove();
		});
	}, 3000);

	$('#table1').dataTable({
		responsive: true,
		rowReorder: {
			selector: 'td:nth-child(2)'
		},
		processing: true,
		serverSide: true,
		ajax: {
			url: "<?php echo site_url('app/ajax_lokasi_presensi_list') ?>",
			type: "POST"
		},
		order: [],
		iDisplayLength: 10,
		columnDefs: [{
			// targets: 8,
			orderable: false
		}]
	});

	function onDelete(id) {
		Swal.fire({
			title: 'Apakah Anda yakin?',
			text: "Data yang dihapus tidak dapat dikembalikan!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya, hapus!',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.isConfirmed) {
				// Send AJAX request to delete the data
				$.ajax({
					url: "<?= base_url('app/hapus_lokasi_presensi/') ?>" + id,
					type: 'POST',
					data: {
						id: id
					},
					success: function(response) {
						if (response.status === 'success') {
							Swal.fire(
								'Terhapus!',
								'Lokasi presensi telah dihapus.',
								'success'
							).then(() => {
								$('#table1').DataTable().ajax.reload(null, false); // Reload table without resetting pagination
							});
						} else {
							Swal.fire(
								'Gagal!',
								'Gagal menghapus lokasi presensi.',
								'error'
							);
						}
					},
					error: function(xhr, status, error) {
						console.error('Error:', error);
						Swal.fire(
							'Kesalahan!',
							'Terjadi kesalahan saat menghapus data.',
							'error'
						);
					}
				});
			}
		});
	}
</script>