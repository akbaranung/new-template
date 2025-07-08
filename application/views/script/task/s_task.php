<script>
  $(document).ready(function() {
    $('#member').select2({
      placeholder: 'Search user',
      ajax: {
        url: '<?= base_url('task/search_user_task') ?>',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term || '',
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: data.more
            }
          };
        },
        cache: true
      }
    });

    $(".btn-submit").click(function(e) {
      e.preventDefault();
      var parent = $(this).parents("form");
      var url = parent.attr("action");
      console.log(parent);
      var formData = new FormData(parent[0]);
      Swal.fire({
        title: "Are you sure?",
        text: "You want to submit the form?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: url,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: () => {
              Swal.fire({
                title: "Loading....",
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                  Swal.showLoading();
                },
              });
            },
            success: function(res) {
              if (res.success) {
                Swal.fire({
                  icon: "success",
                  title: `${res.msg}`,
                  showConfirmButton: false,
                  timer: 1500,
                }).then(function() {
                  Swal.close();
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: `${res.msg}`,
                  showConfirmButton: false,
                  timer: 1500,
                }).then(function() {
                  Swal.close();
                });
              }
            },
            error: function(xhr, status, error) {
              console.log(xhr);
              Swal.fire({
                icon: "error",
                title: `${error}`,
                showConfirmButton: false,
                timer: 1500,
              });
            },
          });
        }
      });
    });

    <?php if ($this->session->flashdata('warning')) { ?>
      Swal.fire({
        title: "Warning",
        text: "<?= $this->session->flashdata('warning') ?>",
        icon: "warning",
      });
    <?php
      unset($_SESSION['warning']);
    } ?>
  })
</script>

<script>
  const uppy = new Uppy.Core({
    autoProceed: false
  }).use(Uppy.Dashboard, {
    inline: true,
    target: '#drag-drop-area',
    proudlyDisplayPoweredByUppy: false,
    theme: 'dark',
    width: '100%',
    height: '100%',
  }).use(Uppy.Form, {
    target: '#form-comment',
    getMetaFromForm: true
  })

  // Tangani submit form manual dengan AJAX
  document.getElementById('form-comment').addEventListener('submit', function(e) {
    e.preventDefault();

    const formElement = e.target;
    const formData = new FormData(formElement);

    // Tambahkan file dari Uppy ke FormData
    uppy.getFiles().forEach(file => {
      formData.append('file[]', file.data);
    });

    Swal.fire({
      title: "Are you sure?",
      text: "You want to submit the form?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= site_url('task/activity_comment') ?>",
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "JSON",
          beforeSend: () => {
            Swal.fire({
              title: "Loading....",
              timerProgressBar: true,
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            });
          },
          success: function(res) {
            if (res.success) {
              Swal.fire({
                icon: "success",
                title: `${res.msg}`,
                showConfirmButton: false,
                timer: 1500,
              }).then(function() {
                Swal.close();
                location.reload();
              });
            } else {
              Swal.fire({
                icon: "error",
                title: `${res.msg}`,
                showConfirmButton: false,
                timer: 1500,
              }).then(function() {
                Swal.close();
              });
            }
          },
          error: function(xhr, status, error) {
            console.log(xhr);
            Swal.fire({
              icon: "error",
              title: `${error}`,
              showConfirmButton: false,
              timer: 1500,
            });
          },
        });
      }
    });
  });
</script>

<script>
  function openTask(id) {
    location.href = "<?= site_url('task/task_view/') ?>" + id
  }

  function openCard(id) {
    location.href = "<?= site_url('task/card_view/') ?>" + id
  }
</script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const isPremium = <?php echo json_encode($this->session->userdata('is_premium')); ?>;
    const upgradeUrl = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed

    function showPremiumDeniedSwal() {
      Swal.fire({
        title: 'Akses Ditolak!',
        text: 'Anda membutuhkan akun premium untuk menambah user. Harap tingkatkan langganan Anda.',
        icon: 'warning',
        confirmButtonText: 'Tingkatkan Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Tidak, Terimakasih'
      }).then((result) => {
        if (result.isConfirmed) {
          // Optional: Redirect to an upgrade page if 'Upgrade Now' is clicked
          window.location.href = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed
        }
      });
    }

    // ... (your existing JavaScript for other buttons) ...

    // Disable Attachment Input if Not Premium
    const attachmentInput = document.getElementById('attach');
    if (attachmentInput) { // Check if the element exists
      if (!isPremium) {
        attachmentInput.disabled = true; // Disable the input field
        // Optional: Add a tooltip or message near the input to explain why it's disabled
        const parentDiv = attachmentInput.closest('.col-sm-6'); // Find the parent div
        if (parentDiv) {
          const message = document.createElement('small');
          message.classList.add('text-danger', 'form-text');
          message.textContent = 'Upgrade to premium to upload attachments.';
          parentDiv.appendChild(message);
        }
      }
    }

    // --- Uppy Initialization for Premium Users ---
    const uppyContainer = document.getElementById('uppy-container');
    const premiumUppyMessage = document.getElementById('premium-uppy-message');

    if (uppyContainer && premiumUppyMessage) { // Ensure both elements exist
      if (isPremium) {
        // If premium, show the Uppy container and initialize Uppy
        uppyContainer.style.display = ''; // Show the container
        premiumUppyMessage.style.display = 'none'; // Hide the message

        const uppy = new Uppy.Core({
          autoProceed: false // Keep this false if you handle submission manually
        }).use(Uppy.Dashboard, {
          inline: true,
          target: '#drag-drop-area',
          proudlyDisplayPoweredByUppy: false,
          theme: 'dark',
          width: '100%',
          height: '100%',
        }).use(Uppy.Form, {
          target: '#form-comment', // Assuming this is the ID of your form
          getMetaFromForm: true
        });

        // Optional: Attach an XHRUpload plugin if you're uploading directly with Uppy
        // uppy.use(Uppy.XHRUpload, {
        //     endpoint: '<?= base_url('your_upload_endpoint') ?>', // Replace with your actual upload URL
        //     fieldName: 'files[]', // Name expected by your server for multiple files
        //     formData: true, // Send as FormData
        //     headers: {
        //         'X-Requested-With': 'XMLHttpRequest' // Example header
        //     }
        // });

        // You might need to handle the Uppy.Form submission manually or via a separate Uppy plugin
        // if you're not using Uppy's built-in uploaders with the Form plugin.
        // Uppy.Form primarily helps with form data, not necessarily the file upload itself.
        // For actual file upload, you'd typically add Uppy.XHRUpload, Uppy.AwsS3, etc.
        // If you submit the form normally and process files via $_FILES in PHP,
        // ensure Uppy is configured to allow that or just hide the dashboard entirely.

      } else {
        // If not premium, hide the Uppy container and show the message
        uppyContainer.style.display = 'none';
        premiumUppyMessage.style.display = ''; // Show the message

        // Attach event listener to the "Upgrade Now" button in the message
        const upgradeUppyBtn = document.getElementById('upgrade-uppy-btn');
        if (upgradeUppyBtn) {
          upgradeUppyBtn.addEventListener('click', function() {
            showPremiumDeniedSwal(); // Re-use your existing Swal function
          });
        }
      }
    }

  });
</script>