<script>
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
</script>