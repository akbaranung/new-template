<script>
    $(document).ready(function() {
        $('#user-table').dataTable({
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
                url: "<?php echo site_url('perusahaan/ajax_cabang_list') ?>",
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
                    url: "<?= base_url('perusahaan/hapus_user/') ?>", // Use POST for ID, don't append to URL unless it's a RESTful DELETE
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

    document.getElementById('addCabangBtn').addEventListener('click', function(event) {
        // Prevent the default link behavior immediately
        event.preventDefault();

        // This PHP snippet retrieves the session data and outputs it as a JavaScript variable.
        // It's crucial this is rendered by PHP on the server side.
        const isPremium = <?php echo json_encode($this->session->userdata('is_premium')); ?>;
        const redirectUrl = "<?= base_url('perusahaan/add_cabang') ?>";

        if (isPremium) {
            // If premium, proceed to the URL
            window.location.href = redirectUrl;
        } else {
            // If not premium, show SweetAlert
            Swal.fire({
                title: 'Access Denied!',
                text: 'You need a premium account to add users. Please upgrade your subscription.',
                icon: 'warning',
                confirmButtonText: 'Upgrade Now',
                showCancelButton: true,
                cancelButtonText: 'No Thanks'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optional: Redirect to an upgrade page if 'Upgrade Now' is clicked
                    window.location.href = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed
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
</script>