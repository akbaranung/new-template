<script>
  $(function() {
    Swal.fire({
      title: "Peringatan!",
      text: "Anda Harus Membuat COA sebelum menggunakan menu Financial! Anda bisa menambahkan Manual atau menggunakan Template/Contoh yang sudah ada.",
      icon: "info"
    });
  })

  $(document).ready(function() {
    applyPriceFormat();
  })


  $(document).ready(function() {
    var table = $('#table-template').DataTable({
      responsive: true,
      rowReorder: {
        selector: 'td:nth-child(2)'
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo site_url('financial_first/ajax_template_coa_list') ?>",
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
              className: 'btn btn-primary',
              action: function(e, dt, button, config) {
                Swal.fire({
                  title: 'Some title',
                  text: "Some text",
                }).then((result) => {
                  if (result.value) {
                    // form.submit();
                    window.location = '<?= base_url('financial_first/ambil_semua_coa') ?>';
                  }
                });
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
      }
      // Corrected DOM structure to ensure elements appear only once

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
        url: "<?php echo site_url('financial_first/tambahCoaAjax') ?>", // Your target URL
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
</script>