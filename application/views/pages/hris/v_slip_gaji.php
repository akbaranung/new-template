<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title_pdf ?></title>

	<style>
		body {
			font-family: Arial, Helvetica, sans-serif !important;
			font-size: 9pt;
		}

		table {
			border-collapse: collapse;
		}

		.title {
			font-weight: bold;
			color: #004e81;
		}

		table {
			width: 100%;
		}

		.table-bordered {
			border: 1px solid black;
		}

		thead>tr>th {
			background-color: #004e81;
			color: white;
			padding: 7px;
			border: 2px solid white;
		}

		.table-bordered>tbody>tr>td {
			background-color: #e7e7e7;
			padding: 7px;
			border: 2px solid white;
		}

		.text-end {
			text-align: right;
		}

		.mb-10 {
			margin-bottom: 8px;
		}

		.mb-30 {
			margin-bottom: 30px;
		}

		.mb-50 {
			margin-bottom: 50px;
		}

		.net-salary-box {
			background-color: #004e81;
			color: white;
			padding: 12px;
			text-align: center;
			margin-top: 5px;
		}

		.net-salary-amount {
			font-size: 16pt;
			font-weight: bold;
		}
	</style>

</head>

<body>

	<div class="container p-0">
		<?php
		$bulan_tahun = date("Y-m", strtotime(date($slip->bulan_gaji)));
		?>

		<!-- Header -->
		<table class="mb-10">
			<tbody>
				<tr>
					<td>
						<img src="<?= $this->session->userdata('icon') ?>" style="max-width: 200px;" alt="">
					</td>
					<td colspan="2" class="text-end">
						<p style="font-size: 20pt" class="title">Slip Gaji</p>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="text-end">
						Periode <br>
						NIP
					</td>
					<td class="text-end" style="width: 25%;">
						<?= periode($bulan_tahun) ?> <br>
						<?= $slip->nip ?>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Employee & Attendance Info -->
		<table class="mb-10">
			<tbody>
				<tr>
					<td style="vertical-align:bottom; width: 49%;">
						<p class="title">Informasi Karyawan</p>
						<hr>
					</td>
					<td style="vertical-align:bottom; width: 2%;">
					</td>
					<td style="vertical-align:bottom; width: 49%;">
						<p class="title">Data Kehadiran</p>
						<hr>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">
						<table style="width: 100%">
							<tr>
								<td style="width: 100px;">NIP</td>
								<td>: <?= $slip->nip ?></td>
							</tr>
							<tr>
								<td>Nama</td>
								<td>: <?= $slip->nama ?></td>
							</tr>
							<tr>
								<td>Jabatan</td>
								<td>: <?= $slip->jabatan ?></td>
							</tr>
						</table>
					</td>
					<td style="vertical-align:bottom; width: 2%;">
					</td>
					<td style="vertical-align:top">
						<table style="width: 100%">
							<tr>
								<td style="width: 100px;">Hari Kerja</td>
								<td>: <?= $slip->hari_kerja ?> hari</td>
							</tr>
							<tr>
								<td>Tidak Hadir</td>
								<td>: <?= $slip->tidak_hadir ?> hari</td>
							</tr>
							<tr>
								<td>Surat Dokter</td>
								<td>: <?= $slip->surat_dokter ?> hari</td>
							</tr>
							<tr>
								<td>Cuti</td>
								<td>: <?= $slip->potong_cuti ?> hari</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Salary Details Table -->
		<table class="table-bordered mb-10">
			<thead>
				<tr>
					<th>Pendapatan</th>
					<th>Nominal</th>
					<th>Potongan</th>
					<th>Nominal</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Gaji Pokok</td>
					<td class="text-end"><?= number_format($slip->gapok) ?></td>
					<td>Kasbon</td>
					<td class="text-end"><?= number_format($slip->pot_kasbon) ?></td>
				</tr>
				<tr>
					<td>Tunjangan Fungsional</td>
					<td class="text-end"><?= number_format($slip->tu_fungsional) ?></td>
					<td>Potongan WFH</td>
					<td class="text-end"><?= number_format($slip->pot_wfh) ?></td>
				</tr>
				<tr>
					<td>Tunjangan Jabatan</td>
					<td class="text-end"><?= number_format($slip->tu_jabatan) ?></td>
					<td>Potongan Absensi</td>
					<td class="text-end"><?= number_format($slip->pot_absen) ?></td>
				</tr>
				<tr>
					<td>Tunjangan Transport</td>
					<td class="text-end"><?= number_format($slip->tu_transport) ?></td>
					<td>Potongan Terlambat</td>
					<td class="text-end"><?= number_format($slip->pot_terlambat) ?></td>
				</tr>
				<tr>
					<td>Uang Makan</td>
					<td class="text-end"><?= number_format($slip->tu_makan) ?></td>
					<td>Potongan Pulang Cepat</td>
					<td class="text-end"><?= number_format($slip->pot_pulang) ?></td>
				</tr>
				<tr>
					<td>Insentif</td>
					<td class="text-end"><?= number_format($slip->tu_insentif) ?></td>
					<td>Potongan BPJS TK</td>
					<td class="text-end"><?= number_format($slip->pot_bpjs_tk) ?></td>
				</tr>
				<tr>
					<td>Uang Lembur</td>
					<td class="text-end"><?= number_format($slip->tu_lembur) ?></td>
					<td>Potongan BPJS Kesehatan</td>
					<td class="text-end"><?= number_format($slip->pot_bpjs_kes) ?></td>
				</tr>
				<tr>
					<td>Tunjangan BPJS TK</td>
					<td class="text-end"><?= number_format($slip->tu_bpjs_tk) ?></td>
					<td>Pinjaman Koperasi</td>
					<td class="text-end"><?= number_format($slip->pot_koperasi) ?></td>
				</tr>
				<tr>
					<td>BPJS Kesehatan</td>
					<td class="text-end"><?= number_format($slip->tu_bpjs_kes) ?></td>
					<td>Simpanan Koperasi</td>
					<td class="text-end"><?= number_format($slip->simp_koperasi) ?></td>
				</tr>
				<tr>
					<td></td>
					<td class="text-end"></td>
					<td>PPh21</td>
					<td class="text-end"><?= number_format($slip->pph21) ?></td>
				</tr>
				<tr>
					<td></td>
					<td class="text-end"></td>
					<td>Potongan Lainnya</td>
					<td class="text-end"><?= number_format($slip->pot_lainnya) ?></td>
				</tr>
			</tbody>
		</table>

		<!-- Summary -->
		<table class="">
			<tbody>
				<tr>
					<td style="width: 50%">
						<p class="title">Catatan</p>
						<hr>
						<p>Slip gaji ini merupakan bukti pembayaran yang sah. Harap disimpan dengan baik.</p>
					</td>
					<td class="text-end" style="width: 25%; vertical-align: top">
						<strong>
							<p>Total Pendapatan</p>
							<p>Total Potongan</p>
							<p>Gaji Bersih</p>
						</strong>
					</td>
					<td class="text-end" style="width: 25%; vertical-align: top">
						<p><?= number_format($slip->gross_gaji) ?></p>
						<p><?= number_format($slip->pot_total) ?></p>
						<p><strong><?= number_format($slip->net_gaji) ?></strong></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="mb-30">Terbilang: <strong><?= ucwords(terbilang($slip->net_gaji)) ?> Rupiah</strong></p>

		<!-- Net Salary Highlight -->
		<div class="net-salary-box">
			<div>GAJI YANG DITERIMA</div>
			<div class="net-salary-amount">Rp <?= number_format($slip->net_gaji) ?>,-</div>
		</div>

		<!-- Signature -->
		<table>
			<tbody>
				<tr>
					<td style="width: 60%;"></td>
					<td style="text-align: center;">
						<p style="margin-top: 15px;">Mengetahui,</p>
						<p style="margin-top: 60px;">___________________</p>
						<p>HRD / Finance</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

</body>

</html>