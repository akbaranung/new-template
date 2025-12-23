<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Buat Nota Penjualan</h1>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="fe fe-shopping-cart"></i> Form Nota Penjualan</h5>
                </div>
                <div class="card-body">
                    <form id="formNota">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="no_nota">No. Nota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_nota" id="no_nota" value="<?= $no_nota ?>" readonly required>
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
                                    <label for="customer">Customer</label>
                                    <input type="text" class="form-control" name="customer" id="customer" placeholder="Nama Customer (Opsional)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Metode Pembayaran <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metode_bayar" id="metode_cash" value="cash" checked>
                                            <label class="form-check-label" for="metode_cash">
                                                <i class="fe fe-dollar-sign"></i> Cash
                                            </label>
                                        </div>
                                        <!-- <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="metode_bayar" id="metode_piutang" value="piutang">
                                            <label class="form-check-label" for="metode_piutang">
                                                <i class="fe fe-credit-card"></i> Piutang
                                            </label>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><i class="fe fe-list"></i> Detail Item Barang</h5>

                        <!-- Table Detail Items -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tableItems">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Barang <span class="text-danger">*</span></th>
                                        <th width="10%" class="text-center">Stok</th>
                                        <th width="12%">Qty <span class="text-danger">*</span></th>
                                        <th width="15%">Harga Jual <span class="text-danger">*</span></th>
                                        <th width="13%">HPP</th>
                                        <th width="13%">Subtotal</th>
                                        <th width="5%">
                                            <button type="button" class="btn btn-sm btn-primary" id="btnAddRow">
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
                                        <td class="text-center">
                                            <span class="badge badge-info stok-display">0</span>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right qty" name="qty[]" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right harga-jual format-rupiah" name="harga_jual[]" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right hpp-display" readonly value="0">
                                            <input type="hidden" class="hpp-value" value="0">
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
                                    <tr class="bg-primary">
                                        <td colspan="6" class="text-right"><strong class="text-white">TOTAL PENJUALAN:</strong></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right font-weight-bold" id="grandTotal" readonly value="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-pink">
                                        <td colspan="6" class="text-right"><strong class="text-white">TOTAL HPP:</strong></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right" id="totalHPP" readonly value="0">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-primary">
                                        <td colspan="6" class="text-right"><strong class="text-white">LABA KOTOR:</strong></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm text-right font-weight-bold text-success" id="labaKotor" readonly value="0">
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
                                <li>HPP dihitung otomatis dari harga modal average</li>
                                <li>Stok akan otomatis berkurang setelah disimpan</li>
                                <li>Jurnal akan dibuat saat <strong>Closing Kasir</strong></li>
                            </ul>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="<?= base_url('nota') ?>" class="btn btn-secondary">
                                    <i class="fe fe-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSimpan">
                                    <i class="fe fe-save"></i> Simpan Nota
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .stok-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .stok-danger {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .qty-error {
            border: 2px solid #dc3545 !important;
        }

        /* Disabled qty input */
        input.qty:disabled {
            background-color: #e9ecef !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        /* Highlight qty yang mendekati max */
        input.qty.near-max {
            background-color: #fff3cd;
        }
    </style>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    $(document).ready(function() {
        let rowCount = 1;

        // Initialize Select2 untuk row pertama
        initSelect2($('.select-item'));

        // Add Row Button
        $('#btnAddRow').on('click', function() {
            addRow();
        });

        // Remove Row
        $(document).on('click', '.btn-remove-row', function() {
            if ($('#itemRows tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
                calculateAll();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        });

        // Enter key untuk add row
        $(document).on('keydown', '#tableItems input, #tableItems select', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                addRow();
            }
        });

        // Calculate subtotal saat qty atau harga jual berubah
        $(document).on('keyup change', '.qty, .harga-jual', function() {
            const row = $(this).closest('tr');

            // Validasi qty tidak melebihi stok
            if ($(this).hasClass('qty')) {
                const qty = parseFloat($(this).val().replace(',', '.')) || 0;
                const maxStok = parseFloat($(this).attr('max')) || 0;

                // Kalau qty melebihi stok, set ke max stok
                if (qty > maxStok && maxStok > 0) {
                    $(this).val(maxStok.toString().replace('.', ','));
                    alert('Qty melebihi stok! Qty diset ke maksimal: ' + maxStok.toFixed(2));
                }
            }

            calculateSubtotal(row);
            calculateAll();
        });

        // Validasi saat blur (keluar dari input)
        $(document).on('blur', '.qty', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat($(this).val().replace(',', '.')) || 0;
            const maxStok = parseFloat($(this).attr('max')) || 0;

            if (qty > maxStok && maxStok > 0) {
                $(this).val(maxStok.toString().replace('.', ','));
                calculateSubtotal(row);
                calculateAll();
            }
        });

        // Format Rupiah
        $(document).on('keyup', '.format-rupiah', function() {
            let value = $(this).val().replace(/\./g, '');
            if (!isNaN(value) && value !== '') {
                $(this).val(formatRupiah(value));
            }
            calculateSubtotal($(this).closest('tr'));
            calculateAll();
        });

        // Submit Form
        $(document).on('submit', '#formNota', function(e) {
            e.preventDefault();

            // Validasi items
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

            // Validasi stok
            let stokCukup = true;
            let pesanError = '';

            $('.item-row').each(function() {
                const row = $(this);
                const itemId = row.find('.select-item').val();

                // Skip jika item belum dipilih
                if (!itemId) return;

                const namaBarang = row.find('.select-item option:selected').text();
                const stok = parseFloat(row.find('.stok-display').text()) || 0;
                const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;

                // Validasi qty harus > 0
                if (qty <= 0) {
                    stokCukup = false;
                    pesanError += '- ' + namaBarang + ': Qty harus lebih dari 0\n';
                    return;
                }

                // Validasi stok tersedia
                if (stok <= 0) {
                    stokCukup = false;
                    pesanError += '- ' + namaBarang + ': Stok habis!\n';
                    return;
                }

                // Validasi qty tidak melebihi stok
                if (qty > stok) {
                    stokCukup = false;
                    pesanError += '- ' + namaBarang + ': Stok tidak cukup (Tersedia: ' + stok.toFixed(2) + ', Diminta: ' + qty.toFixed(2) + ')\n';
                    return;
                }
            });

            if (!stokCukup) {
                alert('Stok tidak mencukupi untuk:\n' + pesanError);
                return;
            }

            // Validasi form
            if (!$('#formNota')[0].checkValidity()) {
                $('#formNota')[0].reportValidity();
                return;
            }

            $('#btnSimpan').prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url("nota/save") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Tampilkan notifikasi sukses
                        alert(response.message);

                        // Buka tab baru untuk print nota
                        const printUrl = '<?= base_url("nota/print_nota/") ?>' + response.id_nota;
                        window.open(printUrl, '_blank', 'width=800,height=600');

                        // Redirect ke daftar nota
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message);
                        $('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Nota');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan data!');
                    $('#btnSimpan').prop('disabled', false).html('<i class="fe fe-save"></i> Simpan Nota');
                }
            });
        });

        // Function Add Row
        function addRow() {
            rowCount++;
            const newRow = `
                <tr class="item-row">
                    <td class="text-center row-number">${rowCount}</td>
                    <td>
                        <select class="form-control form-control-sm select-item" 
                                name="id_item[]" required>
                            <option value="">-- Pilih Barang --</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info stok-display">0</span>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right qty" 
                               name="qty[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right harga-jual format-rupiah" 
                               name="harga_jual[]" placeholder="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right hpp-display" 
                               readonly value="0">
                        <input type="hidden" class="hpp-value" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-right subtotal" 
                               readonly value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemRows').append(newRow);

            // Initialize Select2 untuk row baru
            const newSelect = $('#itemRows tr:last .select-item');
            initSelect2(newSelect);

            // Focus ke select2 yang baru
            newSelect.select2('open');
        }

        // Initialize Select2
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
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // Saat item dipilih, autofill data
            element.on('select2:select', function(e) {
                const data = e.params.data;
                const row = $(this).closest('tr');
                const qtyInput = row.find('.qty');
                const stok = parseFloat(data.stok) || 0;

                // Set harga jual
                if (data.harga_jual) {
                    row.find('.harga-jual').val(formatRupiah(data.harga_jual));
                }

                // Set HPP
                if (data.harga_modal) {
                    row.find('.hpp-value').val(data.harga_modal);
                    row.find('.hpp-display').val(formatRupiah(data.harga_modal));
                }

                // Set stok display
                if (data.stok !== undefined) {
                    row.find('.stok-display').text(stok.toFixed(2));
                }

                // ========== LOGIC BARU ==========
                // Kalau stok 0, disable input qty
                if (stok <= 0) {
                    qtyInput.prop('disabled', true);
                    qtyInput.val('0');
                    qtyInput.attr('placeholder', 'Stok Habis');
                    row.find('.stok-display').addClass('stok-danger');
                    alert('Stok ' + data.text + ' habis! Tidak bisa diinput.');

                    // Clear selection
                    row.find('.select-item').val(null).trigger('change');
                    return;
                } else {
                    // Enable input qty dan set max attribute
                    qtyInput.prop('disabled', false);
                    qtyInput.attr('max', stok);
                    qtyInput.attr('placeholder', 'Max: ' + stok.toFixed(2));

                    // Focus ke qty
                    qtyInput.focus();
                }
            });
        }

        // Calculate Subtotal
        function calculateSubtotal(row) {
            const qty = parseFloat(row.find('.qty').val().replace(',', '.')) || 0;
            const hargaJual = parseFloat(row.find('.harga-jual').val().replace(/\./g, '')) || 0;
            const stok = parseFloat(row.find('.stok-display').text()) || 0;
            const subtotal = qty * hargaJual;

            row.find('.subtotal').val(formatRupiah(Math.floor(subtotal)));

            // Visual warning untuk stok
            const stokDisplay = row.find('.stok-display');
            const qtyInput = row.find('.qty');

            // Reset class
            stokDisplay.removeClass('stok-warning stok-danger');
            qtyInput.removeClass('qty-error');

            if (qty > 0) {
                if (stok <= 0) {
                    // Stok habis
                    stokDisplay.addClass('stok-danger');
                    qtyInput.addClass('qty-error');
                } else if (qty > stok) {
                    // Qty melebihi stok
                    stokDisplay.addClass('stok-warning');
                    qtyInput.addClass('qty-error');
                } else if (stok <= 5) {
                    // Stok menipis (warning)
                    stokDisplay.addClass('stok-warning');
                }
            }
        }

        // Calculate All (Grand Total, HPP, Laba)
        function calculateAll() {
            let totalPenjualan = 0;
            let totalHPP = 0;

            $('.item-row').each(function() {
                const qty = parseFloat($(this).find('.qty').val().replace(',', '.')) || 0;
                const hargaJual = parseFloat($(this).find('.harga-jual').val().replace(/\./g, '')) || 0;
                const hargaModal = parseFloat($(this).find('.hpp-value').val()) || 0;

                totalPenjualan += (qty * hargaJual);
                totalHPP += (qty * hargaModal);
            });

            const labaKotor = totalPenjualan - totalHPP;

            $('#grandTotal').val(formatRupiah(Math.floor(totalPenjualan)));
            $('#totalHPP').val(formatRupiah(Math.floor(totalHPP)));
            $('#labaKotor').val(formatRupiah(Math.floor(labaKotor)));
        }

        // Update Row Numbers
        function updateRowNumbers() {
            $('#itemRows tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
            rowCount = $('#itemRows tr').length;
        }

        // Parse Rupiah
        function parseRupiah(value) {
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                let cleaned = value.replace(/\./g, '').replace(',', '.');
                return parseFloat(cleaned) || 0;
            }
            return 0;
        }

        // Format Rupiah
        function formatRupiah(angka) {
            let number = Math.floor(parseFloat(angka));
            if (isNaN(number)) number = 0;
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });
</script>