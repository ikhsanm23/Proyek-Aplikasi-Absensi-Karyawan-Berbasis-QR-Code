<?php
require 'koneksi.php';
require 'cek.php';

$usernameResult = $koneksi->query("SELECT username FROM login");
$username = $usernameResult->fetch_assoc()['username'];

// Menghapus karyawan jika parameter 'delete' ada dalam request GET
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Persiapkan query DELETE
    $sql = "DELETE FROM karyawan WHERE id_karyawan = ?";
    $stmt = $koneksi->prepare($sql);

    // Bind parameter id sebagai integer
    $stmt->bind_param("i", $id);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke halaman karyawan.php dengan pesan sukses
        echo "<script>alert('Karyawan berhasil dihapus.'); window.location.href='karyawan.php';</script>";
    } else {
        // Jika gagal, redirect ke halaman karyawan.php dengan pesan gagal
        echo "<script>alert('Gagal menghapus karyawan.'); window.location.href='karyawan.php';</script>";
    }
    $stmt->close(); // Tutup statement
}

// Fetch user data
$user_result = $koneksi->query("SELECT username FROM login");
if (!$user_result) {
    die("Error fetching user data: " . $koneksi->error);
}

// Fetch employee data
$sql = "SELECT * FROM karyawan";
$employee_result = $koneksi->query($sql);
if (!$employee_result) {
    die("Error fetching employee data: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>
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
        <span>Welcome, <?php echo $username; ?></span>
    </div>

    <div class="table-container">
        <h2>Data Pegawai</h2>
        <div class="table-actions">
            <div>
                <label for="entries2">Show</label>
                <select id="entries2">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                entries
            </div>
            <div>
                <label for="search">Search:</label>
                <input type="text" placeholder="Inputkan ID atau Nama" id="search" onkeyup="searchData()">
            </div>
            <button onclick="window.location.href='tambah_karyawan.php'">Tambah Data</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIP</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>                   
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="employeeTable2">
                <!-- Data will be populated here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script src="js/script.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function editEmployee(id) {
        // Redirect ke halaman edit_karyawan.php dengan ID karyawan
        window.location.href = 'edit_karyawan.php?id=' + id;
    }

    function deleteEmployee(id) {
        if (confirm('Anda yakin ingin menghapus karyawan ini?')) {
            // Kirim permintaan GET ke halaman ini sendiri dengan parameter delete
            window.location.href = 'karyawan.php?delete=' + id;
        }
    }

    function searchData() {
        const searchInput = document.getElementById('search').value.trim().toLowerCase();
        const tableRows = document.getElementById('employeeTable2').getElementsByTagName('tr');
        
        for (let i = 0; i < tableRows.length; i++) {
            const cells = tableRows[i].getElementsByTagName('td');
            let rowContainsSearchTerm = false;
            
            if (cells[0] || cells[2]) { 
                const idText = cells[0].innerText.trim().toLowerCase();
                const nameText = cells[2].innerText.trim().toLowerCase();
                if (idText.includes(searchInput) || nameText.includes(searchInput)) {
                    rowContainsSearchTerm = true;
                }
            }

            tableRows[i].style.display = rowContainsSearchTerm ? '' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const entriesSelect = document.getElementById('entries2');
        entriesSelect.addEventListener('change', function() {
            const limit = entriesSelect.value;
            fetchEmployeeData(limit);
        });

        // Initial load
        fetchEmployeeData(10);
    });

    function fetchEmployeeData(limit) {
        fetch(`get_employees.php?limit=${limit}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('employeeTable2');
                tbody.innerHTML = '';

                if (data.success) {
                    let num = 0;
                    data.employees.forEach(employee => {
                        num++;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${num}</td>
                            <td>${employee.nip}</td>
                            <td>${employee.nama}</td>
                            <td>${employee.jabatan}</td>
                            <td class='action-buttons'>
                                <button class='edit-btn' onclick='editEmployee(${employee.id_karyawan})'>Edit</button>
                                <button class='delete-btn' onclick='deleteEmployee(${employee.id_karyawan})'>Hapus</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5">No records found</td></tr>';
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }
    
</script>


</body>
</html>
