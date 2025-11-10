<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Data Approval HRD</h1>
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- <p class="card-title"><strong>Data Approval Atasan</strong></p> -->
                    <div style="display: flex;">
                        <a href="<?= site_url('cuti/view') ?>" class="btn btn-warning mr-2"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</a>
                        <a href="<?= base_url('cuti/export_cuti/' . $this->input->get('filter')) ?>" class="btn btn-success mr-2"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#cutiModalHrd"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Manual</button>
                    </div>
                    <div style="margin-top: 2em;">
                        <form action="" method="get">
                            <div class='input-group date' id='myDatepicker2' style="width: 40%;">
                                <input type='text' id='filter' name='filter' class="form-control" placeholder="yyyy-mm" value="<?= $this->input->get('filter') ?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Search</button>
                        </form>
                    </div>
                </div>
                <div class="card-body" id="all">
                    <div class="table-responsive">
                        <table id="table-approve-hrd" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title">No.</th>
                                    <th class="column-title">Nama</th>
                                    <th class="column-title">Jenis</th>
                                    <th class="column-title">Alasan</th>
                                    <th class="column-title">Tanggal Pengajuan</th>
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

<!-- Modal Form Cuti Manual HRD -->
<div class="modal fade " id="cutiModalHrd">
    <div class="modal-dialog modal-centered">
        <div class="modal-content">
            <!-- header-->
            <div class="modal-header">
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Form Pengajuan Cuti Online</h4>
            </div>
            <!--body-->
            <div class="modal-body">
                <form action="" id="formCutiHrd" method="post">
                    <div class="form-group">
                        <label for="nama_karyawan">Nama Karyawan</label>
                        <select name="nama_karyawan" id="nama_karyawan" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $k) : ?>
                                <option value="<?= $k->nip ?>"><?= $k->nama ?> [<?= $k->nip ?>]</option>
                            <?php endforeach ?>
                        </select>
                        <span id="err_namakar" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="pengganti_cuti">Pengganti</label>
                        <select name="pengganti_cuti" id="pengganti_cuti" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Pengganti --</option>
                        </select>
                        <span id="err_namapeng" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="jenis_cuti">Jenis</label>
                        <select class="form-control select2" id="jenis_cuti" name="jenis_cuti" style="width:100%;">
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($all_jenis as $row) : ?>
                                <option value="<?= $row['Id'] ?>"><?= $row['nama_jenis'] ?></option>
                            <?php endforeach ?>
                        </select>
                        <span id="err_jenis_cuti" class="text-danger"></span>
                    </div>
                    <div class="form-group" id="select_detail">
                        <label for="detail_cuti">Detail Cuti</label>
                        <select class="form-control select2" id="detail_cuti" name="detail_cuti" style="width: 100%;">
                            <option value="">-- Detail Cuti --</option>
                        </select>
                        <span id="err_detail_detail" class="text-danger"></span>
                    </div>
                    <div class="form-group" id="file_pendukung_form">
                        <label for="file_pendukung">Dokumen Pendukung</label>
                        <input type="file" class="form-control" id="file_pendukung" name="file_pendukung">
                        <span id="err_file_pendukung" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="alamat_cuti">Alamat</label>
                        <textarea name="alamat_cuti" id="alamat_cuti" class="form-control"></textarea>
                        <span id="err_alamat_cuti" class="text-danger"></span>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" id="error_mulai">
                                <label for="mulai_cuti">Dari</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control" placeholder="Mulai Cuti" id="mulai_cuti" name="mulai_cuti">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                                <span id="err_mulai_cuti" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" id="error_akhir">
                                <label for="akhir_cuti">Sampai</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control" placeholder="Akhir Cuti" id="akhir_cuti" name="akhir_cuti">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                                <span id="err_akhir_cuti" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" id="error_jumlah">
                                <label for="jumlah_cuti">Jumlah Cuti</label>
                                <input type="text" class="form-control" placeholder="Jumlah Cuti" id="jumlah_cuti" name="jumlah_cuti" readonly>
                                <span id="err_jumlah" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="error_alasan">
                        <label for="alasan_cuti">Alasan</label>
                        <input type="text" class="form-control" placeholder="Alasan Cuti" id="alasan_cuti" name="alasan_cuti">
                        <span id="err_alasan_cuti" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="sisa_cuti">Sisa Cuti Reguler</label>
                        <input type="text" class="form-control" placeholder="Sisa cuti" id="sisa_cuti" name="sisa_cuti" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nama_atasan">Atasan</label>
                        <input type="hidden" readonly class="form-control" placeholder="Nip Atasan" id="nip_atasan" name="nip_atasan">
                        <input type="text" readonly class="form-control" placeholder="Nama Atasan" id="nama_atasan" name="nama_atasan">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
                        <button type="button" class="btn btn-primary" id="btn-form-cuti-hrd"><i class="fa fa-paper-plane" aria-hidden="true"></i> Kirim</button>
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


<!-- Modal Update Cuti -->
<div class="modal fade" id="modal-update-cuti-hrd">
    <div class="modal-dialog modal-centered">
        <div class="modal-content">
            <!-- header-->
            <div class="modal-header">
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Update Cuti</h4>
            </div>
            <!--body-->
            <div class="modal-body">
                <form action="<?= base_url('cuti/update_cuti_hrd/') ?>" id="form-update-cuti-hrd" method="post">
                    <div class="form-group">
                        <input type="hidden" readonly class="form-control" id="id_cuti" name="id_cuti">
                    </div>
                    <div class="form-group" id="error_jenis">
                        <label for="status_cuti">Status</label>
                        <select class="form-control select2" id="status_cuti" name="status_cuti" style="width:100%;">
                            <option value=""> -- Pilih Status --</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                        <span id="err_status_cuti" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3"></textarea>
                    </div>
                    <!--footer-->
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btn-update-cuti"><i class="fa fa-paper-plane" aria-hidden="true"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>