<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="page-title">Ajukan Pinjaman</h1>

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
                    <form action="<?= base_url('pinjaman/submit_pengajuan') ?>" method="POST" id="formPengajuan">

                        <!-- Data Nasabah -->
                        <h5 class="mb-3 text-primary">Data Nasabah</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_nasabah">Pilih Nasabah <span class="text-danger">*</span></label>
                                    <select name="id_nasabah" id="id_nasabah" class="form-control" required>
                                        <option value="">:: Pilih Nasabah</option>
                                        <?php foreach ($nasabah as $n) : ?>
                                            <option value="<?= $n->no_cib ?>" data-nik="<?= $n->no_ktp ?>" data-alamat="<?= $n->alamat ?>" data-hp="<?= $n->no_telp ?>">
                                                <?= $n->no_cib ?> - <?= $n->nama ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIK</label>
                                    <input type="text" class="form-control" id="display_nik" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" id="display_alamat" rows="2" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. HP</label>
                                    <input type="text" class="form-control" id="display_hp" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Data Pinjaman -->
                        <h5 class="mb-3 text-primary">Data Pinjaman</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_pinjaman">Jumlah Pinjaman (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control" placeholder="Contoh: 10000000" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lama_pinjaman">Lama Pinjaman (Bulan) <span class="text-danger">*</span></label>
                                    <input type="number" name="lama_pinjaman" id="lama_pinjaman" class="form-control" placeholder="Contoh: 12" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bunga_per_tahun">Bunga Per Tahun (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="bunga_per_tahun" id="bunga_per_tahun" class="form-control" placeholder="Contoh: 12" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_bunga">Jenis Perhitungan Bunga <span class="text-danger">*</span></label>
                                    <select name="jenis_bunga" id="jenis_bunga" class="form-control" required>
                                        <option value="">:: Pilih Jenis Bunga</option>
                                        <option value="anuitas">Anuitas (Efektif)</option>
                                        <option value="flat">Flat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_pinjaman">Jenis Pinjaman <span class="text-danger">*</span></label>
                                    <select name="jenis_pinjaman" id="jenis_pinjaman" class="form-control" required>
                                        <option value="">:: Pilih Jenis Pinjaman</option>
                                        <option value="modal_kerja">Modal Kerja</option>
                                        <option value="konsumsi">Konsumsi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_dropping">Tanggal Dropping <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_dropping" id="tanggal_dropping" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Preview Perhitungan -->
                        <div id="previewSection" style="display: none;">
                            <h5 class="mb-3 text-primary">Preview Perhitungan</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white mb-3">
                                        <div class="card-body">
                                            <h4 id="preview_angsuran" class="mb-0 text-white">Rp 0</h4>
                                            <h6 class="mb-1 text-white">Angsuran per Bulan</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-danger text-white mb-3">
                                        <div class="card-body">
                                            <h4 id="preview_bunga" class="mb-0 text-white">Rp 0</h4>
                                            <h6 class="mb-1 text-white">Total Bunga</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-dark text-white mb-3">
                                        <div class="card-body">
                                            <h4 id="preview_total" class="mb-0 text-white">Rp 0</h4>
                                            <h6 class="mb-1 text-white">Total Pembayaran</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-info" id="btnPreview">
                                    <i class="fa fa-eye"></i> Preview Perhitungan
                                </button>
                                <a href="<?= base_url('pinjaman/simulasi') ?>" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i> Ajukan Pinjaman
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    // Format Rupiah
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        const number = parseInt(angka);
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function unformatRupiah(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
    }

    // Auto format input jumlah pinjaman
    $('#jumlah_pinjaman').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            $(this).val(parseInt(val).toLocaleString('id-ID'));
        }
    });

    // Display data nasabah saat dipilih
    $('#id_nasabah').on('change', function() {
        const selected = $(this).find(':selected');
        $('#display_nik').val(selected.data('nik') || '');
        $('#display_alamat').val(selected.data('alamat') || '');
        $('#display_hp').val(selected.data('hp') || '');
    });

    // Auto-fill dari URL parameter (dari simulasi)
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('jumlah')) {
            const jumlah = parseInt(urlParams.get('jumlah'));
            $('#jumlah_pinjaman').val(jumlah.toLocaleString('id-ID'));
        }

        if (urlParams.has('tenor')) {
            $('#lama_pinjaman').val(urlParams.get('tenor'));
        }

        if (urlParams.has('bunga')) {
            $('#bunga_per_tahun').val(urlParams.get('bunga'));
        }

        if (urlParams.has('jenis')) {
            $('#jenis_bunga').val(urlParams.get('jenis'));
        }

        // Set tanggal dropping default (besok)
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        $('#tanggal_dropping').val(tomorrow.toISOString().split('T')[0]);
    });

    // Hitung Anuitas
    function hitungAnuitas(pokok, bunga_tahunan, tenor) {
        const bunga_bulanan = bunga_tahunan / 100 / 12;
        const angsuran = pokok * (bunga_bulanan * Math.pow(1 + bunga_bulanan, tenor)) /
            (Math.pow(1 + bunga_bulanan, tenor) - 1);

        const total_bayar = angsuran * tenor;
        const total_bunga = total_bayar - pokok;

        return {
            angsuran_bulanan: angsuran,
            total_bunga: total_bunga,
            total_bayar: total_bayar
        };
    }

    // Hitung Flat
    function hitungFlat(pokok, bunga_tahunan, tenor) {
        const bunga_bulanan = (pokok * (bunga_tahunan / 100) * (tenor / 12)) / tenor;
        const pokok_bulanan = pokok / tenor;
        const angsuran = pokok_bulanan + bunga_bulanan;

        const total_bunga = bunga_bulanan * tenor;
        const total_bayar = pokok + total_bunga;

        return {
            angsuran_bulanan: angsuran,
            total_bunga: total_bunga,
            total_bayar: total_bayar
        };
    }

    // Preview Perhitungan
    $('#btnPreview').on('click', function() {
        const pokok = unformatRupiah($('#jumlah_pinjaman').val());
        const tenor = parseInt($('#lama_pinjaman').val());
        const bunga = parseFloat($('#bunga_per_tahun').val());
        const jenis = $('#jenis_bunga').val();

        if (!pokok || !tenor || !bunga || !jenis) {
            alert('Harap isi semua field pinjaman terlebih dahulu!');
            return;
        }

        let hasil;
        if (jenis === 'anuitas') {
            hasil = hitungAnuitas(pokok, bunga, tenor);
        } else {
            hasil = hitungFlat(pokok, bunga, tenor);
        }

        // Update preview
        $('#preview_angsuran').text(formatRupiah(hasil.angsuran_bulanan));
        $('#preview_bunga').text(formatRupiah(hasil.total_bunga));
        $('#preview_total').text(formatRupiah(hasil.total_bayar));

        // Show preview section
        $('#previewSection').slideDown();

        // Scroll to preview
        $('html, body').animate({
            scrollTop: $('#previewSection').offset().top - 20
        }, 500);
    });

    // Validasi sebelum submit
    $('#formPengajuan').on('submit', function(e) {
        const id_nasabah = $('#id_nasabah').val();

        if (!id_nasabah) {
            e.preventDefault();
            alert('Harap pilih nasabah terlebih dahulu!');
            return false;
        }

        return confirm('Apakah Anda yakin ingin mengajukan pinjaman ini?');
    });
</script>