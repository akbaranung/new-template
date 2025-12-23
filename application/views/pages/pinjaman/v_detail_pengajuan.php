<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">Detail Pengajuan Pinjaman</h1>
                <a href="<?= base_url('pinjaman') ?>" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Kembali
                </a>
            </div>

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

            <?php
            // Status badge
            $badge_class = [
                'pending' => 'badge-warning',
                'approved' => 'badge-info',
                'rejected' => 'badge-danger',
                'disbursed' => 'badge-success'
            ];
            $badge = $badge_class[$pengajuan->status] ?? 'badge-secondary';
            ?>

            <!-- Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">
                            <i class="fe fe-file-text"></i> <?= $pengajuan->no_pengajuan ?>
                        </h5>
                        <span class="badge <?= $badge ?> badge-lg">
                            <?= strtoupper($pengajuan->status) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri: Info Nasabah -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fe fe-user"></i> Data Nasabah
                            </h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>No. Nasabah</strong></td>
                                    <td>: <?= $pengajuan->no_cib ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td>: <?= $pengajuan->nama_nasabah ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIK</strong></td>
                                    <td>: <?= $pengajuan->no_ktp ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>: <?= $pengajuan->alamat ?></td>
                                </tr>
                                <tr>
                                    <td><strong>No. HP</strong></td>
                                    <td>: <?= $pengajuan->no_telp ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Kolom Kanan: Info Pinjaman -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fe fe-credit-card"></i> Data Pinjaman
                            </h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Jenis Pinjaman</strong></td>
                                    <td>: <?= ucwords(str_replace('_', ' ', $pengajuan->jenis_pinjaman)) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pengajuan</strong></td>
                                    <td>: <?= format_indo($pengajuan->tanggal_pengajuan) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dropping</strong></td>
                                    <td>: <?= format_indo($pengajuan->tanggal_dropping) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Bunga</strong></td>
                                    <td>: <?= strtoupper($pengajuan->jenis_bunga) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Bunga Per Tahun</strong></td>
                                    <td>: <?= $pengajuan->bunga_per_tahun ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($pengajuan->keterangan) : ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary">
                                    <i class="fe fe-info"></i> Keterangan
                                </h6>
                                <p class="mb-0"><?= nl2br($pengajuan->keterangan) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <h4 class="mb-0 text-white"><?= rupiah($pengajuan->jumlah_pinjaman, 0) ?></h4>
                            <h6 class="mb-1 text-white">Jumlah Pinjaman</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <h4 class="mb-0 text-white"><?= rupiah($pengajuan->angsuran_per_bulan, 0) ?></h4>
                            <h6 class="mb-1 text-white">Angsuran per Bulan</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            <h4 class="mb-0 text-white"><?= rupiah($pengajuan->total_bunga, 0) ?></h4>
                            <h6 class="mb-1 text-white">Total Bunga</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            <h4 class="mb-0 text-white"><?= rupiah($pengajuan->total_pembayaran, 0) ?></h4>
                            <h6 class="mb-1 text-white">Total Pembayaran</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Angsuran -->
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 text-white">
                        <i class="fe fe-calendar"></i> Jadwal Angsuran (<?= $pengajuan->lama_pinjaman ?> Bulan)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center" width="8%">Angsuran Ke</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-right">Angsuran</th>
                                    <th class="text-right">Pokok</th>
                                    <th class="text-right">Bunga</th>
                                    <th class="text-right">Sisa Pinjaman</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_angsuran = 0;
                                $total_pokok = 0;
                                $total_bunga_detail = 0;

                                foreach ($detail_angsuran as $da) :
                                    $total_angsuran += $da->angsuran;
                                    $total_pokok += $da->pokok;
                                    $total_bunga_detail += $da->bunga;

                                    // Status badge
                                    $status_badge = [
                                        'belum' => 'badge-warning',
                                        'lunas' => 'badge-success',
                                        'sebagian' => 'badge-info'
                                    ];
                                    $status_class = $status_badge[$da->status_bayar] ?? 'badge-secondary';
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $da->angsuran_ke ?></td>
                                        <td class="text-center"><?= format_indo($da->tanggal_jatuh_tempo) ?></td>
                                        <td class="text-right"><?= rupiah($da->angsuran, 0) ?></td>
                                        <td class="text-right"><?= rupiah($da->pokok, 0) ?></td>
                                        <td class="text-right"><?= rupiah($da->bunga, 0) ?></td>
                                        <td class="text-right"><?= rupiah($da->sisa_pinjaman, 0) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $status_class ?> text-white">
                                                <?= strtoupper($da->status_bayar) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="2" class="text-right">Total:</th>
                                    <th class="text-right"><?= rupiah($total_angsuran, 0) ?></th>
                                    <th class="text-right"><?= rupiah($total_pokok, 0) ?></th>
                                    <th class="text-right"><?= rupiah($total_bunga_detail, 0) ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?= base_url('pinjaman/daftar_pengajuan') ?>" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Kembali
                            </a>
                            <a href="<?= base_url('pinjaman/print_detail/' . $pengajuan->id) ?>" class="btn btn-info" target="_blank">
                                <i class="fe fe-printer"></i> Print Detail
                            </a>
                            <?php if ($this->session->userdata('role') == 'Keuangan' && $pengajuan->status == 'pending') : ?>
                                <a href="<?= base_url('pinjaman/approval/' . $pengajuan->id) ?>" class="btn btn-success text-white">
                                    <i class="fe fe-check"></i> Proses Approval
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($this->session->userdata('role') == 'keuangan' && $pengajuan->status == 'pending') : ?>
                                <a href="<?= base_url('pinjaman/approval/' . $pengajuan->id) ?>" class="btn btn-success">
                                    <i class="fe fe-check"></i> Proses Approval
                                </a>
                            <?php endif; ?>

                            <?php if ($pengajuan->status == 'pending') : ?>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                    <i class="fe fe-trash-2"></i> Hapus Pengajuan
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
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
                <p><strong><?= $pengajuan->no_pengajuan ?></strong></p>
                <p class="text-danger">
                    <i class="fe fe-alert-circle"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fe fe-x"></i> Batal
                </button>
                <form action="<?= base_url('pinjaman/delete_pengajuan/' . $pengajuan->id) ?>" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-trash-2"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>