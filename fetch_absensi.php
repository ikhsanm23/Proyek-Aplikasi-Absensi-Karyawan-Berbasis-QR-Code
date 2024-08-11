<?php
require 'koneksi.php';

$query = "SELECT * FROM absensi ORDER BY waktu DESC";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . $row['id_karyawan'] . "</td>";
        echo "<td>" . $row['nip'] . "</td>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['jabatan'] . "</td>";
        echo "<td>" . $row['waktu'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Tidak ada data absensi</td></tr>";
}
?>
    