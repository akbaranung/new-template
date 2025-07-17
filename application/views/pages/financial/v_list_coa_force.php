<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Financial First</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title"><strong>Buat Coa</strong></p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 col-xs-12 form-group pull-right top_search">
              <form class="form-horizontal form-label-left" method="post" action="<?= base_url('financial_first/force_make_coa_sbb') ?>">
                <div class="input-group">
                  <input type="text" class="form-control" name="keyword" placeholder="Search for..." value="<?= $keyword ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit">Go!</button>
                    <a href="<?= base_url('financial_first/reset_coa') ?>" class="btn btn-warning" style="color:white;">Reset</a>
                    <button class="btn btn-primary text-white" data-toggle="modal" data-target="#tambahCoa" type="button" style="color: white;">Tambah CoA</button>
                  </span>
                </div>
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <table id="" class="table table-striped" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>No.</th>
                  <th>BB</th>
                  <th>Sub BB</th>
                  <th>Nama Perkiraan</th>
                  <th class="text-center">Saldo Awal</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($coa) {
                  $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * 10) + 1) : '1';

                  foreach ($coa as $i) : ?>
                    <form action="<?= base_url('financial_first/tambahCoa') ?>" method="post">
                      <tr>
                        <td><?= $no++ ?>.</td>
                        <td><input type="hidden" name="no_bb" value="<?= $i['no_bb'] ?>"><?= $i['no_bb'] ?></td>
                        <td><input type="hidden" name="no_sbb" value="<?= $i['no_sbb'] ?>"><?= $i['no_sbb'] ?></td>
                        <td><input type="hidden" name="nama_coa" value="<?= $i['nama_perkiraan'] ?>"><?= ($i['nama_perkiraan']) ?></td>
                        <td><input type="text" name="saldo_awal" id="saldo_awal" class="form-control uang" value="0"></td>
                        <!-- <td class="text-right"><?= $i['nominal'] != null ? number_format($i['nominal']) : 0 ?></td> -->
                        <td class="text-right"><button class="btn btn-primary" type="submit">Buat</button></td>
                      </tr>
                    </form>
                  <?php
                  endforeach;
                } else {
                  ?>
                  <tr>
                    <td colspan="5">Tidak ada data yang ditampilkan</td>
                  </tr>
                <?php
                } ?>
              </tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-md-12 col-xs-12">
              <!-- <h6>*klik kode untuk lihat neraca tersimpan</h6> -->
            </div>
            <div class="col-md-12 col-xs-12 text-right">
              <?= $this->pagination->create_links() ?>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="tambahCoa">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Tambah CoA Baru
        </h4>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial_first/tambahCoa') ?>">
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12">
              <label for="no_bb" class="form-label">No. BB</label>
              <input type="text" name="no_bb" id="no_bb" class="form-control">
            </div>
            <div class="col-12 mt-3">
              <label for="no_sbb" class="form-label">No. SBB</label>
              <input type="text" name="no_sbb" id="no_sbb" class="form-control">
            </div>
            <div class="col-12 mt-3">
              <label for="nama_coa" class="form-label">Nama CoA</label>
              <input type="text" name="nama_coa" id="nama_coa" class="form-control" oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="col-12 mt-3">
              <label for="saldo_awal" class="form-label">Saldo Awal</label>
              <input type="text" name="saldo_awal" id="saldo_awal" class="form-control uang" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">
            Tambah CoA
          </button>
        </div>
      </form>
    </div>
  </div>
</div>