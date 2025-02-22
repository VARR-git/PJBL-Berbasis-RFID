<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $no_telp = $_POST['no_telp'];

    $sql = "INSERT INTO data_siswa (uid, nama, kelas, no_telp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $uid, $nama, $kelas, $no_telp);

    if ($stmt->execute()) {
        echo "Pendaftaran berhasil!";
        echo "<br><a href='index.php'>Kembali</a>";
    } else {
        echo "Gagal mendaftar!";
    }
}

$conn->close();
?>
