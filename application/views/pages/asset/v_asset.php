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
            <div style="min-width: 951px;">
              <table class="table table-sm table-hover" style="width: 100% !important;">
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

                          <a href="<?= site_url('asset/detail/') . $data->Id ?>" class="btn btn-sm" style="background-color: #3498db; color:white;"><i class="fe fe-eye fe-12"></i> Detail</a>
                        </td>
                      </tr>
                  <?php }
                  } ?>
                </tbody>
              </table>
            </div>
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