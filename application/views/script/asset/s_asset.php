<script>
  const fileInput = document.getElementById('foto');
  const previewImage = document.getElementById('preview-image');
  const imagePreview = document.getElementById('image-preview');

  $(document).ready(function() {
    applyPriceFormat();
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
                  location.href = `${res.reload}`
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
                title: `${status}`,
                showConfirmButton: false,
                timer: 1500,
              });
            },
          });
        }
      });
    });


    $('#detail_penyusutan').DataTable({
      "ajax": "<?php echo site_url('asset/get_penyusutan/' . $this->uri->segment(3)) ?>",
      "columns": [{
          "data": "kode"
        },
        {
          "data": "asset"
        },
        {
          "data": "spesifikasi"
        },
        {
          "data": "harga_perolehan"
        },
        {
          "data": "umur"
        },
        {
          "data": "penyusutan_perbulan"
        },
        {
          "data": "total_penyusutan"
        },
        {
          "data": "nilai_buku"
        },
        {
          "data": "sisa_umur"
        }
      ]
    });

    $('#tablePenyusutanPengecualian').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [],
      "ajax": {
        "url": "<?= base_url('asset/penyusutan_pengecualian_ajax_list') ?>",
        "type": "POST",
        // "success": function(res) {
        //   console.log(res)
        // }
      },
      "columnDefs": [{
        "targets": [0, 3, 4],
        "orderable": false
      }],
    })
  })

  fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0]; // Get the first file from the input

    if (file) {
      const reader = new FileReader(); // Create a new FileReader object

      reader.onload = function(e) {
        // When the file is loaded, set the source of the image element
        previewImage.src = e.target.result;
        previewImage.style.display = 'block'; // Make the image visible
        imagePreview.classList.remove('d-none'); // Corrected line
      }

      reader.readAsDataURL(file); // Read the file as a data URL
    } else {
      // If no file is selected, hide the image and reset its source
      previewImage.src = '#';
      previewImage.style.display = 'none';
      imagePreview.classList.add('d-none'); // Hide the entire preview container
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    const isPremium = <?= $this->session->userdata('is_premium') ?>;
    const upgradeUrl = '<?= base_url('subscription/upgrade') ?>'; // Adjust this URL as needed

    function showPremiumDeniedSwal() {
      Swal.fire({
        title: 'Siap Menjadi Raja <?= '<img src="' . base_url() . 'assets/icons/sword_gray.png" alt="Sword Icon" width="32" height="32">' ?>', // New title: "Ready to Become King?"
        html: 'Kekuasaan untuk menambah dan mengelola pengguna dalam kendali Anda di tangan Anda! Tingkatkan akun Anda sekarang untuk membuka singgasana dan mengklaim tahta Anda..', // New text with HTML for emphasis
        icon: 'warning', // IMPORTANT: Set icon to undefined or remove it if you're using iconHtml
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
    const attachmentInput = document.getElementById('foto');
    if (attachmentInput) { // Check if the element exists
      if (!isPremium) {
        attachmentInput.disabled = true; // Disable the input field
        // Optional: Add a tooltip or message near the input to explain why it's disabled
        const parentDiv = attachmentInput.closest('.div-foto'); // Find the parent div
        if (parentDiv) {
          const message = document.createElement('small');
          message.classList.add('text-danger', 'form-text');
          message.textContent = 'Upgrade to premium to upload attachments.';
          parentDiv.appendChild(message);
        }
      }
    }
  })

  function applyPriceFormat() {
    $('.uang').each(function() {
      new Cleave(this, {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.',
        prefix: 'Rp ',
        numeralDecimalScale: 2,
        rawValueTrimPrefix: true
      });
    });
  }

  function hapusPengecualian(id) {
    Swal.fire({
      title: "Apakah kamu yakin?",
      text: "Untuk menghapus asset ini dari pengecualian penyusutan?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= site_url('asset/hapus_pengecualian/') ?>" + id,
          method: "POST",
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
  }
</script>