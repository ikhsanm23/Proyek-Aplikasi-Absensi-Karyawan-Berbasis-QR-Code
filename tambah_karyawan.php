<?php
require 'koneksi.php';
require 'cek.php';

// Ambil data user yang sedang login (misalnya dari session atau database)
$user_query = "SELECT username FROM login WHERE id = '1'"; // Sesuaikan dengan sistem autentikasi Anda
$user_result = $koneksi->query($user_query);

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];

    // Query untuk menambah data karyawan baru
    $sql = "INSERT INTO karyawan (nip, nama, jabatan) VALUES ('$nip', '$nama', '$jabatan')";
    
    if ($koneksi->query($sql) === TRUE) {
        // Set a session variable to indicate success
        $_SESSION['success'] = "Data karyawan berhasil ditambahkan.";
        // Redirect back to the form page
        header('Location: tambah_karyawan.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan</title>
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
    </div>

    <!-- Formulir untuk tambah karyawan -->
    <div class="content2">
        <div class="form-container2">
            <h2>Tambah Data Karyawan</h2>
            <div class="bungkus">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table class="form-table">
                    <tr>
                        <div class="form-group2">
                            <td><label for="nip">NIP</label></td>
                            <td>:</td>
                            <td><input type="text" id="nip" name="nip" required></td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group2">
                            <td><label for="nama">Nama</label></td>
                            <td>:</td>
                            <td><input type="text" id="nama" name="nama" required></td>
                        </div>
                    </tr>
                    <tr>
                        <div class="form-group2">
                            <td><label for="jabatan">Jabatan</label></td>
                            <td>:</td>
                            <td><input type="text" id="jabatan" name="jabatan" required></td>
                        </div>
                    </tr>
                </table>
                <button type="submit">Submit</button>
            </form>
            </div>
            <a href="karyawan.php">Kembali</a>
        </div>
    </div> 
    
</div>

<script src="js/script.js"></script>
<script>
    // Function to show success message and then redirect
    function showSuccessAndRedirect(message, redirectUrl) {
        alert(message);
        window.location.href = redirectUrl;
    }

    // Check if there is a success message in the session
    <?php if (isset($_SESSION['success'])): ?>
        showSuccessAndRedirect('<?php echo $_SESSION['success']; ?>', 'karyawan.php');
        <?php unset($_SESSION['success']); // Clear the message after displaying ?>
    <?php endif; ?>
</script>

</body>
</html>
