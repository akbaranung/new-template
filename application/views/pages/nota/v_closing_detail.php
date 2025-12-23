<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">Detail Closing Kasir</h1>
                <a href="<?= base_url('closing_kasir') ?>" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Header Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-info"></i> Informasi Closing</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Tanggal Closing</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <span class="badge badge-primary badge-lg">
                                            <?= date('d F Y', strtotime($closing->tanggal)) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Transaksi</strong></td>
                                    <td>:</td>
                                    <td>
                                        <h5 class="mb-0"><?= $closing->total_transaksi ?> Nota</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Diproses Oleh</strong></td>
                                    <td>:</td>
                                    <td>
                                        <?= $closing->created_by ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($closing->created_at)) ?>
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Penjualan Cash</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        <h6 class="mb-0 text-success">
                                            Rp <?= number_format($closing->total_penjualan_cash, 0, ',', '.') ?>
                                        </h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Penjualan Piutang</strong></td>
                                    <td>:</td>
                                    <td>
                                        <h6 class="mb-0 text-info">
                                            Rp <?= number_format($closing->total_penjualan_piutang, 0, ',', '.') ?>
                                        </h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Penjualan</strong></td>
                                    <td>:</td>
                                    <td>
                                        <h5 class="mb-0 text-success">
                                            Rp <?= number_format($closing->total_penjualan, 0, ',', '.') ?>
                                        </h5>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Total Penjualan</h6>
                                    <h3 class="mb-0 text-white">
                                        Rp <?= number_format($closing->total_penjualan, 0, ',', '.') ?>
                                    </h3>
                                </div>
                                <i class="fe fe-dollar-sign fe-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Total HPP</h6>
                                    <h3 class="mb-0 text-white">
                                        Rp <?= number_format($closing->total_hpp, 0, ',', '.') ?>
                                    </h3>
                                </div>
                                <i class="fe fe-trending-down fe-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Laba Kotor</h6>
                                    <h3 class="mb-0 text-white">
                                        Rp <?= number_format($closing->laba_kotor, 0, ',', '.') ?>
                                    </h3>
                                </div>
                                <i class="fe fe-trending-up fe-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Nota yang Di-closing -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-list"></i> Daftar Nota</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No. Nota</th>
                                    <th>Waktu</th>
                                    <th>Customer</th>
                                    <th width="10%" class="text-center">Item</th>
                                    <th class="text-right">Total Penjualan</th>
                                    <th class="text-right">HPP</th>
                                    <th class="text-right">Laba</th>
                                    <th class="text-center">Metode</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($nota_list)) {
                                    $no = 1;
                                    foreach ($nota_list as $n) :
                                        $badge_metode = $n->metode_bayar == 'cash' ? 'badge-success' : 'badge-info';
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><strong><?= $n->no_nota ?></strong></td>
                                            <td><?= date('H:i', strtotime($n->tanggal)) ?></td>
                                            <td><?= $n->customer ?: '-' ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-secondary"><?= $n->total_item ?></span>
                                            </td>
                                            <td class="text-right">
                                                Rp <?= number_format($n->total_penjualan, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right">
                                                Rp <?= number_format($n->total_hpp, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right text-success">
                                                Rp <?= number_format($n->laba_kotor, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_metode ?>">
                                                    <?= strtoupper($n->metode_bayar) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('nota/detail/' . $n->id) ?>" class="btn btn-sm btn-info" title="Detail Nota">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-warning mb-0">
                                                <i class="fe fe-alert-triangle"></i> Tidak ada nota
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

            <!-- Jurnal yang Tercatat -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-book"></i> Jurnal Akuntansi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fe fe-check-circle"></i> <strong>Status:</strong> Jurnal sudah tercatat pada tanggal <?= date('d/m/Y', strtotime($closing->tanggal)) ?>
                    </div>

                    <?php if ($closing->total_penjualan_cash > 0) : ?>
                        <h6 class="mt-4"><strong>Jurnal 1: Penjualan Cash</strong></h6>
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
                                        <td><strong>Kas</strong></td>
                                        <td class="text-right text-success">
                                            <strong>Rp <?= number_format($closing->total_penjualan_cash, 0, ',', '.') ?></strong>
                                        </td>
                                        <td class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Penjualan</strong></td>
                                        <td class="text-right">-</td>
                                        <td class="text-right text-danger">
                                            <strong>Rp <?= number_format($closing->total_penjualan_cash, 0, ',', '.') ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if ($closing->total_penjualan_piutang > 0) : ?>
                        <h6 class="mt-4"><strong>Jurnal 2: Penjualan Piutang</strong></h6>
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
                                        <td><strong>Piutang</strong></td>
                                        <td class="text-right text-success">
                                            <strong>Rp <?= number_format($closing->total_penjualan_piutang, 0, ',', '.') ?></strong>
                                        </td>
                                        <td class="text-right">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Penjualan</strong></td>
                                        <td class="text-right">-</td>
                                        <td class="text-right text-danger">
                                            <strong>Rp <?= number_format($closing->total_penjualan_piutang, 0, ',', '.') ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <h6 class="mt-4"><strong>Jurnal 3: HPP (Harga Pokok Penjualan)</strong></h6>
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
                                    <td><strong>HPP / Beban Pokok Penjualan</strong></td>
                                    <td class="text-right text-success">
                                        <strong>Rp <?= number_format($closing->total_hpp, 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-right">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Persediaan Barang</strong></td>
                                    <td class="text-right">-</td>
                                    <td class="text-right text-danger">
                                        <strong>Rp <?= number_format($closing->total_hpp, 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <p class="mb-1"><strong>Keterangan:</strong></p>
                        <p class="text-muted mb-0">
                            Closing Kasir - <?= date('d/m/Y', strtotime($closing->tanggal)) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-4">
                <div class="col-md-12 text-right">
                    <a href="<?= base_url('closing_kasir') ?>" class="btn btn-secondary">
                        <i class="fe fe-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fe fe-printer"></i> Print Laporan
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    .fe-3x {
        font-size: 3rem;
    }

    @media print {

        .btn,
        .page-title {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
            page-break-inside: avoid;
        }

        body {
            font-size: 11px;
        }

        h5,
        h6 {
            font-size: 13px;
        }
    }
</style>