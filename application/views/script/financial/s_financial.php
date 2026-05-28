<script>
	document.addEventListener('DOMContentLoaded', function() {
		const isPremium = <?php echo json_encode($this->session->userdata('is_premium')); ?>;
		const upgradeUrl = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed

		function showPremiumDeniedSwal() {
			Swal.fire({
				title: 'Siap Menjadi Raja <?= '<img src="' . base_url() . 'assets/icons/sword_gray.png" alt="Sword Icon" width="32" height="32">' ?>', // New title: "Ready to Become King?"
				html: 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..', // New text with HTML for emphasis
				icon: 'warning', // IMPORTANT: Set icon to undefined or remove it if you're using iconHtml
				iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>', // Changed icon to question, suitable for asking a choice
				confirmButtonText: 'Ambil Mahkota Sekarang!', // New confirm button text: "Take the Crown Now!"
				showCancelButton: true,
				cancelButtonText: 'Nanti Saja, Belum Siap Jadi Raja', // New cancel button text: "Later, Not Ready to Be King Yet"
				customClass: {
					confirmButton: 'btn btn-primary', // Optional: Use your custom btn-pink class for the confirm button
					cancelButton: 'btn btn-pink' // Optional: Style the cancel button differently
				},
				buttonsStyling: false // Important if you use customClass for buttons
			}).then((result) => {
				if (result.isConfirmed) {
					// Optional: Redirect to an upgrade page if 'Ambil Mahkota Sekarang!' is clicked
					window.location.href = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed
				}
			});
		}

		// ... (your existing JavaScript for other buttons) ...

		// Disable Attachment Input if Not Premium
		const attachmentInput = document.getElementById('file');
		if (attachmentInput) { // Check if the element exists
			if (!isPremium) {
				attachmentInput.disabled = true; // Disable the input field
				// Optional: Add a tooltip or message near the input to explain why it's disabled
				const parentDiv = attachmentInput.closest('.div-file'); // Find the parent div
				if (parentDiv) {
					const message = document.createElement('small');
					message.classList.add('text-danger', 'form-text');
					message.textContent = 'Upgrade to premium to upload attachments.';
					parentDiv.appendChild(message);
				}
			}
		}
	})
</script>
<script>
	const allCoaBb = <?= json_encode($all_coa_bb) ?>;

	$(document).ready(function() {
		$('#no_bb').select2({
			placeholder: 'Cari Kode BB...',
			minimumInputLength: 0, // Set to 0 to show all options initially
			allowClear: true,
			data: allCoaBb, // Use the pre-loaded data
			templateResult: function(option) {
				// Return the text directly for normal display
				return option.text;
			},
			templateSelection: function(option) {
				// Return the text directly for selected display
				return option.text;
			}
		});
	});
</script>
<script>
	function onEdit_report_per_coa(id) {
		$('#updateCoaForm')[0].reset(); // reset form on modals
		// $('.form-group').removeClass('has-error'); // clear error class
		// $('.help-block').empty(); // clear error string
		// $('.modal-title').text('Edit Poster');

		$.ajax({
			url: "<?php echo site_url('financial/ajax_edit_report_coa') ?>/" + id,
			type: "POST",
			dataType: "JSON",
			success: function(response) {
				var data = response.data;

				console.log(response);

				JSON.stringify(data.id);
				// alert(JSON.stringify(data));

				$('#update_id').val(data.id);
				$('#update_neraca_debit').val(data.akun_debit).trigger('change');
				$('#update_neraca_kredit').val(data.akun_kredit).trigger('change');
				// var formattedNominal = new Intl.NumberFormat('id-ID', {
				// 	style: 'currency',
				// 	currency: 'IDR',
				// 	minimumFractionDigits: 0
				// }).format(data.jumlah_debit);

				// // Ganti simbol Rp yang ada spasinya (bawaan Intl) jika perlu
				// formattedNominal = formattedNominal.replace(/(\D+)/, 'Rp ');

				// BARU - pakai formatAngka supaya konsisten, support desimal
				var rawNominal = parseFloat(data.jumlah_debit) || 0;
				var formattedNominal = rawNominal.toLocaleString('id-ID', {
					minimumFractionDigits: 0,
					maximumFractionDigits: 2
				});

				$('#update_input_nominal').val(formattedNominal);
				$('#update_input_keterangan').val(data.keterangan);
				$('#update_tanggal').val(data.tanggal);
				// if (coaEntry.table_source == "t_coa_sbb") {
				//   $('#update_no_bb').val(data.no_bb);
				//   $('#update_no_sbb').val(data.no_sbb);
				// } else {

				//   $('#update_no_bb').val(data.no_lr_bb);
				//   $('#update_no_sbb').val(data.no_lr_sbb);
				// }
				// $('#update_nama_perkiraan').val(data.nama_perkiraan);
				// $('#update_nominal').val(data.nominal);


				$('#updateCoaModal').modal('show'); // show bootstrap modal when complete loaded

			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error get data from ajax');
			}
		});
	}

	$(document).ready(function() {
		function formatState(state, colorAktiva, colorPasiva, signAktiva, signPasiva) {
			if (!state.id) {
				return state.text;
			}

			var color = state.element.dataset.posisi == "AKTIVA" ? colorAktiva : colorPasiva;
			var sign = state.element.dataset.posisi == "AKTIVA" ? signAktiva : signPasiva;

			var $state = $('<p style="background-color: ' + color + ';"><strong style="color: #fff;">' + state.text + ' ' + sign + '</strong></p>');

			return $state;
		};

		function formatStateDebit(state) {
			return formatState(state, '#3f51b5', '#e81f63', '(+)', '(-)');
		}

		function formatStateKredit(state) {
			return formatState(state, '#e81f63', '#3f51b5', '(-)', '(+)');
		}

		$('#update_neraca_debit').select2({
			// templateResult: formatStateDebit,
			templateSelection: formatStateDebit,
			theme: 'bootstrap4',
		});

		$('#update_neraca_kredit').select2({
			// templateResult: formatStateKredit,
			templateSelection: formatStateKredit,
			theme: 'bootstrap4',
		});

		$('#update_neraca_debit, #update_neraca_kredit').change(function() {
			var debit = $('#update_neraca_debit').find(":selected").val();
			var kredit = $('#update_neraca_kredit').find(":selected").val();
			disabledSubmit(debit, kredit);
		});

		function disabledSubmit(debit, kredit) {

			// const inputFieldDisableFinancialEntry = document.getElementById('inputToCheck');
			const warningBoxDisableFinancialEntry = document.getElementById('warningMessage');

			if (debit && kredit) {
				if (debit == kredit) {
					console.log('sama');
					// $('.btn-primary').prop('disabled', true);
					$('#btn-submit').prop('disabled', true);
					warningBoxDisableFinancialEntry.style.display = 'block';
					warningBoxDisableFinancialEntry.innerHTML = '⚠️ **Peringatan!** Nomor COA tidak boleh sama. Silahkan Pilih Nomor COA Lain'; // Optional: Also highlight the input border red for better feedback
					// this.style.borderColor = '#dc3545';
				} else {
					console.log('tidak sama');
					$('#btn-submit').prop('disabled', false);
					// Hide the warning message by setting display to 'none'
					warningBoxDisableFinancialEntry.style.display = 'none';

					// Reset input field border
					// this.style.borderColor = '';
					warningBoxDisableFinancialEntry.innerHTML = '';
				}
			}
		}
	});

	function onDeleteArusKas() {
		let id = $('#update_id').val();
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
				$.ajax({
					url: "<?= base_url('financial/hapus_arus_kas/') ?>", // Use POST for ID, don't append to URL unless it's a RESTful DELETE
					type: 'POST', // Keep as POST
					data: {
						id: id
					},
					dataType: 'json', // Expect JSON response
					success: function(response) {
						let iconType = 'error'; // Default to error
						if (response.status == 'success') {
							iconType = 'success';
						} else if (response.status == 'info') {
							iconType = 'info'; // Use info icon for "not found" cases
						}

						Swal.fire(
							response.status === 'success' ? 'Berhasil!' : 'Perhatian!', // Dynamic title
							response.message, // Display the message from the backend
							iconType
						).then(() => {
							// Only reload the table if it was a success or a clear 'info' (already deleted) case
							if (response.status === 'success' || response.status === 'info') {
								// Assuming your DataTables ID is 'datatable', not 'table1' based on previous snippets
								// $('#datatable_lokasi').DataTable().ajax.reload(null, false);
								location.reload();
							}
						});
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', status, error, xhr.responseText); // Log full error for debugging
						Swal.fire(
							'Kesalahan Jaringan!', // More specific error message
							'Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.',
							'error'
						);
					}
				});
			}
		});
	}
