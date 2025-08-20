<style>
  /* This is a general rule for positioning notification badges. 
   Adjust values as needed for your specific theme. */
  .badge-counter {
    position: absolute;
    top: 5px;
    /* Adjust this value to move the badge up or down */
    right: 5px;
    /* Adjust this value to move the badge left or right */
    border-radius: 50%;
    font-size: 10px;
    padding: 3px 5px;
  }

  /* Hide the "Become the" text on screen sizes up to 767px (common mobile breakpoint) */
  @media (max-width: 767px) {
    .desktop-only {
      display: none;
    }
  }
</style>

<nav class="topnav navbar navbar-light">
  <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">

    <?php
    if ($this->uri->segment(1) != "subscription") {
    ?>
      <i class="fe fe-menu navbar-toggler-icon"></i>
    <?php
    }
    ?>
  </button>


  <ul class="nav">

    <li class="nav-item">
      <a href="#" onclick="upgrade_premium()" data-is-premium="<?= (int)($this->session->userdata('is_premium') == '1'); ?>" class="my-2 btn btn-<?= ($this->session->userdata('is_premium') == '1') ? 'primary' : 'pink' ?>">
        <?php if ($this->session->userdata('is_premium') == '1') {
          if ($this->session->userdata('Tenggat_waktu')) {
            echo '<b><span class="desktop-only">Masa Berlaku Titah : </span>' . $this->session->userdata('Tenggat_waktu') . ' Hari </b> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
          <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
        </svg>';
          } else {
            echo '<b><span class="desktop-only">You are </span>The King </b> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16">
          <path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z" />
        </svg>';
          }
        } else {
          echo '<b><span class="desktop-only">Become the</span> King!</b> <img src="' . base_url() . 'assets/icons/sword_white.png" alt="Sword Icon" width="16" height="16" style="vertical-align: middle; margin-left: 5px;">';
        }
        ?>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-muted my-2" href="#" id="modeSwitcher" data-mode="light">
        <i class="fe fe-sun fe-16"></i>
      </a>
    </li>

    <?php
    // Assuming $this->cb and $this->db are already loaded and configured
    $this->cb = $this->load->database('corebank', TRUE);

    $this->db->from('users');
    $this->db->join($this->cb->database . '.t_cabang', 't_cabang.uid = users.id_cabang');
    $this->db->where('t_cabang.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
    $total_user = $this->db->get()->num_rows();

    // Define the crown SVG once
    $crown_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="16" height="16"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>';

    // Check if user is premium once
    $is_premium_user = $this->session->userdata('is_premium');

    $this->cb->from('t_cabang');
    $this->cb->where('uid', $this->session->userdata('kode_cabang'));
    $cabang_now = $this->cb->get()->row();
    $cek_saldo_awal = $cabang_now->generate_sawal;

    if ($total_user >= 5 && $cek_saldo_awal == 1) {
    ?>
      <?php
      $nip = $this->session->userdata('nip');

      // Count unread memos
      $sql = "SELECT COUNT(Id) FROM memo WHERE (nip_kpd LIKE '%$nip%' OR nip_cc LIKE '%$nip%') AND (`read` NOT LIKE '%$nip%');";
      $query = $this->db->query($sql);
      $res2 = $query->result_array();
      $jumlah_notifikasi_memo = $res2[0]['COUNT(Id)'];

      // Count unread tasks
      $sql4 = "SELECT COUNT(id) FROM task WHERE (`member` LIKE '%$nip%' or `pic` like '%$nip%') and activity='1'";
      $query4 = $this->db->query($sql4);
      $res4 = $query4->result_array();
      $jumlah_notifikasi_tello = $res4[0]['COUNT(id)'];

      $jumlah_notifikasi = $jumlah_notifikasi_memo + $jumlah_notifikasi_tello;

      if ($this->session->userdata('level_jabatan') == 3) {

        // Count unread tasks
        $sql5 = "SELECT COUNT(id_cuti) FROM cuti WHERE atasan = '$nip' AND status_atasan is NULL";
        $query5 = $this->db->query($sql5);
        $res5 = $query5->result_array();
        $jumlah_notifikasi_cuti = $res5[0]['COUNT(id_cuti)'];

        $jumlah_notifikasi = $jumlah_notifikasi + $jumlah_notifikasi_cuti;
      }
      ?>


      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-muted pr-0 my-2" href="#" id="navbarDropdownNotification" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fe fe-bell fe-16"></i>
          <?php if ($jumlah_notifikasi > 0): ?>
            <span class="badge badge-pill badge-danger badge-counter"><?= $jumlah_notifikasi ?></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownNotification">
          <a class="dropdown-item" href="<?= site_url('app/inbox') ?>">
            Inbox Memo
            <?php if ($jumlah_notifikasi_memo > 0): ?>
              <span class="badge badge-pill badge-danger float-right"><?= $jumlah_notifikasi_memo ?></span>
            <?php endif; ?>
          </a>
          <a class="dropdown-item" href="<?= site_url('task') ?>">
            Tello
            <?php if ($jumlah_notifikasi_tello > 0): ?>
              <span class="badge badge-pill badge-danger float-right"><?= $jumlah_notifikasi_tello ?></span>
            <?php endif; ?>
          </a>
          <?php
          if ($this->session->userdata('level_jabatan') == 3) {
          ?>
            <a class="dropdown-item" href="<?= site_url('cuti/data_approve_atasan_view') ?>">
              Cuti
              <?php if ($jumlah_notifikasi_cuti > 0): ?>
                <span class="badge badge-pill badge-danger float-right"><?= $jumlah_notifikasi_cuti ?></span>
              <?php endif; ?>
            </a>
          <?php
          }
          ?>
        </div>
      </li>
      <span id="memo-notification-count" data-count="<?= $jumlah_notifikasi_memo ?>" style="display:none;"></span>
    <?php
    }
    ?>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="avatar avatar-sm mt-2">
          <!-- <img src="<?= base_url('assets') ?>/avatars/face-1.jpg" alt="..." class="avatar-img rounded-circle"> -->
          <img src="<?= base_url('assets') ?>/avatars/avatar.png" alt="..." class="avatar-img rounded-circle">
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
        <a class="dropdown-item" href="#"><?= $this->session->userdata('nama') ?></a>
        <a class="dropdown-item" href="<?= site_url('auth/logout') ?>">Logout</a>
      </div>
    </li>
  </ul>
</nav>