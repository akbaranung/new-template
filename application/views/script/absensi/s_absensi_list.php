<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tanggal_export_absensi').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true
        });
        $("a[id='button-reset-cuti']").click(function(e) {
            if (!confirm('Apakah anda yakin ingin mereset cuti?')) {
                e.preventDefault();
            }

        });

        <?php if ($this->session->flashdata('error')) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= $this->session->flashdata('error') ?>',
            })
        <?php } ?>

        $("button[id='btn-hapus-tgl-libur']").click(function(e) {
            if (!confirm('Apakah anda yakin ingin menghapus tanggal libur tersebut?')) {
                e.preventDefault();
            }
        });


        $('#user-table').dataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('absensi/ajax_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                // targets: 8,
                orderable: false
            }], // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

        $('#team-table').dataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('absensi/ajax_list2') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                // targets: 8,
                orderable: false
            }], // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

        $('#approval-table').dataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('absensi/ajax_list3') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1,
                orderable: false
            }], // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

    })

    function showUser() {
        document.getElementById('user').style.display = 'flex';
        document.getElementById('team').style.display = 'none';
        document.getElementById('excel').style.display = 'none';
        document.getElementById('approval').style.display = 'none';
    }

    function showTeam() {
        document.getElementById('user').style.display = 'none';
        document.getElementById('team').style.display = 'flex';
        document.getElementById('excel').style.display = 'none';
        document.getElementById('approval').style.display = 'none';
    }

    function showApproval() {
        document.getElementById('user').style.display = 'none';
        document.getElementById('team').style.display = 'none';
        document.getElementById('excel').style.display = 'none';
        document.getElementById('approval').style.display = 'flex';
    }

    function showExport() {

        document.getElementById('user').style.display = 'none';
        document.getElementById('team').style.display = 'none';
        document.getElementById('approval').style.display = 'none';
        document.getElementById('excel').style.display = 'flex';

    }
</script>