<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Dashboard</h2>
    <a href="kehadiran.php">Data Kehadiran</a> | 
    <a href="siswa.php">Data Siswa</a> | 
    <a href="logout.php">Logout</a>
</body>
</html>