<?php
require 'koneksi.php';
require 'cek.php';

// Fetch user data
$user_result = $koneksi->query("SELECT username FROM login");
if (!$user_result) {
    die("Error fetching user data: " . $koneksi->error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_karyawan = $_POST['id_karyawan'];
    $status = $_POST['status'];
    $waktu = date('Y-m-d H:i:s'); // Include time for more precision

    // Check if id_karyawan exists in the karyawan table and get the details
    $stmt = $koneksi->prepare("SELECT id_karyawan,nip, nama, jabatan FROM karyawan WHERE id_karyawan = ?");
    $stmt->bind_param('s', $id_karyawan);
    $stmt->execute();
    $stmt->bind_result($id_karyawan,$nip, $nama, $jabatan);
    $stmt->fetch();
    $stmt->close();

    if ($id_karyawan && $nip && $nama && $jabatan) {
        // Check if the status is "Berangkat" and time is before 08:00 AM
        if ($status == 'Berangkat') {
            $current_time = date('H:i:s');
            if ($current_time > '08:00:00') {
                $status = 'Terlambat';
            }
        }
        
        // Insert data into absensi table
        $stmt = $koneksi->prepare("INSERT INTO absensi (id_karyawan, nip, nama, jabatan, waktu, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $id_karyawan, $nip, $nama, $jabatan, $waktu, $status);
        $stmt->execute();
        $stmt->close();

        echo 'Attendance recorded for employee ID ' . $id_karyawan . ' (' . $status . '). <a href="index.php">Go back</a>';
    } else {
        echo 'Error: Employee ID ' . $id_karyawan . ' does not exist or lacks necessary information. <a href="scan.php">Try again</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QRCode</title>
    <link rel="stylesheet" href="css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        #camera { width: 320px; height: 240px; border: 1px solid black; }
        #qr-reader {
            margin-top: 20px;
            position: relative;
            width: 100%;
            max-width: 500px;
            height: auto;
        }
        #scan-result {
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
    </style>
    <script src="assets/webcamjs/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
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

    <div class="scan-qr">
        <h3>Scan QRCode</h3>
        <div id="camera"></div>
        <canvas id="canvas" style="display: none;"></canvas>
        <div id="scan-result"></div>

        <form method="post" action="scan.php">
            <div>
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Berangkat">Berangkat</option>
                    <option value="Pulang">Pulang</option>
                </select>
            </div>
            <br>
            <input type="hidden" id="id_karyawan" name="id_karyawan">
            <button type="submit" class="btn">Record Attendance</button>
        </form>
    </div>
</div>

<script src="js/script.js"></script>
<script>
    Webcam.set({
        width: 400,
        height: 300,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach('#camera');

    function scanQRCode() {
        Webcam.snap(function(data_uri) {
            let image = new Image();
            image.src = data_uri;
            image.onload = function() {
                let canvas = document.getElementById('canvas');
                let context = canvas.getContext('2d');
                canvas.width = image.width;
                canvas.height = image.height;
                context.drawImage(image, 0, 0, canvas.width, canvas.height);
                let imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                let code = jsQR(imageData.data, imageData.width, imageData.height);

                if (code) {
                    let decodedData = JSON.parse(code.data);
                    document.getElementById('id_karyawan').value = decodedData.id_karyawan;
                    document.getElementById('scan-result').innerText = "QR Code detected: ID " + decodedData.id_karyawan + ", Name " + decodedData.nama;
                } 
            };
        });
    }

    setInterval(scanQRCode, 3000);
</script>

</body>
</html>
