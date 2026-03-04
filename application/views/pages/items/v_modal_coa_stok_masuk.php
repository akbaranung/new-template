<!-- items/v_modal_coa_stok_masuk.php -->
<form id="formCOA">
    <div class="alert alert-info">
        <i class="fe fe-info"></i> <strong>Informasi:</strong><br>
        Pilih akun COA untuk pencatatan jurnal input stok ini.
    </div>

    <div class="form-group">
        <label for="coa_persediaan">
            COA Persediaan (Debit) <span class="text-danger">*</span>
        </label>
        <select class="form-control" name="coa_persediaan" id="coa_persediaan" required>
            <option value="">-- Pilih COA Persediaan --</option>
            <?php foreach ($coa_list as $coa) : ?>
                <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Akun persediaan barang akan di-debit</small>
    </div>

    <div class="form-group">
        <label for="coa_kas_utang">
            <?= $metode_bayar == 'cash' ? 'COA Kas' : 'COA Utang Dagang' ?> (Kredit) <span class="text-danger">*</span>
        </label>
        <select class="form-control" name="coa_kas_utang" id="coa_kas_utang" required>
            <option value="">-- Pilih COA <?= $metode_bayar == 'cash' ? 'Kas' : 'Utang Dagang' ?> --</option>
            <?php foreach ($coa_list as $coa) : ?>
                <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">
            <?= $metode_bayar == 'cash' ? 'Akun kas akan di-kredit' : 'Akun utang dagang akan di-kredit' ?>
        </small>
    </div>

    <div class="card bg-light">
        <div class="card-body">
            <h6 class="font-weight-bold">Preview Jurnal:</h6>
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Persediaan Barang</td>
                        <td class="text-right">Rp XXX</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td><?= $metode_bayar == 'cash' ? 'Kas' : 'Utang Dagang' ?></td>
                        <td class="text-right">-</td>
                        <td class="text-right">Rp XXX</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-right mt-3">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fe fe-x"></i> Batal
        </button>
        <button type="button" class="btn btn-primary" id="btnConfirmCOA">
            <i class="fe fe-check"></i> Konfirmasi & Simpan
        </button>
    </div>
</form>