</script>
<script>
	$(document).ready(function() {
		$('.tab-button').on('click', function() {
			var activeTab = $(this).data('tab');

			// Use AJAX to send the active tab value to the controller
			$.ajax({
				url: '<?= base_url("financial/set_active_tab_session") ?>',
				method: 'POST',
				data: {
					active_tab: activeTab
				},
				success: function(response) {
					console.log('Session updated successfully!');

					// Remove 'active' class from all buttons
					$('.tab-button').removeClass('active');

					// Add 'active' class to the clicked button
					$('#' + activeTab + '-tab').addClass('active');

					// Show the correct tab pane content
					$('.tab-pane').removeClass('show active');
					$('#' + activeTab).addClass('show active');
				},
				error: function() {
					console.error('Failed to update session.');
				}
			});
		});
	});
</script>
<script>
	$(document).ready(function() {

		$(".btn-submit").click(function(e) {
			e.preventDefault();
			var parent = $(this).parents("form");
			var url = parent.attr("action");
			console.log(parent);
			var formData = new FormData(parent[0]);
			Swal.fire({
				title: "Are you sure?",
				text: "You want to submit the form?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes",
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: url,
						method: "POST",
						data: formData,
						processData: false,
						contentType: false,
						dataType: "JSON",
						beforeSend: () => {
							Swal.fire({
								title: "Loading....",
								timerProgressBar: true,
								allowOutsideClick: false,
								didOpen: () => {
									Swal.showLoading();
								},
							});
						},
						success: function(res) {
							if (res.success) {
								Swal.fire({
									icon: "success",
									title: `${res.msg}`,
									showConfirmButton: false,
									timer: 1500,
								}).then(function() {
									Swal.close();
									location.href = `${res.reload}`
								});
							} else {
								Swal.fire({
									icon: "error",
									title: `${res.msg}`,
									showConfirmButton: false,
									timer: 1500,
								}).then(function() {
									Swal.close();
								});
							}
						},
						error: function(xhr, status, error) {
							console.log(xhr);
							Swal.fire({
								icon: "error",
								title: `${status}`,
								showConfirmButton: false,
								timer: 1500,
							});
						},
					});
				}
			});
		});

		var rowCount = 1; // Inisialisasi row

		$('#addRow').on('click', function() {
			// Periksa apakah ada input yang kosong di baris sebelumnya
			var previousRow = $('.baris').last();
			var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
			var isEmpty = false;

			inputs.each(function() {
				if ($(this).val().trim() === '') {
					isEmpty = true;
					return false; // Berhenti iterasi jika ditemukan input kosong
				}
			});

			// Jika ada input yang kosong, tampilkan pesan peringatan
			if (isEmpty) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
				});
				return; // Hentikan penambahan baris baru
			}

			// Salin baris terakhir
			var newRow = previousRow.clone();

			// Kosongkan nilai input di baris baru
			newRow.find('input').val('');
			newRow.find('input[name="total[]"]').val('0');
			newRow.find('input[name="jumlah[]"]').val('0');
			newRow.find('input[name="total_amount[]"]').val('0');
			// Show the 'Hapus' button by removing the 'd-none' class
			newRow.find('.hapusRow').removeClass('d-none');
			newRow.find('.brRow').removeClass('d-none');

			// Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
			rowCount++;

			// Tambahkan baris baru setelah baris terakhir
			previousRow.after(newRow);
		});
		$(document).on('change click keyup input paste', 'input[name="jumlah[]"], input[name="total[]"]', function(event) {
			$(this).val(function(index, value) {
				return value.replace(/(?!\.)\D/g, "")
					.replace(/(?<=\..*)\./g, "")
					.replace(/(?<=\.\d\d).*/g, "")
					.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});

			var row = $(this).closest('.baris');

			hitungTotal(row);
			updateTotalBelanja();
			updateTotal();
		});

		// Saat input qty atau harga diubah
		// $(document).on('input', 'input[name="chargeable_weight[]"], input[name="harga[]"], input[name="awb_fee[]"], input[name="jumlah[]"]', function() {
		// $(document).on('input', 'input[name="jumlah[]"], input[name="total[]"]', function() {
		//     var value = $(this).val();
		//     var formattedValue = parseFloat(value.split('.').join(''));
		//     $(this).val(formattedValue);

		//     var row = $(this).closest('.baris');
		//     hitungTotal(row);
		//     updateTotalBelanja();
		//     updateTotal();
		// });

		// // Tambahkan event listener untuk event keyup
		// $(document).on('keyup', 'input[name="jumlah[]"], input[name="total[]"]', function() {
		//     var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
		//     var formattedValue = formatNumber(parseFloat(value.split('.').join('')));
		//     $(this).val(formattedValue);
		//     if (isNaN(value)) { // Jika nilai input kosong
		//         $(this).val(''); // Atur nilai input menjadi 0
		//     }
		//     var row = $(this).closest('.baris');
		//     hitungTotal(row);
		//     updateTotalBelanja();
		//     updateTotal();
		// });

		function hitungTotal(row) {
			var total = row.find('input[name="total[]"]').val().replace(/\,/g, '');
			var jumlah = row.find('input[name="jumlah[]"]').val().replace(/\,/g, '');

			total = (total) || 0;
			jumlah = (jumlah) || 0;

			var total_amount = Number(total) * Number(jumlah);

			row.find('input[name="total_amount[]"]').val(formatNumber(total_amount.toFixed(0)));
			updateTotalBelanja();
		}

		function updateTotalBelanja() {
			var total_pos_fix = 0;

			$(".baris").each(function() {
				var total = $(this).find('input[name="total_amount[]"]').val().replace(/\,/g, ''); // Ambil nilai total dari setiap baris
				total = parseFloat(total); // Ubah string ke angka float

				if (!isNaN(total)) { // Pastikan total adalah angka
					total_pos_fix += total; // Tambahkan nilai total ke total_pos_fix
				}
			});
			$('#nominal').val(formatNumber(total_pos_fix)); // Atur nilai input #total_basic_rate dengan total_basic_rate
		}

		// Tambahkan event listener untuk tombol hapus row
		$(document).on('click', '.hapusRow', function() {
			$(this).closest('.baris').remove();
			updateTotalBelanja(); // Perbarui total belanja setelah menghapus baris
			updateTotal();
		});

		// Saat opsi diskon berubah
		$('#diskon').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotal();
		});
		$('#ppn').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotal();
		});
		$('#opsi_pph').on('change', function() {
			// console.log("tes")
			// updatePPH();
			updateTotal();
		});

		// Fungsi untuk mengupdate besaran diskon dan total
		function updateTotal() {
			var diskon = parseFloat($('#diskon').val());
			var ppn = parseFloat($('#ppn').val());
			var pph = 0.02;
			// var opsi_pph = document.getElementById("opsi_pph").value;
			var besaranpph = parseFloat($('#besaran_pph').val());

			var subtotal = 0;
			// Hitung subtotal dari total setiap baris
			$('.baris').each(function() {
				var totalBaris = parseInt($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, '') || 0);
				subtotal += totalBaris;
			});
			// Hitung besaran diskon
			var besaranDiskon = subtotal * diskon;
			var besaranDiskon = subtotal;
			// Hitung total setelah diskon
			var total = subtotal;

			// Jika opsi_pph dicentang
			if ($('#opsi_pph').is(':checked')) {
				besaranpph = total * pph;
			} else {
				besaranpph = 0;
			}

			// console.log(besaranpph)
			var besaranppn = total * ppn;
			var total_nonpph = total + besaranppn;
			var total_denganpph = total + besaranppn - besaranpph;
			var pendapatan = total - besaranpph;
			var nominal_bayar = total + besaranppn - besaranpph;

			// console.log(subtotal);
			// console.log((ppn));
			// console.log(formatNumber(besaranppn));
			// Atur nilai input besaran_diskon dan total dengan format angka yang sesuai
			$('#besaran_ppn').val(formatNumber(besaranppn.toFixed(0)));
			$('#besaran_pph').val(formatNumber(besaranpph.toFixed(0)));
			$('#besaran_diskon').val(formatNumber(besaranDiskon));
			$('#total_nonpph').val(formatNumber(total_nonpph.toFixed(0)));
			$('#total_denganpph').val(formatNumber(total_denganpph.toFixed(0)));
			$('#nominal_pendapatan').val(formatNumber(pendapatan.toFixed(0)));
			$('#nominal_bayar').val(formatNumber(nominal_bayar.toFixed(0)));
		}

		$('#diskonEdit').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotalEdit();
		});

		function updateTotalEdit() {
			var diskon = parseFloat($('#diskonEdit').val());

			var subtotal = parseInt($('#nominal').val().replace(/\,/g, '') || 0);

			// Hitung besaran diskon
			var besaranDiskon = subtotal * diskon;
			// Hitung total setelah diskon
			var total = subtotal - besaranDiskon;
			// Atur nilai input besaran_diskon dan total dengan format angka yang sesuai
			$('#besaran_diskon').val(formatNumber(besaranDiskon));
			$('#total_nonpph').val(formatNumber(total));
		}

		$('#diskonEdit').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotalEdit();
		});


		$(document).on('input', 'input[name="qty"], input[name="harga"]', function() {
			var value = $(this).val();
			var formattedValue = parseFloat(value.split('.').join(''));
			$(this).val(formattedValue);

			var row = $(this).closest('.baris');
			hitungTotalItem(row);
		});

		function hitungTotalItem(row) {
			var qty = row.find('input[name="qty"]').val().replace(/\,/g, ''); // Hapus tanda titik
			var harga = row.find('input[name="harga"]').val().replace(/\,/g, ''); // Hapus tanda titik
			qty = parseInt(qty); // Ubah string ke angka float
			harga = parseInt(harga); // Ubah string ke angka float

			qty = isNaN(qty) ? 0 : qty;
			harga = isNaN(harga) ? 0 : harga;

			var total = qty * harga;
			row.find('input[name="harga"]').val(formatNumber(harga));
			row.find('input[name="total"]').val(formatNumber(total));
		}

		$('#addNewRow').on('click', function() {
			// Periksa apakah ada input yang kosong di baris sebelumnya
			var previousRow = $('.barisEdit').last();
			var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
			var isEmpty = false;

			inputs.each(function() {
				if ($(this).val().trim() === '') {
					isEmpty = true;
					return false; // Berhenti iterasi jika ditemukan input kosong
				}
			});

			// Jika ada input yang kosong, tampilkan pesan peringatan
			if (isEmpty) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
				});
				return; // Hentikan penambahan baris baru
			}

			// Salin baris terakhir
			var newRow = previousRow.clone();

			// Kosongkan nilai input di baris baru
			newRow.find('input').val('');
			newRow.find('input[name="newHarga[]"]').val('0');

			// Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
			rowCount++;

			// Tambahkan baris baru setelah baris terakhir
			previousRow.after(newRow);
		});


		$(document).on('click', '.hapusRowAddItem', function() {
			$(this).closest('.barisEdit').remove();
		});

		$(document).on('input', 'input[name="newHarga[]"]', function() {
			var value = $(this).val();
			var formattedValue = parseFloat(value.split('.').join(''));
			$(this).val(formattedValue);

			var row = $(this).closest('.barisEdit');
			hitungTotalNewItem(row);
		});

		// Tambahkan event listener untuk event keyup
		$(document).on('keyup', 'input[name="newHarga[]"]', function() {
			var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
			var formattedValue = formatNumber(parseFloat(value.split('.').join('')));
			$(this).val(formattedValue);
			if (isNaN(value)) { // Jika nilai input kosong
				$(this).val(''); // Atur nilai input menjadi 0
			}
			var row = $(this).closest('.barisEdit');
			hitungTotalNewItem(row);
		});

		function hitungTotalNewItem(row) {
			var harga = row.find('input[name="newHarga[]"]').val().replace(/\,/g, ''); //
			harga = parseInt(harga);

			harga = isNaN(harga) ? 0 : harga;

			// var total = qty * harga;
			// row.find('input[name="newTotal[]"]').val(formatNumber(total));
		}

		// Function to clean and convert number strings (e.g., "1.234.567" to 1234567)
		function cleanAndParseNumber(value) {
			// Remove commas (if any) and periods (if they are thousand separators)
			// Then parse as a float
			return parseFloat(value.replace(/\./g, '').replace(/,/g, ''));
		}

		// Use event delegation for inputs with name="nominal_bayar"
		// This handles dynamically added rows as well
		$(document).on('input change', 'input[name="nominal_bayar"]', function() {
			var $this = $(this); // The current nominal_bayar input that changed
			var currentId = $this.attr('id').replace('nominal_bayar', ''); // Extract the dynamic ID (e.g., '1' from 'nominal_bayar1')

			// Get the corresponding piutang and status_bayar elements using the extracted ID
			var $piutangInput = $('#piutang' + currentId);
			var $statusBayarCheckbox = $('#status_bayar' + currentId);

			// Get values and clean them for comparison
			var nominalBayar = cleanAndParseNumber($this.val());
			var piutang = cleanAndParseNumber($piutangInput.val());

			// Compare the values
			if (nominalBayar === piutang && piutang > 0) { // Add check for piutang > 0 to avoid checking if both are 0
				$statusBayarCheckbox.prop('checked', true);
			} else {
				$statusBayarCheckbox.prop('checked', false);
			}
		});

		// --- Your existing addRowInvoice/addRow function would be here ---
		var rowCount = 1; // Inisialisasi row

		$('#addRow').on('click', function() {
			console.log('masuk');
			var previousRow = $('.baris').last();
			var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
			var isEmpty = false;

			inputs.each(function() {
				if ($(this).val().trim() === '' && $(this).attr('name') !== 'total_amount[]') { // Exclude readonly total_amount
					isEmpty = true;
					return false;
				}
			});

			if (isEmpty) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
				});
				return;
			}

			var newRow = previousRow.clone();

			newRow.find('input').val('');
			newRow.find('input[name="total[]"]').val('0');
			newRow.find('input[name="jumlah[]"]').val('0');
			newRow.find('input[name="total_amount[]"]').val('0');
			newRow.find('input[type="checkbox"]').prop('checked', false); // Uncheck cloned checkbox

			newRow.find('.hapusRow').removeClass('d-none');

			previousRow.after(newRow);
			rowCount++;
		});
		// --- End of existing addRowInvoice/addRow function ---

		// Ensure SweetAlert2 and jQuery are loaded before this script.
	});
