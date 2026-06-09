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


  $(document).ready(function() {
    applyPriceFormat(); // pasang ke baris awal
    $("select[name='direksi']").change(function() {
      var val = $(this).val();
      if (val == 1) {
        $('#nama_direksi').attr('disabled', false);
      } else {
        $('#nama_direksi').attr('disabled', true);
      }
    })

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
        width: '100%',
        templateSelection: formatStateDebit,
        theme: 'bootstrap4',
      });
    });

    $('.coa_kredit').each(function() {
      $(this).select2({
        width: '100%',
        templateSelection: formatStateKredit,
        theme: 'bootstrap4',
      });
    });
  });

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

  $("#uraian-pengajuan").on('click', '.add-row', function() {
    var row = $(this).parents().closest('tr#clone');
    var newId = Date.now();

    // Membuat baris baru
    var newRow = row.clone();

    newRow.find('textarea[name="uraian[]"]').each(function(index, value) {
      $(this).attr('id', newId)
      $(this).val('')
    })

    newRow.find('input[name="qty[]"]').each(function(index, value) {
      $(this).attr('id', newId)
      $(this).val('1')
    })

    newRow.find('input[name="price[]"]').each(function(index, value) {
      $(this).attr('id', newId)
      $(this).val('0')

    })

    newRow.find('input[name="subtotal[]"]').each(function(index, value) {
      $(this).attr('id', newId)
      $(this).val('0')
    })

    newRow.insertAfter(row);

    attachEvents(newRow);

    applyPriceFormat();

  })

  $(document).on('click', '.hapus-row', function() {
    $(this).closest('tr').remove();
    // Hitung ulang grand total
    hitungGrandTotal();
  });

  function formatToRupiah(num) {
    return num.toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 2
    });
  }

  // Fungsi pasang event pada satu baris
  function attachEvents(row) {
    row.find('input[name="qty[]"], input[name="price[]"]').off('input').on('input', function() {
      hitungSubtotal(row);
      applyPriceFormat()
    });

    // Hitung pertama kali juga
    hitungSubtotal(row);
    applyPriceFormat()
  }

  // Fungsi hitung subtotal satu baris
  function hitungSubtotal(row) {
    const qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
    const priceFormatted = row.find('input[name="price[]"]').val();
    const price = unformatRupiah(priceFormatted);
    const subtotal = qty * price;
    row.find('input[name="subtotal[]"]').val(formatToRupiah(subtotal));
    hitungGrandTotal();
  }

  // Pasang event listener awal saat halaman load
  $('#uraian-pengajuan tr#clone').each(function() {
    attachEvents($(this));
  });

  // Fungsi untuk menghapus format rupiah menjadi angka
  function unformatRupiah(rp) {
    if (!rp) return 0;
    return parseFloat(rp.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
  }

  // Hitung total dari semua subtotal
  function hitungGrandTotal() {
    let total = 0;
    $('input[name="subtotal[]"]').each(function() {
      total += unformatRupiah($(this).val());
    });

    $('input[name="total"]').val(formatToRupiah(total));
  }

  function applyPriceFormat() {
    $('.price, .subtotal, .total, .realisasi').each(function() {
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
    $(".btn-submit-bayar").click(function(e) {
      e.preventDefault();

      const form = $("#form-pembayaran");
      const coaSelects = $('select[name="coa_credit[]"]');
      const subtotalInputs = $('input[name="subtotal[]"]');
      const idItemInputs = $('input[name="id_item[]"]');

      var parent = $(this).parents("form");
      var url = parent.attr("action");
      console.log(parent);
      var formData = new FormData(parent[0]);

      let coaData = [];
      let allCoaSelected = true;

      // Validasi pertama: Cek apakah semua COA sudah dipilih
      coaSelects.each(function() {
        if ($(this).val() === '') {
          allCoaSelected = false;
          return false; // Menghentikan loop .each()
        }
      });

      if (!allCoaSelected) {
        Swal.fire('Peringatan', 'Harap pilih COA Kredit untuk semua uraian.', 'warning');
        return; // Menghentikan seluruh fungsi click handler
      }

      // Jika semua COA sudah dipilih, kumpulkan data untuk AJAX
      coaSelects.each(function(index) {
        const selectedOption = $(this).find('option:selected');
        const coaValue = selectedOption.val();
        const subtotalValue = parseFloat(subtotalInputs.eq(index).val());
        const itemId = idItemInputs.eq(index).val();

        coaData.push({
          item_id: itemId,
          coa_credit: coaValue,
          subtotal: subtotalValue
        });
      });

      // Panggilan AJAX untuk memvalidasi nominal COA
      $.ajax({
        url: "<?= site_url('pengajuan/check_bayar_coa_details') ?>",
        type: "POST",
        dataType: "json",
        data: {
          coa_data: coaData,
        },
        beforeSend: function() {
          Swal.fire({
            title: 'Memeriksa Data...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
        },
        success: function(response) {
          Swal.close();

          if (response.status === 'error') {
            // Jika nominal COA tidak cukup
            Swal.fire({
              title: 'Peringatan!',
              html: response.message,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Ya, Lanjutkan',
              cancelButtonText: 'Batalkan'
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
          } else {
            // Jika nominal COA cukup, lanjutkan dengan konfirmasi final
            let messageHtml = 'Anda akan menyimpan data dengan COA Kredit berikut:<ul>';
            coaSelects.each(function() {
              const selectedOption = $(this).find('option:selected');
              const item = $(this).closest('tr').find('td:eq(1)').text();
              messageHtml += `<li>Uraian : <strong>${item}</strong> : ${selectedOption.text()}</li>`;
            });
            messageHtml += '</ul><br>Lanjutkan?';

            Swal.fire({
              title: 'Konfirmasi Pembayaran',
              html: messageHtml,
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: 'Ya, Lanjutkan',
              cancelButtonText: 'Batalkan'
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
          }
        },
        error: function(xhr, status, error) {
          Swal.close();
          Swal.fire({
            title: 'Error',
            text: 'Terjadi kesalahan saat memeriksa data. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'Oke'
          });
          console.error("AJAX Error: ", status, error, xhr);
        }
      });
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.rupiah-input');

    inputs.forEach(input => {
      input.addEventListener('keyup', function(e) {
        // Dapatkan nilai input tanpa tanda titik atau karakter non-digit lainnya
        let value = this.value.replace(/\D/g, '');

        // Format ulang dengan menambahkan titik sebagai pemisah ribuan
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Perbarui nilai input
        this.value = value;
      });
    });
  });
</script>