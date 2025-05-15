<script>
  $(document).ready(function() {
    applyPriceFormat(); // pasang ke baris awal
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
                location.reload();
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
              title: `${error}`,
              showConfirmButton: false,
              timer: 1500,
            });
          },
        });
      }
    });
  });

  $("#uraian-pengajuan").on('click', '.add-row', function() {
    var row = $(this).parents().closest('tr');
    var newId = Date.now();

    // Membuat baris baru
    var newRow = row.clone();

    newRow.find('input[name="uraian[]"]').each(function(index, value) {
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

  function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 2
    }).format(number);
  }

  // Fungsi pasang event pada satu baris
  function attachEvents(row) {
    row.find('input[name="qty[]"], input[name="price[]"]').off('input').on('input', function() {
      hitungSubtotal(row);
    });

    // Hitung pertama kali juga
    hitungSubtotal(row);
  }

  // Fungsi hitung subtotal satu baris
  function hitungSubtotal(row) {
    const qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
    const priceFormatted = row.find('input[name="price[]"]').val();
    const price = unformatRupiah(priceFormatted);
    const subtotal = qty * price;
    row.find('input[name="subtotal[]"]').val(formatRupiah(subtotal));
    hitungGrandTotal();
  }

  // Pasang event listener awal saat halaman load
  $('#uraian-pengajuan tr').each(function() {
    attachEvents($(this));
  });

  // Fungsi untuk menghapus format rupiah menjadi angka
  function unformatRupiah(rp) {
    if (!rp) return 0;
    return parseFloat(rp.replace(/[^0-9,-]+/g, '').replace(',', '.')) || 0;
  }

  // Hitung total dari semua subtotal
  function hitungGrandTotal() {
    let total = 0;
    $('input[name="subtotal[]"]').each(function() {
      total += unformatRupiah($(this).val());
    });

    $('input[name="total"]').val(formatRupiah(total));
  }

  function applyPriceFormat() {
    $('input[name="price[]"]').each(function() {
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
</script>