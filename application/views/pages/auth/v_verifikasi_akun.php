<style>
  input {
    text-align: center;
  }
</style>
<div class="row align-items-center h-100 w-100 m-0">
  <form class="col-lg-3 col-md-4 col-10 mx-auto" action="<?= site_url('auth/cek_token') ?>" method="post">
    <div class="card shadow p-4">
      <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets') ?>/images/logo.png" alt="logo" class="w-100">
        <br>
        <h4>Verifikasi Akun</h4>
      </a>
      <br>
      <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong><?= $this->session->flashdata('error'); ?>!</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="inputEmail" class="sr-only">Token</label>
        <input type="text" id="token" name="token" class="form-control form-control-lg" placeholder="Please enter Token" autofocus="true">
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Verifikasi</button>
      <p class="mt-5 mb-3 text-muted text-center">IT BARIS KODE INDONESIA © <?= date('Y') ?></p>
    </div>
  </form>
</div>