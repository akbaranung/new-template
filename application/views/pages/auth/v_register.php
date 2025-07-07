<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="<?= base_url('assets/') ?>progress-bar.css">

<div class="row align-items-center h-100 w-100">
  <div class="col-lg-12 col-md-4 col-10 mx-auto">
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
      <!-- START: Progress Bar Integration -->
      <div class="col-6 col-7 mx-auto">
        <!-- <h3 class="text-center mb-3">Application Progress</h3> -->
        <h3 class="text-center mb-3">Proses Registrasi <?= $this->session->userdata('data_perusahaan') ?></h3>
        <div class="progress-stepper">
          <div id="step1" class="step active">Registrasi User</div>
          <div id="step2" class="step">Registrasi Perusahaan</div>
          <div id="step3" class="step">Registrasi Cabang</div>
          <!-- <div id="step4" class="step">Step 4</div> -->
        </div>
        <!-- <div class="d-flex justify-content-center mt-3">
            <button id="prevBtn" class="btn btn-secondary-custom btn-custom mr-3" disabled>Previous</button>
            <button id="nextBtn" class="btn btn-primary-custom btn-custom">Next</button>
          </div> -->
      </div>
    </div>
  </div>
  <form class="col-lg-3 col-md-4 col-10 mx-auto" action="<?= site_url('auth/proccess_register') ?>" method="post">
    <div class="card shadow p-4">
      <div class="row">
        <!-- END: Progress Bar Integration -->
        <div class="col-lg-12 col-md-4 col-10 mx-auto">
          <!-- New input fields for company data -->
          <div class="form-group text-left">
            <label for="nama">NIP</label>
            <input type="text" id="nip" name="nip" class="form-control form-control-lg" placeholder="Please enter NIP" autofocus="true" required>
          </div>
          <div class="form-group text-left">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control form-control-lg" placeholder="Please enter Nama" autofocus="true">
          </div>
          <div class="form-group text-left">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control form-control-lg" placeholder="Please enter Username" autofocus="true">
          </div>
          <div class="form-group text-left">
            <label for="password">Password</label>
            <input type="password" id="password1" name="password" class="form-control form-control-lg" placeholder="Password">
          </div>

          <div class="form-group text-left">
            <label for="password">Konfirmasi Password</label>
            <input type="password" id="password2" name="password_confirm" class="form-control form-control-lg" placeholder="Password">
          </div>

          <div class="form-group text-left">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Email">
          </div>
          <div class="form-group text-left">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" class="form-control form-control-lg" placeholder="Phone Number">
          </div>
          <!-- End new input fields -->

          <button class="btn btn-lg btn-primary btn-block btn-regis-perusahaan" type="submit">Registrasi</button>
          <!-- <p class="mt-5 mb-3 text-muted text-center">Belum punya akun? <a href="<?= base_url('auth/') ?>register">Buat Akun Perusahaan Anda</a></p> -->
          <p class="mt-5 mb-3 text-muted text-center">Sudah punya akun? <a href="<?= base_url('auth/') ?>">Masuk dengan Akun Perusahaan Anda</a></p>
          <p class="mt-5 mb-3 text-muted text-center">IT BARIS KODE INDONESIA © <?= date('Y') ?></p>
        </div>
      </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  // JavaScript to handle step activation for the progress bar
  document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentStepIndex = 0; // Starts at 0 for "Step 1"

    // Function to update the active step
    function updateActiveStep() {
      steps.forEach((step, index) => {
        if (index === currentStepIndex) {
          step.classList.add('active');
        } else {
          step.classList.remove('active');
        }
      });

      // Enable/disable buttons
      prevBtn.disabled = currentStepIndex === 0;
      nextBtn.disabled = currentStepIndex === steps.length - 1;
    }

    // Event listener for Next button
    nextBtn.addEventListener('click', function() {
      if (currentStepIndex < steps.length - 1) {
        currentStepIndex++;
        updateActiveStep();
      }
    });

    // Event listener for Previous button
    prevBtn.addEventListener('click', function() {
      if (currentStepIndex > 0) {
        currentStepIndex--;
        updateActiveStep();
      }
    });

    // Initial call to set the first step as active
    updateActiveStep();
  });
</script>