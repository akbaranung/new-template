<!-- items/form.php -->
<input type="hidden" name="id" id="id" value="<?= isset($item) ? $item->Id : '' ?>">

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="kode_item">Kode Barang <span class="text-danger">*</span></label>
			<input type="text" class="form-control" name="kode_item" id="kode_item" value="<?= isset($item) ? $item->kode_item : $kode_item ?>" <?= isset($item) ? 'readonly' : '' ?> required>
			<small class="text-muted">Kode barang otomatis</small>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="satuan">Satuan <span class="text-danger">*</span></label>
			<select class="form-control" name="satuan" id="satuan" required>
				<option value="">-- Pilih Satuan --</option>
				<option value="pcs" <?= isset($item) && $item->satuan == 'pcs' ? 'selected' : '' ?>>Pcs (Pieces)</option>
				<option value="unit" <?= isset($item) && $item->satuan == 'unit' ? 'selected' : '' ?>>Unit</option>
				<option value="box" <?= isset($item) && $item->satuan == 'box' ? 'selected' : '' ?>>Box</option>
				<option value="lusin" <?= isset($item) && $item->satuan == 'lusin' ? 'selected' : '' ?>>Lusin</option>
				<option value="kg" <?= isset($item) && $item->satuan == 'kg' ? 'selected' : '' ?>>Kg (Kilogram)</option>
				<option value="liter" <?= isset($item) && $item->satuan == 'liter' ? 'selected' : '' ?>>Liter</option>
				<option value="meter" <?= isset($item) && $item->satuan == 'meter' ? 'selected' : '' ?>>Meter</option>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="nama_item">Nama Barang <span class="text-danger">*</span></label>
	<input type="text" class="form-control" name="nama_item" id="nama_item" value="<?= isset($item) ? $item->nama_item : '' ?>" placeholder="Masukkan nama barang" required>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label for="harga_modal">Harga Modal <span class="text-danger">*</span></label>
			<input type="text" class="form-control format-rupiah" name="harga_modal" id="harga_modal" value="<?= isset($item) ? number_format($item->harga_modal, 0, ',', '.') : '0' ?>" placeholder="0" required>
			<small class="text-muted">Harga modal per unit</small>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<label for="harga_jual">Harga Jual <span class="text-danger">*</span></label>
			<input type="text" class="form-control format-rupiah" name="harga_jual" id="harga_jual" value="<?= isset($item) ? number_format($item->harga_jual, 0, ',', '.') : '0' ?>" placeholder="0" required>
			<small class="text-muted">Harga jual per unit</small>
		</div>
	</div>
</div>

<div class="form-group">
	<label for="coa_persediaan">COA Persediaan <span class="text-danger">*</span></label>
	<select class="form-control" name="coa_persediaan" id="coa_persediaan" required>
		<option value="">-- Pilih COA Persediaan --</option>
		<?php foreach ($coa_list as $coa) : ?>
			<option value="<?= $coa->no_sbb ?>"
				<?= isset($item) && $item->coa_persediaan == $coa->no_sbb ? 'selected' : '' ?>>
				<?= $coa->no_sbb ?> - <?= $coa->nama_perkiraan ?>
			</option>
		<?php endforeach; ?>
	</select>
	<small class="text-muted">COA persediaan barang ini untuk pencatatan jurnal otomatis</small>
</div>

<div class="alert alert-info">
	<i class="fe fe-info"></i> <strong>Catatan:</strong>
	<ul class="mb-0 pl-3">
		<li>Harga modal di sini hanya sebagai <strong>harga acuan/estimasi</strong></li>
		<li>Stok dan nilai persediaan akan otomatis ter-isi setelah <strong>Input Stok</strong></li>
		<li>Harga modal akan otomatis update dengan metode <strong>Average</strong> saat input stok</li>
		<li>Stok akan otomatis berkurang saat buat nota penjualan</li>
	</ul>
</div>

<!-- ← Select2 CSS & JS (kalau belum ada di parent layout, uncomment ini) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	$(document).ready(function() {
		// ← Init Select2 untuk COA Persediaan
		// dropdownParent ke #modalForm supaya dropdown muncul di dalam modal
		$('#coa_persediaan').select2({
			placeholder: '-- Pilih COA Persediaan --',
			allowClear: true,
			width: '100%',
			dropdownParent: $('#modalForm')
		});

		$('#nama_item').focus();
	});
</script>
