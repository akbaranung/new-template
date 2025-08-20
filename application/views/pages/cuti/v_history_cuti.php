<style>
    tr>th {
        /* background-color: #e91f62; */
        background-color: #3e51b4;
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
            <?php
            $nip = $this->session->userdata('nip');
            $user = $this->db->get_where('users', ['nip' => $nip])->row();

            ?>
            <h1 class="page-title">History Cuti <b><?= $users['nama'] ?></h1>
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- <p class="card-title"><strong>List Cuti</strong></p> -->

                    <a href="<?= site_url('cuti/data_approve_atasan_view') ?>" class="btn btn-warning"><i class="fa fa-chevron-left" aria-hidden="true"></i> Kembali</a>
                    <!-- <a href="#" id="addCabangBtn" class="btn btn-primary">
						Add Cabang
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
							<path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
						</svg>
					</a> -->

                </div>
                <div class="card-body" id="all">
                    <div class="table-responsive">
                        <table id="table-all" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Jenis Cuti</th>
                                    <th>Detail Cuti</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Mulai Cuti</th>
                                    <th>Jumlah Cuti</th>
                                    <th>Status Atasan</th>
                                    <th>Status Hrd</th>
                                    <th>Status Dirsdm</th>
                                    <th>Status Dirut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($historyCuti as $hs) :
                                    // Nama User
                                    $this->db->select('nama');
                                    $users = $this->db->get_where('users', ['nip' => $hs['nip']])->row_array();

                                    // Nama Jenis Cuti
                                    $this->db->select('nama_jenis');
                                    $jenis = $this->db->get_where('jenis_cuti', ['Id' => $hs['jenis']])->row_array();

                                    // Nama Sub Jenis Cuti
                                    $this->db->select('nama_sub_jenis');
                                    $detail = $this->db->get_where('sub_jenis_cuti', ['Id' => $hs['detail_cuti']])->row_array();
                                ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $users['nama'] ?></td>
                                        <td><?= $jenis['nama_jenis'] ?></td>
                                        <td><?= $hs['detail_cuti'] == 0 || $hs['detail_cuti'] == null ? "-" : $detail['nama_sub_jenis'] ?></td>
                                        <td><?= date('d F Y', strtotime($hs['date_created'])) ?></td>
                                        <td><?= date('d F Y', strtotime($hs['tgl_cuti'])) ?></td>
                                        <td><?= $hs['jumlah_cuti'] . " hari" ?></td>
                                        <td><?= $hs['status_atasan'] == null ? "Menunggu Proses" : $hs['status_atasan'] ?></td>
                                        <td><?= $hs['status_hrd'] == null ? "Menunggu Proses" : $hs['status_atasan'] ?></td>
                                        <td><?= $hs['status_dirsdm'] == null ? "-" : $hs['status_dirsdm'] ?></td>
                                        <td><?= $hs['status_dirut'] == null ? "-" : $hs['status_dirut'] ?></td>
                                    </tr>
                                <?php
                                    $i++;
                                endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
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