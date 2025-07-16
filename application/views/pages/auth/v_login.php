<style>
  /* Overlay for the modal */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    /* Semi-transparent black */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
  }

  .modal-overlay.show {
    opacity: 1;
    visibility: visible;
  }

  /* Modal content box */
  .modal-content {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 0.75rem;
    /* rounded-xl */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    /* shadow-2xl */
    max-width: 90%;
    width: 600px;
    /* Increased width for a larger modal */
    max-height: 80vh;
    /* Set a max height for scrollability */
    overflow-y: auto;
    /* Enable vertical scrolling */
    transform: translateY(-20px);
    opacity: 0;
    transition: transform 0.3s ease, opacity 0.3s ease;
  }

  .modal-overlay.show .modal-content {
    transform: translateY(0);
    opacity: 1;
  }
</style>

<div class="row align-items-center h-100 w-100 m-0">
  <!-- <form class="col-lg-3 col-md-4 col-10 mx-auto" action="<?= site_url('auth/login') ?>" method="post"> -->
  <form class="col-lg-3 col-md-4 col-10 mx-auto" id="login_username" method="POSTx">

    <div class="card shadow p-4">
      <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets') ?>/images/logo.png" alt="logo" class="w-100">
      </a>
      <br>
      <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong><?= $this->session->flashdata('error'); ?>!</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
      <?php endif; ?>
      <div id="usernameSection">
        <div class=" form-group">
          <label for="inputEmail" class="sr-only">Username</label>
          <input type="text" id="username" name="username" class="form-control form-control-lg" placeholder="Please enter username" autofocus="true">
        </div>
      </div>
      <div id="passwordSection" class="d-none">
        <!-- <h6 id="nsAddressDisplay" class="text-black text-xl mb-3"></h6> -->
        <div class="form-group">
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password">
        </div>
      </div>

      <!-- <div class="form-group">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Password">
      </div> -->
      <button id="checkUsernameBtn" class="btn btn-lg btn-primary btn-block" type="button" onclick="onCheck()">
        Next
      </button>
      <button id="loginbtn" class="btn btn-lg btn-primary btn-block btn-login d-none" type="button" disabled>Login</button>
      <p class="mt-5 mb-3 text-muted text-center">Belum punya akun? <a href="<?= base_url('auth/') ?>register">Daftarkan Akun Perusahaan Anda</a></p>
      <p class="mt-5 mb-3 text-muted text-center">IT BARIS KODE INDONESIA © <?= date('Y') ?></p>
    </div>
  </form>
</div>

<script>
  function check() {
    $('#passwordSection').removeClass('d-none');
    $('#loginbtn').removeAttr('disabled');

  }

  function onCheck() {
    const username = $('#username').val();
    const loginUsernameElement = document.getElementById('login_username');
    const loginForm = loginUsernameElement.closest('form');
    const url = "<?= base_url() ?>"

    $.ajax({
      url: "<?= base_url('auth/cek_user/') ?>", // Use POST for ID, don't append to URL unless it's a RESTful DELETE
      type: 'POST', // Keep as POST
      data: {
        username: username
      },
      dataType: 'json', // Expect JSON response
      success: function(response) {
        let iconType = 'error'; // Default to error
        if (response.status == 'success') {
          // iconType = 'success';
          // Swal.fire(
          //   response.status === 'success' ? 'Berhasil!' : 'Perhatian!', // Dynamic title
          //   response.message, // Display the message from the backend
          //   iconType = 'success',
          // ).then(() => {
          // Only reload the table if it was a success or a clear 'info' (already deleted) case
          if (response.status === 'success' || response.status === 'info') {
            // Assuming your DataTables ID is 'datatable', not 'table1' based on previous snippets
            $('#passwordSection').removeClass('d-none');
            // $('#nsAddressDisplay').html('NS Address : ' + response.ns_address);
            $('#loginbtn').removeClass('d-none');
            $('#checkUsernameBtn').addClass('d-none');
            $('#loginbtn').removeAttr('disabled');
            // loginForm.setAttribute('action', response.ns_address + '/login');
            // loginForm.setAttribute('action', 'https://admin.kodesis.id/login/login_form');
            loginForm.setAttribute('action', 'https://' + response.ns_address + '/login/login_form');
            // loginForm.setAttribute('action', url + '/auth/login');
            // loginForm.setAttribute('action', 'http://localhost/new-template-test/auth/login');

          }
          // });
        } else {
          Swal.fire(
            title = 'Perhatian',
            response.message, // Display the message from the backend
            iconType = 'error',
          ).then(() => {
            // Only reload the table if it was a success or a clear 'info' (already deleted) case
            if (response.status === 'success' || response.status === 'info') {
              // Assuming your DataTables ID is 'datatable', not 'table1' based on previous snippets
              $('#passwordSection').removeClass('d-none');
              // $('#nsAddressDisplay').html('NS Address : ' + response.ns_address);
              $('#loginbtn').removeClass('d-none');
              $('#checkUsernameBtn').addClass('d-none');
              $('#loginbtn').removeAttr('disabled');
              // loginForm.setAttribute('action', response.ns_address + '/login');
              // loginForm.setAttribute('action', 'https://admin.kodesis.id/login/login_form');
              loginForm.setAttribute('action', 'https://' + response.ns_address + '/login/login_form');
            }
          });
        }


      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error, xhr.responseText); // Log full error for debugging
        Swal.fire(
          'Kesalahan Jaringan!', // More specific error message
          'Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.',
          'error'
        );
      }
    });
  }
</script>