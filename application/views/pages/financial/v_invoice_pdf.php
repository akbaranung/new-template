<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title_pdf; ?></title>

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10pt;
			color: #2d2d2d;
			line-height: 1.5;
		}

		/* ── Utilities ── */
		.text-end {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.fw-bold {
			font-weight: bold;
		}

		.text-brand {
			color: #004e81;
		}

		.mt-4 {
			margin-top: 4px;
		}

		.mt-8 {
			margin-top: 8px;
		}

		.mt-16 {
			margin-top: 16px;
		}

		.mt-24 {
			margin-top: 24px;
		}

		.mt-40 {
			margin-top: 40px;
		}

		.mb-4 {
			margin-bottom: 4px;
		}

		.mb-8 {
			margin-bottom: 8px;
		}

		.mb-16 {
			margin-bottom: 16px;
		}

		.mb-24 {
			margin-bottom: 24px;
		}

		/* ── Divider ── */
		hr.brand {
			border: none;
			border-top: 2px solid #004e81;
			margin: 6px 0 10px;
		}

		hr.light {
			border: none;
			border-top: 1px solid #d0d0d0;
			margin: 6px 0 10px;
		}

		/* ── Header ── */
		.header-table {
			width: 100%;
			margin-bottom: 24px;
		}

		.invoice-title {
			font-size: 24pt;
			font-weight: bold;
			color: #004e81;
			letter-spacing: 2px;
			text-align: right;
			margin-right: 120px;
		}

		.invoice-meta-label {
			color: #6c757d;
			font-size: 9pt;
		}

		.invoice-meta-value {
			font-size: 9pt;
			font-weight: bold;
		}

		/* ── Info Perusahaan & Customer ── */
		.info-table {
			width: 100%;
			margin-bottom: 20px;
		}

		.info-box-title {
			font-size: 7.5pt;
			font-weight: bold;
			color: #004e81;
			text-transform: uppercase;
			letter-spacing: 0.06em;
			margin-bottom: 4px;
		}

		.info-box-title-2 {
			font-size: 8.5pt;
			font-weight: bold;
			color: #004e81;
			/* text-transform: uppercase; */
			letter-spacing: 0.06em;
			margin-bottom: 4px;
		}

		.info-box-name {
			font-size: 10.5pt;
			font-weight: bold;
			color: #2d2d2d;
			margin-bottom: 2px;
		}

		.info-box-body {
			font-size: 9pt;
			color: #555;
			line-height: 1.6;
		}

		/* ── Item Table ── */
		.item-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 0;
			font-size: 9.5pt;
		}

		.item-table thead tr th {
			background-color: #004e81;
			color: #ffffff;
			padding: 8px 10px;
			border: none;
			font-weight: bold;
			font-size: 9pt;
		}

		.item-table tbody tr td {
			padding: 7px 10px;
			border-bottom: 1px solid #e5e5e5;
			color: #2d2d2d;
			vertical-align: top;
		}

		.item-table tbody tr:nth-child(even) td {
			background-color: #f4f8fb;
		}

		.item-table tbody tr:nth-child(odd) td {
			background-color: #ffffff;
		}

		/* ── Summary ── */
		.summary-table {
			width: 100%;
			margin-top: 10px;
			;
		}

		.summary-box {
			background-color: #f4f8fb;
			border: 1px solid #ccdde8;
			padding: 10px 14px;
		}

		.summary-row-label {
			font-size: 9pt;
			color: #555;
		}

		.summary-row-value {
			font-size: 9pt;
			text-align: right;
			color: #2d2d2d;
		}

		.summary-total-label {
			font-size: 10.5pt;
			font-weight: bold;
			color: #004e81;
		}

		.summary-total-value {
			font-size: 10.5pt;
			font-weight: bold;
			color: #004e81;
			text-align: right;
		}

		.terbilang-box {
			background-color: #f4f8fb;
			border-left: 3px solid #f4f8fb;
			padding: 8px 12px;
			font-size: 9pt;
			color: #555;
		}

		.terbilang-value {
			font-weight: bold;
			color: #2d2d2d;
			margin-top: 2px;
		}

		/* ── Footer: Pesan & TTD ── */
		.footer-table {
			width: 100%;
			margin-top: 28px;
		}

		.pesan-title {
			font-size: 8pt;
			font-weight: bold;
			color: #004e81;
			text-transform: uppercase;
			letter-spacing: 0.06em;
			margin-bottom: 4px;
		}

		.pesan-body {
			font-size: 9pt;
			color: #444;
			line-height: 1.7;
		}

		.ttd-company {
			font-size: 9pt;
			font-weight: bold;
			color: #2d2d2d;
			margin-bottom: 50px;
			/* ruang TTD */
		}

		.ttd-line {
			border-top: 1px solid #2d2d2d;
			width: 100%;
			margin-bottom: 4px;
		}

		.ttd-name {
			font-size: 9.5pt;
			font-weight: bold;
			color: #2d2d2d;
		}

		.ttd-jabatan {
			font-size: 8.5pt;
			color: #6c757d;
		}
	</style>

