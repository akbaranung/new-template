<script>
    const isPremium = <?php echo json_encode($this->session->userdata('is_premium')); ?>;
    $(document).ready(function() {
        $('#user-table').dataTable({
            // responsive: true,

            // If you are using Scroller, you would add 'deferRender: true' and 'scrollY' options here,
            // and optionally 'scroller: true'.
            // Example:
            // deferRender: true,
            // scrollY: 200, // or '50vh'
            // scroller: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('nasabah/ajax_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

        $('.btn-reset-cuti').click(function(e) {
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
                    if (isPremium) {
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
                    } else {
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
                }
            });

        })
    });

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
                window.location.href = "<?= base_url('nasabah/delete/') ?>" + no_cib;
            }
        });
    }
</script>