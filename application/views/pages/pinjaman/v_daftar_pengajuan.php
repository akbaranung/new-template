<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Daftar Pengajuan Pinjaman</h1>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Filter & Search -->
                    <form method="POST" action="<?= base_url('pinjaman/daftar_pengajuan') ?>">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="status_filter" id="status_filter" class="form-control">
                                        <option value="">:: Semua Status</option>
                                        <option value="pending" <?= $this->input->post('status_filter') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= $this->input->post('status_filter') == 'approved' ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= $this->input->post('status_filter') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        <option value="disbursed" <?= $this->input->post('status_filter') == 'disbursed' ? 'selected' : '' ?>>Dicairkan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Cari No. Pengajuan, Nama Nasabah, NIK..." value="<?= $this->input->post('keyword') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-search"></i> Cari
                                </button>
                                <a href="<?= base_url('pinjaman/daftar_pengajuan') ?>" class="btn btn-warning text-white">
                                    <i class="fe fe-refresh-cw"></i> Reset
                                </a>
                                <a href="<?= base_url('pinjaman/ajukan') ?>" class="btn btn-success text-white">
                                    <i class="fe fe-plus"></i> Ajukan Pinjaman
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 text-white"><?= $stats['pending'] ?? 0 ?></h4>
                                            <h6 class="mb-0 text-white">Pending</h6>
                                        </div>
                                        <i class="fe fe-clock fe-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 text-white"><?= $stats['disbursed'] ?? 0 ?></h4>
                                            <h6 class="mb-0 text-white">Dicairkan</h6>
                                        </div>
                                        <i class="fe fe-check-circle fe-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 text-white"><?= $stats['rejected'] ?? 0 ?></h4>
                                            <h6 class="mb-0 text-white">Rejected</h6>
                                        </div>
                                        <i class="fe fe-x-circle fe-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 text-white"><?= rupiah($stats['total_pinjaman'] ?? 0, 0) ?></h5>
                                            <h6 class="mb-0 text-white">Total Pinjaman</h6>
                                        </div>
                                        <i class="fe fe-dollar-sign fe-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No. Pengajuan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Nasabah</th>
                                    <th class="text-right">Jumlah Pinjaman</th>
                                    <th class="text-center">Tenor</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($pengajuan)) {
                                    $no = 1;
                                    foreach ($pengajuan as $p) :
                                        // Status badge
                                        $badge_class = [
                                            'pending' => 'badge-warning',
                                            'approved' => 'badge-info',
                                            'rejected' => 'badge-danger',
                                            'disbursed' => 'badge-success'
                                        ];

                                        $badge = $badge_class[$p->status] ?? 'badge-secondary';
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $p->no_pengajuan ?></strong>
                                            </td>
                                            <td><?= format_indo($p->tanggal_pengajuan) ?></td>
                                            <td>
                                                <div><strong><?= $p->nama_nasabah ?></strong></div>
                                                <small class="text-muted"><?= $p->no_cib ?> | <?= $p->no_ktp ?></small>
                                            </td>
                                            <td class="text-right">
                                                <strong><?= rupiah($p->jumlah_pinjaman, 0) ?></strong>
                                            </td>
                                            <td class="text-center"><?= $p->lama_pinjaman ?> bulan</td>
                                            <td class="text-center">
                                                <span class="badge badge-secondary">
                                                    <?= strtoupper($p->jenis_bunga) ?>
                                                </span>
                                                <br>
                                                <small><?= ucwords(str_replace('_', ' ', $p->jenis_pinjaman)) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge ?>">
                                                    <?= strtoupper($p->status) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <!-- Button Detail -->
                                                <a href="<?= base_url('pinjaman/detail/' . $p->id) ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fe fe-eye"></i>
                                                </a>

                                                <!-- Button Approval (Khusus Keuangan & Status Pending) -->
                                                <?php if ($this->session->userdata('role') == 'Keuangan' && $p->status == 'pending') : ?>
                                                    <a href="<?= base_url('pinjaman/approval/' . $p->id) ?>" class="btn btn-sm btn-success text-white" title="Proses Approval">
                                                        <i class="fe fe-check"></i> Approval
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Button Delete (Khusus Status Pending) -->
                                                <?php if ($p->status == 'pending') : ?>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $p->id ?>" data-no="<?= $p->no_pengajuan ?>" title="Hapus">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fe fe-info"></i> Tidak ada data pengajuan pinjaman
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($pengajuan)) : ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Menampilkan data pengajuan pinjaman
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?= $this->pagination->create_links() ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fe fe-alert-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengajuan pinjaman:</p>
                <p><strong id="delete-no-pengajuan"></strong></p>
                <p class="text-danger">
                    <i class="fe fe-alert-circle"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fe fe-x"></i> Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-trash-2"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle Delete Button
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');
            const noPengajuan = $(this).data('no');

            $('#delete-no-pengajuan').text(noPengajuan);
            $('#deleteForm').attr('action', '<?= base_url("pinjaman/delete_pengajuan/") ?>' + id);

            $('#deleteModal').modal('show');
        });
    });
</script>