<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qrData = $_POST['qrData'];
    // Here you can process the QR code data
    echo "QR Code data received: " . htmlspecialchars($qrData);
} else {
    echo "No data received";
}
?>
