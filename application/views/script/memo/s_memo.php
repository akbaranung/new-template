<script>
  $(document).ready(function() {
    $('#tujuan, #cc').select2({
      placeholder: 'Search user',
      ajax: {
        url: '<?= base_url('app/search_user_memo') ?>',
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

    <?php if (isset($selected_item)) { ?>
      // Optional: Load preselected items (e.g., in edit form)
      var preselected = <?= json_encode($selected_item) ?>; // Example: [{id: 1, text: "Item A"}, {id: 2, text: "Item B"}]
      preselected.forEach(function(item) {
        var option = new Option(item.text, item.id, true, true);
        $('#tujuan').append(option).trigger('change');
      });
    <?php } ?>

    <?php if (isset($selected_item_cc)) { ?>
      // Optional: Load preselected items (e.g., in edit form)
      var preselectedcc = <?= json_encode($selected_item_cc) ?>; // Example: [{id: 1, text: "Item A"}, {id: 2, text: "Item B"}]
      preselectedcc.forEach(function(item) {
        var optioncc = new Option(item.text, item.id, true, true);
        $('#cc').append(optioncc).trigger('change');
      });
    <?php } ?>



    $(".btn-send").click(function(e) {
      e.preventDefault();
      for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
      }
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
  })
</script>

<script>
  function openMemo(id) {
    location.href = "<?= site_url('app/memo_view/') ?>" + id
  }
</script>