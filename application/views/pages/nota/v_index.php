<!-- nota/v_index.php -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Nota Penjualan</h1>

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

            <!-- Alert Nota Belum Closing -->
            <?php
            $nota_belum_closing_today = $this->M_nota->get_belum_closing(date('Y-m-d'));
            if (!empty($nota_belum_closing_today)) :
            ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fe fe-alert-circle"></i>
                    <strong>Perhatian!</strong>
                    Ada <strong><?= count($nota_belum_closing_today) ?> nota</strong> hari ini yang belum di-closing.
                    <a href="<?= base_url('closing_nota/form?tanggal=' . date('Y-m-d')) ?>" class="btn btn-sm btn-warning ml-2 text-white">
                        <i class="fe fe-lock"></i> Proses Closing Sekarang
                    </a>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Search & Filter -->
                    <form method="GET" action="<?= base_url('nota') ?>">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="tanggal_dari" class="form-control" placeholder="Tanggal Dari" value="<?= htmlspecialchars($tanggal_dari ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="tanggal_sampai" class="form-control" placeholder="Tanggal Sampai" value="<?= htmlspecialchars($tanggal_sampai ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari No. Nota atau Customer..." value="<?= htmlspecialchars($search ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-dark">
                                    <i class="fe fe-search"></i> Cari
                                </button>
                                <a href="<?= base_url('nota') ?>" class="btn btn-pink text-white">
                                    <i class="fe fe-refresh-cw"></i> Reset
                                </a>
                                <a href="<?= base_url('nota/form') ?>" class="btn btn-primary text-white">
                                    <i class="fe fe-plus"></i> Buat Nota
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="13%">No. Nota</th>
                                    <th width="12%">Tanggal</th>
                                    <th>Customer</th>
                                    <th width="8%" class="text-center">Item</th>
                                    <th width="13%" class="text-right">Total</th>
                                    <th width="10%" class="text-right">Laba</th>
                                    <th width="10%" class="text-center">Metode</th>
                                    <th width="8%" class="text-center">Status</th>
                                    <th width="8%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($nota)) {
                                    $no = (isset($_GET['per_page']) ? $_GET['per_page'] : 0) + 1;
                                    foreach ($nota as $n) :
                                        $badge_metode = $n->metode_bayar == 'cash' ? 'badge-primary' : 'badge-info';
                                        $badge_status = $n->is_closed == 1 ? 'badge-secondary' : 'badge-pink';
                                        $status_text = $n->is_closed == 1 ? 'CLOSED' : 'OPEN';
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $n->no_nota ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($n->tanggal)) ?></td>
                                            <td><?= $n->customer ?: '-' ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-primary"><?= $n->total_item ?></span>
                                            </td>
                                            <td class="text-right">
                                                <strong>Rp <?= number_format($n->total_penjualan, 0, ',', '.') ?></strong>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-primary">
                                                    Rp <?= number_format($n->laba_kotor, 0, ',', '.') ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_metode ?>">
                                                    <?= strtoupper($n->metode_bayar) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_status ?>">
                                                    <?= $status_text ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('nota/detail/' . $n->id) ?>" class="btn btn-sm btn-primary" title="Detail">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                                <a href="<?= base_url('nota/detail/' . $n->id) ?>" class="btn btn-sm btn-pink" title="Print nota">
                                                    <i class="fe fe-printer"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fe fe-info"></i> Tidak ada data nota penjualan
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($nota)) : ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Menampilkan data nota penjualan
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?= $pagination ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
