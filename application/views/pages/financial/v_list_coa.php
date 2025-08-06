<style>
  .dt-length label {
    margin-left: 8px;
    /* Adjust this value (e.g., 5px, 10px, 0.5em) as needed */
  }

  .balance-info {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
  }

  .balance-info.balanced {
    background-color: #dcfce7;
    color: #16a34a;
  }

  .balance-info.unbalanced {
    background-color: #fee2e2;
    color: #dc2626;
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">List Coa </h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header">
          <p class="card-title"><strong>List Coa</strong></p>
        </div> -->
        <div class="card-body">
          <?php if ($this->session->flashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> <?= $this->session->flashdata('error'); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <!-- <strong><?= $this->session->flashdata('error'); ?>!</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"> -->
                <span aria-hidden="true">x</span>
              </button>
            </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success!</strong> <?= $this->session->flashdata('success'); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">x</span>
              </button>
            </div>
          <?php endif; ?> <div class="row">
            <div class="col-md-12 col-xs-12 form-group pull-right top_search">
              <form class="form-horizontal form-label-left" method="post" action="<?= base_url('financial/list_coa') ?>">
                <div class="input-group">
                  <select class="form-control" name="cabang_select" id="cabang_select">
                    <!-- <option value="1">Tes</option> -->
                    <?php
                    foreach ($cabang as $c) {
                    ?>
                      <option <?= $cabang_now == $c->uid ? 'selected' : '' ?> value="<?= $c->uid ?>">Cabang : <?= $c->nama_cabang ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  <input type="text" class="form-control" name="keyword" placeholder="Search for..." value="<?= $keyword ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit">Go!</button>
                    <a href="<?= base_url('financial/reset_coa') ?>" class="btn btn-warning" style="color:white;">Reset</a>
                    <button class="btn btn-primary text-white" data-toggle="modal" data-target="#tambahCoa" type="button" style="color: white;">Buat CoA</button>
                    <button class="btn btn-primary text-white" data-toggle="modal" data-target="#TemplateCoa" type="button" style="color: white;">Ambil CoA</button>
                    <?php
                    if ($is_sawal == 0 && $cabang_now == $this->session->userdata('kode_cabang')) {
                    ?>
                      <button class="btn btn-primary text-white" data-toggle="modal" data-target="#saldoAwal" type="button" style="color: white;">Buat Saldo Awal</button>
                    <?php
                    }
                    ?>
                  </span>
                </div>
              </form>
            </div>
          </div>
          <div class="table-responsive">
            <table id="" class="table table-sm" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>No.</th>
                  <th>BB</th>
                  <th>Sub BB</th>
                  <th>Nama Perkiraan</th>
                  <!-- <th class="text-center">Nominal</th> -->
                  <th class="text-center">Saldo Awal</th>
                  <?php
                  if ($is_sawal == 0 && $cabang_now == $this->session->userdata('kode_cabang')) {
                  ?>
                    <th class="text-center">Aksi</th>
                  <?php
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($coa) {
                  $no = ($this->uri->segment(3)) ? ((($this->uri->segment(3) - 1) * 10) + 1) : '1';

                  foreach ($coa as $i) : ?>
                    <tr style="height: 35px;">
                      <td><?= $no++ ?>.</td>
                      <td><?= $i['no_bb'] ?></td>
                      <td><?= $i['no_sbb'] ?></td>
                      <td><?= ($i['nama_perkiraan']) ?></td>
                      <td class="text-right"><?= $i['nominal'] != null ? number_format($i['nominal']) : 0 ?></td>
                      <?php
                      if ($is_sawal == 0 && $cabang_now == $this->session->userdata('kode_cabang')) {
                      ?>
                        <td class="text-center"><button class="btn btn-sm btn-warning text-white" onclick="onEdit(<?= $i['no_sbb'] ?>, <?= $i['id_cabang'] ?>)" type="button">Update</button></td>
                      <?php
                      }
                      ?>
                    </tr>

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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Buat CoA Baru
        </h4>
      </div>
      <form class="form-horizontal form-label-left" method="POST" action="<?= base_url('financial/tambahCoa') ?>">
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
          <!-- <hr>
          <h6>Template</h6>
          <div class="table-responsive">
            <table id="table-template" class="table table-striped" style="width:100%">
              <thead class="thead-dark">
                <tr>
                  <th>No.</th>
                  <th>BB</th>
                  <th>Sub BB</th>
                  <th>Nama Perkiraan</th>
                  <th class="text-center">Nominal</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
            </table>
          </div> -->
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
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="TemplateCoa">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
          Ambil CoA Baru dari Template
        </h4>
      </div>
      <div class="modal-body">
        <!-- <div class="float-right">
          <a href="<?= base_url('financial/ambil_semua_coa') ?>" class="btn btn-primary">Ambil Semua</a>
        </div> -->
        <div class="table-responsive">
          <table id="table-template" class="table table-striped" style="width:100%">
            <thead class="thead-dark">
              <tr>
                <th>No.</th>
                <th>BB</th>
                <th>Sub BB</th>
                <th>Nama Perkiraan</th>
                <!-- <th class="text-center">Nominal</th> -->
                <th class="text-center">Saldo Awal</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="submit" class="btn btn-primary">
          Tambah CoA
        </button> -->
      </div>
    </div>
  </div>
</div>
<!-- Update COA Modal -->
<div class="modal fade" id="updateCoaModal" tabindex="-1" aria-labelledby="updateCoaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateCoaModalLabel">Update COA Entry</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
      </div>
      <form id="updateCoaForm" action="<?php echo site_url('financial/update_coa'); ?>" method="POST">
        <div class="modal-body">
          <!-- Hidden input for the COA ID (assuming 'id_coa' is your primary key) -->
          <input type="hidden" id="update_table_coa" name="table_coa">
          <input type="hidden" id="update_id_coa" name="id_coa">

          <div class="mb-3">
            <label for="update_no_bb" class="form-label">No. BB</label>
            <input type="text" class="form-control" id="update_no_bb" name="no_bb" readonly>
          </div>
          <div class="mb-3">
            <label for="update_no_sbb" class="form-label">No. SBB</label>
            <input type="text" class="form-control" id="update_no_sbb" name="no_sbb" readonly>
          </div>
          <div class="mb-3">
            <label for="update_nama_perkiraan" class="form-label">Nama Perkiraan</label>
            <input type="text" class="form-control" id="update_nama_perkiraan" name="nama_perkiraan" required>
          </div>
          <div class="mb-3">
            <label for="update_nominal" class="form-label">Saldo Awal</label>
            <input type="text" class="form-control" id="update_nominal" name="nominal" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Update COA Modal -->
<div class="modal fade" id="saldoAwal" tabindex="-1" aria-labelledby="updateCoaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateCoaModalLabel">Buat Saldo Awal COA</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
      </div>
      <form id="saldoawalForm" action="<?php echo site_url('financial/buat_saldo_awal'); ?>" method="POST">
        <div class="modal-body">
          <div class="row">
            <div id="balanceResult" class="col-12 balance-info hidden mb-2"></div>
            <div class="col-lg-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary mb-3">Save changes</button>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6 col-md-6 col-xs-12">
              <h2 class="text-center">Activa</h2>
              <p class="text-right">Total: <strong id="total_aktiva"><?= (isset($sum_activa)) ? number_format($sum_activa, 2) : 0 ?></strong></p>
              <div class="table-responsive">
                <table class="table table-sm" style="width:100%">
                  <thead class="thead-dark">
                    <tr style="height: 50px;">
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (isset($activa)) : ?>
                      <?php foreach ($activa as $a) : ?>
                        <tr style="height: 35px;">
                          <td>
                            <button class="btn btn-primary arus_kas btn-sm" type="button" data-id="<?= $a->no_sbb ?>">
                              <?= $a->no_sbb ?>
                            </button>
                          </td>
                          <td><?= $a->nama_perkiraan ?></td>
                          <td class="text-right"><?= number_format($a->nominal, 2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <tr>
                        <td colspan="3">Tidak ada activa yang ditampilkan</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-lg-6 col-md-6 col-xs-12">
              <h2 class="text-center">Pasiva</h2>
              <p class="text-right">Total: <strong id="total_pasiva"><?= (isset($sum_pasiva)) ? number_format($sum_pasiva, 2) : 0 ?></strong></p>
              <div class="table-responsive">
                <table id="" class="table table-sm" style="width:100%">
                  <thead class="thead-dark">
                    <tr style="height: 50px;">
                      <th>No. Coa</th>
                      <th>Nama Coa</th>
                      <th>Nominal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (isset($pasiva)) : ?>
                      <?php foreach ($pasiva as $a) : ?>
                        <tr style="height: 35px;">
                          <td>
                            <button class="btn btn-primary arus_kas btn-sm" type="button" data-id="<?= $a->no_sbb ?>">
                              <?= $a->no_sbb ?>
                            </button>
                          </td>
                          <td><?= $a->nama_perkiraan ?></td>
                          <td class="text-right"><?= number_format($a->nominal, 2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                      <tr style="height: 35px;">
                        <td>31030</td>
                        <td>LABA TAHUN BERJALAN</td>
                        <td class="text-right"><?= number_format($laba, 2) ?></td>
                      </tr>
                    <?php else : ?>
                      <tr>
                        <td colspan="3">Tidak ada pasiva yang ditampilkan</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <div id="balanceResult2" class="col-12 balance-info hidden mb-2"></div>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>