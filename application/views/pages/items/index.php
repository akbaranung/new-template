<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
            <h1 class="page-title">Master Barang</h1>

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
                    <!-- Search & Button -->
                    <form method="GET" action="<?= base_url('items') ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari Kode Barang atau Nama Barang..." value="<?= htmlspecialchars($search ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-search"></i> Cari
                                </button>
                                <a href="<?= base_url('items') ?>" class="btn btn-warning text-white">
                                    <i class="fe fe-refresh-cw"></i> Reset
                                </a>
                                <button type="button" class="btn btn-success text-white" id="btnTambah">
                                    <i class="fe fe-plus"></i> Tambah Barang
                                </button>
                                <a href="<?= base_url('stok_masuk') ?>" class="btn btn-dark text-white">
                                    <i class="fe fe-plus"></i> Stok masuk
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover" style="width:100%">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <!-- <th width="10%">Satuan</th> -->
                                    <th width="13%" class="text-right">Harga Modal</th>
                                    <th width="13%" class="text-right">Harga Jual</th>
                                    <th width="20%" class="text-center">Stok</th>
                                    <th width="12%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($items)) {
                                    $no = (isset($_GET['per_page']) ? $_GET['per_page'] : 0) + 1;
                                    foreach ($items as $item) :
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><strong><?= $item->kode_item ?></strong></td>
                                            <td><?= $item->nama_item ?></td>
                                            <!-- <td class="text-center"><?= $item->satuan ?></td> -->
                                            <td class="text-right">Rp <?= number_format($item->harga_modal, 0, ',', '.') ?></td>
                                            <td class="text-right">Rp <?= number_format($item->harga_jual, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= number_format($item->stok, 2, ',', '.') ?></span> <?= $item->satuan ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning btn-edit text-white" data-id="<?= $item->id ?>" title="Edit">
                                                    <i class="fe fe-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $item->id ?>" data-nama="<?= htmlspecialchars($item->nama_item) ?>" title="Hapus">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fe fe-info"></i> Tidak ada data barang
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($items)) : ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Menampilkan data master barang
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?= $pagination ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="modalTitle">
                    <i class="fe fe-package"></i> Tambah Barang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formItem">
                <div class="modal-body" id="modalBody">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Memuat form...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fe fe-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fe fe-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fe fe-alert-triangle"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus barang:</p>
                <p><strong id="delete-nama-barang"></strong></p>
                <p class="text-danger">
                    <i class="fe fe-alert-circle"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fe fe-x"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fe fe-trash-2"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        let deleteId = null;

        // Button Tambah
        $('#btnTambah').on('click', function() {
            $('#modalTitle').html('<i class="fe fe-package"></i> Tambah Barang');
            $('#modalBody').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Memuat form...</p>
                </div>
            `);
            $('#modalForm').modal('show');

            $.ajax({
                url: '<?= base_url("items/form") ?>',
                type: 'GET',
                success: function(response) {
                    $('#modalBody').html(response);
                },
                error: function() {
                    $('#modalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fe fe-alert-circle"></i> Gagal memuat form!
                        </div>
                    `);
                }
            });
        });

        // Button Edit
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            $('#modalTitle').html('<i class="fe fe-edit"></i> Edit Barang');
            $('#modalBody').html(`
                <div class="text-center">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Memuat form...</p>
                </div>
            `);
            $('#modalForm').modal('show');

            $.ajax({
                url: '<?= base_url("items/form/") ?>' + id,
                type: 'GET',
                success: function(response) {
                    $('#modalBody').html(response);
                },
                error: function() {
                    $('#modalBody').html(`
                        <div class="alert alert-danger">
                            <i class="fe fe-alert-circle"></i> Gagal memuat form!
                        </div>
                    `);
                }
            });
        });

        // Submit Form
        $(document).on('submit', '#formItem', function(e) {
            e.preventDefault();

            $('#btnSimpan').prop('disabled', true).html('<i class="fe fe-loader"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url("items/save") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalForm').modal('hide');
                        alert(response.message);
                        location.reload();
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
        });

        // Button Delete
        $('.btn-delete').on('click', function() {
            deleteId = $(this).data('id');
            const namaBarang = $(this).data('nama');

            $('#delete-nama-barang').text(namaBarang);
            $('#deleteModal').modal('show');
        });

        // Confirm Delete
        $('#confirmDelete').on('click', function() {
            if (!deleteId) return;

            $(this).prop('disabled', true).html('<i class="fe fe-loader"></i> Menghapus...');

            $.ajax({
                url: '<?= base_url("items/delete") ?>',
                type: 'POST',
                data: {
                    id: deleteId
                },
                dataType: 'json',
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    $('#deleteModal').modal('hide');
                    alert('Terjadi kesalahan saat menghapus data!');
                }
            });
        });

        // Format Rupiah Input
        $(document).on('keyup', '.format-rupiah', function() {
            let value = $(this).val().replace(/\./g, '');
            if (!isNaN(value) && value !== '') {
                $(this).val(formatRupiah(value));
            }
        });

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });
</script>