<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h3 class="page-title"><?= ($description) ?></h3>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="POST" action="<?= base_url('financial/bukubesarMonthly') ?>">
                                <div class="d-flex flex-wrap align-items-center justify-content-end">
                                    <!-- Select Year -->
                                    <div class="form-group mb-2 mr-2">
                                        <select id="per_bulan" name="per_bulan" class="form-control">
                                            <option value="">Select Month</option>
                                            <?php
                                            $months = [
                                                'January', 'February', 'March', 'April', 'May', 'June',
                                                'July', 'August', 'September', 'October', 'November', 'December'
                                            ];

                                            foreach ($months as $key => $monthName) {
                                                $month = $key + 1;
                                                $selected = $month == $per_bulan ? 'selected' : '';
                                                // $key + 1 gives the numeric month value (1-12)
                                                echo '<option value="' . ($month) . '" ' . $selected . '>' . $monthName . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2 mr-2">
                                        <select name="per_tahun" id="per_tahun" class="form-control">
                                            <?php
                                            $current_year = date('Y');
                                            for ($year = $current_year; $year >= 2020; $year--) :
                                            ?>
                                                <option value="<?= $year ?>" <?= $year == $per_tahun ? 'selected' : '' ?>>
                                                    <?= $year ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="form-group mb-2">
                                        <button type="submit" name="button_sbm" class="btn btn-primary mr-2" value="lihat">Lihat</button>
                                        <!-- <button type="submit" name="button_sbm" class="btn btn-success mr-2" value="excel">
                                            <i class="fa fa-file"></i> Excel
                                        </button> -->
                                        <button type="submit" name="button_sbm" class="btn btn-danger mr-2" value="pdf" formtarget="_blank">
                                            <i class="fa fa-file"></i> Unduh PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="" class="table table-sm table-striped table-bordered" style="width:100%">
                            <?php
                            $has_transaction = false; // Flag untuk cek ada transaksi atau nggak

                            if ($list_coa) {
                                foreach ($list_coa as $lc) :
                                    $saldo_awal_value = isset($saldo_awal[$lc->no_sbb]) ? $saldo_awal[$lc->no_sbb] : 0;

                                    $transaction = $this->M_coa->getCoaReportMonthly($lc->no_sbb, $per_periode);

                                    if ($transaction) {
                                        $has_transaction = true; ?>
                                        <thead class="thead-dark">
                                            <tr class="headings">
                                                <th style="width: 15%"><?= $lc->no_sbb ?></th>
                                                <th style="width: 40%"><?= strtoupper($lc->nama_perkiraan) ?></th>
                                                <th class="text-right" colspan="3">IDR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center" colspan="2">Keterangan</th>
                                                <th class="text-center">Debit</th>
                                                <th class="text-center">Kredit</th>
                                            </tr>
                                            <?php

                                            $total_debit = 0;
                                            $total_kredit = 0;
                                            $selisih = 0;

                                            foreach ($transaction as $tr) :
                                                if ($lc->no_sbb == $tr->akun_debit) { ?>
                                                    <tr>
                                                        <td><?= format_indo($tr->tanggal) ?></td>
                                                        <td colspan="2"><?= $tr->keterangan ?></td>
                                                        <td class="text-right"><?= number_format($tr->jumlah_debit) ?></td>
                                                        <td class="text-right">-</td>
                                                    </tr>
                                                <?php
                                                    $total_debit += $tr->jumlah_debit;
                                                    $total_kredit += 0;
                                                } else {
                                                ?>
                                                    <tr>
                                                        <td><?= format_indo($tr->tanggal) ?></td>
                                                        <td colspan="2"><?= $tr->keterangan ?></td>
                                                        <td class="text-right">-</td>
                                                        <td class="text-right"><?= number_format($tr->jumlah_kredit) ?></td>
                                                    </tr>
                                                <?php
                                                    $total_kredit += $tr->jumlah_kredit;
                                                    $total_debit += 0;
                                                } ?>
                                            <?php
                                            endforeach;

                                            if ($lc->posisi === "AKTIVA") {
                                                $selisih = $total_debit - $total_kredit;
                                            } else {
                                                $selisih = $total_kredit - $total_debit;
                                            }
                                            ?>

                                            <tr>
                                                <th class="text-right">Saldo awal:</th>
                                                <th class="text-right"><?= number_format($saldo_awal_value) ?></th>
                                                <th class="text-right">Total</th>
                                                <th class="text-right"><?= number_format($total_debit) ?></th>
                                                <th class="text-right"><?= number_format($total_kredit) ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-right">Saldo akhir:</th>
                                                <th class="text-right"><?= number_format($saldo_awal_value + $selisih) ?></th>
                                                <th class="text-right">Mutasi</th>
                                                <th class="text-right"><?= number_format($selisih) ?></th>
                                                <th class="text-right"></th>
                                            </tr>
                                        </tbody>
                                    <?php
                                    }
                                endforeach;



                                // Tampilkan pesan jika ga ada transaksi sama sekali
                                if (!$has_transaction) { ?>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fa fa-info-circle"></i> Belum ada transaksi tercatat untuk bulan <?= $bulan ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                <?php }
                                ?>
                            <?php
                            } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" id="reportBBMonthly" tabindex="-1" role="dialog" aria-labelledby="reportBBMonthlyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportBBMonthlyLabel">Report BB per Bulan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= base_url('financial/bukubesarMonthly') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="per_bulan">Bulan</label>
                        <select id="per_bulan" name="per_bulan" class="form-control" required>
                            <option value="">Pilih Bulan</option>
                            <?php
                            $months = [
                                'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ];

                            foreach ($months as $key => $monthName) {
                                echo '<option value="' . ($key + 1) . '">' . $monthName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="per_tahun">Tahun</label>
                        <select name="per_tahun" id="per_tahun" class="form-control" required>
                            <?php
                            $current_year = date('Y');
                            for ($year = $current_year; $year >= 2020; $year--) :
                            ?>
                                <option value="<?= $year ?>" <?= $year == $per_tahun ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Lihat Buku Besar</button>
                </div>
            </form>
        </div>
    </div>
</div>
