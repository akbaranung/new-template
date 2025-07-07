<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">User <?= ($this->uri->segment(3) == false) ? 'Add' : 'Edit' ?></h1>
      <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title mb-0"><strong><?= ($this->uri->segment(3) == false) ? 'Tambah' : 'Ubah' ?> User</strong></p>
          <!-- <a href="<?= base_url('perusahaan/add_user') ?>" class="btn btn-primary">Add User</a> -->
        </div>
        <div class="card-body" id="user">
          <font style="font-size:14px;">
            <?php if ($this->uri->segment(3) == false) { ?> <!-- add user -->
              <?= $this->session->flashdata('msg') ?>
              <form action="<?= base_url('perusahaan/proccess_add_user') ?>" method="POST">
                <input type="hidden" value="add" name="add">
                <input type="hidden" value="<?= $this->uri->segment('3') ?>" name="id">
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
                  <tr>
                    <th width="200">Date of birth</th>
                    <td>
                      <div class='input-group date' id='myDatepicker2'>
                        <input type='date' id='date_pic' name='tgl_lahir' class="form-control" placeholder="yyyy-mm-dd" data-validate-words="1" required="required" />
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Status</th>
                    <td>
                      <input name="status" value="1" type="radio" id="active">
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
                    <th>Phone</th>
                    <td><input type="tel" name="phone" class="form-control"></td>
                  </tr>
                  <tr>
                    <th>Code Agent</th>
                    <td><input type="text" name="kd_agent" class="form-control"></td>
                  </tr>
                  <tr>
                    <th>Nip</th>
                    <td><input type="number" name="nip" class="form-control"></td>
                  </tr>
                  <tr>
                    <th>Level Jabatan</th>
                    <td>
                      <select name="level_jabatan" id="" class="form-control">
                        <option value="">Pilih Jabatan</option>
                        <option value="1">Staff</option>
                        <option value="2">Supervisor</option>
                        <option value="3">Manajer</option>
                        <option value="4">General Manajer</option>
                        <option value="5">Direktur</option>
                        <option value="6">Direktur Utama</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th>Bagian</th>
                    <td>
                      <select name="bagian" class="form-control" id="">
                        <?php $xx = $this->db->from('bagian')->where('id_prsh', $this->session->userdata('user_perusahaan_id'))->get()->result();
                        foreach ($xx as $k) {
                          if (!empty($user)) {
                        ?>
                            <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->nama ?></option>
                          <?php } else { ?>
                            <option value="<?= $k->Id ?>"><?= $k->nama ?></option>
                        <?php }
                        } ?>
                      </select>
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
                          <?php if ($parent_menu->is_active == 1) : ?>
                            <div class="col-md-6 mb-3 mt-2">
                              <div class="form-check">
                                <input class="form-check-input parent-checkbox" type="checkbox"
                                  name="menu_ids[]"
                                  value="<?= html_escape($parent_menu->Id); ?>"
                                  id="menu_<?= html_escape($parent_menu->Id); ?>">
                                <label class="form-check-label font-weight-bold" for="menu_<?= html_escape($parent_menu->Id); ?>">
                                  <i class="<?= html_escape($parent_menu->icon); ?>"></i> <?= html_escape($parent_menu->menu_name); ?>
                                </label>
                              </div>
                              <?php if (!empty($parent_menu->children)) : ?>
                                <div style="margin-left: 25px;"> <?php foreach ($parent_menu->children as $child_menu) : ?>
                                    <?php if ($child_menu->is_active == 1) : ?>
                                      <div class="form-check">
                                        <input class="form-check-input child-checkbox" type="checkbox"
                                          name="menu_ids[]"
                                          value="<?= html_escape($child_menu->Id); ?>"
                                          id="menu_<?= html_escape($child_menu->Id); ?>"
                                          data-parent-id="<?= html_escape($parent_menu->Id); ?>">
                                        <label class="form-check-label" for="menu_<?= html_escape($child_menu->Id); ?>">
                                          <i class="<?= html_escape($child_menu->icon); ?>"></i> <?= html_escape($child_menu->menu_name); ?>
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

                  <!-- <div class="col-md-6">
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
                  </div> -->
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
                      <input readonly type="number" name="nip" class="form-control" value="<?= $user->nip ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Level Jabatan</label>
                      <input type="text" name="level_jabatan" class="form-control" value="<?= $user->level_jabatan ?>">
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
                      <select name="bagian" class="form-control js-example-basic-multiple" id="">
                        <option value=""> -- Pilih Bagian --</option>
                        <?php $xx = $this->db->get('bagian')->result();
                        foreach ($xx as $k) { ?>
                          <option <?= $k->Id == $user->bagian ? 'selected' : '' ?> value="<?= $k->Id ?>"><?= $k->nama ?></option>
                        <?php } ?>
                      </select>
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
                          <option <?= $selected ?> value="<?= $data->nip ?>"><?= $data->nama_jabatan ?></option>
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
                                name="menu_ids[]"
                                value="<?= html_escape($parent_menu->Id); ?>" id="menu_<?= html_escape($parent_menu->Id); ?>" <?= in_array($parent_menu->Id, $user_menu_ids) ? 'checked' : ''; ?>> <label class="form-check-label font-weight-bold" for="menu_<?= html_escape($parent_menu->Id); ?>"> <i class="<?= html_escape($parent_menu->icon); ?>"></i> <?= html_escape($parent_menu->menu_name); ?>
                              </label>
                            </div>
                            <?php if (!empty($parent_menu->children)) : ?>
                              <div style="margin-left: 25px;">
                                <?php foreach ($parent_menu->children as $child_menu) : ?>
                                  <?php if ($child_menu->is_active == 1) : // Optional: Only show active child menus 
                                  ?>
                                    <div class="form-check">
                                      <input class="form-check-input child-checkbox" type="checkbox"
                                        name="menu_ids[]"
                                        value="<?= html_escape($child_menu->Id); ?>" id="menu_<?= html_escape($child_menu->Id); ?>" data-parent-id="<?= html_escape($parent_menu->Id); ?>" <?= in_array($child_menu->Id, $user_menu_ids) ? 'checked' : ''; ?>> <label class="form-check-label" for="menu_<?= html_escape($child_menu->Id); ?>"> <i class="<?= html_escape($child_menu->icon); ?>"></i> <?= html_escape($child_menu->menu_name); ?>
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