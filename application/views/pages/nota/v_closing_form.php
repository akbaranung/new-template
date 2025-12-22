<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">Proses Closing Kasir</h1>
                <a href="<?= base_url('closing_nota') ?>" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($total_transaksi == 0) : ?>
                <!-- No Transaction Alert -->
                <div class="alert alert-warning">
                    <i class="fe fe-alert-circle"></i> <strong>Tidak ada transaksi!</strong>
                    <p class="mb-0">Tidak ada nota yang perlu di-closing untuk tanggal <?= date('d F Y', strtotime($tanggal)) ?>.</p>
                </div>
                <a href="<?= base_url('nota/form') ?>" class="btn btn-primary">
                    <i class="fe fe-plus"></i> Buat Nota Baru
                </a>
            <?php else : ?>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-info text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Total Transaksi</h6>
                                        <h2 class="mb-0 text-white"><?= $total_transaksi ?></h2>
                                        <small>Nota</small>
                                    </div>
                                    <i class="fe fe-file-text fe-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Total Penjualan</h6>
                                        <h4 class="mb-0 text-white">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></h4>
                                    </div>
                                    <i class="fe fe-dollar-sign fe-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Total HPP</h6>
                                        <h4 class="mb-0 text-white">Rp <?= number_format($total_hpp, 0, ',', '.') ?></h4>
                                    </div>
                                    <i class="fe fe-trending-down fe-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-white">Laba Kotor</h6>
                                        <h4 class="mb-0 text-white">Rp <?= number_format($laba_kotor, 0, ',', '.') ?></h4>
                                    </div>
                                    <i class="fe fe-trending-up fe-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Breakdown -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0 text-white"><i class="fe fe-pie-chart"></i> Breakdown Penjualan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success"><i class="fe fe-dollar-sign"></i> Penjualan Cash</h6>
                                        <h3 class="text-success">Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="text-info"><i class="fe fe-credit-card"></i> Penjualan Piutang</h6>
                                        <h3 class="text-info">Rp <?= number_format($total_penjualan_piutang, 0, ',', '.') ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List Nota yang akan di-closing -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0 text-white"><i class="fe fe-list"></i> Daftar Nota yang Akan Di-Closing</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>No. Nota</th>
                                        <th>Waktu</th>
                                        <th>Customer</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">HPP</th>
                                        <th class="text-right">Laba</th>
                                        <th class="text-center">Metode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($nota_belum_closing as $n) :
                                        $badge_metode = $n->metode_bayar == 'cash' ? 'badge-success' : 'badge-info';
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $n->no_nota ?></td>
                                            <td><?= date('H:i', strtotime($n->tanggal)) ?></td>
                                            <td><?= $n->customer ?: '-' ?></td>
                                            <td class="text-right">Rp <?= number_format($n->total_penjualan, 0, ',', '.') ?></td>
                                            <td class="text-right">Rp <?= number_format($n->total_hpp, 0, ',', '.') ?></td>
                                            <td class="text-right text-success">Rp <?= number_format($n->laba_kotor, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_metode ?>">
                                                    <?= strtoupper($n->metode_bayar) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Pilih COA -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white"><i class="fe fe-book"></i> Pilih Chart of Account (COA)</h5>
                    </div>
                    <div class="card-body">
                        <form id="formClosing">
                            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">

                            <div class="alert alert-info">
                                <i class="fe fe-info"></i> <strong>Informasi:</strong><br>
                                Pilih akun COA untuk pencatatan jurnal closing ini. Jurnal akan dibuat secara otomatis.
                            </div>

                            <?php if ($total_penjualan_cash > 0) : ?>
                                <div class="form-group">
                                    <label for="coa_kas">
                                        COA Kas (Debit) <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="coa_kas" id="coa_kas" required>
                                        <option value="">-- Pilih COA Kas --</option>
                                        <?php foreach ($coa_list as $coa) : ?>
                                            <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">
                                        Akun kas akan di-debit sebesar Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?>
                                    </small>
                                </div>
                            <?php else : ?>
                                <input type="hidden" name="coa_kas" value="0">
                            <?php endif; ?>

                            <?php if ($total_penjualan_piutang > 0) : ?>
                                <div class="form-group">
                                    <label for="coa_piutang">
                                        COA Piutang (Debit) <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="coa_piutang" id="coa_piutang" required>
                                        <option value="">-- Pilih COA Piutang --</option>
                                        <?php foreach ($coa_list as $coa) : ?>
                                            <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">
                                        Akun piutang akan di-debit sebesar Rp <?= number_format($total_penjualan_piutang, 0, ',', '.') ?>
                                    </small>
                                </div>
                            <?php else : ?>
                                <input type="hidden" name="coa_piutang" value="0">
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="coa_penjualan">
                                    COA Penjualan (Kredit) <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="coa_penjualan" id="coa_penjualan" required>
                                    <option value="">-- Pilih COA Penjualan --</option>
                                    <?php foreach ($coa_list as $coa) : ?>
                                        <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    Akun penjualan akan di-kredit sebesar Rp <?= number_format($total_penjualan, 0, ',', '.') ?>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="coa_hpp">
                                    COA HPP/Beban Pokok Penjualan (Debit) <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="coa_hpp" id="coa_hpp" required>
                                    <option value="">-- Pilih COA HPP --</option>
                                    <?php foreach ($coa_list as $coa) : ?>
                                        <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    Akun HPP akan di-debit sebesar Rp <?= number_format($total_hpp, 0, ',', '.') ?>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="coa_persediaan">
                                    COA Persediaan Barang (Kredit) <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="coa_persediaan" id="coa_persediaan" required>
                                    <option value="">-- Pilih COA Persediaan --</option>
                                    <?php foreach ($coa_list as $coa) : ?>
                                        <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    Akun persediaan akan di-kredit sebesar Rp <?= number_format($total_hpp, 0, ',', '.') ?>
                                </small>
                            </div>

                            <hr>

                            <div class="text-right">
                                <a href="<?= base_url('closing_nota') ?>" class="btn btn-secondary">
                                    <i class="fe fe-x"></i> Batal
                                </a>
                                <button type="button" class="btn btn-info" id="btnPreview">
                                    <i class="fe fe-eye"></i> Preview Jurnal
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnProses">
                                    <i class="fe fe-lock"></i> Proses Closing
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Preview Jurnal -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fe fe-book"></i> Preview Jurnal Closing
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {

        // Preview Jurnal
        $('#btnPreview').on('click', function() {
            // Validasi form
            if (!$('#formClosing')[0].checkValidity()) {
                $('#formClosing')[0].reportValidity();
                return;
            }

            const coa_kas = $('#coa_kas option:selected').text();
            const coa_piutang = $('#coa_piutang option:selected').text();
            const coa_penjualan = $('#coa_penjualan option:selected').text();
            const coa_hpp = $('#coa_hpp option:selected').text();
            const coa_persediaan = $('#coa_persediaan option:selected').text();

            let html = `
                <div class="alert alert-info">
                    <i class="fe fe-info"></i> <strong>Preview Jurnal yang Akan Dibuat:</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="50%">Akun</th>
                                <th width="25%" class="text-right">Debit</th>
                                <th width="25%" class="text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            <?php if ($total_penjualan_cash > 0) : ?>
                html += `
                <tr class="table-success">
                    <td colspan="3"><strong>Jurnal 1: Penjualan Cash</strong></td>
                </tr>
                <tr>
                    <td>${coa_kas}</td>
                    <td class="text-right text-success"><strong>Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?></strong></td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>${coa_penjualan}</td>
                    <td class="text-right">-</td>
                    <td class="text-right text-danger"><strong>Rp <?= number_format($total_penjualan_cash, 0, ',', '.') ?></strong></td>
                </tr>
            `;
            <?php endif; ?>

            <?php if ($total_penjualan_piutang > 0) : ?>
                html += `
                <tr class="table-info">
                    <td colspan="3"><strong>Jurnal 2: Penjualan Piutang</strong></td>
                </tr>
                <tr>
                    <td>${coa_piutang}</td>
                    <td class="text-right text-success"><strong>Rp <?= number_format($total_penjualan_piutang, 0, ',', '.') ?></strong></td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>${coa_penjualan}</td>
                    <td class="text-right">-</td>
                    <td class="text-right text-danger"><strong>Rp <?= number_format($total_penjualan_piutang, 0, ',', '.') ?></strong></td>
                </tr>
            `;
            <?php endif; ?>

            html += `
                <tr class="table-warning">
                    <td colspan="3"><strong>Jurnal 3: HPP</strong></td>
                </tr>
                <tr>
                    <td>${coa_hpp}</td>
                    <td class="text-right text-success"><strong>Rp <?= number_format($total_hpp, 0, ',', '.') ?></strong></td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>${coa_persediaan}</td>
                    <td class="text-right">-</td>
                    <td class="text-right text-danger"><strong>Rp <?= number_format($total_hpp, 0, ',', '.') ?></strong></td>
                </tr>
                <tr class="table-secondary">
                    <td class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($total_penjualan + $total_hpp, 0, ',', '.') ?></strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($total_penjualan + $total_hpp, 0, ',', '.') ?></strong></td>
                </tr>
            `;

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#previewContent').html(html);
            $('#modalPreview').modal('show');
        });

        // Submit Form
        $('#formClosing').on('submit', function(e) {
            e.preventDefault();

            if (!confirm('Apakah Anda yakin ingin memproses closing?\n\nSetelah closing, nota tidak dapat diubah lagi!')) {
                return;
            }

            $('#btnProses').prop('disabled', true).html('<i class="fe fe-loader"></i> Memproses...');

            $.ajax({
                url: '<?= base_url("closing_nota/proses") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message);
                        $('#btnProses').prop('disabled', false).html('<i class="fe fe-lock"></i> Proses Closing');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat proses closing!');
                    $('#btnProses').prop('disabled', false).html('<i class="fe fe-lock"></i> Proses Closing');
                }
            });
        });
    });
</script>

<style>
    .fe-3x {
        font-size: 3rem;
    }
</style>