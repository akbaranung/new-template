<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Outstanding</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title"><strong>Outstanding</strong></p>
        </div> -->
        <div class="card-body">
          <div class="table-responsive">
            <table id="" class="table table-sm table-striped table-bordered" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>No.</th>
                  <th>Agen</th>
                  <th>Current</th>
                  <!-- <th>Out 1</th>
                  <th>Out 2</th>
                  <th>Out 3</th>
                  <th>Out 4</th> -->
                  <th>OS 1</th>
                  <th>OS 2</th>
                  <th>OS 3</th>
                  <th>OS 4</th>
                  <th>Total</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($outstanding) {
                  $no = 1;
                  $total_current = 0;
                  $total_out1 = 0;
                  $total_out2 = 0;
                  $total_out3 = 0;
                  $total_out4 = 0;
                  $total_out5 = 0;
                  $total = 0;
                  foreach ($outstanding as $i) : ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= $i['nama_customer'] ?></td>
                      <td class="text-right"><?= rupiah($i['current']) ?></td>
                      <td class="text-right"><?= rupiah($i['out1']) ?></td>
                      <td class="text-right"><?= rupiah($i['out2']) ?></td>
                      <td class="text-right"><?= rupiah($i['out3']) ?></td>
                      <td class="text-right"><?= rupiah($i['out4']) ?></td>
                      <td class="text-right"><?= rupiah($i['total']) ?></td>
                      <td>
                        <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/invoice') ?>">
                          <input type="hidden" value="<?= $i['id_customer'] ?>" name="customer_id">
                          <button type="submit" class="btn btn-primary btn-sm">Detail</button>
                        </form>
                      </td>
                    </tr>

                  <?php
                    $total_current += $i['current'];
                    $total_out1 += $i['out1'];
                    $total_out2 += $i['out2'];
                    $total_out3 += $i['out3'];
                    $total_out4 += $i['out4'];
                    $total += $i['total'];
                  endforeach;
                  $total_outstanding = $total_out1 + $total_out2 + $total_out3 + $total_out4 + $total_out5; ?>
                  <tr>
                    <td colspan="2" class="text-center" rowspan="2"><strong>Total</strong></td>
                    <td class="text-right" rowspan="2"><strong><?= rupiah($total_current) ?></strong></td>
                    <td class="text-right"><strong><?= rupiah($total_out1) ?></strong></td>
                    <td class="text-right"><strong><?= rupiah($total_out2) ?></strong></td>
                    <td class="text-right"><strong><?= rupiah($total_out3) ?></strong></td>
                    <td class="text-right"><strong><?= rupiah($total_out4) ?></strong></td>
                    <td class="text-right" rowspan="2"><strong><?= rupiah($total) ?></strong></td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-center"><strong><?= rupiah($total_outstanding) ?></td>
                  </tr>
                <?php

                } else {
                ?>
                  <tr>
                    <td colspan="8">Tidak ada data yang ditampilkan</td>
                  </tr>
                <?php
                } ?>
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-md-6">
              <h6>*klik nomor invoice untuk lihat detail invoice</h6>
              <p>Keterangan:</p>
              <p>Outstanding 1 = 1 - 10 hari</p>
              <p>Outstanding 2 = 11 - 30 hari</p>
              <p>Outstanding 3 = 31 - 90 hari</p>
              <p>Outstanding 4 = >91 hari</p>
            </div>
            <div class="col-md-6 text-right">
              <?= $this->pagination->create_links() ?>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->