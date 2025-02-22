<?php
header("Content-Type: application/json");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];

    // Cek apakah UID sudah terdaftar
    $sql = "SELECT * FROM data_siswa WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // UID belum terdaftar, arahkan ke halaman pendaftaran
        echo json_encode(["status" => "unregistered", "message" => "Kartu tidak terdaftar", "redirect" => "http://yourserver.com/register.php?uid=" . $uid]);
    } else {
        // UID sudah terdaftar, lakukan absensi
        $siswa = $result->fetch_assoc();
        date_default_timezone_set("Asia/Jakarta");
        $current_time = date("H:i");

        $status = ($current_time > "06:45") ? "Terlambat" : "Tepat Waktu";
        $insertAbsensi = "INSERT INTO data_absen (uid, status) VALUES (?, ?)";
        $stmt = $conn->prepare($insertAbsensi);
        $stmt->bind_param("ss", $uid, $status);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Absensi berhasil", "data" => $siswa, "status_absensi" => $status]);
    }
}

$conn->close();
?>
