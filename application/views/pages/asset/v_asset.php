<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Daftar Aset</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="<?= site_url('asset/tambah') ?>" class="btn btn-primary">Tambah Aset</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('asset') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Cari aset" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('asset') ?>" class="btn btn-warning">Reset</a>
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
                  <th style="color: white;">Kode</th>
                  <th style="color: white;">Nama</th>
                  <th style="color: white;">Spesifikasi</th>
                  <th style="color: white;">Ruang</th>
                  <th style="color: white;">Lokasi</th>
                  <th style="color: white;">Jenis</th>
                  <th style="color: white;">Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (empty($data_asset)) { ?>
                  <tr>
                    <td colspan="8" class="text-center">Data tidak ditemukan</td>
                  </tr>
                  <?php } else {
                  foreach ($data_asset as $data) {
                    $lokasi = $this->cb->select('nama_cabang')->from('t_cabang')->where('uid', $data->lokasi)->get()->row();
                  ?>
                    <tr>
                      <td><?= ++$page; ?></td>
                      <td><?= $data->kode ?></td>
                      <td><?= $data->nama_asset ?></td>
                      <td><?= $data->spesifikasi ?></td>
                      <td><?= $data->nama_ruangan ?></td>
                      <td><?= $lokasi->nama_cabang ?></td>
                      <td><?= $data->nama_jenis ?></td>
                      <td>
                        <a href="<?= site_url('asset/detail/') . $data->Id ?>" class="btn btn-sm btn-primary"><i class="fe fe-eye fe-12"></i> Detail</a>
                        <a href="#" class="btn btn-sm" data-toggle="modal" data-target="#hapusBuku<?= $data->Id ?>" style="background-color: #e91e63;color:white;"><i class="fe fe-trash fe-12"></i> Hapus Buku</a>
                        <a href="#" class="btn btn-sm" data-toggle="modal" data-target="#hapusAset<?= $data->Id ?>" style="background-color: #34495e;color:white;"><i class="fe fe-trash fe-12"></i> Hapus Aset</a>
                        <!-- Modal Hapus Buku -->
                        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="hapusBuku<?= $data->Id ?>">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">
                                  Hapus Buku
                                </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                                </button>
                              </div>
                              <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('asset/hapus_buku/') . $data->Id ?>">
                                <div class="modal-body">
                                  <p><strong>Masukan keterangan atau asalan hapus buku dan password anda terlebih dahulu untuk memproses hapus buku!</strong></p>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Kode Aset</label>
                                      <input type="text" name="kode" id="kode" class="form-control" value="<?= $data->kode ?>" readonly>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Nama Aset</label>
                                      <input type="text" name="nama" id="nama" class="form-control" value="<?= $data->nama_asset ?>" readonly>
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Keterangan</label>
                                      <input type="text" name="keterangan" id="keterangan" class="form-control">
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Password</label>
                                      <input type="password" name="password" id="password" class="form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary btn-submit">
                                    Proses
                                  </button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <!-- Modal Hapus asset -->
                        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="hapusAset<?= $data->Id ?>">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">
                                  Hapus Aset
                                </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">×</span>
                                </button>
                              </div>
                              <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('asset/delete_asset/') . $data->Id ?>">
                                <div class="modal-body">
                                  <p><strong>Masukan password anda terlebih dahulu untuk memproses hapus asset!</strong></p>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Kode Aset</label>
                                      <input type="text" name="kode" id="kode" class="form-control" value="<?= $data->kode ?>" readonly>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Nama Aset</label>
                                      <input type="text" name="nama" id="nama" class="form-control" value="<?= $data->nama_asset ?>" readonly>
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-12">
                                      <label for="form-label">Password</label>
                                      <input type="password" name="password" id="password" class="form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary btn-submit">
                                    Proses
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