<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title">
            <strong>Daftar Invoice</strong>
          </p>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
              <a href="<?= site_url('financial/create_invoice') ?>" class="btn btn-primary">Create Inv.</a>
            </div>
          </div>
          <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/invoice') ?>">
            <div class="row">
              <div class="col-md-3 col-xs-12">
                <div class="form-group">
                  <select name="customer_id" id="customer_id" class="form-control select2" style="width: 100%;">
                    <option value="">:: Semua customer</option>
                    <?php
                    foreach ($customers as $c) :
                    ?>
                      <option <?= ($this->input->post('customer_id') == $c->id) ? "selected" : "" ?> value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
                    <?php
                    endforeach;
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-5 col-xs-12">
                <div class="form-group">
                  <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Masukkan nomor invoice">
                </div>
              </div>
              <div class="col-md-2 col-xs-12">
                <button type="submit" class="btn btn-success">Cari</button>
                <a href="<?= base_url('financial/invoice') ?>" class="btn btn-warning">Reset</a>
              </div>
            </div>
          </form>
          <div class="col-md-1"></div>
          <div class="table-responsive">
            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>No.</th>
                  <th>Tanggal</th>
                  <th>Customer</th>
                  <th>Total</th>
                  <th>User</th>
                  <th>Stt. Bayar</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($invoices) {
                  foreach ($invoices as $i) : ?>
                    <tr>
                      <td><?= $i['no_invoice'] ?></td>
                      <td><?= format_indo($i['tanggal_invoice']) ?></td>
                      <td><?= $i['nama_customer'] ?></td>
                      <td class="text-right"><?= number_format($i['total_nonpph'], 0) ?></td>
                      <td><?= isset($i['created_by_name']) ? $i['created_by_name'] : 'N/A' ?></td>
                      <td>
                        <?php
                        if ($i['status_void'] == "1") {
                        ?>
                          <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="right" title="" data-original-title="Alasan: <?= $i['alasan_void'] ?>">Sudah divoid</span>
                        <?php
                        }

                        if ($i['status_bayar'] == "1") {
                        ?>
                          <span class="badge badge-pill badge-success">Sudah dibayar</span>
                        <?php
                        }

                        if ($i['status_bayar'] == "0" and $i['status_void'] != "1") {
                          $piutang = $i['total_denganpph'] - $i['total_termin']; ?>
                          <a href="#" class="badge btn-danger" data-toggle="modal" data-target="#void<?= $i['Id'] ?>">Void</a>
                          <a href="#" class="badge btn-primary" data-toggle="modal" data-target="#modal<?= $i['Id'] ?>">Bayar</a>

                          <div class="modal fade" id="modal<?= $i['Id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 class="modal-title" id="myModalLabel">
                                    <?= $i['no_invoice'] ?>
                                  </h4>
                                </div>
                                <form action="<?= base_url('financial/paid/' . $i['no_invoice']) ?>" method="post">
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="nominal_invoice" class="form-label">Nominal Invoice</label>
                                          <input type="text" name="nominal_invoice" id="nominal_invoice<?= $i['Id'] ?>" class="form-control" value="<?= number_format($i['total_denganpph']) ?>" readonly>
                                        </div>
                                      </div>
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="piutang" class="form-label">Belum bayar</label>
                                          <input type="text" name="piutang" id="piutang<?= $i['Id'] ?>" class="form-control" value="<?= number_format($piutang) ?>" readonly>
                                        </div>
                                      </div>
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="nominal_bayar" class="form-label">Nominal bayar</label>
                                          <input type="text" name="nominal_bayar" id="nominal_bayar<?= $i['Id'] ?>" class="form-control" value="<?= number_format(($i['opsi_termin'] == 0) ? $piutang : '0', 0, ',', '.') ?>" <?= ($i['opsi_termin'] == 0) ? 'readonly' : '' ?> required>
                                        </div>
                                      </div>
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="tanggal_bayar" class="form-label">Tanggal bayar</label>
                                          <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required>
                                        </div>
                                      </div>
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="coa_debit" class="form-label">CoA Debit</label>
                                          <select name="coa_debit" id="coa_debit<?= $i['Id'] ?>" class="form-control" required>
                                            <option value="">:: Pilih CoA Kas</option>
                                            <?php
                                            foreach ($coa_kas as $c) :
                                            ?>
                                              <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                                            <?php
                                            endforeach; ?>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                          <label for="coa_kredit" class="form-label">CoA Kredit</label>
                                          <select name="coa_kredit" id="coa_kredit<?= $i['Id'] ?>" class="form-control" required>
                                            <option value="">:: Pilih CoA Kas</option>
                                            <?php
                                            foreach ($coa_pendapatan as $c) :
                                            ?>
                                              <option value="<?= $c->no_sbb ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                                            <?php
                                            endforeach; ?>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-sm-2 col-xs-12">
                                        <div class="form-group">
                                          <label for="Lunas" class="form-label">Lunas <?= $i['opsi_termin'] ?></label>
                                          <div class="checkbox text-end">
                                            <input type="checkbox" name="status_bayar" id="status_bayar<?= $i['Id'] ?>" value="1" <?= ($i['opsi_termin'] == 0) ? 'checked ' : '' ?>> Ya
                                          </div>
                                        </div>
                                      </div>

                                      <div class="col-sm-12 col-xs-12">
                                        <div class="form-group">
                                          <label for="keterangan" class="form-label">Keterangan</label>
                                          <textarea name="keterangan" id="keterangan" class="form-control uppercase" required>Pembayaran invoice nomor <?= $i['no_invoice'] ?></textarea>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                      Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                      Process
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade" id="void<?= $i['Id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h4 class="modal-title" id="myModalLabel">
                                    <?= $i['no_invoice'] ?>
                                  </h4>
                                </div>
                                <form action="<?= base_url('financial/void_invoice/' . $i['no_invoice']) ?>" method="post">
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-sm-12 col-xs-12">
                                        <div class="form-group">
                                          <label for="keterangan" class="form-label">Keterangan</label>
                                          <textarea name="keterangan" id="keterangan" class="form-control uppercase" required></textarea>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                      Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                      Process
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        <?php
                        } ?>
                      </td>
                      <td>
                        <a href="<?= base_url('financial/print_invoice/' . $i['Id']) ?>" class="badge btn-warning" target="_blank" style="vertical-align: top;">
                          Cetak
                        </a>
                        <?php
                        if ($i['status_bayar'] == "0" and $i['status_void'] != "1") {
                        ?>
                          <a href="<?= base_url('financial/edit_invoice/' . $i['Id']) ?>" class="badge btn-success" style="vertical-align: top;">
                            Edit
                          </a>
                        <?php
                        } ?>
                      </td>
                    </tr>

                  <?php
                  endforeach;
                } else {
                  ?>
                  <tr>
                    <td colspan="7">Tidak ada data yang ditampilkan</td>
                  </tr>
                <?php
                } ?>
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-md-6">
              <h6>*klik nomor invoice untuk lihat detail invoice</h6>
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