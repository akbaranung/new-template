<script>
    $(document).ready(function() {
        $('#user-table').dataTable({
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
                url: "<?php echo site_url('resetpassword/ajax_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            layout: {
                // 'search' is the search input
                // 'resetAll' is our new custom button
                topStart: 'search',
                topEnd: {
                    buttons: [{
                        text: 'Reset All User Password',
                        className: 'btn btn-pink',
                        action: function(e, dt, button, config) {
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: "Anda akan mengubah semua Password User.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ya, Reset!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                // Check if the user clicked the "Confirm" button (Ya, Tambahkan!)
                                if (result.isConfirmed) {

                                    // Proceed with your action, like redirecting
                                    var url;
                                    url = "<?php echo site_url('Resetpassword/resetalluserpassword_progress/') ?>";

                                    // window.location = url_base;

                                    $.ajax({
                                        url: url,
                                        type: "POST",
                                        dataType: "json", // Change to 'text' to handle server-sent events
                                        contentType: false,
                                        processData: false,
                                        beforeSend: function() {
                                            // Show the progress dialog before sending the request
                                            swal.fire({
                                                title: 'Mohon Tunggu...',
                                                text: 'Sedang memproses',
                                                icon: 'info',
                                                showConfirmButton: false,
                                                allowOutsideClick: false,
                                            });
                                        },
                                        success: function(data) {
                                            try {
                                                // Attempt to parse the final response
                                                if (!data.status) swal.fire('Gagal menyimpan data', 'error');
                                                else {

                                                    // document.getElementById('rumahadat').reset();
                                                    // $('#add_modal').modal('hide');
                                                    (JSON.stringify(data));
                                                    // alert(data)
                                                    swal.fire({
                                                        customClass: 'slow-animation',
                                                        icon: 'success',
                                                        showConfirmButton: false,
                                                        title: 'Berhasil Mereset Semua Password User',
                                                        text: data.message,
                                                        timer: 3000
                                                    });
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
                                // If result.isDismissed is true (user clicked cancel, outside, or pressed escape),
                                // then no further action is taken.
                            });
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button')
                        },
                        attr: {
                            // title: 'Copy',
                            id: 'btn-reset-semua-user-password'
                        }
                    }]
                }, // Changed to include the new button placeholder
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
        });
    });


    function ResetPasswordUser(id) {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                InputEvent: 'form-control',
                confirmButton: 'btn btn-primary text-white',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
            title: 'Ingin Melanjutkan Reset Password User?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Tidak',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                var url;
                var formData;
                url = "<?php echo site_url('Resetpassword/reset_password_user/') ?>" + id;

                // window.location = url_base;

                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "json", // Change to 'text' to handle server-sent events
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        // Show the progress dialog before sending the request
                        swal.fire({
                            title: 'Mohon Tunggu...',
                            text: 'Sedang memproses',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    },
                    success: function(data) {
                        try {
                            // Attempt to parse the final response
                            if (!data.status) swal.fire('Gagal menyimpan data', 'error');
                            else {

                                // document.getElementById('rumahadat').reset();
                                // $('#add_modal').modal('hide');
                                (JSON.stringify(data));
                                // alert(data)
                                swal.fire({
                                    customClass: 'slow-animation',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    title: 'Berhasil Mereset Password User',
                                    timer: 3000
                                });
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
</script>