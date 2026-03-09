<!--items/v_form_stok_masuk.php -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Input Stok Barang</h1>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-package"></i> Form Input Stok</h5>
                </div>
                <div class="card-body">
                    <form id="formStokMasuk">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="no_transaksi">No. Transaksi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_transaksi" id="no_transaksi" value="<?= $no_transaksi ?>" readonly required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="supplier">Supplier <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="supplier" id="supplier" placeholder="Nama Supplier" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Metode Pembayaran <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metode_bayar" id="metode_cash" value="cash" checked>
                                            <label class="form-check-label" for="metode_cash">
                                                <i class="fe fe-dollar-sign"></i> Cash
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metode_bayar" id="metode_kredit" value="kredit" disabled>
                                            <label class="form-check-label" for="metode_kredit">
                                                <i class="fe fe-credit-card"></i> Kredit
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coa_kas_utang">COA Kas <span class="text-danger">*</span></label>
                                    <select class="form-control" name="coa_kas_utang" id="coa_kas_utang" required>
                                        <option value="">-- Pilih COA Kas --</option>
                                        <?php foreach ($coa_list as $coa) : ?>
                                            <option value="<?= $coa->no_sbb ?>"><?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Akun kas/utang yang akan di-kredit</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><i class="fe fe-list"></i> Detail Item Barang</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tableItems">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Barang <span class="text-danger">*</span></th>
                                        <th width="12%">Qty <span class="text-danger">*</span></th>
                                        <th width="18%">Harga Modal <span class="text-danger">*</span></th>
                                        <th width="18%">Harga Jual <span class="text-danger">*</span></th>
                                        <th width="12%">Subtotal</th>
                                        <th width="5%">
                                            <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                                                <i class="fe fe-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    <tr class="item-row">
                                        <td class="text-center row-number">1</td>
                                        <td>
                                            <select class="form-control form-control-sm select-item" name="id_item[]" required>
                                                <option value="">-- Pilih Barang --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right qty" name="qty[]" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right harga-modal format-rupiah" name="harga_modal[]" placeholder="0" value="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right harga-jual-input format-rupiah" name="harga_jual[]" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right subtotal" readonly value="0">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right font-weight-bold" id="grandTotal" readonly value="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fe fe-info"></i> <strong>Petunjuk:</strong>
                            <ul class="mb-0 pl-3">
                                <li>Tekan <strong>Enter</strong> untuk menambah baris baru</li>
                                <li>Pilih barang dari dropdown (ketik untuk mencari)</li>
                                <li>Harga modal akan otomatis update ke master barang (last price)</li>
                                <li>Stok akan otomatis bertambah setelah disimpan</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="<?= base_url('stok_masuk') ?>" class="btn btn-secondary">
                                    <i class="fe fe-x"></i> Batal
                                </a>
                                <button type="button" class="btn btn-primary" id="btnSimpan">
                                    <i class="fe fe-save"></i> Simpan
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    $(document).ready(function() {
        let rowCount = 1;

        // Init Select2 item & COA Kas
        initSelect2($('.select-item'));
        $('#coa_kas_utang').select2({
            placeholder: '-- Pilih COA Kas --',
            allowClear: true,
            width: '100%'
        });

        // Update label COA saat metode bayar berubah
        $('input[name="metode_bayar"]').on('change', function() {
            const label = $(this).val() === 'kredit' ? 'COA Utang Dagang' : 'COA Kas';
            $('label[for="coa_kas_utang"]').html(label + ' <span class="text-danger">*</span>');
            $('#coa_kas_utang option:first').text('-- Pilih ' + label + ' --');
        });

        $('#btnAddRow').on('click', function() {
            addRow();
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#itemRows tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
                calculateGrandTotal();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });

        $(document).on('keydown', '#tableItems input, #tableItems select', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                addRow();
            }
        });

        $(document).on('keyup', '.qty, .harga-modal', function() {
            calculateSubtotal($(this).closest('tr'));
            calculateGrandTotal();
        });

        $(document).on('keyup', '.format-rupiah', function() {
            let value = $(this).val().replace(/\./g, '');
            if (!isNaN(value) && value !== '') {
                $(this).val(formatRupiah(value));
            }
            calculateSubtotal($(this).closest('tr'));
            calculateGrandTotal();
        });

        // Button Simpan — langsung submit tanpa modal
        $('#btnSimpan').on('click', function() {
            let hasItem = false;
            $('.select-item').each(function() {
                if ($(this).val() !== '') {
                    hasItem = true;
                    return false;
                }
            });

            if (!hasItem) {
                alert('Pilih minimal 1 item barang!');
                return;
            }

            if (!$('#coa_kas_utang').val()) {
                alert('Pilih COA Kas/Utang terlebih dahulu!');
                return;
            }

            if (!$('#formStokMasuk')[0].checkValidity()) {
                $('#formStokMasuk')[0].reportValidity();
                return;
            }

            submitForm();
        });

        function addRow() {
            rowCount++;
            const newRow = `
                <tr class="item-row">
                    <td class="text-center row-number">${rowCount}</td>
                    <td>
                        <select class="form-control form-control-sm select-item" name="id_item[]" required>
                            <option value="">-- Pilih Barang --</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right qty" name="qty[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right harga-modal format-rupiah" name="harga_modal[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right harga-jual-input format-rupiah" name="harga_jual[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right subtotal" readonly value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemRows').append(newRow);
            const newSelect = $('#itemRows tr:last .select-item');
            initSelect2(newSelect);
            newSelect.select2('open');
        }

        function initSelect2(element) {
            element.select2({
                placeholder: '-- Pilih Barang --',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '<?= base_url("items/get_items") ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });

            element.on('select2:select', function(e) {
                const data = e.params.data;
                const row = $(this).closest('tr');

                if (data.harga_modal) {
                    row.find('.harga-modal').val(formatRupiah(parseFloat(data.harga_modal)));
                }
                if (data.harga_jual) {
                    row.find('.harga-jual-input').val(formatRupiah(parseFloat(data.harga_jual)));
                }

                row.find('.qty').focus();
                calculateSubtotal(row);
                calculateGrandTotal();
            });
        }

        function calculateSubtotal(row) {
            const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;
            const hargaModal = parseFloat(row.find('.harga-modal').val().replace(/\./g, '')) || 0;
            const subtotal = qty * hargaModal;
            row.find('.subtotal').val(formatRupiah(Math.floor(subtotal)));
        }

        function calculateGrandTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                const value = parseFloat($(this).val().replace(/\./g, '')) || 0;
                total += value;
            });
            $('#grandTotal').val(formatRupiah(Math.floor(total)));
        }

        function updateRowNumbers() {
            $('#itemRows tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
            rowCount = $('#itemRows tr').length;
        }

        function formatRupiah(angka) {
            let number = Math.floor(parseFloat(angka));
            if (isNaN(number) || number < 0) number = 0;
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function submitForm() {
            $('#btnSimpan').prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url("stok_masuk/save") ?>',
                type: 'POST',
                data: $('#formStokMasuk').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message);
                        $('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan data!');
                    $('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan');
                }
            });
        }
    });
</script>
