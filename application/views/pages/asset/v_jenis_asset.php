<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Daftar Jenis Aset</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#tambahjenis">Tambah Jenis</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('asset/jenis_aset') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Cari nama jenis" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('asset/jenis_aset') ?>" class="btn btn-warning">Reset</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <!-- <div style="min-width: 951px;"> -->
            <table class="table table-sm table-hover" style="display: table;">
              <thead style="background-color:#3498db;">
                <tr>
                  <th style="color: white;">No</th>
                  <th style="color: white;">Nama Jenis</th>
                  <th style="color: white;">#</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (empty($data_jenis)) { ?>
                  <tr>
                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
                  </tr>
                  <?php } else {
                  $nip = $this->session->userdata('nip');
                  foreach ($data_jenis as $data) {
                  ?>
                    <tr>
                      <td><?= ++$page; ?></td>
                      <td><?= $data->nama_jenis ?></td>
                      <td>
                        <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ubahjenis<?= $data->Id ?>"><i class="fe fe-edit"></i> Ubah</a>
                        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="ubahjenis<?= $data->Id ?>">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">
                                  Ubah Jenis Aset
                                </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                                </button>
                              </div>
                              <form class="form-horizontal form-label-left" method="POST" action="<?= site_url('asset/store_jenis') ?>">
                                <input type="hidden" name="id_jenis" value="<?= $data->Id ?>">
                                <div class="modal-body">
                                  <div class="form-group row">
                                    <div class="col-12 mt-3">
                                      <label for="jenis_asset" class="form-label">Nama Jenis</label>
                                      <input type="text" name="jenis_asset" id="jenis_asset" class="form-control" value="<?= $data->nama_jenis ?>">
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary btn-submit">
                                    Simpan
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
            <!-- </div> -->
          </div>


          <!-- Pagination -->
          <nav aria-label="Table Paging" class="mb-0">
            <?= $pagination ?>
          </nav>

        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<!-- Modal tambah jenis aset -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="tambahjenis">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Tambah Jenis Aset
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('asset/store_jenis') ?>">
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-12 mt-3">
              <label for="jenis_asset" class="form-label">Nama Jenis</label>
              <input type="text" name="jenis_asset" id="jenis_asset" class="form-control uppercase" placeholder="Masukkan nama jenis aset...">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-submit">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>