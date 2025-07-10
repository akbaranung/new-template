<script>
    $(document).ready(function() {
        $('#table-approve-atasan').dataTable({
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
                url: "<?php echo site_url('cuti/dataApproveAtasan') ?>",
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
</script>
<script type="text/javascript">
    $(document).ready(() => {
        $('.select2').select2();

        // var myTable = $('#table-approve-atasan').DataTable({
        //     "ajax": {
        //         type: "POST",
        //         url: "<?= base_url('cuti/dataApproveAtasan') ?>",
        //         data: function(d) {

        //         }
        //     },
        // })

        $("#select-pengganti").hide();
        $("#status_cuti").change(function() {
            var value = $(this).val();

            if (value == "Disetujui") {
                $("#select-pengganti").show();
            } else {
                $("#select-pengganti").hide();
            }
        })

        $("#btn-update-cuti-atasan").on('click', function(e) {
            e.preventDefault();
            var id_cuti = $("#id_cuti").val();
            var pengganti = $("#pengganti").val();
            var catatan = $('#catatan').val();
            var status_cuti = $('#status_cuti').val();
            var url = $("#form-update-cuti-atasan").attr('action');

            Swal.fire({
                icon: 'warning',
                title: "Apakah anda yakin data cuti sudah sesuai dan dapat dipertanggung jawabkan?",
                showCancelButton: true,
                cancelButtonText: "Batal",
                confirmButtonText: "Ya",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + id_cuti,
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            pengganti: pengganti,
                            catatan: catatan,
                            status_cuti: status_cuti
                        },
                        beforeSend: () => {
                            Swal.fire({
                                title: 'Sending...',
                                timerProgressBar: true,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                            })
                            $('#btn-update-cuti-atasan').attr('disabled', true)
                        },
                        complete: () => {
                            $('#btn-update-cuti-atasan').attr('disabled', false)
                        },
                        success: function(res) {
                            if (!res.error) {
                                Swal.fire({
                                        type: "success",
                                        icon: "success",
                                        title: `${res.msg}`,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                    },
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1500)
                                );
                            } else {
                                Swal.fire({
                                        icon: "error",
                                        title: `${res.msg}`,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                    },
                                    setTimeout(function() {
                                        Swal.close();
                                        res.err_status_cuti ?
                                            $("span#err_status_cuti").html(
                                                res.err_status_cuti
                                            ) :
                                            $("span#err_status_cuti").html("");

                                        res.err_pengganti ?
                                            $("span#err_pengganti").html(
                                                res.err_pengganti
                                            ) :
                                            $("span#err_pengganti").html("");
                                    }, 1500)
                                );
                            }
                        }
                    })
                }
            })
        })
    })

    function update_cuti_atasan(id) {
        $.ajax({
            url: "<?= base_url('cuti/approveAtasan/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(res) {
                $("#formConfirmAtasan").modal('show');
                $("#id_cuti").val(res.cuti.id_cuti);
                $("#pengganti").html(res.option);
            }
        })
    }

    function detailCuti(id) {
        $("#detail-cuti").modal('show');
        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "<?= base_url('cuti/detailCuti/') ?>" + id,
            success: (res) => {
                $("#detail-cuti-byID").html(res);
            }
        })
    }

    function historyCuti(nip) {
        location.href = "<?= site_url('cuti/historyCuti/') ?>" + nip;
    }

    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>