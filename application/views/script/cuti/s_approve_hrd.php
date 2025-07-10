<script>
    $(document).ready(function() {
        $('#table-approve-hrd').dataTable({
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
                url: "<?php echo site_url('cuti/data_approve_hrd?filter=' . $this->input->get('filter')) ?>",
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

        $("#nama_karyawan").change(() => {
            var nip = $('#nama_karyawan').val()
            $.ajax({
                url: "<?= base_url('cuti/get_data_karyawan') ?>",
                type: "POST",
                dataType: "JSON",
                data: {
                    nip: nip
                },
                success: (res) => {
                    console.log(res)
                    $('#nama_atasan').val(res.atasan.nama);
                    $('#nip_atasan').val(res.atasan.nip);
                    $('#sisa_cuti').val(res.sisa_cuti);
                    $('#pengganti_cuti').html(res.pengganti);
                }
            })
        })

        $('#select_detail').hide();
        $('#file_pendukung_form').hide();
        $('#jenis_cuti').change(function() {
            var value = $(this).val();
            if (value > 0) {
                $.ajax({
                    url: "<?= base_url('cuti/ambilDataDetail') ?>",
                    type: "post",
                    dataType: "json",
                    data: {
                        id: value,
                        cuti: 'hrd'
                    },
                    success: (res) => {
                        if (res.jenis.file_pendukung == 1) {
                            $("#file_pendukung_form").show();
                            if (res.detail == 0) {
                                $("#select_detail").hide();
                            } else {
                                $("#select_detail").show();
                                $("#detail_cuti").html(res.detail);
                            }
                        } else {
                            $("#file_pendukung_form").hide();
                            if (res.detail == 0) {
                                $("#select_detail").hide();
                            } else {
                                $("#select_detail").show();
                                $("#detail_cuti").html(res.detail);
                            }
                        }
                    }
                })
            }
        })

        $("#btn-update-cuti").click(function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: "Apakah anda yakin data cuti sudah sesuai dan dapat dipertanggung jawabkan?",
                showCancelButton: true,
                cancelButtonText: "Batal",
                confirmButtonText: "Ya",
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = $("#form-update-cuti-hrd").attr('action');
                    var id = $("#id_cuti").val();
                    var status_cuti = $("#status_cuti").val();
                    var catatan = $("#catatan").val();

                    $.ajax({
                        url: url + id,
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            status_cuti: status_cuti,
                            catatan: catatan
                        },
                        beforeSend: () => {
                            Swal.fire({
                                title: 'Loading...',
                                timerProgressBar: true,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                            })
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
                                        res.err_status ?
                                            $("span#err_status_cuti").html(
                                                res.err_status
                                            ) :
                                            $("span#err_status_cuti").html("");
                                    }, 1500)
                                );
                            }
                        }
                    })
                }
            })
        })
    });
</script>
<script type="text/javascript">
    $('#mulai_cuti').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    })

    $('#akhir_cuti').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    })

    $('#akhir_cuti').change(function() {
        if ($("#jenis_cuti").val() == 2 || $("#jenis_cuti").val() == 3 || $("#jenis_cuti").val() == 4 || $("#jenis_cuti").val() == 5) {
            var awal = $('#mulai_cuti').val()
            awal = new Date(awal.split('/')[2], awal.split('/')[1] - 1, awal.split('/')[0])
            var akhir = $('#akhir_cuti').val()
            akhir = new Date(akhir.split('/')[2], akhir.split('/')[1] - 1, akhir.split('/')[0])
            var time = akhir.getTime() - awal.getTime();
            var hari = (time / (1000 * 3600 * 24)) + 1;
            $('#jumlah_cuti').val(hari);
        } else {
            var firstDate = $('#mulai_cuti').val();
            firstDate = new Date(firstDate.split('/')[2], firstDate.split('/')[1] - 1, firstDate.split('/')[0])
            var secondDate = $('#akhir_cuti').val();
            secondDate = new Date(secondDate.split('/')[2], secondDate.split('/')[1] - 1, secondDate.split('/')[0])
            const daysWithOutWeekEnd = [];
            for (var currentDate = new Date(firstDate); currentDate <= secondDate; currentDate.setDate(currentDate.getDate() + 1)) {
                // console.log(currentDate);
                if (currentDate.getDay() != 0 && currentDate.getDay() != 6) {
                    daysWithOutWeekEnd.push(new Date(currentDate));
                }
            }
            $('#jumlah_cuti').val(daysWithOutWeekEnd.length);
        }
    })

    $('#btn-form-cuti-hrd').on('click', function() {
        var formData = new FormData($('#formCutiHrd')[0]);
        $.ajax({
            url: "<?= base_url('cuti/cuti_manual') ?>",
            type: "post",
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                Swal.fire({
                    title: 'Sending...',
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
                $('#btn-form-cuti-hrd').attr('disabled', true)
            },
            complete: () => {
                $('#btn-form-cuti-hrd').attr('disabled', false)
            },
            success: function(res) {
                if (!res.error) {
                    Swal.fire({
                        icon: 'success',
                        title: res.msg,
                        showConfirmButton: false,
                    }, setTimeout(() => {
                        location.reload()
                    }, 1500))
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.msg,
                        showConfirmButton: false,
                    }, setTimeout(function() {
                        res.err_namakar != "" ? $("#err_namakar").html(res.err_namakar) : $("#err_namakar").html("");
                        res.err_namapeng != "" ? $("#err_namapeng").html(res.err_namapeng) : $("#err_namapeng").html("");
                        res.err_jenis != "" ? $("#err_jenis_cuti").html(res.err_jenis) : $("#err_jenis_cuti").html("");
                        res.err_detail != "" ? $("#err_detail_cuti").html(res.err_detail) : $("#err_detail_cuti").html("");
                        res.err_mulai != "" ? $("#err_mulai_cuti").html(res.err_mulai) : $("#err_mulai_cuti").html("");
                        res.err_akhir != "" ? $("#err_akhir_cuti").html(res.err_akhir) : $("#err_akhir_cuti").html("");
                        res.err_jumlah != "" ? $("#err_jumlah_cuti").html(res.err_jumlah) : $("#err_jumlah_cuti").html("");
                        res.err_alasan != "" ? $("#err_alasan_cuti").html(res.err_alasan) : $("#err_alasan_cuti").html("");
                        res.err_alamat != "" ? $("#err_alamat_cuti").html(res.err_alamat) : $("#err_alamat_cuti").html("");
                        res.err_file != "" ? $("#err_alamat_cuti").html(res.err_alamat) : $("#err_alamat_cuti").html("");
                        Swal.close()
                    }, 2000))
                }
            }
        })
    })

    function update_cuti_hrd(id) {
        $('#modal-update-cuti-hrd').modal('show');
        $('#id_cuti').val(id);
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