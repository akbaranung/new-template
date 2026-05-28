<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
<script>
    $(document).ready(function() {
        $('#table-all').dataTable({
            responsive: true,

            // If you are using Scroller, you would add 'deferRender: true' and 'scrollY' options here,
            // and optionally 'scroller: true'.
            // Example:
            // deferRender: true,
            // scrollY: 200, // or '50vh'
            // scroller: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('cuti/cuti_all_gm') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1, // Adjust target index if 'Action' column is not always the 9th column (index 8)
                orderable: false
            }],
            // Custom DOM structure for layout (from previous answer)
            dom: '<"dataTables_top_wrapper clear-fix"<"dataTables_length_custom"l><"dataTables_filter_custom"f>>t<"dataTables_bottom_wrapper clear-fix"<"dataTables_info_custom"i><"dataTables_paginate_custom"p>>'
        });
    });
</script>

<script type="text/javascript">
    function detailCuti(id) {
        $("#detail-cuti").modal('show');
        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "<?= base_url('cuti/detailCuti/') ?>" + id,
            success: (res) => {
                $("#detail-cuti-byID").html(res);
            }
        })
    }

    function historyCuti(nip) {
        location.href = "<?= site_url('cuti/historyCuti/') ?>" + nip;
    }

    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>