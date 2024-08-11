<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_qr";

$koneksi = new mysqli($servername, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