</script>
<script>
	$(document).ready(function() {
		applyPriceFormat();
	})


	function applyPriceFormat() {
		$('.uang').each(function() {
			new Cleave(this, {
				numeral: true,
				numeralThousandsGroupStyle: 'thousand',
				numeralDecimalMark: ',',
				delimiter: '.',
				prefix: 'Rp ',
				numeralDecimalScale: 2,
				rawValueTrimPrefix: true
			});
		});
	}

	$(document).ready(function() {

		$(document).on('change click keyup input paste', 'input[name="jumlah[]"], input[name="total[]"]', function(event) {
			$(this).val(function(index, value) {
				return value.replace(/(?!\.)\D/g, "")
					.replace(/(?<=\..*)\./g, "")
					.replace(/(?<=\.\d\d).*/g, "")
					.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});

			var row = $(this).closest('.baris');

			hitungTotal(row);
			updateTotalBelanja();
			updateTotal();
		});

		// Saat input qty atau harga diubah
		// $(document).on('input', 'input[name="chargeable_weight[]"], input[name="harga[]"], input[name="awb_fee[]"], input[name="jumlah[]"]', function() {
		// $(document).on('input', 'input[name="jumlah[]"], input[name="total[]"]', function() {
		//     var value = $(this).val();
		//     var formattedValue = parseFloat(value.split('.').join(''));
		//     $(this).val(formattedValue);

		//     var row = $(this).closest('.baris');
		//     hitungTotal(row);
		//     updateTotalBelanja();
		//     updateTotal();
		// });

		// // Tambahkan event listener untuk event keyup
		// $(document).on('keyup', 'input[name="jumlah[]"], input[name="total[]"]', function() {
		//     var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
		//     var formattedValue = formatNumber(parseFloat(value.split('.').join('')));
		//     $(this).val(formattedValue);
		//     if (isNaN(value)) { // Jika nilai input kosong
		//         $(this).val(''); // Atur nilai input menjadi 0
		//     }
		//     var row = $(this).closest('.baris');
		//     hitungTotal(row);
		//     updateTotalBelanja();
		//     updateTotal();
		// });

		function hitungTotal(row) {
			var total = row.find('input[name="total[]"]').val().replace(/\,/g, '');
			var jumlah = row.find('input[name="jumlah[]"]').val().replace(/\,/g, '');

			total = (total) || 0;
			jumlah = (jumlah) || 0;

			var total_amount = Number(total) * Number(jumlah);

			row.find('input[name="total_amount[]"]').val(formatNumber(total_amount.toFixed(0)));
			updateTotalBelanja();
		}

		function updateTotalBelanja() {
			var total_pos_fix = 0;

			$(".baris").each(function() {
				var total = $(this).find('input[name="total_amount[]"]').val().replace(/\,/g, ''); // Ambil nilai total dari setiap baris
				total = parseFloat(total); // Ubah string ke angka float

				if (!isNaN(total)) { // Pastikan total adalah angka
					total_pos_fix += total; // Tambahkan nilai total ke total_pos_fix
				}
			});
			$('#nominal').val(formatNumber(total_pos_fix)); // Atur nilai input #total_basic_rate dengan total_basic_rate
		}

		// Tambahkan event listener untuk tombol hapus row
		$(document).on('click', '.hapusRow', function() {
			$(this).closest('.baris').remove();
			updateTotalBelanja(); // Perbarui total belanja setelah menghapus baris
			updateTotal();
		});

		// Saat opsi diskon berubah
		$('#diskon').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotal();
		});
		$('#ppn').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotal();
		});
		$('#opsi_pph').on('change', function() {
			// console.log("tes")
			// updatePPH();
			updateTotal();
		});

		// Fungsi untuk mengupdate besaran diskon dan total
		function updateTotal() {
			var diskon = parseFloat($('#diskon').val());
			var ppn = parseFloat($('#ppn').val());
			var pph = 0.02;
			// var opsi_pph = document.getElementById("opsi_pph").value;
			var besaranpph = parseFloat($('#besaran_pph').val());

			var subtotal = 0;
			// Hitung subtotal dari total setiap baris
			$('.baris').each(function() {
				var totalBaris = parseInt($(this).find('input[name="total_amount[]"]').val().replace(/\,/g, '') || 0);
				subtotal += totalBaris;
			});
			// Hitung besaran diskon
			var besaranDiskon = subtotal * diskon;
			var besaranDiskon = subtotal;
			// Hitung total setelah diskon
			var total = subtotal;

			// Jika opsi_pph dicentang
			if ($('#opsi_pph').is(':checked')) {
				besaranpph = total * pph;
			} else {
				besaranpph = 0;
			}

			// console.log(besaranpph)
			var besaranppn = total * ppn;
			var total_nonpph = total + besaranppn;
			var total_denganpph = total + besaranppn - besaranpph;
			var pendapatan = total - besaranpph;
			var nominal_bayar = total + besaranppn - besaranpph;

			// console.log(subtotal);
			// console.log((ppn));
			// console.log(formatNumber(besaranppn));
			// Atur nilai input besaran_diskon dan total dengan format angka yang sesuai
			$('#besaran_ppn').val(formatNumber(besaranppn.toFixed(0)));
			$('#besaran_pph').val(formatNumber(besaranpph.toFixed(0)));
			$('#besaran_diskon').val(formatNumber(besaranDiskon));
			$('#total_nonpph').val(formatNumber(total_nonpph.toFixed(0)));
			$('#total_denganpph').val(formatNumber(total_denganpph.toFixed(0)));
			$('#nominal_pendapatan').val(formatNumber(pendapatan.toFixed(0)));
			$('#nominal_bayar').val(formatNumber(nominal_bayar.toFixed(0)));
		}

		$('#diskonEdit').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotalEdit();
		});

		function updateTotalEdit() {
			var diskon = parseFloat($('#diskonEdit').val());

			var subtotal = parseInt($('#nominal').val().replace(/\,/g, '') || 0);

			// Hitung besaran diskon
			var besaranDiskon = subtotal * diskon;
			// Hitung total setelah diskon
			var total = subtotal - besaranDiskon;
			// Atur nilai input besaran_diskon dan total dengan format angka yang sesuai
			$('#besaran_diskon').val(formatNumber(besaranDiskon));
			$('#total_nonpph').val(formatNumber(total));
		}

		$('#diskonEdit').on('change', function() {
			// Panggil fungsi untuk mengupdate besaran diskon dan total
			updateTotalEdit();
		});


		$(document).on('input', 'input[name="qty"], input[name="harga"]', function() {
			var value = $(this).val();
			var formattedValue = parseFloat(value.split('.').join(''));
			$(this).val(formattedValue);

			var row = $(this).closest('.baris');
			hitungTotalItem(row);
		});

		function hitungTotalItem(row) {
			var qty = row.find('input[name="qty"]').val().replace(/\,/g, ''); // Hapus tanda titik
			var harga = row.find('input[name="harga"]').val().replace(/\,/g, ''); // Hapus tanda titik
			qty = parseInt(qty); // Ubah string ke angka float
			harga = parseInt(harga); // Ubah string ke angka float

			qty = isNaN(qty) ? 0 : qty;
			harga = isNaN(harga) ? 0 : harga;

			var total = qty * harga;
			row.find('input[name="harga"]').val(formatNumber(harga));
			row.find('input[name="total"]').val(formatNumber(total));
		}

		$('#addNewRow').on('click', function() {
			// Periksa apakah ada input yang kosong di baris sebelumnya
			var previousRow = $('.barisEdit').last();
			var inputs = previousRow.find('input[type="text"], input[type="datetime-local"]');
			var isEmpty = false;

			inputs.each(function() {
				if ($(this).val().trim() === '') {
					isEmpty = true;
					return false; // Berhenti iterasi jika ditemukan input kosong
				}
			});

			// Jika ada input yang kosong, tampilkan pesan peringatan
			if (isEmpty) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Mohon isi semua input pada baris sebelumnya terlebih dahulu!',
				});
				return; // Hentikan penambahan baris baru
			}

			// Salin baris terakhir
			var newRow = previousRow.clone();

			// Kosongkan nilai input di baris baru
			newRow.find('input').val('');
			newRow.find('input[name="newHarga[]"]').val('0');

			// Perbarui tag <h4> pada baris baru dengan nomor urut yang baru
			rowCount++;

			// Tambahkan baris baru setelah baris terakhir
			previousRow.after(newRow);
		});


		$(document).on('click', '.hapusRowAddItem', function() {
			$(this).closest('.barisEdit').remove();
		});

		$(document).on('input', 'input[name="newHarga[]"]', function() {
			var value = $(this).val();
			var formattedValue = parseFloat(value.split('.').join(''));
			$(this).val(formattedValue);

			var row = $(this).closest('.barisEdit');
			hitungTotalNewItem(row);
		});

		// Tambahkan event listener untuk event keyup
		$(document).on('keyup', 'input[name="newHarga[]"]', function() {
			var value = $(this).val().trim(); // Hapus spasi di awal dan akhir nilai
			var formattedValue = formatNumber(parseFloat(value.split('.').join('')));
			$(this).val(formattedValue);
			if (isNaN(value)) { // Jika nilai input kosong
				$(this).val(''); // Atur nilai input menjadi 0
			}
			var row = $(this).closest('.barisEdit');
			hitungTotalNewItem(row);
		});

		function hitungTotalNewItem(row) {
			var harga = row.find('input[name="newHarga[]"]').val().replace(/\,/g, ''); //
			harga = parseInt(harga);

			harga = isNaN(harga) ? 0 : harga;

			// var total = qty * harga;
			// row.find('input[name="newTotal[]"]').val(formatNumber(total));
		}
	});
