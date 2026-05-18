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
  // const uppy = new Uppy.Core({
  //   autoProceed: false
  // }).use(Uppy.Dashboard, {
  //   inline: true,
  //   target: '#drag-drop-area',
  //   proudlyDisplayPoweredByUppy: false,
  //   theme: 'dark',
  //   width: '100%',
  //   height: '100%',
  // }).use(Uppy.Form, {
  //   target: '#form-comment',
  //   getMetaFromForm: true
  // })

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
          title: 'Siap Menjadi Raja <?= '<img src="' . base_url() . 'assets/icons/sword_gray.png" alt="Sword Icon" width="32" height="32">' ?>', // New title: "Ready to Become King?"
          html: 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..', // New text with HTML for emphasis
          icon: undefined, // IMPORTANT: Set icon to undefined or remove it if you're using iconHtml
          iconHtml: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50"><path fill="#FFD43B" d="M309 106c11.4-7 19-19.7 19-34c0-22.1-17.9-40-40-40s-40 17.9-40 40c0 14.4 7.6 27 19 34L209.7 220.6c-9.1 18.2-32.7 23.4-48.6 10.7L72 160c5-6.7 8-15 8-24c0-22.1-17.9-40-40-40S0 113.9 0 136s17.9 40 40 40c.2 0 .5 0 .7 0L86.4 427.4c5.5 30.4 32 52.6 63 52.6l277.2 0c30.9 0 57.4-22.1 63-52.6L535.3 176c.2 0 .5 0 .7 0c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40c0 9 3 17.3 8 24l-89.1 71.3c-15.9 12.7-39.5 7.5-48.6-10.7L309 106z"/></svg>', // Changed icon to question, suitable for asking a choice
          confirmButtonText: 'Ambil Mahkota Sekarang!', // New confirm button text: "Take the Crown Now!"
          showCancelButton: true,
          cancelButtonText: 'Nanti Saja, Belum Siap Jadi Raja', // New cancel button text: "Later, Not Ready to Be King Yet"
          customClass: {
            confirmButton: 'btn btn-primary', // Optional: Use your custom btn-pink class for the confirm button
            cancelButton: 'btn btn-pink' // Optional: Style the cancel button differently
          },
          buttonsStyling: false // Important if you use customClass for buttons
        }).then((result) => {
          if (result.isConfirmed) {
            // Optional: Redirect to an upgrade page if 'Ambil Mahkota Sekarang!' is clicked
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

      // if (uppyContainer && premiumUppyMessage) { // Ensure both elements exist
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

    // }
  );
</script>