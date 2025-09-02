<script>
  // This script will run after the page content loads
  document.addEventListener('DOMContentLoaded', function() {
    // Check the PHP flag. Note: It's important to echo the value.
    var forceShowCard2 = <?php echo json_encode($show_card2); ?>;

    if (forceShowCard2) {
      // Get the buttons and panes
      var card1Tab = document.getElementById('card1-tab');
      var card2Tab = document.getElementById('card2-tab');
      var card1Pane = document.getElementById('card1');
      var card2Pane = document.getElementById('card2');

      // Deactivate Card 1
      card1Tab.classList.remove('active');
      card1Tab.setAttribute('aria-selected', 'false');
      card1Pane.classList.remove('active', 'show');

      // Activate Card 2
      card2Tab.classList.add('active');
      card2Tab.setAttribute('aria-selected', 'true');
      card2Pane.classList.add('active', 'show');

      // Disable Card 1 tab to prevent it from being clicked
      card1Tab.disabled = true;
      card1Tab.style.pointerEvents = 'none'; // Optional: for visual disabling
      card1Tab.style.cursor = 'default'; // Optional: for cursor change
    }
  });
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
// If no flash data messages are present, show the default warning
else {
?>
  <script>
    $(function() {
      Swal.fire({
        title: "Peringatan!",
        text: "Anda Harus Membuat COA sebelum menggunakan menu Financial! Anda bisa menambahkan Manual atau menggunakan Template/Contoh yang sudah ada.",
        icon: "info"
      });
    });
  </script>
<?php
}
?>
<script>
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
                  title: 'Apakah Anda yakin?',
                  text: "Anda akan meng-ambil semua coa SBB yang tersedia.",
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
                    window.location = '<?= base_url('financial_first/ambil_semua_coa') ?>';
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

  $(document).ready(function() {
    var table = $('#table-template-2').DataTable({
      responsive: true,
      rowReorder: {
        selector: 'td:nth-child(2)'
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo site_url('financial_first/ajax_template_coa_bb_list') ?>",
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
                    window.location = '<?= base_url('financial_first/ambil_semua_coa_bb') ?>';
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
        url: "<?php echo site_url('financial_first/tambahCoaBBAjax') ?>", // Your target URL
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