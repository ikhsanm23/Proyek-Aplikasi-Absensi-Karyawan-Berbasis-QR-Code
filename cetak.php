<?php 
require 'koneksi.php';
require 'cek.php';
require 'assets/phpqrcode/qrlib.php';

// Inisialisasi variabel untuk menangani pesan error
$error = '';
$qrGenerated = false;
$qrFile = '';
$result = null;

$user_result = $koneksi->query("SELECT username FROM login");
if (!$user_result) {
    die("Error fetching user data: " . $koneksi->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    
    try {
        // Fetch the employee's name and job title from the database
        $employeeQuery = $koneksi->prepare("SELECT nip, nama, jabatan FROM karyawan WHERE id_karyawan = ?");
        $employeeQuery->bind_param('s', $id);
        $employeeQuery->execute();
        $employeeResult = $employeeQuery->get_result();
        
        if ($employeeResult->num_rows > 0) {
            $employee = $employeeResult->fetch_assoc();
            $nama = $employee['nama'];
            $nip = $employee['nip'];
            $jabatan = $employee['jabatan'];
            
            // Generate QR Code
            $tempDir = 'images/';
            $fileName = $tempDir . 'QR_' . $id . '.png';
            
            $qrContent = json_encode([
                "id_karyawan" => $id,
                "nip" => $nip,
                "nama" => $nama,
                "jabatan" => $jabatan
            ]);
            QRcode::png($qrContent, $fileName, QR_ECLEVEL_L, 10);
            
            $qrGenerated = true;
            $qrFile = $fileName;

            // Menyimpan informasi cetak QR code ke tabel qr_cetak
            $stmt = $koneksi->prepare("INSERT INTO qr_cetak (id_karyawan, qr_code) VALUES (?, ?)");
            $stmt->bind_param('ss', $id, $fileName);
            $stmt->execute();
            $stmt->close();

            // Ambil data username dari tabel login
            $result = $koneksi->query("SELECT username FROM login");
        } else {
            $error = "Karyawan tidak ditemukan!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QRCode</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body onLoad="pindah()">

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
        <a href="scan.php"><i class='bx bx-qr-scan'></i> Scan QR</a>
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

    <div class="content">
        <div class="form-container">
            <h2>Generate QRCode</h2>
            <form method="POST" action="cetak.php">
                <div class="form-group">
                    <label for="id">Input ID Karyawan</label>
                    <br><br>
                    <input type="text" id="id" name="id" class="form-control" placeholder="Masukkan ID Karyawan yang terdaftar di Data Karyawan" required>
                </div>
                <br>
                <button type="submit" class="btn btn-primary btn-lg btn3d">Submit</button>
            </form>
        </div>
        <div class="result-container">
            <h2>Informasi QRCode</h2>
            <div id="qr-result">
                <?php 
                if ($qrGenerated) {
                    echo '<p>NIP: ' . htmlspecialchars($nip) . '</p>';
                    echo '<p>Nama: ' . htmlspecialchars($nama) . '</p>';
                    echo '<p>Jabatan: ' . htmlspecialchars($jabatan) . '</p>';
                    echo '<img src="' . htmlspecialchars($qrFile) . '" alt="QRCode">';
                } elseif ($error !== '') {
                    echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript">
    function pindah() {
        $('#id').focus();
    }
</script>
</body>
</html>
