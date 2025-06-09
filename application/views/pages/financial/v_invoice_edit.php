<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title">
            <strong>Financial Entry</strong>
          </p>
        </div>
        <div class="card-body">
          <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/update_invoice/' . $inv['Id']) ?>">
            <div class="form-group row">
              <div class="col-md-2 col-xs-12">
                <label for="no_invoice" class="form-label">Number</label>
                <input type="text" class="form-control" name="no_invoice" value="<?= $inv['no_invoice'] ?>" readonly>
              </div>
              <div class="col-md-3 col-xs-12">
                <label for="tgl_invoice" class="form-label">Date</label>
                <input type="date" class="form-control" name="tgl_invoice" value="<?= $inv['tanggal_invoice'] ?>">
              </div>
              <div class="col-md-5 col-xs-12">
                <label for="customer" class="form-label">Bill to</label>
                <select name="customer" id="customer" class="form-control select2" style="width: 100%" required>
                  <option value="">:: Pilih customer</option>
                  <?php
                  foreach ($customers as $c) : ?>
                    <option <?= ($inv['id_customer'] == $c->id) ? "selected" : "" ?> value="<?= $c->id ?>"><?= $c->nama_customer ?></option>
                  <?php
                  endforeach; ?>
                </select>
              </div>
              <!-- <div class="col-md-2 col-xs-12">
                                            <label for="diskon" class="form-label">Discount</label>
                                            <select name="diskon" id="diskon" class="form-control">
                                                <option value="0">0%</option>
                                                <option value="0.05">5%</option>
                                                <option value="0.1">10%</option>
                                            </select>
                                        </div> -->
              <div class="col-md-2 col-xs-12">
                <label for="ppn" class="form-label">PPN</label>
                <select name="ppn" id="ppn" class="form-control">
                  <option <?= ($inv['ppn'] == "0.000") ? "selected" : "" ?> value="0.000">0%</option>
                  <option <?= ($inv['ppn'] == "0.110") ? "selected" : "" ?> value="0.110">11%</option>
                </select>
              </div>
            </div>
            <div class="form-group row">

              <div class="col-md-12">
                <label for="keterangan" class="form-label">Notes</label>
                <input name="keterangan" id="keterangan" class="form-control uppercase" placeholder="Enter notes here..." value="<?= $inv['keterangan'] ?>" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-2 col-xs-12">
                <label for="nominal" class="form-label">Subtotal</label>
                <input type="text" class="form-control" name="nominal" id="nominal" value="<?= number_format($inv['subtotal'], 0, ',', ',') ?>" readonly>
              </div>
              <!-- <div class="col-md-2 col-xs-12">
                                            <label for="besaran_diskon" class="form-label">Discount</label>
                                            <input type="text" class="form-control" name="besaran_diskon" id="besaran_diskon" value="0" readonly>
                                        </div> -->
              <div class="col-md-2 col-xs-12">
                <label for="besaran_ppn" class="form-label">PPN</label>
                <input type="text" class="form-control" name="besaran_ppn" id="besaran_ppn" value="<?= number_format($inv['besaran_ppn'], 0, ',', ',') ?>" readonly>
              </div>
              <div class="col-md-2 col-xs-12">
                <label for="besaran_pph" class="form-label">PPh 23</label>
                <input type="text" class="form-control" name="besaran_pph" id="besaran_pph" value="<?= number_format($inv['besaran_pph'], 0, ',', ',') ?>" readonly>
              </div>
              <div class="col-md-2 col-xs-12">
                <label for="total_nonpph" class="form-label">Total (non PPh)</label>
                <input type="text" class="form-control" name="total_nonpph" id="total_nonpph" value="<?= number_format($inv['total_nonpph'], 0, ',', ',') ?>" readonly>
              </div>
              <div class="col-md-2 col-xs-12">
                <label for="total_denganpph" class="form-label">Total (w/ PPh)</label>
                <input type="text" class="form-control" name="total_denganpph" id="total_denganpph" value="<?= number_format($inv['total_denganpph'], 0, ',', ',') ?>" readonly>
              </div>
              <div class="col-md-2 col-xs-12">
                <label for="total_denganpph" class="form-label">Pendapatan</label>
                <input type="text" class="form-control" name="nominal_pendapatan" id="nominal_pendapatan" value="<?= number_format($inv['nominal_pendapatan'], 0, ',', ',') ?>" readonly>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-2 col-xs-12">
                <label for="nominal_bayar" class="form-label">Nominal bayar</label>
                <input type="text" class="form-control" name="nominal_bayar" id="nominal_bayar" value="<?= number_format($inv['nominal_bayar'], 0, ',', ',') ?>" readonly>
              </div>

              <div class="col-md-3">
                <label for="coa_debit" class="form-label">CoA Debit</label>
                <select name="coa_debit" id="coa_debit" class="form-control select2" style="width: 100%" required>
                  <option value="">:: Pilih CoA Debit</option>
                  <?php
                  foreach ($pendapatan as $pd) :
                  ?>
                    <option <?= ($pd->no_sbb == $inv['coa_debit']) ? "selected" : "" ?> value="<?= $pd->no_sbb ?>"><?= $pd->no_sbb . ' - ' . $pd->nama_perkiraan ?></option>
                  <?php
                  endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <label for="coa_kredit" class="form-label">CoA Kredit</label>
                <select name="coa_kredit" id="coa_kredit" class="form-control select2" style="width: 100%" required>
                  <option value="">:: Pilih CoA Kredit</option>
                  <?php
                  foreach ($persediaan as $ps) :
                  ?>
                    <option <?= ($ps->no_sbb == $inv['coa_kredit']) ? "selected" : "" ?> value="<?= $ps->no_sbb ?>"><?= $ps->no_sbb . ' - ' . $ps->nama_perkiraan ?></option>
                  <?php
                  endforeach; ?>
                </select>
              </div>
              <div class="col-md-1 col-xs-12">
                <label for="termin" class="form-label">Termin</label>
                <div class="checkbox text-end">
                  <input type="checkbox" class="icheckbox_flat-green" style="margin-left: 0px;" name="opsi_termin" value="1">
                </div>
              </div>
              <div class="col-md-1 col-xs-12">
                <label for="opsi_pph" class="form-label">PPh 23</label>
                <div class="checkbox text-end">
                  <input type="checkbox" class="icheckbox_flat-green" style="margin-left: 0px;" name="opsi_pph" id="opsi_pph" value="1">
                  <!-- <input id="toggleSwitch" type="checkbox" data-toggle="toggle" class="flat"> -->
                </div>
              </div>
              <div class="col-md-2 col-xs-12 text-right">
                <label for="keterangan" class="form-label">&nbsp;</label>
                <div class="mt-2">
                  <a href="<?= base_url('financial/invoice') ?>" class="btn btn-sm btn-warning"><i class="bi bi-arrow-return-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary btn-sm">Save <i class="bi bi-save"></i></button>
                </div>
              </div>
            </div>
            <table class="table mt-5 table-responsive">
              <thead>
                <tr>
                  <th>Keterangan</th>
                  <th>Jumlah</th>
                  <th>Nominal</th>
                  <th>Amount</th>
                  <th>Del.</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($details) {
                  foreach ($details as $d) : ?>
                    <tr class="baris">
                      <td>
                        <input type="text" class="form-control uppercase" name="item[]" value="<?= $d->item ?>">
                      </td>
                      <td>
                        <input type="text" class="form-control" name="jumlah[]" value="<?= number_format($d->qty, 0, ",", ",") ?>">
                      </td>
                      <td>
                        <input type="text" class="form-control total" name="total[]" value="<?= number_format($d->total, 0, ",", ",") ?>">
                      </td>
                      <td>
                        <input type="text" class="form-control" name="total_amount[]" value="<?= number_format($d->total_amount, 0, ",", ",") ?>" readonly>
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger btn-sm hapusRow">Hapus</button>
                      </td>
                    </tr>
                  <?php
                  endforeach;
                } else {
                  ?>
                  <tr class="baris">
                    <td>
                      <input type="text" class="form-control uppercase" name="item[]">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="jumlah[]" value="0">
                    </td>
                    <td>
                      <input type="text" class="form-control total" name="total[]" value="0">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="total_amount[]" value="0" readonly>
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm hapusRow">Hapus</button>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
            <div class="row">
              <div class="col-lg-12 text-end">
                <button type="button" class="btn btn-secondary btn-sm" id="addRow">Add new row</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->