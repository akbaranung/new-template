<style>
  .open-memo {
    cursor: pointer;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">Pengajuan Biaya</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <strong class="card-title">List Approval Keuangan</strong>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
              <div class="row">
                <div class="col-md-4 mb-4">
                  <div class="card shadow bg-warning text-white">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col">
                          <span class="h2 mb-0 text-white"><?= $belum_proses_keuangan ?></span>
                          <p class="small mb-0">Belum Diproses</p>
                        </div>
                        <div class="col-auto">
                          <span class="fe fe-32 fe-clock mb-0"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="card shadow bg-secondary text-white">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col">
                          <span class="h2 mb-0 text-white"><?= $belum_bayar ?></span>
                          <p class="small mb-0">Belum Bayar</p>
                        </div>
                        <div class="col-auto">
                          <span class="fe fe-32 fe-dollar-sign mb-0"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="card shadow bg-danger text-white">
                    <div class="card-body">
                      <div class="row align-items-center">
                        <div class="col">
                          <span class="h2 mb-0 text-white"><?= $belum_close ?></span>
                          <p class="small mb-0">Belum Close</p>
                        </div>
                        <div class="col-auto">
                          <span class="fe fe-32 fe-x-square mb-0"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <form action="<?= site_url('pengajuan/approval_keuangan') ?>" method="get">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Cari no pengajuan" name="search" id="search" value="<?= $this->input->get('search') ?>">
                  <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">
                      Cari
                    </button>
                    <a href="<?= site_url('pengajuan/approval_keuangan') ?>" class="btn btn-warning">Tampilkan Semua</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <table class="table table-hover table-sm">
            <thead style="background-color:#3498db;">
              <tr>
                <th style="color: white;">No</th>
                <th style="color: white;">User</th>
                <th style="color: white;">Rekening</th>
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
                    <td><?= $data->nama ?></td>
                    <td><?= $data->no_rekening ?></td>
                    <td><?= date('d/m/y', strtotime($data->tanggal)) ?></td>
                    <td><?= rupiah($data->total); ?></td>
                    <td><?= $data->posisi; ?></td>
                    <td>
                      <a href="<?= site_url('pengajuan/detail/') . $data->kode . '/finance' ?>" class="btn btn-warning btn-sm"><i class="fe fe-eye fe-12"></i> Detail</a>
                      <?php if ($data->status == 3 and $data->posisi == 'Diarahkan ke pembayaran') { ?>
                        <a href="<?= site_url('pengajuan/bayar/') . $data->kode ?>" class="btn btn-success btn-sm"><i class="fe fe-dollar-sign fe-12"></i> Bayar</a>
                      <?php } ?>
                      <?php if ($data->status == 4 and $data->posisi == 'Sudah dibayar') { ?>
                        <a href="<?= site_url('pengajuan/close/') . $data->kode ?>" class="btn btn-success btn-sm"><i class="fe fe-x-square fe-12"></i> Close</a>
                      <?php } ?>
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