<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Perhitungan Pinjaman - Print</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 30px;
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .info-box {
            background: #f8f9fa;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px solid #333;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #555;
        }

        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
        }

        .summary-card.primary {
            background: #3f51b5;
        }

        .summary-card.danger {
            background: #e81f63;
        }

        .summary-card.dark {
            background: #2c3e50;
        }

        .summary-card h3 {
            font-size: 28px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .summary-card p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .table-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            /* background: #667eea; */
            color: black;
        }

        .table th,
        .table td {
            padding: 5px;
            border: 1px solid #dee2e6;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            background: white;
            color: black;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        @media print {
            body {
                padding: 15px;
            }

            .summary-cards,
            .summary-card,
            .info-box,
            .table thead {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button class="btn btn-primary btn-print" onclick="window.print()">
        <i class="fa fa-print"></i> Print / Save PDF
    </button>

    <div class="header">
        <h1>SIMULASI PERHITUNGAN PINJAMAN</h1>
        <p>Dicetak pada: <?= date('l, d F Y, H:i:s') ?></p>
    </div>

    <?php
    // Hitung ulang di server side
    $pokok = $jumlah_pinjaman;
    $tenor = $lama_pinjaman;
    $bunga = $bunga_pinjaman;
    $jenis = $jenis_bunga;

    if ($jenis == 'anuitas') {
        $bunga_bulanan = $bunga / 100 / 12;
        $angsuran = $pokok * ($bunga_bulanan * pow(1 + $bunga_bulanan, $tenor)) / (pow(1 + $bunga_bulanan, $tenor) - 1);

        $detail = [];
        $sisa = $pokok;
        $total_bunga = 0;

        for ($i = 1; $i <= $tenor; $i++) {
            $bunga_amount = $sisa * $bunga_bulanan;
            $pokok_bayar = $angsuran - $bunga_amount;
            $sisa -= $pokok_bayar;
            $total_bunga += $bunga_amount;

            $detail[] = [
                'bulan' => $i,
                'angsuran' => $angsuran,
                'pokok' => $pokok_bayar,
                'bunga' => $bunga_amount,
                'sisa' => max(0, $sisa)
            ];
        }
    } else {
        // Flat
        $bunga_bulanan = ($pokok * ($bunga / 100) * ($tenor / 12)) / $tenor;
        $pokok_bulanan = $pokok / $tenor;
        $angsuran = $pokok_bulanan + $bunga_bulanan;

        $detail = [];
        $sisa = $pokok;
        $total_bunga = 0;

        for ($i = 1; $i <= $tenor; $i++) {
            $sisa -= $pokok_bulanan;
            $total_bunga += $bunga_bulanan;

            $detail[] = [
                'bulan' => $i,
                'angsuran' => $angsuran,
                'pokok' => $pokok_bulanan,
                'bunga' => $bunga_bulanan,
                'sisa' => max(0, $sisa)
            ];
        }
    }

    $total_bayar = $pokok + $total_bunga;
    ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 style="font-size: 20px; margin: 0;">Ringkasan Pinjaman</h2>
        <span class="badge"><?= strtoupper($jenis) ?></span>
    </div>

    <div class="summary-cards">
        <div class="summary-card primary">
            <h3>Rp <?= number_format($angsuran, 0, ',', '.') ?></h3>
            <p>Angsuran per Bulan</p>
        </div>
        <div class="summary-card danger">
            <h3>Rp <?= number_format($total_bunga, 0, ',', '.') ?></h3>
            <p>Total Bunga</p>
        </div>
        <div class="summary-card dark">
            <h3>Rp <?= number_format($total_bayar, 0, ',', '.') ?></h3>
            <p>Total Pembayaran</p>
        </div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Jumlah Pinjaman:</span>
            <span class="info-value">Rp <?= number_format($pokok, 0, ',', '.') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Bunga per Tahun:</span>
            <span class="info-value"><?= $bunga ?>%</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lama Pinjaman:</span>
            <span class="info-value"><?= $tenor ?> Bulan</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Bunga yang Dibayar:</span>
            <span class="info-value" style="color: #e81f63;">Rp <?= number_format($total_bunga, 0, ',', '.') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Pembayaran:</span>
            <span class="info-value" style="color: #3f51b5;">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
        </div>
    </div>

    <div class="table-title">Rincian Angsuran per Bulan</div>
    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">Bulan</th>
                <th class="text-right" style="width: 22%">Angsuran</th>
                <th class="text-right" style="width: 22%">Pokok</th>
                <th class="text-right" style="width: 22%">Bunga</th>
                <th class="text-right" style="width: 24%">Sisa Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detail as $item) : ?>
                <tr>
                    <td class="text-center"><?= $item['bulan'] ?></td>
                    <td class="text-right">Rp <?= number_format($item['angsuran'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item['pokok'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item['bunga'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item['sisa'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem simulasi perhitungan pinjaman</p>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>

</html>