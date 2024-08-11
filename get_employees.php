<?php
header('Content-Type: application/json');
require 'koneksi.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

$query = "SELECT * FROM karyawan LIMIT ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

$response = [
    'success' => true,
    'employees' => $employees
];

echo json_encode($response);

$stmt->close();
$koneksi->close();
?>
