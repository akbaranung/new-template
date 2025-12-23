<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">Approval & Pencairan Pinjaman</h1>
                <a href="<?= base_url('pinjaman/daftar_pengajuan') ?>" class="btn btn-secondary">
                    <i class="fe fe-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Info Pengajuan -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">
                        <i class="fe fe-file-text"></i> Informasi Pengajuan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Data Nasabah -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fe fe-user"></i> Data Nasabah
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>No. Nasabah</strong></td>
                                    <td>: <?= $pengajuan->no_cib ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td>: <?= $pengajuan->nama_nasabah ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIK</strong></td>
                                    <td>: <?= $pengajuan->no_ktp ?></td>
                                </tr>
                                <tr>
                                    <td><strong>No. HP</strong></td>
                                    <td>: <?= $pengajuan->no_telp ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Data Pinjaman -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fe fe-credit-card"></i> Data Pinjaman
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>No. Pengajuan</strong></td>
                                    <td>: <?= $pengajuan->no_pengajuan ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Pinjaman</strong></td>
                                    <td>: <strong><?= rupiah($pengajuan->jumlah_pinjaman, 0) ?></strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Tenor</strong></td>
                                    <td>: <?= $pengajuan->lama_pinjaman ?> Bulan</td>
                                </tr>
                                <tr>
                                    <td><strong>Angsuran/Bulan</strong></td>
                                    <td>: <?= rupiah($pengajuan->angsuran_per_bulan, 0) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pembayaran</strong></td>
                                    <td>: <?= rupiah($pengajuan->total_pembayaran, 0) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Approval / Reject -->
            <form action="<?= base_url('pinjaman/submit_approval') ?>" method="POST" id="formApproval">
                <input type="hidden" name="id_pengajuan" value="<?= $pengajuan->id ?>">

                <!-- Pilih Action -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0 text-white">
                            <i class="fe fe-check-square"></i> Keputusan Approval
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Pilih Keputusan <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="action_approve" name="action" value="approve" class="custom-control-input" required>
                                        <label class="custom-control-label" for="action_approve">
                                            <i class="fe fe-check-circle text-success"></i>
                                            <strong>Approve & Cairkan Pinjaman</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="action_reject" name="action" value="reject" class="custom-control-input" required>
                                        <label class="custom-control-label" for="action_reject">
                                            <i class="fe fe-x-circle text-danger"></i>
                                            <strong>Reject / Tolak Pengajuan</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pencairan (Muncul kalau Approve) -->
                <div class="card shadow mb-4" id="formPencairan" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 text-white">
                            <i class="fe fe-dollar-sign"></i> Data Pencairan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Rekening Asal (Koperasi) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_rekening_asal">Rekening Asal (Koperasi) <span class="text-danger">*</span></label>
                                    <input type="text" name="no_rekening_asal" id="no_rekening_asal" class="form-control" placeholder="Contoh: 1234567890">
                                    <small class="form-text text-muted">Nomor rekening koperasi sebagai sumber dana</small>
                                </div>
                            </div>

                            <!-- Rekening Tujuan (Nasabah) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_rekening_tujuan">Rekening Tujuan (Nasabah) <span class="text-danger">*</span></label>
                                    <select name="no_rekening_tujuan" id="no_rekening_tujuan" class="form-control">
                                        <option value="">:: Pilih Rekening Nasabah</option>
                                        <?php foreach ($rekening_nasabah as $rek) : ?>
                                            <option value="<?= $rek->no_rekening ?>">
                                                <?= $rek->no_rekening ?> - <?= $rek->jenis_tabungan ?> (Saldo: <?= rupiah($rek->saldo, 0) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Pilih rekening nasabah untuk pencairan dana</small>
                                </div>
                            </div>

                            <!-- COA Debit -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coa_debit">COA Debit <span class="text-danger">*</span></label>
                                    <select name="coa_debit" id="coa_debit" class="form-control">
                                        <option value="">:: Pilih COA Debit</option>
                                        <?php foreach ($coa as $c) : ?>
                                            <option value="<?= $c->no_sbb ?>">
                                                <?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Akun yang akan didebit (biasanya: Piutang Pinjaman)</small>
                                </div>
                            </div>

                            <!-- COA Kredit -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coa_kredit">COA Kredit <span class="text-danger">*</span></label>
                                    <select name="coa_kredit" id="coa_kredit" class="form-control">
                                        <option value="">:: Pilih COA Kredit</option>
                                        <?php foreach ($coa as $c) : ?>
                                            <option value="<?= $c->no_sbb ?>">
                                                <?= $c->no_sbb ?> - <?= $c->nama_perkiraan ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Akun yang akan dikredit (biasanya: Kas/Bank)</small>
                                </div>
                            </div>

                            <!-- Catatan Keuangan -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="catatan_keuangan">Catatan Keuangan</label>
                                    <textarea name="catatan_keuangan" id="catatan_keuangan" class="form-control" rows="3" placeholder="Catatan tambahan untuk bagian keuangan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info">
                            <i class="fe fe-info"></i>
                            <strong>Informasi:</strong> Setelah approve, dana sebesar
                            <strong><?= rupiah($pengajuan->jumlah_pinjaman, 0) ?></strong>
                            akan otomatis ditambahkan ke saldo rekening nasabah yang dipilih.
                        </div>
                    </div>
                </div>

                <!-- Form Alasan Reject (Muncul kalau Reject) -->
                <div class="card shadow mb-4" id="formReject" style="display: none;">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0 text-white">
                            <i class="fe fe-x-circle"></i> Alasan Penolakan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="alasan_reject">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="alasan_reject" id="alasan_reject" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan pengajuan pinjaman..."></textarea>
                            <small class="form-text text-muted">Alasan ini akan dicatat dalam sistem</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fe fe-alert-triangle"></i>
                            <strong>Perhatian:</strong> Pengajuan yang ditolak tidak dapat diproses kembali. Nasabah harus mengajukan pinjaman baru.
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('pinjaman') ?>" class="btn btn-secondary">
                                <i class="fe fe-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary text-white" id="btnSubmit" disabled>
                                <i class="fe fe-check"></i> <span id="btnText">Pilih Keputusan</span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle Radio Change
        $('input[name="action"]').on('change', function() {
            const action = $(this).val();

            if (action === 'approve') {
                $('#formPencairan').slideDown();
                $('#formReject').slideUp();
                $('#btnSubmit').prop('disabled', false);
                $('#btnText').html('Approve & Cairkan');
                $('#btnSubmit').removeClass('btn-danger').addClass('btn-success');

                // Set required
                $('#no_rekening_asal, #no_rekening_tujuan, #coa_debit, #coa_kredit').prop('required', true);
                $('#alasan_reject').prop('required', false);

            } else if (action === 'reject') {
                $('#formReject').slideDown();
                $('#formPencairan').slideUp();
                $('#btnSubmit').prop('disabled', false);
                $('#btnText').html('Tolak Pengajuan');
                $('#btnSubmit').removeClass('btn-success').addClass('btn-danger');

                // Set required
                $('#alasan_reject').prop('required', true);
                $('#no_rekening_asal, #no_rekening_tujuan, #coa_debit, #coa_kredit').prop('required', false);
            }
        });

        // Validasi sebelum submit
        $('#formApproval').on('submit', function(e) {
            const action = $('input[name="action"]:checked').val();

            if (action === 'approve') {
                const confirmMsg = 'Apakah Anda yakin ingin APPROVE dan CAIRKAN pinjaman ini?\n\n' +
                    'Jumlah: <?= rupiah($pengajuan->jumlah_pinjaman, 0) ?>\n' +
                    'Dana akan otomatis ditransfer ke rekening nasabah.';

                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                    return false;
                }
            } else if (action === 'reject') {
                const alasan = $('#alasan_reject').val().trim();

                if (!alasan) {
                    e.preventDefault();
                    alert('Harap isi alasan penolakan!');
                    return false;
                }

                if (!confirm('Apakah Anda yakin ingin REJECT pengajuan ini?\n\nPengajuan yang ditolak tidak dapat diproses kembali.')) {
                    e.preventDefault();
                    return false;
                }
            }

            return true;
        });
    });
</script>