<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan Biaya</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Detail Pengajuan <?= $pengajuan->kode ?></strong></p>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <a href="<?= site_url('pengajuan/list') ?>" class="btn btn-warning">Kembali</a>
            </div>
          </div>
          <?php if ($this->uri->segment(4) != 'finance' or !$this->uri->segment(4)) { ?>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="25px">No.</th>
                      <th width="400px">Uraian</th>
                      <th width="25px">Qty</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>Realisasi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($pengajuan_detail as $row) : ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row->item ?></td>
                        <td><?= $row->qty ?></td>
                        <td align="right"><?= rupiah($row->price) ?></td>
                        <td align="right"><?= rupiah($row->total) ?></td>
                        <td align="right"><?= $row->realisasi ? rupiah($row->realisasi) : '-' ?></td>
                      </tr>
                    <?php endforeach ?>
                    <tr>
                      <td colspan="4" align="right">Total</td>
                      <td align="right"><?= rupiah($pengajuan->total) ?></td>
                      <td align="right"><?= $pengajuan->total_realisasi ? rupiah($pengajuan->total) : "-" ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">Lampiran</td>
                      <td colspan="4"><a href="<?= base_url('uploads/pengajuan/') . $pengajuan->bukti_pengajuan ?>" class="btn btn-success btn-sm" target="_blank"><i class="fe fe-download fe-12"> Lampiran</i></a></td>
                    </tr>
                    <tr>
                      <td colspan="2">Bukti Bayar</td>
                      <td colspan="4">
                        <?php if ($pengajuan->bukti_bayar) { ?>
                          <a href="<?= base_url('uploads/pengajuan/') . $pengajuan->bukti_bayar ?>" class="btn btn-success btn-sm" target="_blank"><i class="fe fe-download fe-12"> Bukti Bayar</i></a>
                        <?php } else {
                          echo "-";
                        } ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">Catatan User</td>
                      <td colspan="4"><?= $pengajuan->catatan ? $pengajuan->catatan : 'Tidak ada catatan' ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">Catatan Supervisi</td>
                      <td colspan="4"><?= $pengajuan->catatan_spv ? $pengajuan->catatan_spv : 'Tidak ada catatan' ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">Catatan Keuangan</td>
                      <td colspan="4"><?= $pengajuan->keuangan ? $pengajuan->keuangan : 'Tidak ada catatan' ?></td>
                    </tr>
                    <tr>
                      <td colspan="2">Catatan Direksi</td>
                      <td colspan="4"><?= $pengajuan->direksi ? $pengajuan->direksi : 'Tidak ada catatan' ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <?php if ($this->uri->segment(4) == 'spv' and $pengajuan->posisi == 'Diajukan kepada supervisi') { ?>
              <hr>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <form action="<?= site_url('pengajuan/update_spv/') . $this->uri->segment(3) ?>" method="post">
                    <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="tanggal" class="form-label">Tanggal</label>
                          <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="status" class="form-label">Status</label>
                          <select name="status" id="status" class="form-control">
                            <option value="1">Disetujui</option>
                            <option value="0">Ditolak</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div>
                      <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            <?php } ?>

            <?php if ($this->uri->segment(4) == 'spv' and ($pengajuan->posisi == 'Ditolak oleh supervisi' or $pengajuan->status == 1)) { ?>
              <hr>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d', strtotime($pengajuan->date_spv)) ?>" disabled>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                      <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" disabled>
                          <option value="1" <?= $pengajuan->status == 1 ? 'selected' : '' ?>>Disetujui</option>
                          <option value="0" <?= $pengajuan->status == 0 ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

          <?php } else { ?>
            <form action="<?= site_url('pengajuan/update_keuangan/') . $this->uri->segment(3) ?>">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th width="400px">Uraian</th>
                        <th width="25px">Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>COA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach ($pengajuan_detail as $row) : ?>
                        <input type="hidden" name="id_item[]" value="<?= $row->Id ?>">
                        <tr>
                          <td><?= $row->item ?></td>
                          <td><?= $row->qty ?></td>
                          <td align="right"><?= rupiah($row->price) ?></td>
                          <td align="right"><?= rupiah($row->total) ?></td>
                          <td>
                            <?php if ($row->status == 1) { ?>
                              <select name="coa[]" id="coa-<?= $no++ ?>" class="form-control select2">
                                <?php foreach ($coa as $c) : ?>
                                  <option value="<?= $c->no_sbb ?>" <?= $c->no_sbb == '15110' ? 'selected' : '' ?>><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                                <?php endforeach ?>
                              </select>
                            <?php } else { ?>
                              <select name="coa[]" id="coa-<?= $no++ ?>" class="form-control select2" disabled>
                                <?php foreach ($coa as $c) : ?>
                                  <option value="<?= $c->no_sbb ?>" <?= $c->no_sbb == '15110' ? 'selected' : '' ?>><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                                <?php endforeach ?>
                              </select>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php endforeach ?>
                      <tr>
                        <td colspan="3" align="right">Total</td>
                        <td align="right"><?= rupiah($pengajuan->total) ?></td>
                      </tr>
                      <tr>
                        <td colspan="2">Lampiran</td>
                        <td colspan="3"><a href="<?= base_url('uploads/pengajuan/') . $pengajuan->bukti_pengajuan ?>" class="btn btn-success btn-sm" target="_blank"><i class="fe fe-download fe-12"> Lampiran</i></a></td>
                      </tr>
                      <tr>
                        <td colspan="2">Bukti Bayar</td>
                        <td colspan="3">
                          <?php if ($pengajuan->bukti_bayar) { ?>
                            <a href="<?= base_url('uploads/pengajuan/') . $pengajuan->bukti_bayar ?>" class="btn btn-success btn-sm" target="_blank"><i class="fe fe-download fe-12"> Bukti Bayar</i></a>
                          <?php } else {
                            echo "-";
                          } ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">Catatan User</td>
                        <td colspan="3"><?= $pengajuan->catatan ? $pengajuan->catatan : 'Tidak ada catatan' ?></td>
                      </tr>
                      <tr>
                        <td colspan="2">Catatan Supervisi</td>
                        <td colspan="3"><?= $pengajuan->catatan_spv ? $pengajuan->catatan_spv : 'Tidak ada catatan' ?></td>
                      </tr>
                      <tr>
                        <td colspan="2">Catatan Keuangan</td>
                        <td colspan="3"><?= $pengajuan->keuangan ? $pengajuan->keuangan : 'Tidak ada catatan' ?></td>
                      </tr>
                      <tr>
                        <td colspan="2">Catatan Direksi</td>
                        <td colspan="3"><?= $pengajuan->direksi ? $pengajuan->direksi : 'Tidak ada catatan' ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <?php if ($this->uri->segment(4) == 'finance' and $pengajuan->posisi == 'Diajukan kepada keuangan') { ?>
                <hr>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="rekening" class="form-label">Rekening</label>
                          <input type="text" class="form-control" name="rekening" id="rekening" value="<?= $pengajuan->no_rekening ?>" disabled>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="metode" class="form-label">Metode Pembayaran</label>
                          <input type="text" class="form-control" name="metode" id="metode" value="<?= $pengajuan->metode_pembayaran ?>" disabled>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="tanggal" class="form-label">Tanggal</label>
                          <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="status" class="form-label">Status</label>
                          <select name="status" id="status" class="form-control">
                            <option value="1">Disetujui</option>
                            <option value="0">Ditolak</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="direksi" class="form-label">Approval Direksi</label>
                          <select name="direksi" id="direksi" class="form-control">
                            <option value="1">Ya</option>
                            <option value="2" selected>Tidak</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                          <label for="status" class="form-label">Pilih Direksi</label>
                          <select name="nama_direksi" id="nama_direksi" class="form-control" disabled>
                            <option value=""> -- Pilih Direksi -- </option>
                            <?php
                            $direksi = $this->db->get_where('users', ['level_jabatan > ' => 4])->result_array();
                            foreach ($direksi as $d) {
                            ?>
                              <option value="<?= $d['nip'] ?>"><?= $d['nama'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="form-group">
                          <label for="catatan" class="form-label">Catatan</label>
                          <textarea name="catatan" id="catatan" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                    <div>
                      <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </form>
          <?php } ?>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->