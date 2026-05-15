<style>
  @media (max-width: 767px) {
    .text-kuota {
      /* display: none !important; */
    }
  }

  .gauge-div {
    /* Default alignment for mobile */
    justify-content: center;
    display: flex;
  }

  @media (min-width: 768px) {
    .gauge-div {
      /* Alignment for desktops */
      justify-content: flex-end;
    }
  }

  .gauge-text-div {
    /* Default alignment for mobile */
    text-align: center;
  }

  @media (min-width: 768px) {
    .gauge-text-div {
      /* Alignment for desktops */
      text-align: left;
    }
  }
</style>
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="row align-items-center mb-2">
        <div class="col">
          <!-- <h2 class="h3 page-title"><?= ($this->session->userdata('is_premium') == '1') ? 'Premium' : '' ?></h2> -->
          <h2 class="h5 page-title">Selamat Datang! <span class="text-pink"> <?= $this->session->userdata('nama') ?></span></h2>
        </div>
        <!-- <div class="col-auto">
          <form class="form-inline">
            <div class="form-group d-none d-lg-inline">
              <label for="reportrange" class="sr-only">Date Ranges</label>
              <div id="reportrange" class="px-2 py-2 text-muted">
                <span class="small"></span>
              </div>
            </div>
            <div class="form-group">
              <button type="button" class="btn btn-sm"><span class="fe fe-refresh-ccw fe-16 text-muted"></span></button>
              <button type="button" class="btn btn-sm mr-2"><span class="fe fe-filter fe-16 text-muted"></span></button>
            </div>
          </form>
        </div> -->
      </div>
      <div class="mb-2 align-items-center">
        <div class="row mt-1 align-items-center">
          <div class="col-12 text-left pl-4">
            <h5 class="mb-1 text-primary">Kuota Penggunaan</h5>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-4 col-6 mb-4">
            <div class="card shadow">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-6 col-12 gauge-text-div">
                    <p class="small mb-0 text-primary"><b>Invoice</b></p>
                    <h5 class="mb-0 text-pink text-kuota"><?= $total_invoice ?>/<?= $perusahaan->kuota_invoice ?></h5>
                  </div>
                  <div class="col-md-6 gauge-div">
                    <div id="gauge1" class="gauge-container"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-4">
            <div class="card shadow">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-6 col-12 gauge-text-div">
                    <p class="small mb-0 text-primary"><b>Memo</b></p>
                    <h5 class="mb-0 text-pink text-kuota"><?= $total_memo ?>/<?= $perusahaan->kuota_memo ?></h5>
                  </div>
                  <div class="col-md-6 gauge-div">
                    <div id="gauge2" class="gauge-container"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-4">
            <div class="card shadow">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-6 col-12 gauge-text-div">
                    <p class="small mb-0 text-primary"><b>Submission</b></p>
                    <h5 class="mb-0 text-pink text-kuota"><?= $total_pengajuan ?>/<?= $perusahaan->kuota_pengajuan_biaya ?></h5>
                  </div>
                  <div class="col-md-6 gauge-div">
                    <div id="gauge3" class="gauge-container"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-4">
            <div class="card shadow">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-6 col-12 gauge-text-div">
                    <p class="small mb-0 text-primary"><b>User</b></p>
                    <h5 class="mb-0 text-pink text-kuota"><?= $total_user ?>/<?= $this->session->userdata('is_premium') == 1 ? $perusahaan->kuota_user : 1 ?></h5>
                  </div>
                  <div class="col-md-6 gauge-div">
                    <div id="gauge4" class="gauge-container"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6 mb-4">
            <div class="card shadow">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-6 col-12 gauge-text-div">
                    <p class="small mb-0 text-primary"><b>Cabang</b></p>
                    <h5 class="mb-0 text-pink text-kuota"><?= $total_cabang ?>/<?= $perusahaan->kuota_cabang ?></h5>
                  </div>
                  <div class="col-md-6 gauge-div">
                    <div id="gauge5" class="gauge-container"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if ($this->session->userdata('is_premium')) {
          ?>
            <div class="col-md-4 col-6 mb-4">
              <div class="card shadow">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-6 col-12 gauge-text-div">
                      <p class="small mb-0 text-primary"><b>Expired</b></p>
                      <h6 class="mb-0 text-pink text-kuota" id="premiumStatusText"></h6> <!-- Displays "Expires on: Date" or "Expired!" -->
                      <span class="small text-mute" id="premiumDaysRemainingText"></span> <!-- Displays "X days remaining" -->
                    </div>
                    <div class="col-md-6 gauge-div">
                      <div id="premiumGauge" class="gauge-container"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
          <?php
          if ($hasFinancialMenu) {
          ?>
            <div class="col-md-4 col-6 mb-4">
              <div class="card shadow">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-6 col-12 gauge-text-div">
                      <p class="small mb-0 text-primary"><b>Invoice Closed</b></p>
                      <h5 class="mb-0 text-pink text-kuota"><?= $total_invoice ?>/<?= $total_invoice_closed ?></h5>
                      <span class="small text-mute" id="invoiceOpenText"></span> <!-- Displays "X days remaining" -->
                    </div>
                    <div class="col-md-6 gauge-div">
                      <div id="gauge7" class="gauge-container"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-6 mb-4">
              <div class="card shadow">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-6 col-12 gauge-text-div">
                      <p class="small mb-0 text-primary"><b>Nota Closed</b></p>
                      <h5 class="mb-0 text-pink text-kuota"><?= $total_nota ?>/<?= $total_nota_closed ?></h5>
                      <span class="small text-mute" id="notaOpenText"></span> <!-- Displays "X days remaining" -->
                    </div>
                    <div class="col-md-6 gauge-div">
                      <div id="gauge8" class="gauge-container"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
        <!-- <div class="row g-3 justify-content-center">
          <div class="col-6 col-lg-4 ">
            <div class="card shadow text-center p-3 h-100 bg-pink">
              <label class="text-white font-weight-bold">Kuota Invoice</label>
              <p class="h4 mt-2 text-white"><?= $total_invoice ?>/<?= $perusahaan->kuota_invoice ?></p>
            </div>
          </div>
          <div class="col-6 col-lg-4">
            <div class="card shadow text-center p-3 h-100" style="background-color: #c01a52;">
              <label class="text-white font-weight-bold">Kuota Memo</label>
              <p class="h4 mt-2 text-white"><?= $total_memo ?>/<?= $perusahaan->kuota_memo ?></p>
            </div>
          </div>
          <div class="col-6 col-lg-4">
            <div class="card shadow text-center p-3 h-100" style="background-color: #9a1442;">
              <label class="text-white font-weight-bold">Kuota Pengajuan Biaya</label>
              <p class="h4 mt-2 text-white"><?= $total_pengajuan ?>/<?= $perusahaan->kuota_pengajuan_biaya ?></p>
            </div>
          </div>
          <div class="col-6 col-lg-4 ">
            <div class="card shadow text-center p-3 h-100" style="background-color: #6272c7;">
              <label class="text-white font-weight-bold">Kuota User</label>
              <p class="h4 mt-2 text-white"><?= $total_user ?>/<?= $perusahaan->kuota_user ?></p>
            </div>
          </div>
          <div class="col-6 col-lg-4 ">
            <div class="card shadow text-center p-3 h-100 bg-primary">
              <label class="text-white font-weight-bold">Kuota Cabang</label>
              <p class="h4 mt-2 text-white"><?= $total_cabang ?>/<?= $perusahaan->kuota_cabang ?></p>
            </div>
          </div>
          <div class="col-6 col-lg-4 ">
            <div class="card shadow text-center p-3 h-100" style="background-color: #24326f;">
              <label class="text-white font-weight-bold">Premium Expired</label>
              <p class="h4 mt-2 text-white"><?= tgl_indo(date('Y-m-d', strtotime($perusahaan->expired_day))) ?></p>
            </div>
          </div>
        </div> -->
        <?php
        if ($hasFinancialMenu) {
        ?>

          <div class="mb-2 align-items-center mt-2">
            <div class="card shadow mb-4">
              <div class="card-body">
                <div class="row mt-1 align-items-center">
                  <div class="col-12 text-left pl-4">
                    <p class="mb-1 small text-muted">Performance 6 bulan terakhir</p>
                  </div>
                </div>
                <div class="chartbox mr-4">
                  <div id="areaChart"></div>
                </div>
              </div>
            </div>
          <?php
        }
          ?>
          </div>
      </div> <!-- .col-12 -->
    </div> <!-- .row -->
  </div> <!-- .container-fluid -->

  <div class="modal fade modal-shortcut modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="defaultModalLabel">Shortcuts</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body px-5">
          <div class="row align-items-center">
            <div class="col-6 text-center">
              <div class="squircle bg-success justify-content-center">
                <i class="fe fe-cpu fe-32 align-self-center text-white"></i>
              </div>
              <p>Control area</p>
            </div>
            <div class="col-6 text-center">
              <div class="squircle bg-primary justify-content-center">
                <i class="fe fe-activity fe-32 align-self-center text-white"></i>
              </div>
              <p>Activity</p>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-6 text-center">
              <div class="squircle bg-primary justify-content-center">
                <i class="fe fe-droplet fe-32 align-self-center text-white"></i>
              </div>
              <p>Droplet</p>
            </div>
            <div class="col-6 text-center">
              <div class="squircle bg-primary justify-content-center">
                <i class="fe fe-upload-cloud fe-32 align-self-center text-white"></i>
              </div>
              <p>Upload</p>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-6 text-center">
              <div class="squircle bg-primary justify-content-center">
                <i class="fe fe-users fe-32 align-self-center text-white"></i>
              </div>
              <p>Users</p>
            </div>
            <div class="col-6 text-center">
              <div class="squircle bg-primary justify-content-center">
                <i class="fe fe-settings fe-32 align-self-center text-white"></i>
              </div>
              <p>Settings</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>