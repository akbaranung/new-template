<!-- items/v_stok_masuk.php -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Daftar Input Stok Barang</h1>

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
                    <!-- Search & Button -->
                    <form method="GET" action="<?= base_url('stok_masuk') ?>">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari No. Transaksi atau Supplier..." value="<?= htmlspecialchars($search ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-search"></i> Cari
                                </button>
                                <a href="<?= base_url('stok_masuk') ?>" class="btn btn-warning text-white">
                                    <i class="fe fe-refresh-cw"></i> Reset
                                </a>
                                <a href="<?= base_url('stok_masuk/form') ?>" class="btn btn-success text-white">
                                    <i class="fe fe-plus"></i> Input Stok
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
                                    <th width="15%">No. Transaksi</th>
                                    <th width="12%">Tanggal</th>
                                    <th>Supplier</th>
                                    <th width="10%" class="text-center">Total Item</th>
                                    <th width="15%" class="text-right">Total Nominal</th>
                                    <th width="10%" class="text-center">Metode</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($stok_masuk)) {
                                    $no = (isset($_GET['per_page']) ? $_GET['per_page'] : 0) + 1;
                                    foreach ($stok_masuk as $sm) :
                                        $badge_metode = $sm->metode_bayar == 'cash' ? 'badge-success' : 'badge-warning';
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= $sm->no_transaksi ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($sm->tanggal)) ?></td>
                                            <td><?= $sm->supplier ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $sm->total_item ?> item</span>
                                            </td>
                                            <td class="text-right">
                                                <strong>Rp <?= number_format($sm->total_nominal, 0, ',', '.') ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_metode ?>">
                                                    <?= strtoupper($sm->metode_bayar) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('stok_masuk/detail/' . $sm->id) ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fe fe-info"></i> Tidak ada data input stok
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($stok_masuk)) : ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Menampilkan data input stok barang
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
