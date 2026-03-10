<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Struk - <?= $nota->no_nota ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Courier New', Courier, monospace;
			font-size: <?= $struk_css['font_size'] ?>;
			width: <?= $struk_css['lebar'] ?>;
			margin: 0 auto;
			padding: <?= $struk_css['padding'] ?>;
			color: #000;
		}

		.center {
			text-align: center;
		}

		.right {
			text-align: right;
		}

		.left {
			text-align: left;
		}

		.bold {
			font-weight: bold;
		}

		.divider {
			border-top: 1px dashed #000;
			margin: 4px 0;
		}

		.divider2 {
			border-top: 2px solid #000;
			margin: 4px 0;
		}

		.toko-nama {
			font-size: <?= $struk_css['font_toko'] ?>;
			font-weight: bold;
			text-align: center;
			margin-bottom: 2px;
		}

		.toko-cabang {
			font-size: <?= $struk_css['font_size_kecil'] ?>;
			text-align: center;
			margin-bottom: 2px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		table td {
			padding: 1px 0;
			vertical-align: top;
			font-size: <?= $struk_css['font_size'] ?>;
		}

		.item-nama {
			width: <?= $struk_css['col_nama'] ?>;
		}

		.item-qty {
			width: <?= $struk_css['col_qty'] ?>;
			text-align: center;
		}

		.item-harga {
			width: <?= $struk_css['col_harga'] ?>;
			text-align: right;
		}

		.total-row td {
			font-weight: bold;
			font-size: <?= $struk_css['font_total'] ?>;
			padding-top: 2px;
		}

		.footer {
			text-align: center;
			font-size: <?= $struk_css['font_size_kecil'] ?>;
			margin-top: 6px;
		}

		.metode-badge {
			display: inline-block;
			border: 1px solid #000;
			padding: 1px 6px;
			font-size: <?= $struk_css['font_size_kecil'] ?>;
			font-weight: bold;
			text-transform: uppercase;
		}

		@media print {
			body {
				width: <?= $struk_css['lebar'] ?>;
				margin: 0;
				padding: <?= $struk_css['padding_print'] ?>;
			}

			.no-print {
				display: none !important;
			}
		}
	</style>
</head>

<body>

	<!-- Header Toko -->
	<div class="toko-nama"><?= htmlspecialchars($setting['nama_toko']) ?></div>
	<div class="toko-cabang"><?= $utility['cabang'] ?? '' ?></div>
	<div class="toko-cabang"><?= $utility['alamat_perusahaan'] ?? '' ?></div>

	<div class="divider2"></div>

	<!-- Info Nota -->
	<table>
		<tr>
			<td class="left">No. Nota</td>
			<td class="right"><?= $nota->no_nota ?></td>
		</tr>
		<tr>
			<td class="left">Tanggal</td>
			<td class="right"><?= date('d/m/Y H:i', strtotime($nota->tanggal)) ?></td>
		</tr>
		<?php if ($setting['show_kasir']): ?>
			<tr>
				<td class="left">Kasir</td>
				<td class="right"><?= $nota->created_by ?></td>
			</tr>
		<?php endif; ?>
		<?php if (!empty($nota->customer)): ?>
			<tr>
				<td class="left">Customer</td>
				<td class="right"><?= $nota->customer ?></td>
			</tr>
		<?php endif; ?>
	</table>

	<div class="divider"></div>

	<!-- Header Kolom Item -->
	<table>
		<tr>
			<td class="item-nama bold">Barang</td>
			<td class="item-qty bold">Qty</td>
			<td class="item-harga bold">Harga</td>
		</tr>
	</table>

	<div class="divider"></div>

	<!-- List Item -->
	<table>
		<?php foreach ($detail as $d): ?>
			<tr>
				<td class="item-nama"><?= $d->nama_item ?></td>
				<td class="item-qty"><?= number_format($d->qty, 0, ',', '.') ?></td>
				<td class="item-harga"><?= number_format($d->subtotal_jual, 0, ',', '.') ?></td>
			</tr>
			<?php if ($setting['show_harga_satuan']): ?>
				<tr>
					<td colspan="2" style="font-size:<?= $struk_css['font_size_kecil'] ?>; color:#555; padding-left:2px;">
						<?= number_format($d->qty, 0) ?> x Rp <?= number_format($d->harga_jual, 0, ',', '.') ?>
					</td>
					<td></td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</table>

	<div class="divider"></div>

	<!-- setelah tabel list item & divider, sebelum tabel total -->

	<!-- Subtotal sebelum diskon -->
	<?php
	$subtotal_sebelum_diskon = 0;
	foreach ($detail as $d) {
		$subtotal_sebelum_diskon += $d->subtotal_jual;
	}
	$ada_diskon = $nota->diskon_amount > 0;
	?>

	<?php if ($ada_diskon): ?>
		<table>
			<tr>
				<td colspan="2" class="left">Subtotal</td>
				<td class="right">Rp <?= number_format($subtotal_sebelum_diskon, 0, ',', '.') ?></td>
			</tr>
			<tr>
				<td colspan="2" class="left">
					Diskon
					<?php if ($nota->diskon_tipe === 'persen'): ?>
						(<?= number_format($nota->diskon_nilai, 0) ?>%)
					<?php endif; ?>
				</td>
				<td class="right">- Rp <?= number_format($nota->diskon_amount, 0, ',', '.') ?></td>
			</tr>
		</table>
		<div class="divider"></div>
	<?php endif; ?>
	
	<!-- Total -->
	<table>
		<tr class="total-row">
			<td colspan="2" class="left">TOTAL</td>
			<td class="right">Rp <?= number_format($nota->total_penjualan, 0, ',', '.') ?></td>
		</tr>
	</table>

	<div class="divider"></div>

	<!-- Metode Bayar -->
	<table>
		<tr>
			<td class="left">Pembayaran</td>
			<td class="right">
				<span class="metode-badge"><?= strtoupper($nota->metode_bayar) ?></span>
			</td>
		</tr>
		<?php if ($nota->metode_bayar === 'card' && !empty($nota->no_kartu)): ?>
			<tr>
				<td class="left">No. Kartu</td>
				<td class="right">****<?= substr($nota->no_kartu, -4) ?></td>
			</tr>
		<?php endif; ?>
	</table>

	<div class="divider2"></div>

	<!-- Footer -->
	<div class="footer">
		<?php if (!empty($setting['footer_1'])): ?><p><?= htmlspecialchars($setting['footer_1']) ?></p><?php endif; ?>
		<?php if (!empty($setting['footer_2'])): ?><p><?= htmlspecialchars($setting['footer_2']) ?></p><?php endif; ?>
		<?php if (!empty($setting['footer_3'])): ?><p><?= htmlspecialchars($setting['footer_3']) ?></p><?php endif; ?>
	</div>

	<!-- Tombol Print -->
	<div class="no-print" style="text-align:center; margin-top: 12px;">
		<button onclick="window.print()"
			style="padding: 6px 20px; font-size: 13px; cursor: pointer; background: #007bff; color: #fff; border: none; border-radius: 4px;">
			🖨️ Cetak Struk
		</button>
		<button onclick="window.close()"
			style="padding: 6px 20px; font-size: 13px; cursor: pointer; background: #6c757d; color: #fff; border: none; border-radius: 4px; margin-left: 6px;">
			✕ Tutup
		</button>
	</div>

	<script>
		window.onload = function() {
			<?php if ($setting['auto_print']): ?>
				setTimeout(function() {
					window.print();
				}, 500);
			<?php endif; ?>
		};
	</script>

</body>

</html>
