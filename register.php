<?php
if (!isset($_GET['uid'])) {
    die("UID tidak ditemukan.");
}
$uid = $_GET['uid'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa</title>
</head>
<body>
    <h2>Pendaftaran Siswa</h2>
    <form action="register_process.php" method="POST">
        <input type="hidden" name="uid" value="<?= htmlspecialchars($uid) ?>">
        <label>Nama: </label><input type="text" name="nama" required><br>
        <label>Kelas: </label><input type="text" name="kelas" required><br>
        <label>No. Telp: </label><input type="text" name="no_telp" required><br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>
