<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    if ($_GET['action'] == "delete") {
        $deleteSQL = "DELETE FROM kartu_invalid WHERE uid = ?";
        $stmt = $conn->prepare($deleteSQL);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
    } elseif ($_GET['action'] == "register") {
        $registerSQL = "INSERT INTO data_siswa (uid, nama, kelas, no_telp) SELECT uid, 'Nama Baru', 'Kelas', 'No Telp' FROM kartu_invalid WHERE uid = ?";
        $stmt = $conn->prepare($registerSQL);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $deleteSQL = "DELETE FROM kartu_invalid WHERE uid = ?";
        $stmt = $conn->prepare($deleteSQL);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
    }
    header("Location: register.php");
    exit();
}

$sql = "SELECT * FROM kartu_invalid";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kartu Tidak Terdaftar</title>
</head>
<body>
    <h2>Daftar Kartu Tidak Terdaftar</h2>
    <table border="1">
        <tr>
            <th>UID</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['uid']; ?></td>
                <td>
                    <a href="register.php?action=register&uid=<?php echo $row['uid']; ?>">Daftarkan</a>
                    <a href="register.php?action=delete&uid=<?php echo $row['uid']; ?>" onclick="return confirm('Hapus kartu ini?');">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
