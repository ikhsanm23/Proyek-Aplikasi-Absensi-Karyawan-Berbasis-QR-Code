<?php
require 'koneksi.php';
require 'cek.php';

// Fetch user data
$user_query = "SELECT username FROM login WHERE id = '1'"; // Adjust this to your actual user id retrieval logic
$user_result = $koneksi->query($user_query);

// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];

    $sql = "UPDATE karyawan SET nip='$nip', nama='$nama', jabatan='$jabatan' WHERE id_karyawan='$id'";
    
    if ($koneksi->query($sql) === TRUE) {
        $_SESSION['success'] = "Data karyawan berhasil diedit.";
        // Redirect back to the form page
        header('Location: tambah_karyawan.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}

// Retrieve employee data based on the ID sent from karyawan.php
$row = null;
if (isset($_GET['id'])) {
    $id_karyawan = $_GET['id'];
    $query = "SELECT * FROM karyawan WHERE id_karyawan='$id_karyawan'";
    $result = $koneksi->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Data karyawan tidak ditemukan.<br>";
            echo "No rows found for ID: " . htmlspecialchars($id_karyawan) . "<br>";
        }
    } else {
        echo "Error executing query: " . $koneksi->error . "<br>";
        echo "Query: " . htmlspecialchars($query) . "<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pegawai</title>
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
        <a href="karyawan.php"><i class='bx bx-folder'></i> Data Karyawan</a>
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
        <img src="images/profile.jpeg" alt="Admin">
    </div>

    <!-- Form for editing employee -->
    <div class="content2">
        <div class="form-container2">
            <h2>Edit Data Pegawai</h2>
            <div class="bungkus">
            <?php if ($row): ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table class="form-table">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id_karyawan']); ?>">
                    <tr>
                        <div class="form-group2">
                            <td><label for="nip">Nip</label></td>
                            <td>:</td>
                            <td> <input type="text" id="nip" name="nip" value="<?php echo htmlspecialchars($row['nip']); ?>" required></td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-gorup2">
                            <td><label for="nama">Nama</label></td>
                            <td>:</td>
                            <td><input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required></td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group2">
                            <td><label for="jabatan">Jabatan</label></td>
                            <td>:</td>
                            <td><input type="text" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($row['jabatan']); ?>" required></td>
                    </tr>
                </table>
                <button type="submit">Submit</button>
                </form>
                <?php else: ?>
                <p>Data karyawan Sudah Masuk</p>
                <?php endif; ?>
            </div>
            <a href="karyawan.php">Kembali</a>
        </div>
    </div>
    
</div>
<script src="js/script.js"></script>

</body>
</html>
