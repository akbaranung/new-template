<script>
    $(document).ready(function() {
        $('#datatable').dataTable({
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('absensi/ajax_lokasi_presensi_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                    targets: -1,
                    orderable: false
                },
                // {
                //     targets: 2, // Target the second column (index 1)
                //     width: "1000px", // A fixed width is a good idea
                //     responsivePriority: 1 // Keep this column from shrinking
                // }
            ],
            // createdRow: function(row, data, dataIndex) {
            //     // Find the index of the status cell
            //     const statusCell = $('td', row).eq(3); // The status is in the 9th column (index 8)

            //     statusCell.css('min-width', '100px'); // Change '100px' to your desired width
            // },
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
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