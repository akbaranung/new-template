<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">List Pengajuan Biaya</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <a href="<?= site_url('pengajuan/create') ?>" class="btn btn-primary">Buat Pengajuan</a>
          <!-- <strong class="card-title">List Pengajuan Biaya</strong> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('pengajuan/list') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Cari no pengajuan" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('pengajuan/list') ?>" class="btn btn-warning">Tampilkan Semua</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <table class="table table-hover table-sm">
            <thead style="background-color:#3498db;">
              <tr>
                <th style="color: white;">No</th>
                <th style="color: white;">No Rekening</th>
                <th style="color: white;">Tanggal</th>
                <th style="color: white;">Total</th>
                <th style="color: white;">Posisi</th>
                <th style="color: white;">#</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (empty($data_pengajuan)) { ?>
                <tr>
                  <td colspan="6" class="text-center">Data tidak ditemukan</td>
                </tr>
                <?php } else {
                $nip = $this->session->userdata('nip');
                foreach ($data_pengajuan as $data) {
                ?>
                  <tr>
                    <td><?= $data->kode; ?></td>
                    <td><?= $data->no_rekening ?></td>
                    <td><?= date('d/m/y', strtotime($data->tanggal)) ?></td>
                    <td><?= rupiah($data->total); ?></td>
                    <td><?= $data->posisi; ?></td>
                    <td>
                      <?php if ($data->status == 0) { ?>
                        <a href="<?= site_url('pengajuan/ubah/') . $data->kode ?>" class="btn btn-success btn-sm"><i class="fe fe-edit-3 fe-12"></i> Update</a>
                      <?php } ?>
                      <a href="<?= site_url('pengajuan/detail/') . $data->kode ?>" class="btn btn-warning btn-sm"><i class="fe fe-eye fe-12"></i> Detail</a>
                    </td>
                  </tr>
              <?php }
              } ?>
            </tbody>
          </table>

          <!-- Pagination -->
          <nav aria-label="Table Paging" class="mb-0">
            <?= $pagination ?>
          </nav>

        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->