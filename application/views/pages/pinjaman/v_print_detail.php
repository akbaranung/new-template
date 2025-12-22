<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan Pinjaman - <?= $pengajuan->no_pengajuan ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 30px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-section h5 {
            background: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
            font-weight: bold;
        }

        .info-table td {
            padding: 5px 10px;
        }

        .info-table td:first-child {
            font-weight: 600;
            width: 35%;
        }

        .summary-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .summary-item:last-child {
            border-bottom: none;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 15px;
        }

        .summary-item .label {
            font-weight: 600;
        }

        .summary-item .value {
            text-align: right;
        }

        .table {
            font-size: 12px;
        }

        .table thead {
            background: #343a40;
            color: white;
        }

        .table th,
        .table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .table tfoot {
            background: #f8f9fa;
            font-weight: bold;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
        }

        .signature-box {
            text-align: center;
            margin-top: 60px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 80px auto 5px auto;
        }

        @media print {
            body {
                padding: 15px;
            }

            .no-print {
                display: none;
            }

            .table thead {
                background-color: #343a40 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <!-- Button Print -->
    <div class="no-print text-right mb-3">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fe fe-printer"></i> Print / Save PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fe fe-x"></i> Tutup
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <h2>DETAIL PENGAJUAN PINJAMAN</h2>
        <p><?= $pengajuan->no_pengajuan ?></p>
        <p>Dicetak pada: <?= date('l, d F Y - H:i:s') ?></p>
    </div>

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

    <!-- Status -->
    <div class="text-center mb-4">
        <h4>Status Pengajuan:
            <span class="badge <?= $badge ?>">
                <?= strtoupper($pengajuan->status) ?>
            </span>
        </h4>
    </div>

    <!-- Info Nasabah & Pinjaman -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-section">
                <h5>DATA NASABAH</h5>
                <table class="info-table" width="100%">
                    <tr>
                        <td>No. Nasabah</td>
                        <td>: <?= $pengajuan->no_cib ?></td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>: <?= $pengajuan->nama_nasabah ?></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>: <?= $pengajuan->no_ktp ?></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: <?= $pengajuan->alamat ?></td>
                    </tr>
                    <tr>
                        <td>No. HP</td>
                        <td>: <?= $pengajuan->no_telp ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-section">
                <h5>DATA PINJAMAN</h5>
                <table class="info-table" width="100%">
                    <tr>
                        <td>Jenis Pinjaman</td>
                        <td>: <?= ucwords(str_replace('_', ' ', $pengajuan->jenis_pinjaman)) ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>: <?= format_indo($pengajuan->tanggal_pengajuan) ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Dropping</td>
                        <td>: <?= format_indo($pengajuan->tanggal_dropping) ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Bunga</td>
                        <td>: <?= strtoupper($pengajuan->jenis_bunga) ?></td>
                    </tr>
                    <tr>
                        <td>Bunga Per Tahun</td>
                        <td>: <?= $pengajuan->bunga_per_tahun ?>%</td>
                    </tr>
                    <tr>
                        <td>Lama Pinjaman</td>
                        <td>: <?= $pengajuan->lama_pinjaman ?> Bulan</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Keterangan -->
    <?php if ($pengajuan->keterangan) : ?>
        <div class="info-section">
            <h5>KETERANGAN</h5>
            <p style="padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff;">
                <?= nl2br($pengajuan->keterangan) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-item">
            <span class="label">Jumlah Pinjaman:</span>
            <span class="value"><?= rupiah($pengajuan->jumlah_pinjaman, 0) ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Angsuran per Bulan:</span>
            <span class="value"><?= rupiah($pengajuan->angsuran_per_bulan, 0) ?></span>
        </div>
        <div class="summary-item">
            <span class="label">Total Bunga:</span>
            <span class="value"><?= rupiah($pengajuan->total_bunga, 0) ?></span>
        </div>
        <div class="summary-item">
            <span class="label">TOTAL PEMBAYARAN:</span>
            <span class="value"><?= rupiah($pengajuan->total_pembayaran, 0) ?></span>
        </div>
    </div>

    <!-- Jadwal Angsuran -->
    <div class="info-section">
        <h5>JADWAL ANGSURAN (<?= $pengajuan->lama_pinjaman ?> BULAN)</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="text-center" width="8%">Ke</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Angsuran</th>
                    <th class="text-center">Pokok</th>
                    <th class="text-center">Bunga</th>
                    <th class="text-center">Sisa Pinjaman</th>
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
                            <span class="badge <?= $status_class ?>">
                                <?= strtoupper($da->status_bayar) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">TOTAL:</th>
                    <th class="text-right"><?= rupiah($total_angsuran, 0) ?></th>
                    <th class="text-right"><?= rupiah($total_pokok, 0) ?></th>
                    <th class="text-right"><?= rupiah($total_bunga_detail, 0) ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer & Tanda Tangan -->
    <div class="footer">
        <div class="row">
            <div class="col-md-6">
                <div class="signature-box">
                    <p>Pemohon,</p>
                    <div class="signature-line"></div>
                    <p><strong><?= $pengajuan->nama_nasabah ?></strong></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="signature-box">
                    <p>Menyetujui,</p>
                    <div class="signature-line"></div>
                    <p><strong>( .......................... )</strong></p>
                    <p>Bagian Keuangan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4" style="font-size: 11px; color: #999;">
        <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
        <p>Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB</p>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>

</html>