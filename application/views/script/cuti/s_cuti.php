<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
<script>
    $(document).ready(function() {
        $('#table-all').dataTable({
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
                url: "<?php echo site_url('cuti/ambilDataCuti') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(() => {
        $('.select2').select2();

        // var myTable = $('#table-all').DataTable({
        //     "ajax": {
        //         type: "POST",
        //         url: "<?= base_url('cuti/ambilDataCuti') ?>",
        //         data: function(d) {

        //         }
        //     },
        // })

        <?php
        // $utility = $this->db->get('utility')->row_array();
        // $array = $utility['libur'];
        $this->db->order_by('tgl_libur', 'ASC');
        $utility = $this->db->get('libur')->result_array();
        $array = "";
        foreach ($utility as $value) {
            $array .= '"' . date('d/m/Y', strtotime($value['tgl_libur'])) . '",';
        }
        $array = "[" . rtrim($array, ',') . "]";
        ?>

        let workingDaysBetweenDates = (d0, d1) => {
            /* Two working days and an sunday (not working day) */
            var holidays = <?= $array ?>;
            var startDate = parseDate(d0);
            var endDate = parseDate(d1);

            // Calculate days between date
            var time = endDate.getTime() - startDate.getTime();
            var days = (time / (1000 * 3600 * 24)) + 1;

            /* Here is the code */
            const tgl = [];
            holidays.forEach(day => {
                tgl.push(day);
            });
            const jml = [];
            for (var currentDate = new Date(startDate); currentDate <= new Date(endDate); currentDate.setDate(currentDate.getDate() + 1)) {
                tgl.find(function(item) {
                    if (parseDate(item).getTime() == currentDate.getTime()) {
                        jml.push(new Date(currentDate));
                    }
                })

                // jika tanggal bertemu dengan sabtu atau minggu
                if (currentDate.getDay() == 0) {
                    days = days - 1
                }

                if (currentDate.getDay() == 6) {
                    days = days - 1
                }
            }

            days = days - jml.length

            return days
        }



        function parseDate(input) {
            // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
            return new Date(input.split('/')[2], input.split('/')[1] - 1, input.split('/')[0])
        }


        // Fungsi menghitung antara 2 hari yang terpilih exclude weekends
        function getBusinessDays(dateObj, days) {
            const libur = [];
            var holi = <?= $array ?>;
            holi.forEach(day => {
                libur.push(new Date(day.split('/')[2], day.split('/')[1] - 1, day.split('/')[0]))
            });

            for (var i = 0; i < days; i++) {
                if (days > 0) {
                    dateObj.setDate(dateObj.getDate() + 1)
                    libur.forEach(element => {
                        if (element.getTime() == dateObj.getTime()) {
                            dateObj.setDate(dateObj.getDate() + 1)
                        }

                        if (dateObj.getDay() == 6 || dateObj.getDay() == 0) {
                            dateObj.setDate(dateObj.getDate() + 2)
                        }

                        if (element.getTime() == dateObj.getTime()) {
                            dateObj.setDate(dateObj.getDate() + 1)
                        }
                    })
                }

            }
            return dateObj;
        }

        $("#selectDetail").hide();
        $("#filePendukung").hide();
        // $("#akhirCuti").datepicker()
        // $("#mulaiCuti").datepicker()
        $("#jenisCuti").change(function() {
            $("#akhirCuti").datepicker('remove').prop('readonly', true);
            $("#mulaiCuti").datepicker('remove').prop('readonly', true);
            $("#mulaiCuti").val('');
            $("#akhirCuti").val('');
            $("#jumlahCuti").val('')
            const sisa_cuti = <?= $sisa_cuti ?>

            var value = $(this).val();
            if (value > 0) {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('cuti/ambilDataDetail') ?>",
                    cache: false,
                    data: {
                        id: value,
                        cuti: 'user'
                    },
                    dataType: "JSON",
                    success: function(res) {
                        if (res.jenis.file_pendukung == 1) {
                            $("#filePendukung").show();
                            if (res.detail == 0) {
                                $("#selectDetail").hide();
                            } else {
                                $("#selectDetail").show();
                                $("#detailCuti").html(res.detail);
                            }
                        } else {
                            $("#filePendukung").hide();
                            if (res.detail == 0) {
                                $("#selectDetail").hide();
                            } else {
                                $("#selectDetail").show();
                                $("#detailCuti").html(res.detail);
                            }
                        }


                        // JIka jenis cuti yang dipilih, cuti panjang
                        if (res.jenis.Id == 2) {
                            $('#mulaiCuti').removeAttr('readonly');
                            $("#mulaiCuti").datepicker({
                                autoclose: true,
                                startDate: "+1m",
                                format: 'dd/mm/yyyy'
                            });
                            $('#mulaiCuti').on('changeDate', (selected) => {
                                const selesai = new Date(selected.date.valueOf());
                                selesai.setMonth(selesai.getMonth() + 1);
                                $("#akhirCuti").datepicker({
                                    autoclose: true,
                                    format: 'dd/mm/yyyy'
                                }).datepicker('setDate', selesai);
                                $("#akhirCuti").datepicker('remove').prop('readonly', true);
                            })
                            // Jika jenis cuti yang dipilih, cuti melahirkan
                        } else if (res.jenis.Id == 3) {
                            $('#mulaiCuti').removeAttr('readonly');
                            $("#mulaiCuti").datepicker({
                                autoclose: true,
                                startDate: "+1m",
                                format: 'dd/mm/yyyy'
                            });
                            $('#mulaiCuti').on('changeDate', (selected) => {
                                const selesai = new Date(selected.date.valueOf());
                                selesai.setMonth(selesai.getMonth() + 3);
                                $("#akhirCuti").datepicker({
                                    autoclose: true,
                                    format: 'dd/mm/yyyy'
                                }).datepicker('setDate', selesai);
                                $("#akhirCuti").datepicker('remove').prop('readonly', true);
                            })
                        } else if (res.jenis.Id == 1) {
                            if (sisa_cuti == 0) {
                                Swal.fire({
                                    title: 'Perhatian!',
                                    text: 'Sisa cuti Anda sudah habis. Anda Tidak dapat melakukan Pengajuan Cuti.',
                                    icon: 'info',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Lanjutkan'
                                });
                            } else {
                                $('#mulaiCuti').removeAttr('readonly');
                                var mulai = res.jenis.min_hari_pengajuan;
                                var disabledWeekend = []

                                res.jenis.Id == 4 || res.jenis.Id == 5 ?
                                    disabledWeekend = [] :
                                    disabledWeekend = [0, 6]

                                mulai > 0 ?
                                    mulai = "+" + mulai + "d" :
                                    mulai = "now()"

                                var max = res.jenis.max_hari;
                                if (max > 0) {
                                    $("#mulaiCuti").datepicker({
                                        autoclose: true,
                                        startDate: "dateToday",
                                        daysOfWeekDisabled: disabledWeekend,
                                        datesDisabled: <?= $array ?>,
                                        format: 'dd/mm/yyyy'
                                    });
                                    $("#mulaiCuti").datepicker('setStartDate', mulai).on('changeDate', function(selected) {
                                        const minDate = new Date(selected.date.valueOf());
                                        minDate.setTime(minDate.getTime() + 3600 * 1000 * 24 * max - 1);
                                        $('#akhirCuti').datepicker({
                                            format: "dd/mm/yyyy"
                                        })
                                        $('#akhirCuti').datepicker('setDate', minDate);
                                        $("#akhirCuti").datepicker('remove').prop('readonly', true);
                                    });
                                } else {
                                    var disabledWeekend = []
                                    res.jenis.Id == 4 || res.jenis.Id == 5 ?
                                        disabledWeekend = [] :
                                        disabledWeekend = [0, 6]

                                    $("#mulaiCuti").datepicker({
                                        autoclose: true,
                                        startDate: "dateToday",
                                        daysOfWeekDisabled: disabledWeekend,
                                        datesDisabled: <?= $array ?>,
                                        format: 'dd/mm/yyyy'
                                    });
                                    $("#mulaiCuti").datepicker('setStartDate', mulai).on('changeDate', function(selected) {
                                        $("#akhirCuti").removeAttr('readonly');
                                        const minDate = new Date(selected.date.valueOf());
                                        $("#akhirCuti").datepicker({
                                            todayBtn: true,
                                            todayHighlight: true,
                                            autoclose: true,
                                            daysOfWeekDisabled: disabledWeekend,
                                            datesDisabled: <?= $array ?>,
                                            format: 'dd/mm/yyyy'
                                        })
                                        $('#akhirCuti').datepicker('setStartDate', minDate)
                                    });
                                }
                            }
                        } else {
                            if (res.detail == 0) {
                                $('#mulaiCuti').removeAttr('readonly');
                                var mulai = res.jenis.min_hari_pengajuan;
                                var disabledWeekend = []

                                res.jenis.Id == 4 || res.jenis.Id == 5 ?
                                    disabledWeekend = [] :
                                    disabledWeekend = [0, 6]

                                mulai > 0 ?
                                    mulai = "+" + mulai + "d" :
                                    mulai = "now()"

                                var max = res.jenis.max_hari;
                                if (max > 0) {
                                    $("#mulaiCuti").datepicker({
                                        autoclose: true,
                                        startDate: "dateToday",
                                        daysOfWeekDisabled: disabledWeekend,
                                        datesDisabled: <?= $array ?>,
                                        format: 'dd/mm/yyyy'
                                    });
                                    $("#mulaiCuti").datepicker('setStartDate', mulai).on('changeDate', function(selected) {
                                        const minDate = new Date(selected.date.valueOf());
                                        minDate.setTime(minDate.getTime() + 3600 * 1000 * 24 * max - 1);
                                        $('#akhirCuti').datepicker({
                                            format: "dd/mm/yyyy"
                                        })
                                        $('#akhirCuti').datepicker('setDate', minDate);
                                        $("#akhirCuti").datepicker('remove').prop('readonly', true);
                                    });
                                } else {
                                    var disabledWeekend = []
                                    res.jenis.Id == 4 || res.jenis.Id == 5 ?
                                        disabledWeekend = [] :
                                        disabledWeekend = [0, 6]

                                    $("#mulaiCuti").datepicker({
                                        autoclose: true,
                                        startDate: "dateToday",
                                        daysOfWeekDisabled: disabledWeekend,
                                        datesDisabled: <?= $array ?>,
                                        format: 'dd/mm/yyyy'
                                    });
                                    $("#mulaiCuti").datepicker('setStartDate', mulai).on('changeDate', function(selected) {
                                        $("#akhirCuti").removeAttr('readonly');
                                        const minDate = new Date(selected.date.valueOf());
                                        $("#akhirCuti").datepicker({
                                            todayBtn: true,
                                            todayHighlight: true,
                                            autoclose: true,
                                            daysOfWeekDisabled: disabledWeekend,
                                            datesDisabled: <?= $array ?>,
                                            format: 'dd/mm/yyyy'
                                        })
                                        $('#akhirCuti').datepicker('setStartDate', minDate)
                                    });
                                }
                            } else {
                                var disabledWeekend = []
                                res.jenis.Id == 4 || res.jenis.Id == 5 ?
                                    disabledWeekend = [] :
                                    disabledWeekend = [0, 6]

                                $("#detailCuti").change(function() {
                                    $('#mulaiCuti').removeAttr('readonly');
                                    $("#mulaiCuti").val('');
                                    $("#akhirCuti").val('');
                                    $("#jumlahCuti").val('')
                                    var valueDetail = $(this).val();
                                    $.ajax({
                                        type: "POST",
                                        dataType: "JSON",
                                        url: "<?= base_url('cuti/dataDetail') ?>",
                                        cache: false,
                                        data: {
                                            idDetail: valueDetail
                                        },
                                        success: function(data) {
                                            var maxDate = data.jatahCuti;
                                            var start = data.min_hari_pengajuan;
                                            start > 0 ?
                                                start = "+" + start + "d" :
                                                start = "now()"

                                            if (maxDate > 0) {
                                                $("#mulaiCuti").datepicker({
                                                    autoclose: true,
                                                    startDate: "dateToday",
                                                    daysOfWeekDisabled: disabledWeekend,
                                                    datesDisabled: <?= $array ?>,
                                                    format: 'dd/mm/yyyy'
                                                });
                                                $("#mulaiCuti").datepicker('setStartDate', start).on('changeDate', function(selected) {
                                                    const minDate = new Date(selected.date.valueOf())
                                                    $('#akhirCuti').datepicker({
                                                        format: 'dd/mm/yyyy'
                                                    })
                                                    $('#akhirCuti').datepicker('setDate', getBusinessDays(minDate, maxDate - 1));
                                                    $("#akhirCuti").datepicker('remove').prop('readonly', true);
                                                });
                                            } else {
                                                $("#mulaiCuti").datepicker({
                                                    autoclose: true,
                                                    startDate: "dateToday",
                                                    daysOfWeekDisabled: disabledWeekend,
                                                    datesDisabled: <?= $array ?>,
                                                    format: 'dd/mm/yyyy'
                                                });
                                                $("#mulaiCuti").datepicker('setStartDate', start).on('changeDate', function(selected) {
                                                    $("#akhirCuti").removeAttr('readonly');
                                                    <?php
                                                    $this->db->select("cuti");
                                                    $data = $this->db->get_where('users', ['nip' => $this->session->userdata('nip')])->row_array();
                                                    ?>
                                                    var jatah = <?= $data['cuti'] ?>;
                                                    var startDate = new Date(selected.date.valueOf());
                                                    const minDate = new Date(selected.date.valueOf());
                                                    $("#akhirCuti").datepicker({
                                                        todayBtn: true,
                                                        todayHighlight: true,
                                                        autoclose: true,
                                                        daysOfWeekDisabled: disabledWeekend,
                                                        datesDisabled: <?= $array ?>,
                                                        format: 'dd/mm/yyyy'
                                                    })
                                                    $('#akhirCuti').datepicker('setStartDate', startDate)
                                                });
                                            }
                                        }
                                    })
                                })
                            }
                        }
                    }
                })
            }
        });

        $('#akhirCuti').change(function() {
            if ($("#jenisCuti").val() == 2 || $("#jenisCuti").val() == 3 || $("#jenisCuti").val() == 4 || $("#jenisCuti").val() == 5) {
                var awal = $('#mulaiCuti').val()
                awal = new Date(awal.split('/')[2], awal.split('/')[1] - 1, awal.split('/')[0])
                var akhir = $('#akhirCuti').val()
                akhir = new Date(akhir.split('/')[2], akhir.split('/')[1] - 1, akhir.split('/')[0])
                var time = akhir.getTime() - awal.getTime();
                var hari = (time / (1000 * 3600 * 24)) + 1;
                $('#jumlahCuti').val(hari);
            } else {
                var firstDate = $('#mulaiCuti').val();
                var secondDate = $('#akhirCuti').val();
                var jumlah = workingDaysBetweenDates(firstDate, secondDate)
                $('#jumlahCuti').val(jumlah == 0 ? 1 : jumlah);
            }
        })

        $('#mulaiCuti').change(function() {
            if ($("#jenisCuti").val() == 2 || $("#jenisCuti").val() == 3 || $("#jenisCuti").val() == 4 || $("#jenisCuti").val() == 5) {
                var awal = $('#mulaiCuti').val()
                awal = new Date(awal.split('/')[2], awal.split('/')[1] - 1, awal.split('/')[0])
                var akhir = $('#akhirCuti').val()
                akhir = new Date(akhir.split('/')[2], akhir.split('/')[1] - 1, akhir.split('/')[0])
                var time = akhir.getTime() - awal.getTime();
                var hari = (time / (1000 * 3600 * 24)) + 1;
                $('#jumlahCuti').val(hari);
            } else {
                var firstDate = $('#mulaiCuti').val();
                var secondDate = $('#akhirCuti').val();
                var jumlah = workingDaysBetweenDates(firstDate, secondDate)
                $('#jumlahCuti').val(jumlah == 0 ? 1 : jumlah);
            }
        })

        $('#btnSubmit').on('click', function() {
            var formData = new FormData($('#formCuti')[0]);
            $.ajax({
                url: "<?= base_url('cuti/sendCuti') ?>",
                type: "POST",
                data: formData,
                dataType: "JSON",
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
                    $('#btnSubmit').attr('disabled', true)
                },
                complete: () => {
                    $('#btnSubmit').attr('disabled', false)
                },
                success: function(res) {
                    if (res.sukses) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg,
                            showConfirmButton: false,
                        }, setTimeout(() => {
                            location.reload();
                        }, 2000))
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.msg,
                            showConfirmButton: false,
                        }, setTimeout(() => {
                            Swal.close()
                            res.err_jenis ? $("#err_jenis").html(res.err_jenis) : $("#err_jenis").html("");
                            res.err_detail ? $("#err_detail").html(res.err_detail) : $("#err_detail").html("");
                            res.err_mulai ? $("#err_mulai").html(res.err_mulai) : $("#err_mulai").html("");
                            res.err_akhir ? $("#err_akhir").html(res.err_akhir) : $("#err_akhir").html("");
                            res.err_jumlah ? $("#err_jumlah").html(res.err_jumlah) : $("#err_jumlah").html("");
                            res.err_alasan ? $("#err_alasan").html(res.err_alasan) : $("#err_alasan").html("");
                            res.err_alamat ? $("#err_alamat").html(res.err_alamat) : $("#err_alamat").html("");
                            res.err_file ? $("#err_file").html(res.err_file) : $("#err_file").html("");
                        }, 2000))
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                            icon: "error",
                            title: `${xhr.responseJSON.message}`,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        },
                        setTimeout(function() {
                            Swal.close();
                        }, 1500)
                    );
                },
            })
        })
    })

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

    function cetak(id) {
        window.open("<?= site_url('cuti/cetakPdf/') ?>" + id, "_blank")
    }

    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>