</script>

<?php
// Check for success message first, as it's typically the most important
if ($this->session->flashdata('message_name')) {
?>
	<script>
		Swal.fire({
			title: "Success!! ",
			text: '<?= $this->session->flashdata('message_name') ?>',
			icon: "success",
		});
	</script>
<?php
	unset($_SESSION['message_name']);
}
// Then check for error message
else if ($this->session->flashdata('message_error')) {
?>
	<script>
		Swal.fire({
			title: "Error!! ",
			text: '<?= $this->session->flashdata('message_error') ?>',
			icon: "error",
		});
	</script>
<?php
	unset($_SESSION['message_error']);
}
?>
<script>
	$(".btn-process").on("click", function(e) {
		e.preventDefault();
		const href = $(this).attr("href");

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, process it!",
		}).then((result) => {
			if (result.isConfirmed) {
				document.location.href = href;
			}
		});
	});

	function formatNumber(number) {
		// Pisahkan bagian integer dan desimal
		let parts = number.toString().split(",");

		// Format bagian integer dengan pemisah ribuan
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

		// Gabungkan bagian integer dan desimal dengan koma sebagai pemisah desimal
		return parts.join(",");
	}

	function format_angka() {
		var nominal = document.getElementById('input_nominal').value;

		var formattedValue = formatNumber(parseFloat(nominal.split('.').join('')));

		document.getElementById('input_nominal').value = formattedValue;
	}

	$(document).ready(function() {
		function formatState(state, colorAktiva, colorPasiva, signAktiva, signPasiva) {
			if (!state.id) {
				return state.text;
			}

			var color = state.element.dataset.posisi == "AKTIVA" ? colorAktiva : colorPasiva;
			var sign = state.element.dataset.posisi == "AKTIVA" ? signAktiva : signPasiva;

			var $state = $('<p style="background-color: ' + color + ';"><strong style="color: #fff;">' + state.text + ' ' + sign + '</strong></p>');

			return $state;
		};

		function formatStateDebit(state) {
			return formatState(state, '#3f51b5', '#e81f63', '(+)', '(-)');
		}

		function formatStateKredit(state) {
			return formatState(state, '#e81f63', '#3f51b5', '(-)', '(+)');
		}

		$('.coa_debit').each(function() {
			$(this).select2({
				templateSelection: formatStateDebit,
				theme: 'bootstrap4',
			});
		});

		$('.coa_kredit').each(function() {
			$(this).select2({
				templateSelection: formatStateKredit,
				theme: 'bootstrap4',
			});
		});

		$('#neraca_debit').select2({
			// templateResult: formatStateDebit,
			templateSelection: formatStateDebit,
			theme: 'bootstrap4',
		});

		$('#neraca_kredit').select2({
			// templateResult: formatStateKredit,
			templateSelection: formatStateKredit,
			theme: 'bootstrap4',
		});

		$('#neraca_debit, #neraca_kredit').change(function() {
			var debit = $('#neraca_debit').find(":selected").val();
			var kredit = $('#neraca_kredit').find(":selected").val();
			disabledSubmit(debit, kredit);
		});

		function disabledSubmit(debit, kredit) {

			// const inputFieldDisableFinancialEntry = document.getElementById('inputToCheck');
			const warningBoxDisableFinancialEntry = document.getElementById('warningMessage');

			if (debit && kredit) {
				if (debit == kredit) {
					console.log('sama');
					// $('.btn-primary').prop('disabled', true);
					$('#btn-submit').prop('disabled', true);
					warningBoxDisableFinancialEntry.style.display = 'block';
					warningBoxDisableFinancialEntry.innerHTML = '⚠️ **Peringatan!** Nomor COA tidak boleh sama. Silahkan Pilih Nomor COA Lain'; // Optional: Also highlight the input border red for better feedback
					// this.style.borderColor = '#dc3545';
				} else {
					console.log('tidak sama');
					$('#btn-submit').prop('disabled', false);
					// Hide the warning message by setting display to 'none'
					warningBoxDisableFinancialEntry.style.display = 'none';

					// Reset input field border
					// this.style.borderColor = '';
					warningBoxDisableFinancialEntry.innerHTML = '';
				}
			}
		}
	});

	$('#add-row').click(function() {
		let newRow = `
            <tr>
                <td>
                    <select name="accounts[]" class="form-control select2" style="width: 100%" required>
                        <option value="">:: Pilih akun</option>
                        <?php foreach ($coa as $c) : ?>
                            <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control uang nominal-input" name="nominals[]" placeholder="Nominal" required>
                </td>
            </tr>`;
		$('#journal-entries').append(newRow);
		$('.select2').select2({
			theme: 'bootstrap4',
		});
		applyPriceFormat();
		// $('.uang').mask('000.000.000.000.000', {
		//   reverse: true
		// });
	});

	function applyPriceFormat() {
		$('.uang').each(function() {
			new Cleave(this, {
				numeral: true,
				numeralThousandsGroupStyle: 'thousand',
				numeralDecimalMark: ',',
				delimiter: '.',
				prefix: 'Rp ',
				numeralDecimalScale: 2,
				rawValueTrimPrefix: true
			});
		});
	}

	function upload_fe() {
		const ttlnamaValue = $('#format_data').val();


		if (!ttlnamaValue) {
			swal.fire({
				customClass: 'slow-animation',
				icon: 'error',
				showConfirmButton: false,
				title: 'Kolom File Tidak Boleh Kosong',
				timer: 1500
			});
		} else {
			const swalWithBootstrapButtons = Swal.mixin({
				customClass: {
					InputEvent: 'form-control',
					confirmButton: 'btn btn-primary',
					cancelButton: 'btn btn-danger'
				},
				buttonsStyling: false
			})

			swalWithBootstrapButtons.fire({
				title: 'Ingin Menambahkan Data?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Tambahkan',
				cancelButtonText: 'Tidak',
				reverseButtons: true
			}).then((result) => {

				if (result.isConfirmed) {

					var url;
					var formData;
					url = "<?php echo site_url('Financial/upload_financial_entry') ?>";

					// window.location = url_base;
					var formData = new FormData($("#upload_file_fe")[0]);
					let accumulatedResponse = ""; // Variable to accumulate the response

					$.ajax({
						url: url,
						type: "POST",
						dataType: "text", // Change to 'text' to handle server-sent events
						data: formData,
						contentType: false,
						processData: false,
						beforeSend: function() {
							// Show the progress dialog before sending the request
							Swal.fire({
								title: 'Uploading...',
								html: `
                <progress id="progressBar" value="0" max="100" style="width: 100%;"></progress>
                <div id="progressText" style="margin-top: 10px; font-weight: bold;">0/0 Data</div>
            `,
								allowOutsideClick: false,
								showConfirmButton: false
							});
						},
						xhrFields: {
							onprogress: function(e) {
								// Read the response text for progress updates
								accumulatedResponse += e.currentTarget.responseText; // Accumulate responses

								var response = e.currentTarget.responseText.trim().split('\n');

								// Loop through each line to find progress data
								response.forEach(function(line) {
									try {
										var progressData = JSON.parse(line.replace("data: ", ""));
										if (progressData.progress) {
											$("#progressBar").val(progressData.progress);
											$("#progressText").text(`${progressData.currentRow}/${progressData.totalRows} Data`);
										}
									} catch (error) {
										console.error("Error parsing progress data:", error);
									}
								});
							},
						},
						success: function(data) {
							try {
								// Attempt to parse the final response
								var finalResponse = JSON.parse(accumulatedResponse.trim().split('\n').pop()); // Get the last line which should be the status
								console.log("Response data:", finalResponse); // Log final response to see its structure
								if (finalResponse.status) {
									const noDebitRows = finalResponse.no_debit_rows ? finalResponse.no_debit_rows.join(', ') : 'Tidak ada';
									const noKreditRows = finalResponse.no_kredit_rows ? finalResponse.no_kredit_rows.join(', ') : 'Tidak ada';

									// document.getElementById('rumahadat').reset();
									// $('#add_modal').modal('hide');
									(JSON.stringify(data));
									// alert(data)
									// swal.fire({
									//   customClass: 'slow-animation',
									//   icon: 'success',
									//   showConfirmButton: false,
									//   title: 'Berhasil Menambahkan Data',
									//   timer: 3000
									// });
									// document.getElementById('upload_file_fe').reset(); // Reset the form
									// $('#upload_modal').modal('hide'); // Hide the modal
									// location.reload();
									Swal.fire({
										icon: 'success',
										title: 'Proses Selesai',
										html: `
                                        <b>Berhasil:</b> ${finalResponse.success_count || 0} Data<br>
                                        <b>COA Debit Tidak Ditemukan:</b> ${finalResponse.no_debit_rows.length || 0} Data<br>
                                        (Baris: ${noDebitRows})<br><br>
                                        <b>COA Kredit Tidak Ditemukan:</b> ${finalResponse.no_kredit_rows.length || 0} Data<br>
                                        (Baris: ${noKreditRows})
                                    `,
										showConfirmButton: true,
										allowOutsideClick: true
									}).then(() => {
										document.getElementById('upload_file_fe').reset();
										$('#upload_modal').modal('hide');
										// location.reload();
									});

								} else {

									swal.fire('Gagal menyimpan data', 'error');
								}
							} catch (error) {
								// If parsing fails, log the error
								console.error("Error parsing final response:", error);
								// swal.fire('Gagal menyimpan data', 'error');
								swal.fire('Gagal menyimpan data', 'Terjadi kesalahan pada respons server.', 'error');
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							swal.fire('Operation Failed!', errorThrown, 'error');
						},
						complete: function() {
							console.log('Editing job done');
						}
					});


				}

			})
		}
	}
</script>

<script>
	$(document).ready(function() {
		$(document).on('click', '.arus_kas', function() {
			var id = $(this).data('id');

			$('#detailModal2 .modal-title').text('Arus kas ' + id);
			// $('#detailModal2 .modal-body').html(id);
			$('#detailModal2 input[name="no_coa"]').val(id);
			$('#detailModal2').modal('show');
		});
	});
</script>
<script>
	$(document).ready(function() {
		<?php foreach ($invoices as $i) : ?>
				(function() {
					var invoiceId = '<?= $i['Id'] ?>';

					// Event listener saat modal ditampilkan
					$('#modal<?= $i['Id'] ?>').on('shown.bs.modal', function() {
						var checkbox = $('#status_bayar' + invoiceId);
						var nominalBayarInput = $('#nominal_bayar' + invoiceId);
						var piutangElement = $('#piutang' + invoiceId);

						if (piutangElement.length > 0 && piutangElement.val() !== '') {
							// Parse format Indonesia: 1.234.567,00 -> 1234567.00
							// Parse format Indonesia: Rp1.234.567,00 -> 1234567.00
							var piutangStr = piutangElement.val()
								.replace(/Rp/g, '') // Hapus "Rp"
								.replace(/\s/g, '') // Hapus spasi
								.replace(/\./g, '') // Hapus titik ribuan
								.replace(',', '.'); // Ganti koma jadi titik
							var piutang = parseFloat(piutangStr);

							// Validasi parsing berhasil
							if (isNaN(piutang)) {
								console.error('Failed to parse piutang:', piutangElement.val());
								piutang = 0;
							}

							// Fungsi untuk format angka ke format Indonesia
							function formatRupiah(angka) {
								return angka.toLocaleString('id-ID', {
									minimumFractionDigits: 0,
									maximumFractionDigits: 0
								});
							}

							// Fungsi untuk memperbarui nilai nominal bayar
							function updateNominalBayar() {
								if (checkbox.is(':checked')) {
									nominalBayarInput.val(formatRupiah(piutang)).attr('readonly', true);
								} else {
									nominalBayarInput.val('0').attr('readonly', false);
								}
							}

							// Inisialisasi
							updateNominalBayar();

							// Event handler untuk checkbox
							checkbox.on('click', function() {
								updateNominalBayar();
							});

							// Event handler untuk input nominal bayar
							nominalBayarInput.on('input', function() {
								// Parse input: hapus titik ribuan, ganti koma jadi titik
								var value = $(this).val().replace(/\./g, '').replace(',', '.');

								if (value === '' || isNaN(parseFloat(value))) {
									$(this).val('0');
								} else {
									value = parseFloat(value);
									if (value > piutang) {
										alert('Nilai nominal bayar tidak boleh lebih dari piutang.');
										$(this).val(formatRupiah(piutang));
									} else {
										$(this).val(formatRupiah(value));
									}
								}
							});
						}
					});
				})();
		<?php endforeach; ?>
	});
</script>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const isPremium = <?php echo json_encode($this->session->userdata('is_premium')); ?>;
		const upgradeUrl = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed

		function showPremiumDeniedSwal() {
			Swal.fire({
				title: 'Siap Menjadi Raja <?= '<img src="' . base_url() . 'assets/icons/sword_gray.png" alt="Sword Icon" width="32" height="32">' ?>', // New title: "Ready to Become King?"
				html: 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..', // New text with HTML for emphasis
				icon: 'warning', // IMPORTANT: Set icon to undefined or remove it if you're using iconHtml
				iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>', // Changed icon to question, suitable for asking a choice
				confirmButtonText: 'Ambil Mahkota Sekarang!', // New confirm button text: "Take the Crown Now!"
				showCancelButton: true,
				cancelButtonText: 'Nanti Saja, Belum Siap Jadi Raja', // New cancel button text: "Later, Not Ready to Be King Yet"
				customClass: {
					confirmButton: 'btn btn-primary', // Optional: Use your custom btn-pink class for the confirm button
					cancelButton: 'btn btn-pink' // Optional: Style the cancel button differently
				},
				buttonsStyling: false // Important if you use customClass for buttons
			}).then((result) => {
				if (result.isConfirmed) {
					// Optional: Redirect to an upgrade page if 'Ambil Mahkota Sekarang!' is clicked
					window.location.href = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed
				}
			});
		}

		// 1. Upload Data Button
		const uploadDataBtn = document.getElementById('uploadDataBtn');
		if (uploadDataBtn) {
			uploadDataBtn.addEventListener('click', function(event) {
				if (!isPremium) {
					event.preventDefault(); // Prevent modal from opening
					showPremiumDeniedSwal();
				} else {
					// If premium, manually trigger the modal (since data-target is removed)
					// Or keep data-target and just rely on the if(!isPremium) block to prevent it.
					// If you remove data-target, you need jQuery or plain JS to show modal:
					// $('#upload_modal').modal('show'); // If using jQuery
					// new bootstrap.Modal(document.getElementById('upload_modal')).show(); // If using Bootstrap 5 without jQuery
					// For Bootstrap 4 with jQuery (common with data-toggle/data-target):
					$('#upload_modal').modal('show');
				}
			});
		}

		// 2. Dropdown Items (Multi Kredit, Multi Debit)
		const premiumCheckLinks = document.querySelectorAll('.dropdown-item.premium-check');
		premiumCheckLinks.forEach(link => {
			link.addEventListener('click', function(event) {
				if (!isPremium) {
					event.preventDefault(); // Prevent navigation
					showPremiumDeniedSwal();
				} else {
					// If premium, proceed to the URL stored in data-target-url
					window.location.href = this.dataset.targetUrl;
				}
			});
		});

		// Optional: Disable the dropdown links entirely if not premium (visual cue)
		if (!isPremium) {
			premiumCheckLinks.forEach(link => {
				// link.classList.add('disabled'); // Add a 'disabled' class (requires CSS for styling)
				// link.style.pointerEvents = 'none'; // Further prevent clicks
			});
		}


		// Add this new block for the download button
		const downloadFormatBtn = document.getElementById('downloadFormatBtn');

		if (downloadFormatBtn) {
			downloadFormatBtn.addEventListener('click', function(event) {
				// Assume 'isPremium' is a global JavaScript variable set in your view
				const isPremium = <?= $this->session->userdata('is_premium') ? 'true' : 'false' ?>;

				if (!isPremium) {
					event.preventDefault(); // Stop the link from navigating
					showPremiumDeniedSwal(); // Show the premium message
				} else {
					// If the user is premium, trigger the download programmatically
					// Create a temporary link element to trigger the download
					const link = document.createElement('a');
					link.href = '<?= base_url('src/format/format_data.xls') ?>';
					link.download = 'format_data.xls';
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				}
			});
		}
	});
	<?php if ($this->session->flashdata('swal_message')) : ?>
		const swalConfig = <?php echo json_encode($this->session->flashdata('swal_message')); ?>;

		// Remove the redirectUrl from swalConfig as it's handled separately
		const redirectUrl = swalConfig.redirectUrl || null;
		delete swalConfig.redirectUrl; // Clean up the config

		Swal.fire(swalConfig).then((result) => {
			if (result.isConfirmed && redirectUrl) {
				window.location.href = redirectUrl;
			}
		});
	<?php endif; ?>

	// If you were *not* redirecting and passing $data['swal_message'] directly:
	<?php
	/*
        if (isset($swal_message)) : ?>
            const swalConfig = <?php echo json_encode($swal_message); ?>;
            const redirectUrl = swalConfig.redirectUrl || null;
            delete swalConfig.redirectUrl;

            Swal.fire(swalConfig).then((result) => {
                if (result.isConfirmed && redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        <?php endif;
        */
	?>

	$(document).ready(function() {
		var table = $('#table-template').DataTable({
			responsive: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			processing: true,
			serverSide: true,
			ajax: {
				url: "<?php echo site_url('financial/ajax_template_coa_list') ?>",
				type: "POST"
			},
			order: [],
			iDisplayLength: 10,
			columnDefs: [{
					targets: [-1], // Target the last column (which will be our new action column)
					orderable: false, // Make this column not sortable
				},
				{
					targets: [-2], // Target the second to last column
					orderable: false
				}
				// Add more column definitions as needed for other columns
			],
			layout: {
				topStart: 'pageLength', // Place the length dropdown in the top-left
				topEnd: [
					'search', // Place the search input
					{
						buttons: [{
							text: 'Ambil Semua',
							className: 'btn btn-pink',
							action: function(e, dt, button, config) {
								Swal.fire({
									title: 'Apakah Anda yakin?',
									text: "Anda akan meng-ambil semua coa yang tersedia.",
									icon: 'warning',
									showCancelButton: true,
									confirmButtonColor: '#3085d6',
									cancelButtonColor: '#d33',
									confirmButtonText: 'Ya, Tambahkan!',
									cancelButtonText: 'Batal'
								}).then((result) => {
									// Check if the user clicked the "Confirm" button (Ya, Tambahkan!)
									if (result.isConfirmed) {
										Swal.fire({
											title: 'Mohon Tunggu',
											text: "Proses...",
											allowOutsideClick: false, // Prevent closing by clicking outside
											allowEscapeKey: false, // Prevent closing by pressing Escape
											didOpen: () => {
												Swal.showLoading(); // Show a loading spinner
											}
										});

										// Proceed with your action, like redirecting
										window.location = '<?= base_url('financial/ambil_semua_coa') ?>';
									}
									// If result.isDismissed is true (user clicked cancel, outside, or pressed escape),
									// then no further action is taken.
								});

								// showLoading();
								// form.submit();
							},
							init: function(api, node, config) {
								$(node).removeClass('dt-button')
							},
							attr: {
								// title: 'Copy',
								id: 'btn-ambil-semua'
							}

						}]
					}
				],
				bottomStart: 'info', // Place table information (showing X of Y entries) in the bottom-left
				bottomEnd: 'paging' // Place pagination controls in the bottom-right
			},

		});

		// --- AJAX Submission Logic ---
		// Use event delegation because table rows are added dynamically by DataTables AJAX
		$('#table-template tbody').on('click', '.submit-coa-btn', function() {
			var $button = $(this); // The clicked "Buat" button
			var $row = $button.closest('tr'); // The parent row of the button

			// Retrieve data from the row
			var no_bb = $row.find('span[data-no_bb]').data('no_bb');
			var no_sbb = $row.find('span[data-no_sbb]').data('no_sbb');
			var nama_coa = $row.find('span[data-nama_coa]').data('nama_coa');
			var saldo_awal = $row.find('.uang').val();

			// Optional: Remove currency formatting if 'uang' class adds it
			// If your 'uang' class uses a library like autoNumeric or cleave.js
			// you might need to get the raw numeric value.
			// For simple formatting, you might just remove commas/dots.
			saldo_awal = saldo_awal.replace(/[^0-9,-]+/g, "").replace(",", "."); // Example: remove non-numeric except comma/dot, change comma to dot for float parsing

			// Create the data object to send
			var postData = {
				no_bb: no_bb,
				no_sbb: no_sbb,
				nama_coa: nama_coa,
				saldo_awal: saldo_awal
			};

			// Disable button to prevent multiple clicks while processing
			$button.prop('disabled', true).text('Saving...');

			// Perform AJAX request
			$.ajax({
				url: "<?php echo site_url('financial/tambahCoaAjax') ?>", // Your target URL
				type: "POST",
				data: postData,
				dataType: "json", // Expecting JSON response from the server
				success: function(response) {
					if (response.status === 'success') {
						// Update UI: e.g., change button to "Saved" or disable the row
						$button.removeClass('btn-primary').addClass('btn-primary').text('Saved!');
						// Optionally disable the saldo_awal input as well
						$row.find('.saldo-awal-input').prop('disabled', true);
						// table.ajax.reload(null, false); // If you want to refresh the table without resetting pagination
						// alert('COA added successfully!'); // Or use a nicer notification
						Swal.fire({
							icon: "success",
							title: "Berhasil",
							html: `${response.msg}`,
							showConfirmButton: false,
							timer: 1500,
						}).then(function() {
							Swal.close();
							location.href = `${response.reload}`
						});
					} else {
						$button.removeClass('btn-primary').addClass('btn-danger').text('Failed');
						// alert('Error: ' + response.message);
						Swal.fire({
							icon: "error",
							title: "Gagal",
							html: `${response.msg}`,
							showConfirmButton: false,
							timer: 1500,
						}).then(function() {
							Swal.close();
							location.href = `${response.reload}`
						});
					}
				},
				error: function(xhr, status, error) {
					// Handle AJAX error
					$button.removeClass('btn-primary').addClass('btn-danger').text('Error');
					Swal.fire({
						icon: "error",
						title: "Gagal",
						html: 'An error occurred: ' + error,
						showConfirmButton: false,
						timer: 1500,
					}).then(function() {
						Swal.close();
						location.href = `${response.reload}`
					});
					alert('An error occurred: ' + error);
					console.error("AJAX Error: ", status, error, xhr.responseText);
				},
				complete: function() {
					// Re-enable the button if it's not permanently disabled by success/fail
					if (!$button.hasClass('btn-primary') && !$button.hasClass('btn-danger')) {
						$button.prop('disabled', false).text('Buat');
					}
				}
			});
		});

	});

	$(document).ready(function() {
		var table = $('#table-template-2').DataTable({
			responsive: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			processing: true,
			serverSide: true,
			ajax: {
				url: "<?php echo site_url('financial/ajax_template_coa_bb_list') ?>",
				type: "POST"
			},
			order: [],
			iDisplayLength: 10,
			columnDefs: [{
					targets: [-1], // Target the last column (which will be our new action column)
					orderable: false, // Make this column not sortable
				},
				{
					targets: [-2], // Target the second to last column
					orderable: false
				}
				// Add more column definitions as needed for other columns
			],
			layout: {
				topStart: 'pageLength', // Place the length dropdown in the top-left
				topEnd: [
					'search', // Place the search input
					{
						buttons: [{
							text: 'Ambil Semua',
							className: 'btn btn-pink',
							action: function(e, dt, button, config) {
								Swal.fire({
									title: 'Apakah Anda yakin?',
									text: "Anda akan meng-ambil semua coa BB yang tersedia.",
									icon: 'warning',
									showCancelButton: true,
									confirmButtonColor: '#3085d6',
									cancelButtonColor: '#d33',
									confirmButtonText: 'Ya, Tambahkan!',
									cancelButtonText: 'Batal'
								}).then((result) => {
									// Check if the user clicked the "Confirm" button (Ya, Tambahkan!)
									if (result.isConfirmed) {
										Swal.fire({
											title: 'Mohon Tunggu',
											text: "Proses...",
											allowOutsideClick: false, // Prevent closing by clicking outside
											allowEscapeKey: false, // Prevent closing by pressing Escape
											didOpen: () => {
												Swal.showLoading(); // Show a loading spinner
											}
										});

										// Proceed with your action, like redirecting
										window.location = '<?= base_url('financial/ambil_semua_coa_bb') ?>';
									}
									// If result.isDismissed is true (user clicked cancel, outside, or pressed escape),
									// then no further action is taken.
								});
							},
							init: function(api, node, config) {
								$(node).removeClass('dt-button')
							},
							attr: {
								// title: 'Copy',
								id: 'btn-ambil-semua-bb'
							}
						}]
					}
				],
				bottomStart: 'info', // Place table information (showing X of Y entries) in the bottom-left
				bottomEnd: 'paging' // Place pagination controls in the bottom-right
			}
			// Corrected DOM structure to ensure elements appear only once


		});

		// --- AJAX Submission Logic ---
		// Use event delegation because table rows are added dynamically by DataTables AJAX
		$('#table-template-2 tbody').on('click', '.submit-coa-bb-btn', function() {
			var $button = $(this); // The clicked "Buat" button
			var $row = $button.closest('tr'); // The parent row of the button

			// Retrieve data from the row
			var no_bb = $row.find('span[data-no_bb]').data('no_bb');
			var nama_coa = $row.find('span[data-nama_coa]').data('nama_coa');

			// Optional: Remove currency formatting if 'uang' class adds it
			// If your 'uang' class uses a library like autoNumeric or cleave.js
			// you might need to get the raw numeric value.
			// For simple formatting, you might just remove commas/dots.

			// Create the data object to send
			var postData = {
				no_bb: no_bb,
				nama_coa: nama_coa,
			};

			// Disable button to prevent multiple clicks while processing
			$button.prop('disabled', true).text('Saving...');

			// Perform AJAX request
			$.ajax({
				url: "<?php echo site_url('financial/tambahCoaBBAjax') ?>", // Your target URL
				type: "POST",
				data: postData,
				dataType: "json", // Expecting JSON response from the server
				success: function(response) {
					if (response.status === 'success') {
						// Update UI: e.g., change button to "Saved" or disable the row
						$button.removeClass('btn-primary').addClass('btn-success').text('Saved!');
						// Optionally disable the saldo_awal input as well
						$row.find('.saldo-awal-input').prop('disabled', true);
						// table.ajax.reload(null, false); // If you want to refresh the table without resetting pagination
						// alert('COA added successfully!'); // Or use a nicer notification
						Swal.fire({
							icon: "success",
							title: "Berhasil",
							html: `${response.msg}`,
							showConfirmButton: false,
							timer: 1500,
						}).then(function() {
							Swal.close();
							location.href = `${response.reload}`
						});
					} else {
						$button.removeClass('btn-primary').addClass('btn-danger').text('Failed');
						// alert('Error: ' + response.message);
						Swal.fire({
							icon: "error",
							title: "Gagal",
							html: `${response.msg}`,
							showConfirmButton: false,
							timer: 1500,
						}).then(function() {
							Swal.close();
							location.href = `${response.reload}`
						});
					}
				},
				error: function(xhr, status, error) {
					// Handle AJAX error
					$button.removeClass('btn-primary').addClass('btn-danger').text('Error');
					Swal.fire({
						icon: "error",
						title: "Gagal",
						html: 'An error occurred: ' + error,
						showConfirmButton: false,
						timer: 1500,
					}).then(function() {
						Swal.close();
						location.href = `${response.reload}`
					});
					alert('An error occurred: ' + error);
					console.error("AJAX Error: ", status, error, xhr.responseText);
				},
				complete: function() {
					// Re-enable the button if it's not permanently disabled by success/fail
					if (!$button.hasClass('btn-success') && !$button.hasClass('btn-danger')) {
						$button.prop('disabled', false).text('Buat');
					}
				}
			});
		});


	});

	function onEdit(no_sbb, id_cabang) {
		$('#updateCoaForm')[0].reset(); // reset form on modals
		// $('.form-group').removeClass('has-error'); // clear error class
		// $('.help-block').empty(); // clear error string
		// $('.modal-title').text('Edit Poster');
		console.log(no_sbb);
		$.ajax({
			url: "<?php echo site_url('financial/ajax_edit_coa') ?>/" + no_sbb + "/" + id_cabang,
			type: "POST",
			dataType: "JSON",
			success: function(response) {
				var coaEntry = response.coa_data;
				var data = response.data;

				console.log(response);

				JSON.stringify(data.id);
				// alert(JSON.stringify(data));

				$('#update_table_coa').val(coaEntry.table_source)
				$('#update_id_coa').val(data.id);
				if (coaEntry.table_source == "t_coa_sbb") {
					$('#update_no_bb').val(data.no_bb);
					$('#update_no_sbb').val(data.no_sbb);
				} else {

					$('#update_no_bb').val(data.no_lr_bb);
					$('#update_no_sbb').val(data.no_lr_sbb);
				}
				$('#update_nama_perkiraan').val(data.nama_perkiraan);
				$('#update_nominal').val(data.nominal);


				$('#updateCoaModal').modal('show'); // show bootstrap modal when complete loaded

			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error get data from ajax');
			}
		});
	}

	function onEditTanpaSaldo(no_sbb, id_cabang) {
		$('#updateCoaFormTanpaSaldo')[0].reset(); // reset form on modals
		// $('.form-group').removeClass('has-error'); // clear error class
		// $('.help-block').empty(); // clear error string
		// $('.modal-title').text('Edit Poster');

		$.ajax({
			url: "<?php echo site_url('financial/ajax_edit_coa') ?>/" + no_sbb + "/" + id_cabang,
			type: "POST",
			dataType: "JSON",
			success: function(response) {
				var coaEntry = response.coa_data;
				var data = response.data;

				console.log(response);

				JSON.stringify(data.id);
				// alert(JSON.stringify(data));

				$('#update_table_coa_tanpa_saldo').val(coaEntry.table_source)
				$('#update_id_coa_tanpa_saldo').val(data.id);
				if (coaEntry.table_source == "t_coa_sbb") {
					$('#update_no_bb_tanpa_saldo').val(data.no_bb);
					$('#update_no_sbb_tanpa_saldo').val(data.no_sbb);
				} else {

					$('#update_no_bb_tanpa_saldo').val(data.no_lr_bb);
					$('#update_no_sbb_tanpa_saldo').val(data.no_lr_sbb);
				}
				$('#update_nama_perkiraan_tanpa_saldo').val(data.nama_perkiraan);
				$('#update_nominal_tanpa_saldo').val(data.nominal);
				if (data.nominal == 0) {
					// KONDISI 1: Nominal 0
					// readOnly = false (dapat diisi) => Ini yang Anda inginkan (DELETE read only)
					document.getElementById("update_no_bb_tanpa_saldo").readOnly = false;
					document.getElementById("update_no_sbb_tanpa_saldo").readOnly = false;
				} else {
					// KONDISI 2: Nominal > 0
					// readOnly = true (tidak dapat diisi) => Input TIDAK BOLEH diedit
					document.getElementById("update_no_bb_tanpa_saldo").readOnly = true;
					document.getElementById("update_no_sbb_tanpa_saldo").readOnly = true;
				}
				$('#updateCoaTanpaSaldoModal').modal('show'); // show bootstrap modal when complete loaded

			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error get data from ajax');
			}
		});
	}

	function onEditBB(no_bb, id_cabang) {
		$('#updateCoaFormTanpaSaldo')[0].reset(); // reset form on modals
		// $('.form-group').removeClass('has-error'); // clear error class
		// $('.help-block').empty(); // clear error string
		// $('.modal-title').text('Edit Poster');

		$.ajax({
			url: "<?php echo site_url('financial/ajax_edit_coa_bb') ?>/" + no_bb + "/" + id_cabang,
			type: "POST",
			dataType: "JSON",
			success: function(response) {
				var coaEntry = response.coa_data;
				var data = response.data;

				console.log(response);

				// JSON.stringify(data.id);
				// alert(JSON.stringify(data));

				$('#updatebb_table_coa').val(coaEntry.table_source)
				$('#updatebb_id_coa').val(data.id);
				if (coaEntry.table_source == "t_coa_bb") {
					console.log(coaEntry.no_bb);
					$('#updatebb_no_bb').val(coaEntry.no_bb);
				} else {
					console.log(coaEntry.no_lr_bb);
					$('#updatebb_no_bb').val(coaEntry.no_lr_bb);
				}
				$('#updatebb_nama_perkiraan').val(data.nama_perkiraan);
				$('#updatebb_nominal').val(data.nominal);
				// KONDISI 2: Nominal > 0
				// readOnly = true (tidak dapat diisi) => Input TIDAK BOLEH diedit
				document.getElementById("updatebb_no_bb").readOnly = true;
				$('#updateCoaBB').modal('show'); // show bootstrap modal when complete loaded

			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error get data from ajax');
			}
		});
	}


	function terbilang(angka) {
		if (typeof angka !== 'number') {
			angka = parseFloat(angka);
		}

		const bilangan = [
			'',
			'Satu',
			'Dua',
			'Tiga',
			'Empat',
			'Lima',
			'Enam',
			'Tujuh',
			'Delapan',
			'Sembilan',
			'Sepuluh',
			'Sebelas'
		];

		if (angka < 12) {
			return bilangan[angka];
		} else if (angka < 20) {
			return bilangan[angka - 10] + ' Belas';
		} else if (angka < 100) {
			return bilangan[Math.floor(angka / 10)] + ' Puluh ' + bilangan[angka % 10];
		} else if (angka < 200) {
			return 'Seratus ' + terbilang(angka - 100);
		} else if (angka < 1000) {
			return bilangan[Math.floor(angka / 100)] + ' Ratus ' + terbilang(angka % 100);
		} else if (angka < 2000) {
			return 'Seribu ' + terbilang(angka - 1000);
		} else if (angka < 1000000) {
			return terbilang(Math.floor(angka / 1000)) + ' Ribu ' + terbilang(angka % 1000);
		} else if (angka < 1000000000) {
			return terbilang(Math.floor(angka / 1000000)) + ' Juta ' + terbilang(angka % 1000000);
		} else {
			return 'Angka terlalu besar';
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('saldoawalForm');
		const totalAktivaStrong = document.getElementById('total_aktiva');
		const totalPasivaStrong = document.getElementById('total_pasiva'); // New: Target for total_pasiva
		const balanceResultDiv = document.getElementById('balanceResult');
		const balanceResultDiv2 = document.getElementById('balanceResult2');

		/**
		 * Parses a number string, removing common formatting like commas.
		 * @param {string} numStr The number string to parse.
		 * @returns {number} The parsed float, or 0 if parsing fails.
		 */
		function parseFormattedNumber(numStr) {
			if (!numStr) return 0;
			// Remove commas and ensure it's a valid number string
			const cleanStr = numStr.replace(/,/g, '');
			const parsed = parseFloat(cleanStr);
			return isNaN(parsed) ? 0 : parsed;
		}

		function updateBalanceDisplay() {
			// Get sum_aktiva from the strong tag's text content
			const sumAktiva = parseFormattedNumber(totalAktivaStrong ? totalAktivaStrong.textContent : '0');
			// Get sum_pasiva from its strong tag's text content
			const sumPasiva = parseFormattedNumber(totalPasivaStrong ? totalPasivaStrong.textContent : '0');
			const difference = sumAktiva - sumPasiva;

			if (balanceResultDiv && balanceResultDiv2) {
				balanceResultDiv.classList.remove('hidden', 'balanced', 'unbalanced');
				balanceResultDiv2.classList.remove('hidden', 'balanced', 'unbalanced');

				if (difference === 0) {
					balanceResultDiv.textContent = 'Saldo Seimbang: 0';
					balanceResultDiv.classList.add('balanced');
					balanceResultDiv2.textContent = 'Saldo Seimbang: 0';
					balanceResultDiv2.classList.add('balanced');

				} else {
					// balanceResultDiv.textContent = `Saldo Tidak Seimbang: ${difference.toFixed(2)}`; // Display difference with 2 decimal places
					// balanceResultDiv.classList.add('unbalanced');

					// balanceResultDiv2.textContent = `Saldo Tidak Seimbang: ${difference.toFixed(2)}`; // Display difference with 2 decimal places
					// balanceResultDiv2.classList.add('unbalanced');

					const formattedDifference = difference.toLocaleString('id-ID', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});

					// balanceResultDiv.textContent = `Sebelum Menyimpan, Pastikan Posisi Aktiva dan Pasiva Seimbang, Selisih Sekarang: ${formattedDifference} (${terbilang(difference)})`;
					balanceResultDiv.textContent = `Sebelum Menyimpan, Pastikan Posisi Aktiva dan Pasiva Harus Seimbang.`;
					balanceResultDiv.classList.add('unbalanced');

					// balanceResultDiv2.textContent = `Sebelum Menyimpan, Pastikan Posisi Aktiva dan Pasiva Seimbang, Selisih Sekarang: ${formattedDifference} (${terbilang(difference)})`;
					balanceResultDiv2.textContent = `Sebelum Menyimpan, Pastikan Posisi Aktiva dan Pasiva Harus Seimbang.`;
					balanceResultDiv2.classList.add('unbalanced');

				}
			}
		}

		// Since both Aktiva and Pasiva are now static display elements,
		// the balance updates only on initial load. If these values
		// change dynamically on your page, you would need to call
		// updateBalanceDisplay() whenever they are updated.

		// Initial display update when the page loads
		updateBalanceDisplay();


		form.addEventListener('submit', function(event) {
			// IMPORTANT: This line prevents the default browser form submission,
			// allowing JavaScript to handle the validation first.
			event.preventDefault();

			// Get values from the strong tags and convert to numbers.
			const sumAktiva = parseFormattedNumber(totalAktivaStrong ? totalAktivaStrong.textContent : '0');
			const sumPasiva = parseFormattedNumber(totalPasivaStrong ? totalPasivaStrong.textContent : '0');

			const difference = sumAktiva - sumPasiva;

			if (difference !== 0) {
				// Saldo is not balanced: Show an error alert
				Swal.fire({
					icon: 'error',
					title: 'Saldo Tidak Seimbang',
					text: 'Jumlah Aktiva dan Pasiva harus seimbang (selisih 0) sebelum melanjutkan.',
					confirmButtonColor: '#4f46e5'
				});
			} else {
				// Saldo is balanced: Show a success alert and then redirect
				Swal.fire({
					icon: 'success',
					title: 'Saldo Seimbang!',
					text: 'Sedang membuat Saldo Awal...',
					showConfirmButton: false,
					timer: 2000, // Close after 2 seconds
					timerProgressBar: true
				}).then(() => {
					// After the alert, perform the relocation.
					// This effectively "submits" the form by navigating to its action URL.
					window.location.href = form.action;
					// If you needed to send the form data via a POST request,
					// you would use: form.submit();
				});
			}
		});
	});
</script>