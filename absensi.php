<?php
require 'koneksi.php';
require 'cek.php';

// Query untuk mendapatkan username dari login
$user_result = $koneksi->query("SELECT username FROM login");
if (!$user_result) {
    die("Query failed: " . $koneksi->error);
}

// Tangani permintaan untuk ekspor data CSV
if (isset($_POST['export_csv'])) {
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal_desc';

    switch ($sort) {
        case 'tanggal_asc':
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu ASC";
            break;
        case 'tanggal_desc':
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu DESC";
            break;
        case 'status_berangkat':
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Berangkat' ORDER BY waktu DESC";
            break;
        case 'status_pulang':
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Pulang' ORDER BY waktu DESC";
            break;
        case 'status_terlambat':
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Terlambat' ORDER BY waktu DESC";
            break;
        default:
            $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu DESC";
            break;
    }

    $result = $koneksi->query($query);
    $filename = "attendance_" . date('Ymd') . ".csv";
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $output = fopen("php://output", "w");

    // Menulis header kolom CSV
    fputcsv($output, array('No', 'ID Pegawai', 'NIP', 'Nama Pegawai', 'Jabatan', 'Waktu', 'Status'), ';');

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        // Menulis baris data CSV
        fputcsv($output, array($no++, $row['id_karyawan'], $row['nip'], $row['nama'], $row['jabatan'], $row['waktu'], $row['status']), ';');
    }

    fclose($output);
    exit();
}

// Tangani permintaan untuk mereset data absensi
if (isset($_POST['reset_data'])) {
    error_log("Reset data button pressed");
    $delete_query = "DELETE FROM absensi";
    if ($koneksi->query($delete_query)) {
        error_log("Delete query executed successfully");
        echo "<script>alert('Data absensi berhasil direset.'); window.location.href='absensi.php';</script>";
    } else {
        error_log("Failed to execute delete query: " . $koneksi->error);
        echo "<script>alert('Gagal mereset data absensi: " . $koneksi->error . "'); window.location.href='absensi.php';</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="sidebar">
    <div class="sidetitle">
        <img class="logo" src="images/kemenkes.svg" alt="Logo">
        <h5>DINAS KESEHATAN KABUPATEN SUMBAWA</h5>
    </div>

    <div class="sidemenu">
        <a href="index.php"><i class='bx bxs-dashboard'></i> Dashboard</a>
    </div>
    <div class="sidemenu">
        <a href="karyawan.php"><i class='bx bx-folder'></i> Data Pegawai</a>
    </div>
    <div class="sidemenu">
        <a href="cetak.php"><i class='bx bx-qr'></i> Cetak QR</a>
    </div>
    <div class="sidemenu">
        <a href="scan.php"><i class='bx bx-qr-scan' ></i> Scan QR</a>
    </div>
    <div class="sidemenu">
        <a href="absensi.php"><i class='bx bxs-file-blank'></i> Data Absensi</a>
    </div>
    <div class="sidemenu">
        <a href="logout.php"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>   

<div class="container">
    <div class="topbar">
        <span class="toggle-btn">&#9776;</span>
        <span>Welcome, 
        <?php
        while ($user_data = mysqli_fetch_array($user_result)) {
            echo htmlspecialchars($user_data['username']);
        }
        ?>
        </span>
    </div>

    <div class="table-container">
        <h2>Data Absensi</h2>
        <div class="table-actions">
            <form method="get" action="absensi.php">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="tanggal_desc">Tanggal (Descending)</option>
                    <option value="tanggal_asc">Tanggal (Ascending)</option>
                    <option value="status_berangkat">Status (Berangkat)</option>
                    <option value="status_pulang">Status (Pulang)</option>
                    <option value="status_terlambat">Status (Terlambat)</option>
                </select>
                <button type="submit">Sort</button>
            </form>
            <form method="post" action="absensi.php">
                <button type="submit" name="export_csv">Export CSV</button>
                <button type="submit" name="reset_data" onclick="return confirm('Apakah Anda yakin ingin mereset data absensi?');">Reset Data</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pegawai</th>
                    <th>NIP</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="absensi-body">
                <?php
                $sort = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal_desc';

                switch ($sort) {
                    case 'tanggal_asc':
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu ASC";
                        break;
                    case 'tanggal_desc':
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu DESC";
                        break;
                    case 'status_berangkat':
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Berangkat' ORDER BY waktu DESC";
                        break;
                    case 'status_pulang':
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Pulang' ORDER BY waktu DESC";
                        break;
                    case 'status_terlambat':
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi WHERE status = 'Terlambat' ORDER BY waktu DESC";
                        break;
                    default:
                        $query = "SELECT id_karyawan, nip, nama, jabatan, waktu, status FROM absensi ORDER BY waktu DESC";
                        break;
                }

                $result = $koneksi->query($query);
                if ($result) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_karyawan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nip']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jabatan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['waktu']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data absensi</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="js/script.js"></script>
<script>
    function fetchAbsensi() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_absensi.php", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("absensi-body").innerHTML = xhr.responseText;
                console.log("Absensi data updated");
            } else {
                console.error("Failed to fetch absensi data");
            }
        };
        xhr.onerror = function () {
            console.error("Request failed");
        };
        xhr.send();
    }

    setInterval(fetchAbsensi, 5000);
</script>

</body>
</html>
