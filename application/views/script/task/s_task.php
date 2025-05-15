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