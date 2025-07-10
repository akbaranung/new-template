<!-- page content -->
<div class="right_col" role="main">
    <div class="x_panel card">
        <div class="title">
            <h3><b>Data Approval Atasan</b></h3>
        </div>
        <div>
            <a href="<?= site_url('cuti/view') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</a>
        </div>
        <div class="table-responsive" style="margin-top: 2em;">
            <table id="table-approve-atasan" class="table table-striped jambo_table bulk_action" width="100%">
                <thead>
                    <tr class="headings">
                        <th class="column-title">No.</th>
                        <th class="column-title">Nama</th>
                        <th class="column-title">Jenis Cuti</th>
                        <th class="column-title">Alasan Cuti</th>
                        <th class="column-title">Tanngal Pengajuan</th>
                        <th class="column-title">Mulai Cuti</th>
                        <th class="column-title">Jumlah Cuti</th>
                        <th class="column-title">Atasan</th>
                        <th class="column-title">Status</th>
                        <th class="column-title">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Approve Atasan -->
<div class="modal fade " id="formConfirmAtasan">
    <div class="modal-dialog modal-centered">
        <div class="modal-content">
            <!-- header-->
            <div class="modal-header">
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Form Persetujuan Atasan</h4>
            </div>
            <!--body-->
            <div class="modal-body">
                <form action="<?= base_url('cuti/update_cuti_atasan/') ?>" id="form-update-cuti-atasan" method="post">
                    <input type="hidden" id="id_cuti" readonly>
                    <div class="form-group">
                        <label for="status_cuti">Status</label>
                        <select class="form-control select2" id="status_cuti" name="status_cuti" style="width: 100%;">
                            <option value="">-- Pilih Status Cuti --</option>
                            <option value="Disetujui"> Disetujui </option>
                            <option value="Ditolak"> Ditolak </option>
                        </select>
                        <span class="text-danger" id="err_status_cuti"></span>
                    </div>
                    <div class="form-group" id="select-pengganti">
                        <label for="pengganti">Pengganti</label>
                        <select class="form-control select2" id="pengganti" name="pengganti" style="width: 100%;">
                            <option value="">-- Pilih Pengganti --</option>
                        </select>
                        <span class="text-danger" id="err_pengganti"></span>
                    </div>
                    <div class="form-group">
                        <label for="pengganti">Catatan (Optional)</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control"></textarea>
                    </div>
                    <!--footer-->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btn-update-cuti-atasan">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Cuti -->
<div class="modal fade " id="detail-cuti">
    <div class="modal-dialog modal-centered">
        <div class="modal-content">
            <!-- header-->
            <div class="modal-header">
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Detail Cuti</h4>
            </div>
            <!--body-->
            <div class="modal-body">
                <table class="table" width="100%" id="detail-cuti-byID">

                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        $('.select2').select2();

        var myTable = $('#table-approve-atasan').DataTable({
            "ajax": {
                type: "POST",
                url: "<?= base_url('cuti/dataApproveAtasan') ?>",
                data: function(d) {

                }
            },
        })

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
</body>

</html>