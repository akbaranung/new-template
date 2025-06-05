<style>
  .navbar-light .navbar-nav .nav-link.active {
    background: #f8f9fa;
    color: #1b68ff;
  }
</style>


<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
  <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
    <i class="fe fe-x"><span class="sr-only"></span></i>
  </a>
  <nav class="vertnav navbar navbar-light">
    <!-- nav bar -->
    <div class="w-100 mb-4 d-flex">
      <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="<?= site_url('/') ?>">
        <img src="<?= $utility['logo'] ?>" alt="logo" style="width: 70%;">
      </a>
    </div>

    <?php
    $current_uri = uri_string();
    $controller = $this->uri->segment(1);
    ?>

    <ul class="navbar-nav flex-fill w-100 mb-2">
      <li class="nav-item w-100">
        <a class="nav-link <?= ($current_uri == 'home' or $current_uri == '') ? 'active' : '' ?>" href="<?= site_url('home') ?>">
          <i class="fe fe-home fe-16"></i>
          <span class="ml-3 item-text">Dashboard</span>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav flex-fill w-100 mb-2">
      <?php
      if ($menus) {
        foreach ($menus as $menu): ?>
          <?php if (empty($menu->submenus)): ?>
            <li class="nav-item w-100">
              <a class="nav-link <?= ($controller == $menu->controller) ? 'active' : '' ?>" href="<?= site_url($menu->url) ?>">
                <i class="<?= $menu->icon ?>"></i>
                <span class="ml-3 item-text"><?= $menu->menu_name ?></span>
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item <?= ($controller == $menu->controller) ? 'active' : '' ?> dropdown">
              <a href="#<?= $menu->url ?>" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
                <i class="<?= $menu->icon ?>"></i>
                <span class="ml-3 item-text"><?= $menu->menu_name ?></span>
              </a>
              <ul class="collapse <?= ($controller == $menu->controller) ? 'show' : '' ?> list-unstyled pl-4 w-100" id="<?= $menu->url ?>">
                <?php foreach ($menu->submenus as $submenu): ?>
                  <li class="nav-item">
                    <a class="nav-link <?= ($current_uri == $submenu->url) ? 'active' : '' ?> pl-3" href="<?= site_url($submenu->url) ?>"><i class="<?= $submenu->icon ?>"></i><span class="ml-1 item-text"><?= $submenu->menu_name ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endif; ?>
      <?php endforeach;
      } ?>
    </ul>

    <ul class="navbar-nav flex-fill w-100 mb-2">
      <li class="nav-item w-100">
        <a class="nav-link" href="<?= site_url('auth/logout') ?>">
          <i class="fe fe-power fe-16"></i>
          <span class="ml-3 item-text">Logout</span>
        </a>
      </li>
    </ul>
  </nav>
</aside>