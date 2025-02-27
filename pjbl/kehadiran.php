<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
$query = "SELECT * FROM data_absen";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Kehadiran</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Data Kehadiran Siswa</h2>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Waktu</th>
            <th>Keterangan</th>
        </tr>
        <?php 
        $no = 1;
        while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['nama']; ?></td>
            <td><?= $row['kelas']; ?></td>
            <td><?= $row['waktu']; ?></td>
            <td><?= $row['keterangan']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="dashboard.php">Kembali</a>
</body>
</html>