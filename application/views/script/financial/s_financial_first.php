<script>
  $(function() {
    Swal.fire({
      title: "Peringatan!",
      text: "Anda Harus Membuat COA sebelum menggunakan menu Financial! Anda bisa menambahkan Manual atau menggunakan Template/Contoh yang sudah ada.",
      icon: "info"
    });
  })

  $(document).ready(function() {
    applyPriceFormat();
  })
</script>