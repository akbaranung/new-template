<style>
    .theme-dark .body-table tr td {
        color: #fff;
    }

    .theme-light .body-table tr td {
        color: #000;
    }
</style>
<div class="table-responsive">
    <table class="table table-striped jambo_table bulk_action">
        <thead>
            <tr>
                <th>Nip</th>
                <th>Name</th>
                <th>Attendance</th>
                <th>Lokasi</th>
                <th>Tanggal/Waktu</th>
                <th>Settings</th>
            </tr>
        </thead>
        <tbody id="studentTableContainer" class="body-table">
            <?php
            if ($tipe == NULL) {
                foreach ($users as $user):
                    echo "<tr>";
                    $username = $user["username"];
                    echo "<td hidden>" . $username . "</td>";
                    echo "<td>" . $user["nip"] . "</td>";
                    echo "<td>" . $user["nama"] . "</td>";
                    echo "<td>Absent</td>";
                    echo "<td></td>";
                    echo "<td hidden></td>";
                    echo "<td></td>";
                    echo "<td><span><i class='ri-edit-line edit'></i><i class='ri-delete-bin-line delete'></i></span></td>";
                    echo "</tr>";
                endforeach;
            } else if ($tipe == 'masuk' || $tipe == 'pulang' || $tipe == 'absensi') {
                foreach ($users as $user):
                    echo "<tr>";
                    $username = $user["username"];
                    echo "<td hidden>" . $username . "</td>";
                    echo "<td>" . $user["nip"] . "</td>";
                    echo "<td>" . $user["nama"] . "</td>";
                    echo "<td>" . $user["attendanceStatus"] . "</td>";
                    echo "<td>" . $user["lokasiAttendance"] . "</td>";
                    echo "<td>" . $user["date"] . ' - ' . $user["waktu"] . "</td>";
                    echo "<td><span><i class='ri-edit-line edit'></i><i class='ri-delete-bin-line delete'></i></span></td>";
                    echo "</tr>";
                endforeach;
            }
            ?>
        </tbody>
    </table>
</div>