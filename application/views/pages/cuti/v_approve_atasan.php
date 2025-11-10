<style>
    .col-xs-3 {
        width: 25%;
        background-color: #004e81;
    }



    .btn_footer_panel .tag_ {
        padding-top: 37px;
    }

    tr>th {
        /* background-color: #e91f62; */
        background-color: #3498db;
        color: white;
    }

    .col-centered {
        float: none;
        margin: 0 auto;
    }

    .dt-length label {
        margin-left: 8px;
        /* Adjust this value (e.g., 5px, 10px, 0.5em) as needed */
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Data Approval Atasan</h1>
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- <p class="card-title"><strong>Data Approval Atasan</strong></p> -->
                    <a href="<?= site_url('cuti/view') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</a>

                </div>
                <div class="card-body" id="all">
                    <div class="table-responsive">
                        <table id="table-approve-atasan" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="column-title">No.</th>
                                    <th class="column-title">Nama</th>
                                    <th class="column-title">Jenis</th>
                                    <th class="column-title">Alasan</th>
                                    <th class="column-title">Tanggal</th>
                                    <th class="column-title">Mulai</th>
                                    <th class="column-title">Jumlah</th>
                                    <th class="column-title">Atasan</th>
                                    <th class="column-title">Status</th>
                                    <th class="column-title">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade " id="formConfirmAtasan">
    <div class="modal-dialog modal-centered">
        <div class="modal-content">
            <!-- header-->
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Form Persetujuan Atasan</h4>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <!--body-->
            <div class="modal-body">
                <form action="<?= base_url('cuti/update_cuti_atasan/') ?>" id="form-update-cuti-atasan" method="post">
                    <input type="hidden" id="id_cuti" readonly>
                    <div class="form-group">
                        <label for="status_cuti">Status</label>
                        <select class="form-control select2" id="status_cuti" name="status_cuti" style="width: 100%;">
                            <option value="">-- Pilih Status --</option>
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
                <h4 class="modal-title" id="myModalLabel">Detail Cuti</h4>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <!--body-->
            <div class="modal-body">
                <table class="table" width="100%" id="detail-cuti-byID">

                </table>
            </div>
        </div>
    </div>
</div>