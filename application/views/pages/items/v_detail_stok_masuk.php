<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">Detail Input Stok</h1>
                <a href="<?= base_url('stok_masuk') ?>" class="btn btn-secondary">
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

            <!-- Header Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-file-text"></i> Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>No. Transaksi</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <span class="badge badge-primary badge-lg">
                                            <?= $stok_masuk->no_transaksi ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>:</td>
                                    <td><?= date('d F Y', strtotime($stok_masuk->tanggal)) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Supplier</strong></td>
                                    <td>:</td>
                                    <td><?= $stok_masuk->supplier ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Metode Pembayaran</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <?php
                                        $badge_metode = $stok_masuk->metode_bayar == 'cash' ? 'badge-success' : 'badge-warning';
                                        ?>
                                        <span class="badge <?= $badge_metode ?>">
                                            <?= strtoupper($stok_masuk->metode_bayar) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Nominal</strong></td>
                                    <td>:</td>
                                    <td>
                                        <h5 class="mb-0 text-success">
                                            Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?>
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>:</td>
                                    <td>
                                        <?= $stok_masuk->created_by ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($stok_masuk->created_at)) ?>
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-package"></i> Detail Item Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th width="10%" class="text-center">Satuan</th>
                                    <th width="12%" class="text-right">Qty</th>
                                    <th width="15%" class="text-right">Harga Modal</th>
                                    <th width="18%" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($detail)) {
                                    $no = 1;
                                    $total_qty = 0;
                                    foreach ($detail as $d) :
                                        $total_qty += $d->qty;
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><strong><?= $d->kode_item ?></strong></td>
                                            <td><?= $d->nama_item ?></td>
                                            <td class="text-center"><?= $d->satuan ?></td>
                                            <td class="text-right">
                                                <?= number_format($d->qty, 2, ',', '.') ?>
                                            </td>
                                            <td class="text-right">
                                                Rp <?= number_format($d->harga_modal, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right">
                                                <strong>Rp <?= number_format($d->subtotal, 0, ',', '.') ?></strong>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                    ?>
                                    <tr class="table-info">
                                        <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                        <td class="text-right">
                                            <strong><?= number_format($total_qty, 2, ',', '.') ?></strong>
                                        </td>
                                        <td></td>
                                        <td class="text-right">
                                            <h5 class="mb-0 text-success">
                                                <strong>Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?></strong>
                                            </h5>
                                        </td>
                                    </tr>
                                <?php
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-warning mb-0">
                                                <i class="fe fe-alert-triangle"></i> Tidak ada detail item
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Jurnal Information (Optional) -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-book"></i> Jurnal Akuntansi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fe fe-info"></i> <strong>Pencatatan Jurnal:</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50%">Akun</th>
                                    <th width="25%" class="text-right">Debit</th>
                                    <th width="25%" class="text-right">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Persediaan Barang</strong></td>
                                    <td class="text-right text-success">
                                        <strong>Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-right">-</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= $stok_masuk->metode_bayar == 'cash' ? 'Kas' : 'Utang Dagang' ?>
                                        </strong>
                                    </td>
                                    <td class="text-right">-</td>
                                    <td class="text-right text-danger">
                                        <strong>Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td class="text-right"><strong>TOTAL:</strong></td>
                                    <td class="text-right">
                                        <strong>Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>Rp <?= number_format($stok_masuk->total_nominal, 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1"><strong>Keterangan:</strong></p>
                        <p class="text-muted mb-0">
                            Input Stok - <?= $stok_masuk->no_transaksi ?> - Supplier: <?= $stok_masuk->supplier ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-4">
                <div class="col-md-12 text-right">
                    <a href="<?= base_url('stok_masuk') ?>" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <!-- <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fe fe-printer"></i> Cetak
                    </button> -->
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {

        .btn,
        .card-header,
        .page-title {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        body {
            font-size: 12px;
        }

        h5 {
            font-size: 14px;
        }

        table {
            width: 100%;
        }
    }
</style>