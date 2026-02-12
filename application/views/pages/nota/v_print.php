<!-- v_print.php -->
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print Nota - <?= $nota->no_nota ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Courier New', monospace;
			font-size: 12px;
			padding: 20px;
			max-width: 80mm;
			margin: 0 auto;
		}

		.header {
			text-align: center;
			margin-bottom: 20px;
			border-bottom: 2px dashed #000;
			padding-bottom: 10px;
		}

		.header h2 {
			font-size: 18px;
			margin-bottom: 5px;
		}

		.header p {
			font-size: 11px;
			margin: 2px 0;
		}

		.info {
			margin-bottom: 15px;
			font-size: 11px;
		}

		.info-row {
			display: flex;
			justify-content: space-between;
			margin: 3px 0;
		}

		.divider {
			border-top: 1px dashed #000;
			margin: 10px 0;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 10px;
		}

		table th {
			text-align: left;
			padding: 5px 0;
			border-bottom: 1px solid #000;
			font-size: 11px;
		}

		table td {
			padding: 5px 0;
			font-size: 11px;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.item-name {
			font-weight: bold;
		}

		.total-section {
			border-top: 2px solid #000;
			padding-top: 10px;
			margin-top: 10px;
		}

		.total-row {
			display: flex;
			justify-content: space-between;
			margin: 5px 0;
			font-size: 12px;
		}

		.total-row.grand {
			font-weight: bold;
			font-size: 14px;
			margin-top: 10px;
			padding-top: 5px;
			border-top: 1px dashed #000;
		}

		.footer {
			text-align: center;
			margin-top: 20px;
			padding-top: 10px;
			border-top: 2px dashed #000;
			font-size: 11px;
		}

		.footer p {
			margin: 5px 0;
		}

		@media print {
			body {
				padding: 0;
			}

			.no-print {
				display: none !important;
			}

			@page {
				margin: 0;
				size: 80mm auto;
			}
		}

		.btn-print {
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 10px 20px;
			background-color: #28a745;
			color: white;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 14px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
			z-index: 1000;
		}

		.btn-print:hover {
			background-color: #218838;
		}
	</style>
</head>

<body>
	<!-- Button Print (tidak akan tercetak) -->
	<button onclick="window.print()" class="btn-print no-print">
		🖨️ Print Nota
	</button>

	<!-- Header -->
	<div class="header">
		<h2><?= $this->session->userdata('nama_perusahaan') ?></h2>
		<p><?= $this->session->userdata('alamat_perusahaan') ?></p>
		<!-- <p>Telp: 021-12345678</p> -->
	</div>

	<!-- Info Transaksi -->
	<div class="info">
		<div class="info-row">
			<span>No. Nota</span>
			<span><strong><?= $nota->no_nota ?></strong></span>
		</div>
		<div class="info-row">
			<span>Tanggal</span>
			<span><?= date('d/m/Y H:i', strtotime($nota->tanggal)) ?></span>
		</div>
		<?php if ($nota->customer) : ?>
			<div class="info-row">
				<span>Customer</span>
				<span><?= $nota->customer ?></span>
			</div>
		<?php endif; ?>
		<div class="info-row">
			<span>Kasir</span>
			<span><?= $nota->created_by ?></span>
		</div>
		<div class="info-row">
			<span>Pembayaran</span>
			<span><strong><?= strtoupper($nota->metode_bayar) ?></strong></span>
		</div>
	</div>

	<div class="divider"></div>

	<!-- Detail Items -->
	<table>
		<thead>
			<tr>
				<th>Item</th>
				<th class="text-center">Qty</th>
				<th class="text-right">Harga</th>
				<th class="text-right">Subtotal</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($detail as $d) : ?>
				<tr>
					<td colspan="4" class="item-name"><?= $d->nama_item ?></td>
				</tr>
				<tr>
					<td><?= $d->kode_item ?></td>
					<td class="text-center"><?= number_format($d->qty, 0) ?></td>
					<td class="text-right"><?= number_format($d->harga_jual, 0) ?></td>
					<td class="text-right"><?= number_format($d->subtotal_jual, 0) ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<!-- Total Section -->
	<div class="total-section">
		<div class="total-row">
			<span>Subtotal:</span>
			<span>Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?></span>
		</div>
		<div class="total-row grand">
			<span>TOTAL:</span>
			<span>Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?></span>
		</div>
	</div>

	<!-- Footer -->
	<div class="footer">
		<p>*** TERIMA KASIH ***</p>
		<p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
		<p>Simpan struk ini sebagai bukti pembayaran</p>
	</div>

	<script>
		// Auto print saat halaman dibuka
		window.onload = function() {
			// Tunggu sebentar biar halaman fully loaded
			setTimeout(function() {
				// window.print(); // Uncomment kalau mau auto print
			}, 500);
		}

		// Close window setelah print (opsional)
		window.onafterprint = function() {
			// window.close(); // Uncomment kalau mau auto close setelah print
		}
	</script>
</body>

</html>