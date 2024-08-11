<?php
require 'koneksi.php';

// cek login
if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, md5($_POST['password']));

    // Cocokan dengan database
    $cekdatabase = mysqli_query($koneksi, "SELECT * FROM login WHERE username='$username' AND password='$password'");
    $hitung = mysqli_num_rows($cekdatabase);

    if($hitung > 0){
        $_SESSION['log'] = 'True';
        header('location:index.php');
        exit();
    } else {
        echo '<script>alert("Username atau Password salah");</script>';
    }
}

// agar tidak bisa kembali ke login setelah login
if (isset($_SESSION['log'])) { 
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="images/kemenkes.svg" alt="Logo" class="logo">
            <h2>SISTEM ABSENSI DINAS KESEHATAN KABUPATEN SUMBAWA</h2>
            <h3>LOGIN</h3>
            <form method="post" action="">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="login" type="submit" name="login">SUBMIT</button>
            </form>
        </div>
    </div>
</body>
</html>
