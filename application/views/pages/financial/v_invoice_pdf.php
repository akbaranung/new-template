<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title_pdf; ?></title>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->

  <style>
    body {
      font-family: Arial, Helvetica, sans-serif !important;
      font-size: 10pt;
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

    /* table.table>th,
        td {
            border: 1px solid black;
        } */

    .table-bordered {
      border: 1px solid black;
    }

    thead>tr>th {
      background-color: #004e81;
      color: white;
      padding: 10px;
      /* background-color: #004e81; */
      border: 2px solid white;
    }

    .table-bordered>tbody>tr>td {
      background-color: #e7e7e7;
      padding: 10px;
      border: 2px solid white;
    }

    .text-end {
      text-align: right;
    }

    .mb-10 {
      margin-bottom: 10px;
    }
  </style>

</head>

<body>

  <div class="container p-0">
    <?php
    $month = substr($invoice['tanggal_invoice'], 5, 2);
    $year = substr($invoice['tanggal_invoice'], 2, 2);

    $nomor = $invoice['no_invoice'] . "/KSI-01/" .  intToRoman($month) . "/" .  $year
    ?>

    <table class="" style="margin-bottom: 30px; width: 100%">
      <tbody>
        <tr>
          <td>
            <img src="<?= $this->session->userdata('icon') ?>" style="width: 150px;" alt="">
          </td>
          <td colspan="2" class="text-end">
            <p style="font-size: 20pt" class="title">Invoice</p>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="text-end">
            Referensi <br>
            Tanggal
          </td>
          <td class="text-end" style="width: 25%;">
            <?= ($invoice['slug_invoice']) ? $invoice['slug_invoice'] : $nomor  ?> <br>
            <?= format_indo($invoice['tanggal_invoice']) ?>
          </td>
        </tr>
      </tbody>
    </table>


    <table class="mb-10">
      <tbody>
        <tr>
          <td style="vertical-align:bottom; width: 49%;">
            <p class="title">Informasi Perusahaan</p>
            <hr>
          </td>
          <td style="vertical-align:bottom; width: 2%;">
          </td>
          <td style="vertical-align:bottom; width: 49%;">
            <p class="title">Tagihan Kepada</p>
            <hr>
          </td>
        </tr>
        <tr>
          <td style="vertical-align:top">
            <p class="title" style="margin-top: 0;"><?= $this->session->userdata('nama_perusahaan') ?></p>
            <p style=""><?= nl2br($this->session->userdata('alamat_perusahaan')) ?></p>
          </td>
          <td style="vertical-align:bottom; width: 2%;">
          </td>
          <td style="vertical-align:top">
            <p class="title" style="margin-top: 0px;"><?= $invoice['nama_customer'] ?></p>
            <?= $invoice['alamat_customer'] ?>
          </td>
        </tr>
      </tbody>
    </table>

    <table class="table-bordered mb-10">
      <thead>
        <tr>
          <!-- <th style="width: 10%">No.</th> -->
          <th>Keterangan</th>
          <th>Item</th>
          <th>Harga</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        foreach ($details as $d) :
        ?>
          <tr>
            <td><?= $d->item ?></td>
            <td class="text-end"><?= number_format($d->qty) ?></td>
            <td class="text-end"><?= number_format($d->total) ?></td>
            <td class="text-end"><?= number_format($d->total_amount) ?></td>
          </tr>
        <?php
        endforeach;
        ?>
      </tbody>
    </table>
    <table class="">
      <tbody>
        <tr>
          <td style="width: 50%">
            <p class="title">Pesan</p>
            <hr>
            <p>
              <?= nl2br($this->session->userdata('nomor_rekening')) ?>
            </p>
          </td>
          <td class="text-end" style="width: 25%; vertical-align: top">
            <strong>
              <p>Subtotal</p>
              <?php
              if ($invoice['besaran_ppn'] != '0.00') {
              ?>
                <p><?= $this->session->userdata('nama_ppn') ?></p>
              <?php
              } ?>
              <p>Total</p>
            </strong>
          </td>
          <td class="text-end" style="width: 25%; vertical-align: top">
            <p><?= number_format($invoice['subtotal']) ?></p>
            <?php
            if ($invoice['besaran_ppn'] != '0.00') {
            ?>
              <p><?= number_format($invoice['besaran_ppn']) ?></p>
            <?php
            } ?>
            <p><?= number_format($invoice['total_nonpph']) ?></p>
          </td>
        </tr>
      </tbody>
    </table>

    <p class="mb-50">Terbilang: <?= terbilang(round($invoice['total_nonpph'])) ?> Rupiah</p>
    <table>
      <tbody>
        <tr>
          <td style="width: 80%;"></td>
          <td>
            <p style="margin-top: 20px;">Dengan Hormat</p>
            <p style="margin-top: 70px;"><?= $user['nama'] ?></p>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</body>

</html>