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
                url: "<?php echo site_url('subscription/ajax_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            layout: {
                topStart: 'search',
                topEnd: '',
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });
    });

    $('#approval_modal').on('show.bs.modal', function(event) {
        // Get the button that triggered the modal
        var button = $(event.relatedTarget);

        // Extract the value from the data-id attribute
        var id_value = button.data('id');

        // Find the hidden input field inside the modal and set its value
        var modal = $(this);
        modal.find('#id_approval').val(id_value);
    });

    function confirm_premium() {
        const ttlnamaValue = $('#confirmation').val();


        if (!ttlnamaValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Opsi Approval Tidak Boleh Kosong',
                timer: 1500
            });
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    InputEvent: 'form-control',
                    confirmButton: 'btn btn-success text-white',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Ingin Melanjutkan Konfirmasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {

                if (result.isConfirmed) {

                    var url;
                    var formData;
                    url = "<?php echo site_url('Subscription/update_confirmation_premium') ?>";

                    // window.location = url_base;
                    var formData = new FormData($("#approval_form")[0]);

                    $.ajax({
                        url: url,
                        type: "POST",
                        dataType: "json", // Change to 'text' to handle server-sent events
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            // Show the progress dialog before sending the request
                            swal.fire({
                                title: 'Mohon Tunggu...',
                                text: 'Sedang memproses Approval',
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
                                        title: 'Berhasil Mengkonfirmasi Approval',
                                        timer: 3000
                                    });
                                    $('#approval_modal').modal('hide'); // Hide the modal
                                    $('#user-table').DataTable().ajax.reload();

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
    }
</script>