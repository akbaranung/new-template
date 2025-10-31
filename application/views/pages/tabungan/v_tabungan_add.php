<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
  .bagian-th {
    /* Default alignment for mobile */
    display: none;
  }

  @media (min-width: 768px) {
    .bagian-th {
      /* Alignment for desktops */
      display: block;
    }
  }

  /* .bagian-td { */
  /* Default alignment for mobile */
  /* margin-left: -120px; */
  /* } */

  @media (min-width: 768px) {
    .bagian-td {
      /* Alignment for desktops */
      margin-left: 0px;
    }
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
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
      <?php endif; ?>
      <!-- <h1 class="page-title">User <?= ($this->uri->segment(2) == 'add_nasabah') ? 'Add' : 'Edit' ?></h1> -->
      <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title mb-0"><strong><?= ($this->uri->segment(2) == 'add_nasabah') ? 'Tambah' : 'Ubah' ?> Pasukan</strong></p>
          <!-- <a href="<?= base_url('perusahaan/add_nasabah') ?>" class="btn btn-primary">Add User</a> -->
        </div>
        <div class="card-body" id="user">
          <?php
          // Retrieve the flash data array. Use the '?? []' structure to safely handle cases where no errors exist.
          $errors = $this->session->flashdata('form_errors') ?? [];

          if (!empty($errors)) {
            // Start an alert container (adjust classes for your CSS/framework, e.g., Bootstrap)
            echo '<div class="alert alert-danger" role="alert">';
            echo '<strong>Terjadi Kesalahan Input:</strong> Harap periksa kembali kolom berikut:';
            echo '<ul>';

            // Loop through the associative array (key is the field name, value is the error message)
            foreach ($errors as $field => $message) {
              // Display only the error message in the list
              echo '<li>' . htmlspecialchars($message) . '</li>';
            }

            echo '</ul>';
            echo '</div>';
          }
          ?>
          <font style="font-size:14px;">
            <?php if ($this->uri->segment(2) == 'add_tabungan') { ?> <!-- add user -->
              <?= $this->session->flashdata('msg') ?>
              <form action="<?= base_url('tabungan/proccess_add') ?>" method="post" class="row">
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">No Tabungan</label>
                  <input type="text" class="form-control" name="no_tabungan" id="no_tabungan_add" value="<?= set_value('no_tabungan', $form_data['no_tabungan'] ?? $new_tabungan_number) ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputPassword1">Nasbah</label>
                  <select class="form-control" name="no_cib" id="no_cib_add">
                    <option selected disabled>Pilih Nasabah</option>
                    <?php
                    foreach ($nasabah as $n) {
                    ?>
                      <option value="<?= $n->no_cib ?>"><?= $n->nama ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Jenis Tabungan</label>
                  <select class="form-control" name="jenis_tabungan" id="jenis_tabungan_add">
                    <option selected disabled>Pilih Jenis Tabungan</option>
                    <?php
                    foreach ($jenis_tabungan as $jt) {
                    ?>
                      <option value="<?= $jt->kode_tabungan ?>"><?= $jt->nama_tabungan ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Status Tabungan</label>
                  <select class="form-control" name="status_tabungan" id="status_tabungan_add">
                    <option selected value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                  </select>
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">No Urut</label>
                  <!-- <input type="text" class="form-control" name="no_urut" id="no_urut_add" value="<?= set_value('no_urut', $form_data['no_urut'] ?? $new_no_urut) ?>"> -->
                  <input type="text" class="form-control" name="no_urut" id="no_urut_add" value="<?= set_value('no_urut', $form_data['no_urut'] ?? $new_tabungan_number) ?>">

                </div>
                <!-- <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nominal</label>
                        <input type="number" class="form-control" name="nominal" id="nominal_add" value="<?= set_value('nominal', $form_data['nominal'] ?? 0) ?>">

                    </div> -->
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Spread Rate</label>
                  <input type="number" class="form-control" name="spread_rate" id="spread_rate_add" value="<?= set_value('spread_rate', $form_data['spread_rate'] ?? '') ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Nominal Blokir</label>
                  <input type="number" class="form-control" name="nominal_blokir" id="nominal_blokir_add" value="<?= set_value('nominal_blokir', $form_data['nominal_blokir'] ?? '') ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Pos Rate</label>
                  <input type="number" class="form-control" name="pos_rate" id="pos_rate_add" value="<?= set_value('pos_rate', $form_data['pos_rate'] ?? '') ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">No LSP</label>
                  <input type="number" class="form-control" name="nolsp" id="nolsp_add" value="<?= set_value('no_lsp', $form_data['no_lsp'] ?? '') ?>">

                </div>
                <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                <div class="form-group col-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            <?php  } else if ($this->uri->segment(2) == 'edit_tabungan') { ?>
              </br>
              <?= $this->session->flashdata('msg') ?>
              <form action="<?= base_url('tabungan/proccess_edit') ?>" method="post" class="row">
                <input type="hidden" class="form-control" name="no_tabungan" id="no_tabungan_add" value="<?= $tabungan->no_tabungan ?>">

                <div class="form-group col-6">
                  <label for="exampleInputPassword1">Nasbah</label>
                  <select class="form-control" name="no_cib" id="no_cib_add">
                    <option selected disabled>Pilih Nasabah</option>
                    <?php
                    foreach ($nasabah as $n) {
                    ?>
                      <option <?= $tabungan->no_cib == $n->no_cib ? 'selected' : '' ?> value="<?= $n->no_cib ?>"><?= $n->nama ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Jenis Tabungan</label>
                  <select class="form-control" name="jenis_tabungan" id="jenis_tabungan_add">
                    <option selected disabled>Pilih Jenis Tabungan</option>
                    <?php
                    foreach ($jenis_tabungan as $jt) {
                    ?>
                      <option <?= $tabungan->jenis_tabungan == $jt->kode_tabungan ? 'selected' : '' ?> value="<?= $jt->kode_tabungan ?>"><?= $jt->nama_tabungan ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Status Tabungan</label>
                  <select class="form-control" name="status_tabungan" id="status_tabungan_add">
                    <option <?= $tabungan->jenis_tabungan == "Aktif" ? 'selected' : '' ?> value="Aktif">Aktif</option>
                    <option <?= $tabungan->jenis_tabungan == "Nonaktif" ? 'selected' : '' ?> value="Nonaktif">Nonaktif</option>
                  </select>
                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">No Urut</label>
                  <input type="text" class="form-control" name="no_urut" id="no_urut_add" value="<?= $tabungan->no_urut ?>">

                </div>
                <!-- <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nominal</label>
                        <input type="text" class="form-control" name="nominal" id="nominal_add" value="<?= $tabungan->nominal ?>">

                    </div> -->
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Spread Rate</label>
                  <input type="text" class="form-control" name="spread_rate" id="spread_rate_add" value="<?= $tabungan->spread_rate ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Nominal Blokir</label>
                  <input type="number" class="form-control" name="nominal_blokir" id="nominal_blokir_add" value="<?= $tabungan->nominal_blokir ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">Pos Rate</label>
                  <input type="number" class="form-control" name="pos_rate" id="pos_rate_add" value="<?= $tabungan->pos_rate ?>">

                </div>
                <div class="form-group col-6">
                  <label for="exampleInputEmail1">No LSP</label>
                  <input type="number" class="form-control" name="nolsp" id="nolsp_add" value="<?= $tabungan->nolsp ?>">

                </div>
                <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                <div class="form-group col-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
              <br>
            <?php } ?>
          </font>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->