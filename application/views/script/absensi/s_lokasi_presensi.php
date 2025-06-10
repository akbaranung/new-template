<script>
    $(document).ready(function() {
        $('#datatable').dataTable({
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            // If you are using Scroller, you would add 'deferRender: true' and 'scrollY' options here,
            // and optionally 'scroller: true'.
            // Example:
            // deferRender: true,
            // scrollY: 200, // or '50vh'
            // scroller: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('absensi/ajax_lokasi_presensi_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            // Custom DOM structure for layout (from previous answer)
            dom: '<"dataTables_top_wrapper clear-fix"<"dataTables_length_custom"l><"dataTables_filter_custom"f>>t<"dataTables_bottom_wrapper clear-fix"<"dataTables_info_custom"i><"dataTables_paginate_custom"p>>'
        });
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
                $.ajax({
                    url: "<?= base_url('absensi/hapus_lokasi_presensi/') ?>", // Use POST for ID, don't append to URL unless it's a RESTful DELETE
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
                                $('#datatable').DataTable().ajax.reload(null, false);
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