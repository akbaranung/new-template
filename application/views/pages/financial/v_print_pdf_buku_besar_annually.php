<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
        }

        h2 {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 20px;
        }

        .coa-header {
            background-color: #E8E8E8;
            font-weight: bold;
            padding: 5px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .summary-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2><?= $description ?></h2>

    <?php
    $has_transaction = false;
    if ($list_coa) {
        foreach ($list_coa as $lc) {
            $saldo_awal_value = isset($saldo_awal[$lc->no_sbb]) ? $saldo_awal[$lc->no_sbb] : 0;
            $transaction = $this->M_coa->getCoaReportAnnually($lc->no_sbb, $tahun);

            if ($transaction) {
                $has_transaction = true;
                // Header COA 
    ?>
                <table>
                    <thead>
                        <tr class="coa-header">
                            <th style="text-align: left;"><?= $lc->no_sbb ?></th>
                            <th style="text-align: left;" colspan="2"><?= strtoupper($lc->nama_perkiraan) ?></th>
                            <th style="text-align: right;">(IDR)</th>
                        </tr>
                        <tr>
                            <th width="15%">Tanggal</th>
                            <th width="50%">Keterangan</th>
                            <th width="17.5%" class="text-right">Debit</th>
                            <th width="17.5%" class="text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $total_debit = 0;
                        $total_kredit = 0;

                        // Data transaksi
                        foreach ($transaction as $tr) {
                            if ($lc->no_sbb == $tr->akun_debit) {
                        ?>
                                <tr>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($tr->tanggal)) ?></td>
                                    <td><?= $tr->keterangan ?></td>
                                    <td class="text-right"><?= number_format($tr->jumlah_debit, 0, ',', '.') ?></td>
                                    <td class="text-right">-</td>
                                </tr>
                            <?php
                                $total_debit += $tr->jumlah_debit;
                            } else {
                            ?>
                                <tr>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($tr->tanggal)) ?></td>
                                    <td><?= $tr->keterangan ?></td>
                                    <td class="text-right">-</td>
                                    <td class="text-right"><?= number_format($tr->jumlah_kredit, 0, ',', '.') ?></td>
                                </tr>
                        <?php
                                $total_kredit += $tr->jumlah_kredit;
                            }
                        }

                        // Hitung mutasi
                        if ($lc->posisi === "AKTIVA") {
                            $mutasi = $total_debit - $total_kredit;
                        } else {
                            $mutasi = $total_kredit - $total_debit;
                        }

                        // $selisih = $total_debit - $total_kredit;
                        $saldo_akhir = $saldo_awal_value + $mutasi;

                        // Summary
                        ?>
                        <tr class="summary-row">
                            <td colspan="2" class="text-right">Total</td>
                            <td class="text-right"><?= number_format($total_debit, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($total_kredit, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="summary-row">
                            <td colspan="3" class="text-right">Saldo Awal</td>
                            <td class="text-right"><?= number_format($saldo_awal_value, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="summary-row">
                            <td colspan="3" class="text-right">Mutasi</td>
                            <td class="text-right"><?= number_format($mutasi, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="summary-row">
                            <td colspan="3" class="text-right">Saldo Akhir</td>
                            <td class="text-right"><?= number_format($saldo_akhir, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            }
        }
        if (!$has_transaction) { ?>
            <table>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle"></i> Belum ada transaksi tercatat untuk tahun <?= $tahun ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
    <?php }
    } ?>
</body>

</html>