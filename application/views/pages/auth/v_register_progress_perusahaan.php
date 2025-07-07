<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="<?= base_url('assets/') ?>progress-bar.css">

<div class="row align-items-center h-100 w-100 m-0">
  <div class="col-lg-12 col-md-4 col-11 mx-auto">
    <div class="row">
      <div class="col-lg-3 col-md-4 col-10 mx-auto">
        <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="<?= base_url('/') ?>">
          <img src="<?= base_url('assets') ?>/images/logo.png" alt="logo" class="w-100">
        </a>
        <br>
        <?php if ($this->session->flashdata('error')) : ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= $this->session->flashdata('error'); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <!-- <strong><?= $this->session->flashdata('error'); ?>!</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"> -->
              <span aria-hidden="true">×</span>
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
      </div>
    </div>
    <div class="row mb-5 mt-3">
      <div class="container">
        <div class="progress-container mx-auto">
          <div class="progress" id="progress"></div>
          <div class="circle active" data-label="User">1</div>
          <div class="circle active" data-label="Perusahaan">2</div>
          <div class="circle" data-label="Cabang">3</div>
        </div>
      </div>
    </div>
  </div>
  <form class="col-lg-3 col-md-4 col-10 mx-auto" action="<?= site_url('auth/process_registrasi_perusahaan') ?>" method="post" enctype="multipart/form-data">
    <div class="card shadow p-4">
      <div class="row">
        <!-- END: Progress Bar Integration -->
        <div class="col-lg-12 col-md-4 col-10 mx-auto">

          <!-- New input fields for company data -->
          <div class="form-group text-left">
            <label for="nama_perusahaan">Logo Perusahaan</label>
            <input type="file" id="logo_perusahaan" name="logo_perusahaan" placeholder="Please enter Logo Perusahaan" required>
          </div>
          <div class="form-group text-left">
            <label for="nama_perusahaan">Nama Perusahaan</label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control form-control-lg" placeholder="Please enter Nama Perusahaan" required>
          </div>
          <div class="form-group text-left">
            <label for="nama_singkat">Nama Singkat</label>
            <input type="text" id="nama_singkat" name="nama_singkat" class="form-control form-control-lg" placeholder="Please enter Nama Singkat" required>
          </div>
          <div class="form-group text-left">
            <label for="nama_ppn">Nama PPN</label>
            <input type="text" id="nama_ppn" name="nama_ppn" class="form-control form-control-lg" placeholder="Please enter Nama PPN">
          </div>
          <div class="form-group text-left">
            <label for="besaran_ppn">Besaran PPN (%)</label>
            <input type="number" id="besaran_ppn" name="besaran_ppn" class="form-control form-control-lg" placeholder="Please enter Besaran PPN (%)" step="1" min="1" max="100">
          </div>
          <div class="form-group text-left">
            <label for="nomor_rekening">Nomor Rekening</label>
            <input type="number" id="nomor_rekening" name="nomor_rekening" class="form-control form-control-lg" placeholder="Please enter Nomor Rekening" min="0">
          </div>
          <div class="form-group text-left">
            <label for="nama_bank">Nama Bank</label>
            <input type="text" id="nama_bank" name="nama_bank" class="form-control form-control-lg" placeholder="Please enter Nama Bank">
          </div>
          <div class="form-group text-left">
            <label for="alamat_perusahaan">Alamat Perusahaan</label>
            <textarea id="alamat_perusahaan" name="alamat_perusahaan" class="form-control form-control-lg" placeholder="Please enter Alamat Perusahaan" rows="3"></textarea>
          </div>
          <div class="form-group text-left">
            <label for="nama_akronim">Nama Akronim</label>
            <input type="text" id="nama_akronim" name="nama_akronim" class="form-control form-control-lg" placeholder="Please enter Nama Akronim">
          </div>
          <!-- End new input fields -->

          <button class="btn btn-lg btn-primary btn-block btn-regis-perusahaan" type="submit">Registrasi</button>
          <!-- <p class="mt-5 mb-3 text-muted text-center">Belum punya akun? <a href="<?= base_url('auth/') ?>register">Buat Akun Perusahaan Anda</a></p> -->
          <p class="mt-5 mb-3 text-muted text-center">IT BARIS KODE INDONESIA © <?= date('Y') ?></p>
        </div>
      </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  const progress = document.getElementById("progress");
  const prev = document.getElementById("prev");
  const next = document.getElementById("next");
  const circles = document.querySelectorAll(".circle");

  let currentActive = 2;


  const update = () => {
    circles.forEach((circle, index) => {
      if (index < currentActive) circle.classList.add("active");
      else circle.classList.remove("active");
    });
    const actives = document.querySelectorAll(".active");
    progress.style.width =
      ((actives.length - 1) / (circles.length - 1)) * 100 + "%";
    if (currentActive === 1) prev.disabled = true;
    else if (currentActive === circles.length) next.disabled = true;
    else {
      prev.disabled = false;
      next.disabled = false;
    }
  };

  update();
</script>