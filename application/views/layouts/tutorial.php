  <?php

  $this->db->select('level_jabatan, COUNT(id) as user_count');
  $this->db->from('users');
  $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
  $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
  // $this->db->where('id_prsh', $this->session->userdata('user_perusahaan_id'));
  $this->db->group_by('level_jabatan');
  $query = $this->db->get();

  $user_counts = [];
  foreach ($query->result() as $row) {
    $user_counts[$row->level_jabatan] = (int)$row->user_count;
  }



  $this->db->from('users');
  $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
  $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
  $this->db->where('nama_jabatan !=', 'Super Admin');
  $total_user = $this->db->get()->num_rows(); // Get the number of rows

  $max_users_for_100_percent = 4; // Define your maximum limit
  // $max_users_for_100_percent = 5; // Define your maximum limit

  // $this->cb->from('v_coa_all');
  // $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
  // $cek_coa_cabang = $this->cb->get()->num_rows();

  $this->cb->from('v_coa_all');
  $this->cb->where('id_cabang', $this->session->userdata('kode_cabang'));
  // Add the OR conditions
  $this->cb->group_start(); // Start a WHERE group for the OR conditions
  // $this->cb->where('no_sbb', '23014');
  // $this->cb->or_where('no_sbb', '23011');
  $this->cb->where_not_in('no_sbb', ['23014', '23011']);
  $this->cb->group_end(); // End the WHERE group
  $cek_coa_cabang = $this->cb->get()->num_rows();
  $user_counts = isset($user_counts) ? $user_counts : [];
  $roles = [

    5 => 'Direktur',
    3 => 'Keuangan',
    2 => 'Manager',
    1 => 'Staff',

  ];
  $active_p = 0;

  ?>
  <link rel="stylesheet" href="<?= base_url('assets/') ?>progress-bar-dashboard.css">
  <style>
    .triangle-right-success {
      margin-left: 4px;
      width: 0;
      height: 0;
      border-top: 8px solid transparent;
      /* border-left: 12px solid #3ad29f; */
      border-left: 12px solid #1b68ff;
      /* Green for success */
      border-bottom: 8px solid transparent;
    }

    .triangle-right-primary {
      margin-left: 4px;
      width: 0;
      height: 0;
      border-top: 8px solid transparent;
      /* border-left: 12px solid #1b68ff; */
      border-left: 12px solid #e81f63;

      /* Blue for primary */
      border-bottom: 8px solid transparent;
    }

    .triangle-right-secondary {
      margin-left: 4px;
      width: 0;
      height: 0;
      border-top: 8px solid transparent;
      border-left: 12px solid #6c757d;
      /* Grey for secondary */
      border-bottom: 8px solid transparent;
    }
  </style>
  <div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <!-- <p class="card-title mb-0"><strong>Tambahkan 4 User Role (Staff, Manager, Keuangan, Direktur) untuk Memulai Hidup Baru! (<?= $total_user . '/' . $max_users_for_100_percent ?>)</strong></p> -->
      <?php
      foreach ($roles as $value => $label) {
        if (isset($user_counts[$value]) && $user_counts[$value] >= 1) {
          continue;
        }
        if ($active_p == 0) {
          $active_p = 1;
      ?>
          <p class="card-title mb-0"><strong>Ayo buat Akun <?= $label ?> (<?= $total_user . '/' . $max_users_for_100_percent ?>)</strong></p>
        <?php
        }
      }

      if ($max_users_for_100_percent == $total_user && $cek_coa_cabang == 0) { ?>
        <p class="card-title mb-0"><strong>Ayo buat COA Sekarang</strong></p>

      <?php
      } ?>
    </div>
    <div class="card-body">
      <div class="container">
        <div class="progress-container mx-auto">
          <div class="progress" id="progress"></div>
          <?php
          $i = 1;
          $active_href = 0;
          foreach ($roles as $value => $label) {
            if (isset($user_counts[$value]) && $user_counts[$value] >= 1) {
              // $active_fishbone = 'active';
          ?>
              <a href="#">
                <div class="circle-success active" data-label="<?= $label ?>">
                  <div class="triangle-right-success"></div>
                </div>
              </a>
            <?php
              $i++;
              continue;
            }

            if ($active_href == 0) {
              $active_href = 1;
              $button_now = base_url('perusahaan/add_user/' . $value . '/' . $label);

              $label_now = "<a class='btn btn-pink' href='$button_now'>" . $label . "</a>";
            ?>
              <a href="<?= $button_now ?>">
                <div class="circle-current" data-label="<?= $label ?>">
                  <div class="triangle-right-primary"></div>
                </div>
              </a>
            <?php
            } else {
            ?>
              <a href="#">
                <div class="circle" data-label="<?= $label ?>">
                  <div class="triangle-right-secondary"></div>
                </div>
              </a>
            <?php
            }
            $i++;
          }

          if ($cek_coa_cabang == 0) {
            ?>
            <a href="#">
              <div class="circle" data-label="Setting COA dan SAWAL">
                <div class="triangle-right-secondary"></div>
              </div>
            </a>
            <?php
          } else if ($max_users_for_100_percent == $total_user && $cek_coa_cabang == 0) {
            $button_now = base_url('financial_first/force_make_coa_sbb');
            // $label = "COA"
            if ($cek_coa_cabang == 0) {
            ?>
              <a href="<?= $button_now ?>">
                <div class="circle-current" data-label="Setting COA Dan SAWAL">
                  <div class="triangle-right-primary"></div>
                </div>
              </a>
            <?php
            } else {
            ?>
              <a href="#">
                <div class="circle-success active" data-label="Setting COA Dan SAWAL">
                  <div class="triangle-right-success"></div>
                </div>
              </a>
          <?php
            }
          }

          ?>
          <!-- <a href="#">
                  <div class="circle" data-label="Setting SAWAL">
                    <div class="triangle-right-secondary"></div>
                  </div>
                </a> -->
          <!-- <div class="circle active" data-label="User">1</div>
              <div class="circle" data-label="Perusahaan">2</div>
              <div class="circle" data-label="Cabang">3</div> -->
        </div>
      </div>
    </div>
  </div>