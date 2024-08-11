<?php 
require 'koneksi.php';
require 'cek.php';

$usernameResult = $koneksi->query("SELECT username FROM login");
$username = $usernameResult->fetch_assoc()['username'];
$jumlahResult = $koneksi->query("SELECT COUNT(id_karyawan) AS jumlah FROM karyawan");
$jumlahkaryawan = $jumlahResult->fetch_assoc()['jumlah'];
$hadirResult = $koneksi->query("SELECT COUNT(id) AS jumlahhadir FROM absensi WHERE status = 'Berangkat' OR status = 'Terlambat'");
$jumlahhadir = $hadirResult->fetch_assoc()['jumlahhadir'];

$tidakhadir = $jumlahkaryawan - $jumlahhadir;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

    <div class="cards">
        <div class="card blue">
            <a href="karyawan.php">
            <h3>Pegawai</h3>
            <div class="info"><?php echo $jumlahkaryawan; ?></div>
            </a>
        </div>
        <div class="card teal">
            <h3>Hadir</h3>
            <div class="info"><?php echo $jumlahhadir; ?></div>
        </div>
        <div class="card red">
            <h3>Tidak Hadir</h3>
            <div class="info"><?php echo $tidakhadir; ?></div>
        </div>
    </div>
        <div class="table-container">
            <h2 style="text-align: center;">Daftar Pegawai</h2>
            <div class="table-actions">
                <div>
                    <label for="entries">Show</label>
                    <select id="entries">
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody">
                    <!-- Data will be populated here by JavaScript -->
                </tbody>
            </table>
        </div>
</div>

<script src="js/script.js"></script>
<script>
    // fungsi js tabel index
document.getElementById('entries').addEventListener('change', function() {
    const numberOfEntries = this.value;
    fetchEmployeeData(numberOfEntries);
});

function fetchEmployeeData(limit) {
    fetch('get_employees.php?limit=' + limit)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('employeeTableBody');
            tbody.innerHTML = ''; // Clear the current content

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
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="2">No data available</td></tr>';
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}

// Initial load
fetchEmployeeData(10);
</script>
</body>
</html>
