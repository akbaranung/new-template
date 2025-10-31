<script>
    $(document).ready(function() {
        $('#nasabah_search').select2({
            // Optional configuration:
            placeholder: 'Cari Nasabah', // Text displayed when nothing is selected
            allowClear: true // Allows a user to clear the selection
        });
    });
</script>
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('tabungan/ajax_list') ?>",
                type: "POST",
                data: function(d) {
                    d.nasabah = $('#nasabah_search').val();
                }
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1,
                orderable: false
            }], // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

        $('#nasabah_search').on('change', function() {
            table.ajax.reload();
        });
    })

    function confirmDelete(no_cib) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Red color for danger
            cancelButtonColor: '#6c757d', // Gray color for cancel
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            // Check if the user clicked the confirm button (Ya, Hapus!)
            if (result.isConfirmed) {
                // If confirmed, execute the redirection to the delete endpoint
                window.location.href = "<?= base_url('tabungan/delete/') ?>" + no_cib;
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Function to get current date in YYYY-MM-DD format
        function getCurrentDateFormatted() {
            const today = new Date();
            const year = today.getFullYear();
            // Add 1 to month (since it's 0-indexed) and pad with '0'
            const month = String(today.getMonth() + 1).padStart(2, '0');
            // Pad day with '0'
            const day = String(today.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        // Get the input element by its ID
        const dateInput = document.getElementById('tgl_pendaftaran_add');

        // Check if the element exists and set its value
        if (dateInput) {
            dateInput.value = getCurrentDateFormatted();
        }
    });
</script>