<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Print Project - <?= $project['no_project'] ?></title>
	<style>
		body {
			font-family: Arial, Helvetica, sans-serif !important;
			font-size: 10pt;
		}

		table {
			border-collapse: collapse;
			width: 100%;
		}

		.title {
			font-weight: bold;
			color: #004e81;
		}

		.table-bordered {
			border: 1px solid black;
		}

		thead>tr>th {
			background-color: #004e81;
			color: white;
			padding: 8px 10px;
			border: 2px solid white;
		}

		.table-bordered>tbody>tr>td {
			background-color: #e7e7e7;
			padding: 8px 10px;
			border: 2px solid white;
		}

		.text-end {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.mb-10 {
			margin-bottom: 15px;
		}

		.tfoot-total td {
			background-color: #004e81 !important;
			color: white;
			font-weight: bold;
			padding: 8px 10px;
			border: 2px solid white;
		}

		hr {
			border: none;
			border-top: 1px solid #004e81;
			margin: 5px 0;
		}

		p {
			margin: 3px 0;
		}

		@media print {
			body {
				margin: 0;
			}

			.no-print {
				display: none;
			}
		}
	</style>
</head>

<body>
	<div class="container">

		<!-- Header: Logo + Judul -->
		<table style="margin-bottom: 70px;">
			<tbody>
				<tr>
					<td style="width: 40%;">
						<img src="<?= $this->session->userdata('icon') ?>" style="width: 150px;" alt="Logo">
					</td>
					<td class="text-end">
						<p style="font-size: 20pt; font-weight: bold; color: #004e81; margin: 0;">Project Journal</p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="text-end" style="padding-top: 8px;">
						<table style="width: auto; float: right; border-collapse: collapse;">
							<tr>
								<td style="padding: 2px 10px 2px 0; color: #555;">No. Project</td>
								<td style="padding: 2px 0;">: <strong><?= $project['no_project'] ?></strong></td>
							</tr>
							<tr>
								<td style="padding: 2px 10px 2px 0; color: #555;">Tanggal</td>
								<td style="padding: 2px 0;">: <?= format_indo($project['tanggal']) ?></td>
							</tr>
							<tr>
								<td style="padding: 2px 10px 2px 0; color: #555;">Dibuat Oleh</td>
								<td style="padding: 2px 0;">: <?= $project['created_by'] ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Info Perusahaan -->
		<table class="mb-10">
			<tbody>
				<tr>
					<td style="width: 49%; vertical-align: bottom;">
						<p class="title">Informasi Perusahaan</p>
						<hr>
					</td>
					<td style="width: 2%;"></td>
					<td style="width: 49%; vertical-align: bottom;">
						<p class="title">Keterangan</p>
						<hr>
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">
						<p class="title" style="margin-top: 4px;"><?= $this->session->userdata('nama_perusahaan') ?></p>
						<p><?= $this->session->userdata('alamat_perusahaan') ? nl2br($this->session->userdata('alamat_perusahaan')) : '' ?></p>
					</td>
					<td></td>
					<td style="vertical-align: top;">
						<p style="margin-top: 4px;"><?= $project['keterangan'] ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Tabel Detail Jurnal -->
		<table class="table-bordered mb-10">
			<thead>
				<tr>
					<th style="width: 5%">#</th>
					<th style="width: 35%">CoA</th>
					<th style="width: 25%" class="text-end">Debit</th>
					<th style="width: 25%" class="text-end">Kredit</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no          = 1;
				$total_debit  = 0;
				$total_kredit = 0;
				foreach ($project_detail as $d) :
					$coa_info = $this->M_coa->getCoa($d['no_coa']);
				?>
					<tr>
						<td><?= $no++ ?></td>
						<td><?= $d['no_coa'] ?> - <?= $coa_info['nama_perkiraan'] ?? '' ?></td>
						<td class="text-end">
							<?php if ($d['posisi'] == 'DEBIT') :
								$total_debit += $d['nominal']; ?>
								<?= number_format($d['nominal'], 2, ',', '.') ?>
							<?php else : ?>
								-
							<?php endif; ?>
						</td>
						<td class="text-end">
							<?php if ($d['posisi'] == 'KREDIT') :
								$total_kredit += $d['nominal']; ?>
								<?= number_format($d['nominal'], 2, ',', '.') ?>
							<?php else : ?>
								-
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr class="tfoot-total">
					<td colspan="2" class="text-end">Total</td>
					<td class="text-end"><?= number_format($total_debit, 2, ',', '.') ?></td>
					<td class="text-end"><?= number_format($total_kredit, 2, ',', '.') ?></td>
				</tr>
			</tfoot>
		</table>

		<!-- Terbilang & Tanda Tangan -->
		<table class="mb-10">
			<tbody>
				<tr>
					<td style="width: 70%; vertical-align: top;">
						<?php if ($project['nama_file']) : ?>
							<p style="color: #555; font-size: 9pt;">📎 Attachment: <?= $project['nama_file'] ?></p>
						<?php endif; ?>
					</td>
					<td style="width: 30%; text-align: center; vertical-align: top;">
						<p>Hormat Kami,</p>
						<p style="margin-top: 60px;"><strong><?= $user['nama'] ?></strong></p>
						<p style="font-size: 9pt; color: #555;"><?= $project['created_by'] ?></p>
					</td>
				</tr>
			</tbody>
		</table>

	</div>
</body>

</html>