</head>

<body>
	<div style="padding: 10px 20px;">

		<?php
		$month = substr($invoice['tanggal_invoice'], 5, 2);
		$year  = substr($invoice['tanggal_invoice'], 2, 2);
		$nomor = $invoice['no_invoice'] . "/KSI-01/" . intToRoman($month) . "/" . $year;
		$slug  = ($invoice['slug_invoice']) ? $invoice['slug_invoice'] : $nomor;
		?>

		<!-- ══ HEADER ══ -->

		<p class="invoice-title">INVOICE</p>
		<table class="header-table">
			<tbody>
				<tr>
					<!-- Logo kiri -->
					<td style="width: 40%; vertical-align: middle;">
						<img src="<?= $this->session->userdata('icon') ?>" style="max-width: 410px" alt="Logo">
					</td>
					<!-- Judul tengah/kanan -->
					<td style="text-align: right; vertical-align: top;">
						<table style="margin-left: auto; margin-top: 8px;">
							<tbody>
								<tr>
									<td class="invoice-meta-label" style="padding-right: 12px;">Referensi</td>
									<td class="invoice-meta-value"><?= $slug ?></td>
								</tr>
								<tr>
									<td class="invoice-meta-label" style="padding-right: 12px;">Tgl. Diterima</td>
									<td class="invoice-meta-value"><?= format_indo($invoice['tanggal_invoice']) ?></td>
								</tr>
								<?php if ($invoice['no_po']) : ?>
									<tr>
										<td class="invoice-meta-label" style="padding-right: 12px;">No. PO</td>
										<td class="invoice-meta-value"><?= $invoice['no_po'] ?></td>
									</tr>
								<?php endif; ?>
								<?php if ($invoice['tgl_po']) : ?>
									<tr>
										<td class="invoice-meta-label" style="padding-right: 12px;">Tgl. PO</td>
										<td class="invoice-meta-value"><?= format_indo($invoice['tgl_po']) ?></td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="invoice-meta-label" style="padding-right: 12px;">Metode Bayar</td>
									<td class="invoice-meta-value"><?= strtoupper($invoice['metode_bayar']) ?></td>
								</tr>
								<tr>
									<td class="invoice-meta-label" style="padding-right: 12px;">Tgl. Jatuh Tempo</td>
									<td class="invoice-meta-value"><?= format_indo($invoice['tgl_jatuh_tempo']) ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		if ($this->session->userdata('header_invoice')) {
		?>
			<p class="info-box-title-2" style="text-align: center;"><?= $this->session->userdata('header_invoice') ?></p>
		<?php
		} ?>

		<hr class="brand">

		<!-- ══ INFO PERUSAHAAN & CUSTOMER ══ -->
		<table class="info-table">
			<tbody>
				<tr>
					<td style="width: 48%; vertical-align: top; padding-right: 16px;">
						<p class="info-box-title">Informasi Perusahaan</p>
						<hr class="light">
						<p class="info-box-name"><?= $this->session->userdata('nama_perusahaan') ?></p>
						<p class="info-box-body">
							<?= $this->session->userdata('alamat_perusahaan') ? nl2br($this->session->userdata('alamat_perusahaan')) : '' ?>
						</p>
						<?php
						if ($this->session->userdata('no_skp')) {
						?>
							<p class="info-box-body" style="margin-top: 3px;">Nomor SKP: <?= $this->session->userdata('no_skp') ?></p>
						<?php
						} ?>
					</td>
					<td style="width: 4%;"></td>
					<td style="width: 48%; vertical-align: top; padding-left: 16px;">
						<p class="info-box-title">Tagihan Kepada</p>
						<hr class="light">
						<p class="info-box-name"><?= $invoice['nama_customer'] ?></p>
						<p class="info-box-body">
							<?= $invoice['alamat_customer'] ?><br>
							NPWP: <?= ($invoice['no_npwp']) ? $invoice['no_npwp'] : '-' ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- ══ TABEL ITEM ══ -->
		<table class="item-table mb-16">
			<thead>
				<tr>
					<!-- Uncomment baris berikut untuk menampilkan kolom No. -->
					<!-- <th style="width: 5%; text-align: center;">No.</th> -->
					<th style="width: 45%;">Keterangan</th>
					<th style="width: 15%; text-align: right;">Item</th>
					<th style="width: 20%; text-align: right;">Harga</th>
					<th style="width: 20%; text-align: right;">Total Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($details as $d) : ?>
					<tr>
						<!-- Uncomment baris berikut untuk menampilkan nomor urut -->
						<!-- <td style="text-align: center;"><?= $no++ ?></td> -->
						<td><?= $d->item ?></td>
						<td class="text-end"><?= number_format($d->qty) ?></td>
						<td class="text-end"><?= number_format($d->total) ?></td>
						<td class="text-end"><?= number_format($d->total_amount) ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<!-- ══ TERBILANG + SUMMARY ══ -->
		<table class="summary-table mb-24">
			<tbody>
				<tr>
					<!-- Terbilang -->
					<td style="width: 52%; vertical-align: top; padding-right: 16px;">
						<div class="terbilang-box">
							<p style="font-size: 8pt; color: #888; margin-bottom: 2px;">Terbilang:</p>
							<p class="terbilang-value"><?= terbilang(round($invoice['total_nonpph'])) ?> Rupiah</p>
						</div>
					</td>
					<!-- Summary angka -->
					<td style="width: 48%; vertical-align: top;">
						<div class="summary-box">
							<table style="width: 100%; border-collapse: collapse;">
								<tbody>
									<tr>
										<td class="summary-row-label" style="padding: 3px 0;">Subtotal</td>
										<td class="summary-row-value" style="padding: 3px 0;"><?= number_format($invoice['subtotal']) ?></td>
									</tr>
									<?php if ($invoice['besaran_ppn'] != '0.00') : ?>
										<tr>
											<td class="summary-row-label" style="padding: 3px 0;"><?= $this->session->userdata('nama_ppn') ?></td>
											<td class="summary-row-value" style="padding: 3px 0;"><?= number_format($invoice['besaran_ppn']) ?></td>
										</tr>
									<?php endif; ?>
									<tr>
										<td colspan="2">
											<hr class="light" style="margin: 6px 0;">
										</td>
									</tr>
									<tr>
										<td class="summary-total-label" style="padding: 2px 0;">Total</td>
										<td class="summary-total-value" style="padding: 2px 0;"><?= number_format($invoice['total_nonpph']) ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- ══ PESAN & TTD ══ -->
		<table class="footer-table">
			<tbody>
				<tr>
					<!-- Pesan / Info Rekening -->
					<td style="width: 55%; vertical-align: top; padding-right: 20px;">
						<p class="pesan-title">Informasi Pembayaran</p>
						<hr class="light">
						<p class="pesan-body">
							<?= $this->session->userdata('nomor_rekening') ? nl2br($this->session->userdata('nomor_rekening')) : '' ?>
						</p>
					</td>
					<!-- TTD -->
					<td style="width: 45%; vertical-align: top; text-align: center;">
						<p class="pesan-title"><?= $this->session->userdata('nama_perusahaan') ?></p>
						<hr class="light">
						<p class="ttd-company" style="margin-top: 6px; margin-bottom: 50px;">&nbsp;</p>
						<div class="ttd-line"></div>
						<p class="ttd-name"><?= $user['nama'] ?></p>
						<p class="ttd-jabatan"><?= $this->session->userdata('nama_jabatan') ?></p>
					</td>
				</tr>
			</tbody>
		</table>

	</div>
</body>

</html>
