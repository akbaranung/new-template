<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

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
      <!-- <h1 class="page-title">User <?= ($this->uri->segment(2) == 'add_user') ? 'Add' : 'Edit' ?></h1> -->
      <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title mb-0"><strong><?= ($this->uri->segment(2) == 'add_user') ? 'Tambah' : 'Ubah' ?> Pasukan</strong></p>
          <!-- <a href="<?= base_url('perusahaan/add_user') ?>" class="btn btn-primary">Add User</a> -->
        </div>
        <div class="card-body" id="user">
          <font style="font-size:14px;">
            <?php if ($this->uri->segment(2) == 'add_user') { ?> <!-- add user -->
              <?= $this->session->flashdata('msg') ?>
              <form action="<?= base_url('perusahaan/proccess_add_user') ?>" method="POST">
                <input type="hidden" value="add" name="add">
                <!-- <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id"> -->
                <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="uri1">
                <input type="hidden" value="<?= $this->uri->segment('4') ?>" name="uri2">
                <table>
                  <tr>
                    <th width="300">Username</th>
                    <td width="300"> <input type="text" value="<?php echo set_value('username'); ?>" name="username" class="form-control"></td>
                  </tr>
                  <tr>
                    <th width="300">Password</th>
                    <td width="300"> <input type="password" name="password" class="form-control"></td>
                  </tr>
                  <tr>
                    <th width="300">Password Confirmation</th>
                    <td width="300"> <input type="password" name="password_confirmation" class="form-control"></td>
                  </tr>
                  <tr>
                    <th width="200">Name</th>
                    <td> <input type="text" name="nama" class="form-control">
                    </td>
                  </tr>
                  <!-- <tr>
                    <th width="200">Date of birth</th>
                    <td>
                      <div class='input-group date' id='myDatepicker2'>
                        <input type='date' id='date_pic' name='tgl_lahir' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </td>
                  </tr> -->
                  <tr>
                    <th>Status</th>
                    <td>
                      <input name="status" value="1" type="radio" id="active" checked>
                      <label for="active">Active</label>
                      <input name="status" value="0" type="radio" id="noactive">
                      <label for="noactive">Not Active</label>
                    </td>
                  </tr>
                  <tr>
                    <th width="200">Email</th>
                    <td> <input type="email" name="email" class="form-control"></td>
                  </tr>
                  <tr>
                    <th>Phone (WhatsApp)</th>
                    <td><input type="tel" name="phone" class="form-control"></td>
                  </tr>
                  <!-- <tr>
                    <th>Code Agent</th>
                    <td><input type="text" name="kd_agent" class="form-control"></td>
                  </tr> -->
                  <!-- <tr>
                    <th>Nip</th>
                    <td><input type="number" name="nip" class="form-control"></td>
                  </tr> -->
                  <tr>
                    <!-- <th>Level Jabatan</th> -->
                    <th>User Role</th>
                    <td>
                      <select name="level_jabatan" id="" class="form-control" <?= ($this->uri->segment(3)) ? 'readonly' : '' ?>>
                        <?php
                        if ($this->uri->segment(3)) {
                        ?>
                          <option selected value="<?= $this->uri->segment(3) ?>"><?= $this->uri->segment(4) ?></option>
                          <?php
                        } else {
                          if ($this->session->userdata('is_premium')) {
                          ?>
                            <option selected disabled>Pilih Jabatan</option>
                            <option value="1">Staff</option>
                            <option value="2">Manager</option>
                            <!-- <option value="2">Supervisor</option> -->
                            <option value="3">Keuangan</option>
                            <!-- <option value="3">Manajer</option> -->
                            <!-- <option value="4">General Manajer</option> -->
                            <option value="5">Direktur</option>
                            <!-- <option value="6">Direktur Utama</option> -->
                          <?php
                          } else {
                          ?>
                            <option selected disabled>Pilih Jabatan</option>
                            <?php
                            $user_counts = isset($user_counts) ? $user_counts : [];
                            $roles = [
                              1 => 'Staff',
                              2 => 'Manager',
                              // 2 => 'Supervisor',
                              3 => 'Keuangan',
                              // 3 => 'Manajer',
                              // 4 => 'General Manajer', // This one is commented out in your example, so keep it commented
                              5 => 'Direktur',
                              // 6 => 'Direktur Utama',
                            ];
                            foreach ($roles as $value => $label) {
                              if (isset($user_counts[$value]) && $user_counts[$value] >= 1) {
                                continue;
                              }
                            ?>
                              <option value="<?= $value ?>"><?= $label ?></option>
                        <?php
                            }
                          }
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>Bagian</th>
                    <td>

                      <div class="row">
                        <div class="col-10">

                          <?php
                          if ($this->session->userdata('is_premium')) {
                          ?>
                            <select name="bagian" class="form-control" id="mySelect">
                              <?php $xx = $this->db->from('bagian')->where('id_prsh', $this->session->userdata('user_perusahaan_id'))->get()->result();
                              foreach ($xx as $k) {
                                if (!empty($user)) {
                              ?>
                                  <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                                <?php } else { ?>
                                  <option value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                              <?php }
                              } ?>
                            </select>
                          <?php
                          } else {
                          ?>
                            <select name="bagian" class="form-control" id="mySelect" readonly>
                              <?php $xx = $this->db->from('bagian')->where('id_prsh', $this->session->userdata('user_perusahaan_id'))->get()->result();
                              foreach ($xx as $k) {
                                if (!empty($user)) {
                              ?>
                                  <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                                <?php } else { ?>
                                  <option value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                              <?php }
                              } ?>
                            </select>
                          <?php
                          }
                          ?>
                        </div>
                        <div class="col-2 ">
                          <!-- <button type="button" id="addOptionBtn" class="btn btn-primary btn-block">Tambahkan Bagian <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                              <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                            </svg></button> -->
                          <button type="button" id="addOptionBtn" class="btn btn-primary btn-block"><i class="fe fe-plus"></i> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                              <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                            </svg></button>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr id="add-bagian-tr" style="display: none;">
                    <th></th>
                    <td>
                      <form id="form-add-bagian">
                        <div class="row">
                          <input type="hidden" class="form-control" id="input_id_prsh" name="input_id_prsh" value="<?= $this->session->userdata('user_perusahaan_id') ?>">
                          <!-- <div class="col-3">
                            <div class="form-group">
                              <input type="text" class="form-control" id="input_kode" name="input_kode" placeholder="Enter Kode">
                            </div>
                          </div> -->
                          <div class="col-4">
                            <div class="form-group">
                              <!-- <label for="input_nama">Nama</label> -->
                              <input type="text" class="form-control" id="input_nama" name="input_nama" placeholder="Enter Nama">
                            </div>
                          </div>
                          <div class="col-5">
                            <div class="form-group">
                              <!-- <label for="input_kode_nama">Kode Nama</label> -->
                              <input type="text" class="form-control" id="input_kode_nama" name="input_kode_nama" placeholder="Enter Kode Nama">
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group">
                              <button type="button" id="submitNewBagianBtn" class="btn btn-primary">Submit</button>
                              <button type="button" id="cancelNewBagianBtn" class="btn btn-danger">Cancel</button>
                            </div>
                          </div>
                        </div>
                      </form>
                      <div id="statusMessageBagian" class="mt-2"></div>
                    </td>
                  </tr>
                  <tr>
                    <th>Nama Jabatan</th>
                    <td><input type="text" name="nama_jabatan" class="form-control"></td>
                  </tr>
                  <tr>
                    <th>Supervisi</th>
                    <td>
                      <select name="supervisi" id="" class="form-control js-example-basic-multiple">
                        <option value="0">None</option>
                        <?php

                        foreach ($supervisi as $data) { ?>
                          <option value="<?= $data->nip ?>"><?= $data->nama_jabatan ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>TMT</th>
                    <td>
                      <div class='input-group date' id='myDatepicker2'>
                        <input type='date' name='tmt' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Cuti Reguler</th>
                    <td><input type="number" name="cuti" class="form-control"></td>
                  </tr>
                  <!-- <tr>
                    <th>
                      Lokasi Presensi
                    </th>
                    <td>
                      <select name="lokasi_presensi" class="form-control js-example-basic-multiple">
                        <option value=""> -- Pilih Lokasi Presensi --</option>
                        <?php
                        $lokasi = $this->db->get('lokasi_presensi')->result();
                        foreach ($lokasi as $data) {
                        ?>
                          <option value="<?= $data->id ?>"><?= $data->nama_lokasi ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr> -->
                  <tr>
                    <th>Cabang</th>
                    <td>
                      <select name="cabang" id="" class="form-control js-example-basic-multiple">
                        <!-- <option value="0">None</option> -->
                        <?php

                        foreach ($cabang as $data) { ?>
                          <option value="<?= $data->uid ?>"><?= $data->nama_cabang . ' - ' . $data->alamat_cabang ?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>User Menu Access</th>
                    <td width="800">
                      <div class="row">
                        <?php foreach ($all_menus_hierarchical as $parent_menu) : ?>
                          <?php if ($parent_menu->is_active == 1) :
                            // if ($parent_menu->menu_name == "Menu Admin" || "Perusahaan")
                          ?>
                            <div class="col-md-6 mb-3 mt-2">
                              <div class="form-check">
                                <input class="form-check-input parent-checkbox" type="checkbox"
                                  name="menu_ids[]"
                                  <?php if ($parent_menu->premium == 1) {
                                    echo $this->session->userdata('is_premium') == $parent_menu->premium ? '' : 'disabled';
                                  } ?>
                                  <?= ($parent_menu->premium == 1 || $parent_menu->menu_name == "Menu Admin" || $parent_menu->menu_name == "Menu Perusahaan") ? "" : "Checked" ?>

                                  value="<?= html_escape($parent_menu->Id); ?>"
                                  id="menu_<?= html_escape($parent_menu->Id); ?>">
                                <label class="form-check-label font-weight-bold" for="menu_<?= html_escape($parent_menu->Id); ?>">
                                  <i class="<?= html_escape($parent_menu->icon); ?>"></i> <?= html_escape($parent_menu->menu_name); ?>
                                  <?= $parent_menu->premium == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>' : '' ?>
                                </label>
                              </div>
                              <?php if (!empty($parent_menu->children)) : ?>
                                <div style="margin-left: 25px;">
                                  <?php foreach ($parent_menu->children as $child_menu) : ?>
                                    <?php if ($child_menu->is_active == 1) : ?>
                                      <div class="form-check">
                                        <input class="form-check-input child-checkbox" type="checkbox"
                                          name="menu_ids[]"

                                          <?php if ($parent_menu->premium == 1) {
                                            echo $this->session->userdata('is_premium') == $parent_menu->premium ? '' : 'disabled';
                                          } ?>

                                          value="<?= html_escape($child_menu->Id); ?>"
                                          id="menu_<?= html_escape($child_menu->Id); ?>"
                                          data-parent-id="<?= html_escape($parent_menu->Id); ?>"
                                          <?php
                                          if ($child_menu->menu_name == "Buat Pengajuan" || $child_menu->menu_name == "List Pengajuan") {
                                            if ($this->uri->segment(4) == "Staff") {
                                              echo "Checked";
                                            }
                                          } else if ($child_menu->menu_name == "Approval Supervisi") {
                                            if ($this->uri->segment(4) == "Manager") {
                                              echo "Checked";
                                            }
                                          } else if ($child_menu->menu_name == "Approval Keuangan") {
                                            if ($this->uri->segment(4) == "Keuangan") {
                                              echo "Checked";
                                            }
                                          } else if ($child_menu->menu_name == "Approval Direktur" || $child_menu->menu_name == "Approval Direksi") {
                                            if ($this->uri->segment(4) == "Direktur") {
                                              echo "Checked";
                                            }
                                          } else if ($parent_menu->menu_name == "Menu Admin" || $parent_menu->menu_name == "Menu Perusahaan") {
                                          } else if ($this->session->userdata('is_premium') != $parent_menu->premium) {
                                            // continue;
                                          } else {
                                            echo "Checked";
                                          }
                                          ?>>
                                        <label class="form-check-label" for="menu_<?= html_escape($child_menu->Id); ?>">
                                          <i class="<?= html_escape($child_menu->icon); ?>"></i> <?= html_escape($child_menu->menu_name); ?>
                                          <?= $parent_menu->premium == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>' : '' ?>
                                        </label>
                                      </div>
                                    <?php endif; ?>
                                  <?php endforeach; ?>
                                </div>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </div>
                    </td>
                  </tr>
                  <br>
                  <tr>
                    <th>
                      <a class="btn btn-warning" href="<?= base_url('perusahaan/user') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                    </th>
                    <td><button type="submit" class="btn btn-primary">Submit</button></td>
                  </tr>
                </table>
              </form>
            <?php  } else if ($this->uri->segment(2) == 'edit_user') { ?>
              </br>
              <?= $this->session->flashdata('msg') ?>
              <form action="<?= base_url('perusahaan/proccess_edit_user/' . $this->uri->segment('3')) ?>" method="POST">
                <input type="hidden" value="edit" name="edit">
                <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Username</label>
                      <input readonly type="text" name="username" class="form-control" value="<?= $user->username ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Name</label>
                      <input type="text" name="nama" class="form-control" value="<?= $user->nama ?>">
                    </div>
                  </div>

                  <!-- <div class="col-md-6">
                    <div class="form-group">
                      <label>Level</label>
                      <select class="form-control js-example-basic-multiple" name="level[]" multiple="multiple">
                        <?php
                        $level_x = explode(',', $user->level);
                        $x = $this->db->get('menu')->result();
                        foreach ($x as $k) {
                          // foreach($level_x as $o) {
                          if (strpos($user->level, $k->level) !== false) {
                        ?>
                            <option selected="selected" value="<?= $k->level ?>"><?= $k->nama ?>
                            </option>
                          <?php } else { ?>
                            <option value="<?= $k->level ?>"><?= $k->nama ?></option>

                        <?php }
                          //}
                        } ?>
                      </select>
                    </div>
                  </div> -->

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Status</label>
                      <br>
                      <div class="form-check">
                        <input <?= $user->status ? 'checked' : '' ?> name="status" type="radio" value="1" id="active" class="form-check-input">
                        <label class="form-check-label" for="active">Active</label>
                        <br>
                        <input <?= $user->status ? '' : 'checked' ?> name="status" type="radio" value="0" id="noactive" class="form-check-input">
                        <label class="form-check-label" for="noactive">Not Active</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Email</label>
                      <input type="email" name="email" class="form-control" value="<?= $user->email ?>">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Phone</label>
                      <input type="tel" name="phone" class="form-control" value="<?= $user->phone ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Code Agent</label>
                      <input type="text" name="kd_agent" class="form-control" value="<?= $user->kd_agent ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nip</label>
                      <!-- <input readonly type="number" name="nip" class="form-control" value="<?= $user->nip ?>"> -->
                      <input readonly type="text" name="nip" class="form-control" value="<?= $user->nip ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <!-- <label>Level Jabatan <?= $user->level_jabatan ?></label> -->
                      <label>Role</label>
                      <!-- <input type="text" name="level_jabatan" class="form-control" value="<?= $user->level_jabatan ?>"> -->
                      <select name="level_jabatan" id="" class="form-control">
                        <option selected disabled>Pilih Jabatan</option>
                        <?php
                        if ($this->session->userdata('is_premium')) {
                        ?>
                          <option <?= ($user->level_jabatan == "1") ? 'selected' : '' ?> value="1">Staff</option>
                          <option <?= ($user->level_jabatan == "2") ? 'selected' : '' ?> value="2">Manager</option>
                          <!-- <option <?= ($user->level_jabatan == "2") ? 'selected' : '' ?> value="2">Supervisor</option> -->
                          <option value="3">Keuangan</option>
                          <!-- <option <?= ($user->level_jabatan == "3") ? 'selected' : '' ?> value="3">Manajer</option> -->
                          <!-- <option <?= ($user->level_jabatan == "4") ? 'selected' : '' ?> value="4">General Manajer</option> -->
                          <option <?= ($user->level_jabatan == "5") ? 'selected' : '' ?> value="5">Direktur</option>
                          <!-- <option <?= ($user->level_jabatan == "6") ? 'selected' : '' ?> value="6">Direktur Utama</option> -->
                          <?php
                        } else {
                          $user_counts = isset($user_counts) ? $user_counts : [];
                          $roles = [
                            1 => 'Staff',
                            2 => 'Manager',
                            // 2 => 'Supervisor',
                            3 => 'Keuangan',
                            // 3 => 'Manajer',
                            // 4 => 'General Manajer', // This one is commented out in your example, so keep it commented
                            5 => 'Direktur',
                            // 6 => 'Direktur Utama',
                          ];
                          foreach ($roles as $value => $label) {
                            if (isset($user_counts[$value]) && $user_counts[$value] >= 1) {
                              if ($user->level_jabatan == $value) {
                          ?>
                                <option selected value="<?= $value ?>"><?= $label ?></option>
                            <?php
                              }
                              continue;
                            }
                            ?>
                            <option <?= ($user->level_jabatan == $value) ?> value="<?= $value ?>"><?= $label ?></option>
                        <?php
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>TMT</label>
                      <input type="date" name="tmt" class="form-control" value="<?= $user->tmt ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Bagian</label>
                      <!-- <select name="bagian" class="form-control js-example-basic-multiple" id="">
                        <option value=""> -- Pilih Bagian --</option>
                        <?php $xx = $this->db->get('bagian')->result();
                        foreach ($xx as $k) { ?>
                          <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->nama ?></option>
                        <?php } ?>
                      </select> -->
                      <div class="row">
                        <div class="col-10">

                          <?php
                          if ($this->session->userdata('is_premium')) {
                          ?>
                            <select name="bagian" class="form-control" id="mySelect">
                              <?php $xx = $this->db->from('bagian')->where('id_prsh', $this->session->userdata('user_perusahaan_id'))->get()->result();
                              foreach ($xx as $k) {
                                if (!empty($user)) {
                              ?>
                                  <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                                <?php } else { ?>
                                  <option value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                              <?php }
                              } ?>
                            </select>
                          <?php
                          } else {
                          ?>
                            <select name="bagian" class="form-control" id="mySelect" readonly>
                              <?php $xx = $this->db->from('bagian')->where('id_prsh', $this->session->userdata('user_perusahaan_id'))->get()->result();
                              foreach ($xx as $k) {
                                if (!empty($user)) {
                              ?>
                                  <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                                <?php } else { ?>
                                  <option value="<?= $k->Id ?>"><?= $k->kode_nama . ' - ' . $k->nama ?></option>
                              <?php }
                              } ?>
                            </select>
                          <?php
                          }
                          ?>
                        </div>
                        <div class="col-2">
                          <!-- <button type="button" id="addOptionBtn" class="btn btn-primary btn-block">Tambahkan Bagian <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                              <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                            </svg></button> -->
                          <button type="button" id="addOptionBtn" class="btn btn-primary btn-block"><i class="fe fe-plus"></i> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
                              <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
                            </svg></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nama Jabatan</label>
                      <input type="text" name="nama_jabatan" class="form-control" value="<?= $user->nama_jabatan ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Supervisi</label>
                      <select name="supervisi" class="form-control js-example-basic-multiple">
                        <option value=""> -- Pilih Supervisi --</option>
                        <?php
                        $supervisi = $this->db->get_where('users', ['level_jabatan >=' => 3])->result();
                        foreach ($supervisi as $data) {
                          if ($user->supervisi != null || $user->supervisi != "") {
                            $super_visi = $this->db->get_where('users', ['nip' => $user->supervisi])->row();
                            $selected = $super_visi->nip == $data->nip ? "selected" : "";
                          } else {
                            $selected = "";
                          }
                        ?>
                          <option <?= $selected ?> value="<?= $data->nip ?>"><?= $data->nama_jabatan ?> - <?= $data->nama ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Cuti</label>
                      <input type="number" name="cuti" class="form-control" value="<?= $user->cuti ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Lokasi Presensi</label>
                      <select name="lokasi_presensi" class="form-control js-example-basic-multiple">
                        <option value=""> -- Pilih Lokasi Presensi --</option>
                        <?php
                        $lokasi = $this->db->get('lokasi_presensi')->result();
                        foreach ($lokasi as $data) {
                          if ($user->id_lokasi_presensi != null || $user->id_lokasi_presensi != "") {
                            $selected = $user->id_lokasi_presensi == $data->id ? "selected" : "";
                          } else {
                            $selected = "";
                          }
                        ?>
                          <option <?= $selected ?> value="<?= $data->id ?>"><?= $data->nama_lokasi ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label>User Menu Access</label>
                    <div class="row">
                      <?php foreach ($all_menus_hierarchical as $parent_menu) : ?>
                        <?php if ($parent_menu->is_active == 1) : // Optional: Only show active parent menus 
                        ?>
                          <div class="col-md-6 mb-3 mt-2">
                            <div class="form-check">
                              <input class="form-check-input parent-checkbox" type="checkbox"
                                <?php if ($parent_menu->premium == 1) {
                                  echo $this->session->userdata('is_premium') == $parent_menu->premium ? '' : 'disabled';
                                } ?>
                                name="menu_ids[]"
                                value="<?= html_escape($parent_menu->Id); ?>" id="menu_<?= html_escape($parent_menu->Id); ?>" <?= in_array($parent_menu->Id, $user_menu_ids) ? 'checked' : ''; ?>> <label class="form-check-label font-weight-bold" for="menu_<?= html_escape($parent_menu->Id); ?>"> <i class="<?= html_escape($parent_menu->icon); ?>"></i> <?= html_escape($parent_menu->menu_name); ?>
                                <?= $parent_menu->premium == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>' : '' ?>
                              </label>
                            </div>
                            <?php if (!empty($parent_menu->children)) : ?>
                              <div style="margin-left: 25px;">
                                <?php foreach ($parent_menu->children as $child_menu) : ?>
                                  <?php if ($child_menu->is_active == 1) : // Optional: Only show active child menus 
                                  ?>
                                    <div class="form-check">
                                      <input class="form-check-input child-checkbox" type="checkbox"
                                        <?php if ($parent_menu->premium == 1) {
                                          echo $this->session->userdata('is_premium') == $parent_menu->premium ? '' : 'disabled';
                                        } ?>
                                        name="menu_ids[]"
                                        value="<?= html_escape($child_menu->Id); ?>" id="menu_<?= html_escape($child_menu->Id); ?>" data-parent-id="<?= html_escape($parent_menu->Id); ?>" <?= in_array($child_menu->Id, $user_menu_ids) ? 'checked' : ''; ?>> <label class="form-check-label" for="menu_<?= html_escape($child_menu->Id); ?>"> <i class="<?= html_escape($child_menu->icon); ?>"></i> <?= html_escape($child_menu->menu_name); ?>
                                        <?= $parent_menu->premium == 1 ? ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>' : '' ?>

                                      </label>
                                    </div>
                                  <?php endif; ?>
                                <?php endforeach; ?>
                              </div>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <a class="btn btn-warning" href="<?= base_url('perusahaan/user') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                  </div>

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