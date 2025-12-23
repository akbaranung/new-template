<style>
    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .result-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        margin-bottom: 20px;
    }

    .result-card h3 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        opacity: 0.9;
    }

    .result-card .amount {
        font-size: 2rem;
        font-weight: bold;
    }

    .summary-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .summary-box .row {
        margin-bottom: 10px;
    }

    .summary-box .row:last-child {
        margin-bottom: 0;
        padding-top: 10px;
        border-top: 2px solid #dee2e6;
        font-weight: bold;
    }

    .table-installment {
        font-size: 0.9rem;
    }

    .table-installment thead {
        background-color: #667eea;
        color: white;
    }

    .badge-method {
        font-size: 0.9rem;
        padding: 8px 15px;
    }

    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 15px;
            background: white;
        }

        .container-fluid {
            padding: 0;
        }

        .result-card {
            background: #667eea !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            page-break-inside: avoid;
            border: 1px solid #667eea;
        }

        .result-card:nth-child(2) {
            background: #e81f63 !important;
            border: 1px solid #e81f63;
        }

        .result-card:nth-child(3) {
            background: #2c3e50 !important;
            border: 1px solid #2c3e50;
        }

        .table {
            page-break-inside: auto;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .table thead {
            display: table-header-group;
            background-color: #667eea !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .summary-box {
            border: 1px solid #667eea;
            page-break-inside: avoid;
        }

        .page-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .print-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .print-date {
            display: block !important;
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }
    }

    .print-header {
        display: none;
    }

    .print-date {
        display: none;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Print Header (hanya muncul saat print) -->
            <div class="print-header">
                <h2>SIMULASI PERHITUNGAN PINJAMAN</h2>
                <div class="print-date" id="printDate"></div>
            </div>

            <h1 class="page-title no-print">Simulasi perhitungan pinjaman</h1>
            <div class="card shadow mb-4 no-print">
                <div class="card-body">
                    <form action="" id="loanForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_pinjaman">Jumlah pinjaman (Rp)</label>
                                    <input type="text" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control" placeholder="Contoh: 1000000" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lama_pinjaman">Lama pinjaman (bulan)</label>
                                    <input type="number" name="lama_pinjaman" id="lama_pinjaman" class="form-control" placeholder="Contoh: 12" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bunga_pinjaman">Bunga per tahun (%)</label>
                                    <input type="number" name="bunga_pinjaman" id="bunga_pinjaman" class="form-control" placeholder="Contoh: 12" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_bunga">Jenis perhitungan bunga</label>
                                    <select name="jenis_bunga" id="jenis_bunga" class="form-control" required>
                                        <option value="">:: Pilih jenis bunga</option>
                                        <option value="anuitas">Anuitas</option>
                                        <option value="flat">Flat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-calculator"></i> Hitung simulasi
                                </button>
                                <button type="reset" class="btn btn-secondary" id="resetBtn">
                                    <i class="fa fa-redo"></i> Reset
                                </button>
                                <button type="button" class="btn btn-success text-white" id="printBtn" style="display: none;">
                                    <i class="fa fa-print"></i> Print / Unduh PDF
                                </button>
                                <button type="button" class="btn btn-info text-white" id="ajukanBtn" style="display: none;">
                                    <i class="fa fa-file-alt"></i> Ajukan Pinjaman
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="resultSection" style="display: none">
                <div class="row">
                    <div class="col-md-4">
                        <div class="result-card text-white" style="background: #3f51b5;">
                            <span class="h2 mb-0 text-white " id="angsuran_bulanan">0</span>
                            <p class="small mb-0">Angsuran per bulan</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-card text-white" style="background: #e81f63">
                            <span class="h2 mb-0 text-white " id="total_bunga">0</span>
                            <p class="small mb-0">Total bunga</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="result-card" style="background: #2c3e50">
                            <span class="h2 mb-0 text-white " id="total_bayar">0</span>
                            <p class="small mb-0">Total pembayaran</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Ringkasan pinjaman</h5>
                            <span class="badge badge-primary badge-method" id="method_badge">0</span>
                        </div>
                        <div class="summary-box">
                            <div class="row">
                                <div class="col-6">Jumlah pinjaman:</div>
                                <div class="col-6 text-right" id="summary_pokok">0</div>
                            </div>
                            <div class="row">
                                <div class="col-6">Bunga per Tahun:</div>
                                <div class="col-6 text-right" id="summary_bunga">-</div>
                            </div>
                            <div class="row">
                                <div class="col-6">Lama Pinjaman:</div>
                                <div class="col-6 text-right" id="summary_tenor">-</div>
                            </div>
                            <div class="row">
                                <div class="col-6">Total Bunga yang Dibayar:</div>
                                <div class="col-6 text-right text-danger" id="summary_total_bunga">-</div>
                            </div>
                            <div class="row">
                                <div class="col-6">Total Pembayaran:</div>
                                <div class="col-6 text-right text-primary" id="summary_total_bayar">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Rincian angsuran per bulan</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered">
                                <thead class="bg-dark">
                                    <tr>
                                        <th class="text-center w-10">Bulan</th>
                                        <th class="text-right w-22">Angsuran</th>
                                        <th class="text-right w-22">Pokok</th>
                                        <th class="text-right w-22">Bunga</th>
                                        <th class="text-right w-22">Sisa pinjaman</th>
                                    </tr>
                                </thead>
                                <tbody id="installmentTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        const number = parseInt(angka);
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function unformatRupiah(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
    }

    $('#jumlah_pinjaman').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            $(this).val(parseInt(val).toLocaleString('id-ID'));
        }
    })

    // Proses hitung anuitas
    function hitungAnuitas(pokok, bunga_tahunan, tenor) {
        const bunga_bulanan = bunga_tahunan / 100 / 12;
        const angsuran = pokok * (bunga_bulanan * Math.pow(1 + bunga_bulanan, tenor)) / (Math.pow(1 + bunga_bulanan, tenor) - 1);

        let detail = [];
        let sisa = pokok;
        let total_bunga = 0;

        for (let i = 1; i <= tenor; i++) {
            const bunga = sisa * bunga_bulanan;
            const pokok_bayar = angsuran - bunga;
            sisa -= pokok_bayar;

            total_bunga += bunga;

            detail.push({
                bulan: i,
                angsuran: angsuran,
                pokok: pokok_bayar,
                bunga: bunga,
                sisa: Math.max(0, sisa)
            });
        }

        return {
            angsuran_bulanan: angsuran,
            total_bunga: total_bunga,
            total_bayar: pokok + total_bunga,
            detail: detail
        };
    }

    function hitungFlat(pokok, bunga_tahunan, tenor) {
        const bunga_bulanan = (pokok * (bunga_tahunan / 100) * (tenor / 12)) / tenor;
        const pokok_bulanan = pokok / tenor;
        const angsuran = pokok_bulanan + bunga_bulanan;

        let detail = [];
        let sisa = pokok;
        let total_bunga = 0;

        for (let i = 1; i <= tenor; i++) {
            sisa -= pokok_bulanan;
            total_bunga += bunga_bulanan;

            detail.push({
                bulan: i,
                angsuran: angsuran,
                pokok: pokok_bulanan,
                bunga: bunga_bulanan,
                sisa: Math.max(0, sisa)
            });
        }

        return {
            angsuran_bulanan: angsuran,
            total_bunga: total_bunga,
            total_bayar: pokok + total_bunga,
            detail: detail
        };
    }

    // Handle Form Submit
    $('#loanForm').on('submit', function(e) {
        e.preventDefault();

        const pokok = unformatRupiah($('#jumlah_pinjaman').val());
        const tenor = parseInt($('#lama_pinjaman').val());
        const bunga = parseFloat($('#bunga_pinjaman').val());
        const jenis = $('#jenis_bunga').val();

        if (!pokok || !tenor || !bunga || !jenis) {
            alert('Harap isi semua field!');
            return;
        }

        let hasil;
        if (jenis === 'anuitas') {
            hasil = hitungAnuitas(pokok, bunga, tenor);
        } else {
            hasil = hitungFlat(pokok, bunga, tenor);
        }

        // Update Summary Cards
        $('#angsuran_bulanan').text(formatRupiah(hasil.angsuran_bulanan));
        $('#total_bunga').text(formatRupiah(hasil.total_bunga));
        $('#total_bayar').text(formatRupiah(hasil.total_bayar));

        // Update Method Badge
        $('#method_badge').text(jenis === 'anuitas' ? 'ANUITAS' : 'FLAT');

        // Update Summary Box
        $('#summary_pokok').text(formatRupiah(pokok));
        $('#summary_bunga').text(bunga + '% per tahun');
        $('#summary_tenor').text(tenor + ' bulan');
        $('#summary_total_bunga').text(formatRupiah(hasil.total_bunga));
        $('#summary_total_bayar').text(formatRupiah(hasil.total_bayar));

        // Update Table
        let tableHTML = '';
        hasil.detail.forEach(item => {
            tableHTML += `
            <tr>
                <td class="text-center">${item.bulan}</td>
                <td class="text-right">${formatRupiah(item.angsuran)}</td>
                <td class="text-right">${formatRupiah(item.pokok)}</td>
                <td class="text-right">${formatRupiah(item.bunga)}</td>
                <td class="text-right">${formatRupiah(item.sisa)}</td>
            </tr>
        `;
        });
        $('#installmentTable').html(tableHTML);

        // Show Result & Print Button
        $('#resultSection').slideDown();
        $('#printBtn').show();
        $('#ajukanBtn').show();
        $('html, body').animate({
            scrollTop: $('#resultSection').offset().top - 20
        }, 500);
    });

    // Handle Ajukan Pinjaman - Redirect dengan data
    $('#ajukanBtn').on('click', function() {
        const pokok = unformatRupiah($('#jumlah_pinjaman').val());
        const tenor = parseInt($('#lama_pinjaman').val());
        const bunga = parseFloat($('#bunga_pinjaman').val());
        const jenis = $('#jenis_bunga').val();

        // Redirect ke halaman ajukan dengan parameter
        const url = '<?= base_url("pinjaman/ajukan") ?>?' +
            'jumlah=' + pokok +
            '&tenor=' + tenor +
            '&bunga=' + bunga +
            '&jenis=' + jenis;

        window.location.href = url;
    });

    // Handle Reset
    $('#resetBtn').on('click', function() {
        $('#resultSection').slideUp();
        $('#printBtn').hide();
        $('#ajukanBtn').hide(); // Tambahkan ini
    });

    // Handle Print - Redirect ke halaman print
    $('#printBtn').on('click', function() {
        // Ambil data dari form
        const pokok = unformatRupiah($('#jumlah_pinjaman').val());
        const tenor = parseInt($('#lama_pinjaman').val());
        const bunga = parseFloat($('#bunga_pinjaman').val());
        const jenis = $('#jenis_bunga').val();

        // Buat form dinamis untuk POST
        const form = $('<form>', {
            'method': 'POST',
            'action': '<?= base_url("pinjaman/print_simulasi") ?>',
            'target': '_blank'
        });

        // Tambah hidden input
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'jumlah_pinjaman',
            'value': pokok
        }));
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'lama_pinjaman',
            'value': tenor
        }));
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'bunga_pinjaman',
            'value': bunga
        }));
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'jenis_bunga',
            'value': jenis
        }));

        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();
    });
</script>