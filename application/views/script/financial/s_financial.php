<script>
  $(document).ready(function() {
    applyPriceFormat();
  })

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
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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