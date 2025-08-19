<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Daftar Penyusutan</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="<?= site_url('asset/proses_penyusutan') ?>" class="btn btn-primary btn-penyusutan">Proses Penyusutan</a>
          <a href="<?= site_url('asset/pengecualian_penyusutan') ?>" class="btn btn-danger">Pengecualian Penyusutan</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('asset/list_penyusutan') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="month" class="form-control" placeholder="Cari aset" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('asset/list_penyusutan') ?>" class="btn btn-warning">Reset</a>
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
                    <th style="color: white;">Periode</th>
                    <th style="color: white;">#</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (empty($penyusutan)) { ?>
                    <tr>
                      <td colspan="3" class="text-center">Data tidak ditemukan</td>
                    </tr>
                    <?php } else {
                    foreach ($penyusutan as $data) {
                    ?>
                      <tr>
                        <td><?= ++$page; ?></td>
                        <td><?= $data->periode ?></td>
                        <td>
                          <a href="<?= site_url('asset/detail_penyusutan/') . $data->Id ?>" class="btn btn-success btn-sm">Detail</a>
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