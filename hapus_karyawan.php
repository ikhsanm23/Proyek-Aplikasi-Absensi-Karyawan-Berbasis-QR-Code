<?php
require 'koneksi.php';

// Cek apakah parameter id ada dalam request GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Persiapkan query DELETE
    $sql = "DELETE FROM karyawan WHERE id_karyawan = ?";
    $stmt = $koneksi->prepare($sql);

    // Bind parameter id sebagai integer
    $stmt->bind_param("i", $id);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Jika berhasil, kirimkan respons JSON dengan status sukses
        echo json_encode(["status" => "success"]);
    } else {
        // Jika gagal, kirimkan respons JSON dengan status error
        echo json_encode(["status" => "error"]);
    }
    $stmt->close(); // Tutup statement
} else {
    // Jika tidak ada parameter id dalam request GET, kirimkan respons JSON dengan status error
    echo json_encode(["status" => "error", "message" => "Parameter id tidak ditemukan"]);
}

$koneksi->close(); // Tutup koneksi
?>
