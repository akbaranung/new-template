<script>
  $(document).ready(function() {
    applyPriceFormat();
  })

  $(document).ready(function() {
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
  });

  <?php
  if ($this->session->flashdata('message_error')) {
  ?>
    Swal.fire({
      title: "Error!! ",
      text: '<?= $this->session->flashdata('message_error') ?>',
      type: "error",
      icon: "error",
    });
  <?php
    // $this->session->sess_destroy('message_error');
    unset($_SESSION['message_error']);
  } ?>

  <?php
  if ($this->session->flashdata('message_name')) {
  ?>
    Swal.fire({
      title: "Success!! ",
      text: '<?= $this->session->flashdata('message_name') ?>',
      icon: "success",
    });
  <?php
    // $this->session->sess_destroy('message_name');
    unset($_SESSION['message_name']);
  } ?>

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

      var $state = $('<span style="background-color: ' + color + ';"><strong>' + state.text + ' ' + sign + '</strong></span>');

      return $state;
    };

    function formatStateDebit(state) {
      return formatState(state, '#2ecc71', '#ff7675', '(+)', '(-)');
    }

    function formatStateKredit(state) {
      return formatState(state, '#ff7675', '#2ecc71', '(-)', '(+)');
    }

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
      if (debit && kredit) {
        if (debit == kredit) {
          console.log('sama');
          $('.btn-success').prop('disabled', true);
        } else {
          console.log('tidak sama');
          $('.btn-success').prop('disabled', false);
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
          confirmButton: 'btn btn-success',
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
                if (!finalResponse.status) swal.fire('Gagal menyimpan data', 'error');
                else {

                  // document.getElementById('rumahadat').reset();
                  // $('#add_modal').modal('hide');
                  (JSON.stringify(data));
                  // alert(data)
                  swal.fire({
                    customClass: 'slow-animation',
                    icon: 'success',
                    showConfirmButton: false,
                    title: 'Berhasil Menambahkan Data',
                    timer: 3000
                  });
                  document.getElementById('upload_file_fe').reset(); // Reset the form
                  $('#upload_modal').modal('hide'); // Hide the modal
                  // location.reload();

                }
              } catch (error) {
                // If parsing fails, log the error
                console.error("Error parsing final response:", error);
                swal.fire('Gagal menyimpan data', 'error');
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

            // console.log('Modal shown for invoice ID:', invoiceId); // Debug log

            if (piutangElement.length > 0 && piutangElement.val() !== '') {
              var piutang = parseFloat(piutangElement.val().replace(/,/g, ''));

              // Fungsi untuk memperbarui nilai nominal bayar
              function updateNominalBayar() {
                // console.log('Checkbox checked:', checkbox.is(':checked')); // Debug log

                if (checkbox.is(':checked')) {
                  nominalBayarInput.val(piutang.toLocaleString('id-ID')).attr('readonly', true);
                } else {
                  nominalBayarInput.val('0').attr('readonly', false);
                }
              }

              // Inisialisasi
              updateNominalBayar();

              // Event handler untuk checkbox menggunakan click event
              checkbox.on('click', function() {
                updateNominalBayar();
              });

              // Event handler untuk input nominal bayar
              nominalBayarInput.on('input', function() {
                var value = $(this).val().replace(/\./g, '').replace(',', '.');

                if (value === '' || isNaN(parseFloat(value))) {
                  $(this).val('0').attr('readonly', false);
                } else {
                  value = parseFloat(value);
                  if (value > piutang) {
                    alert('Nilai nominal bayar tidak boleh lebih dari piutang.');
                    $(this).val(piutang.toLocaleString('id-ID'));
                  } else {
                    $(this).val(value.toLocaleString('id-ID'));
                  }
                }
              });
            }
          });
        })();
    <?php endforeach; ?>
  });
</script>