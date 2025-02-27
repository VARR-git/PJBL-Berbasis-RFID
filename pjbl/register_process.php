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

    // Insert UID ke data_murid
    $sql = "INSERT INTO data_murid (uid, nama, kelas, no_telp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $uid, $nama, $kelas, $no_telp);

    if ($stmt->execute()) {
        // Hapus UID dari kartu_invalid setelah berhasil daftar
        $delete_sql = "DELETE FROM kartu_invalid WHERE uid = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $uid);
        $delete_stmt->execute();

        echo "Pendaftaran berhasil! Kartu telah dihapus dari daftar invalid.";
        echo "<br><a href='index.php'>Kembali</a>";
    } else {
        echo "Gagal mendaftar!";
    }
}

$conn->close();
?>
