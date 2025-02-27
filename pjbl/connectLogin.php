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
    $sql = "SELECT * FROM data_murid WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // UID belum terdaftar, masukkan ke tabel kartu_invalid
        $insertInvalid = "INSERT INTO kartu_invalid (uid) VALUES (?)";
        $stmt = $conn->prepare($insertInvalid);
        $stmt->bind_param("s", $uid);
        $stmt->execute();

        echo json_encode(["status" => "unregistered", "message" => "Kartu tidak terdaftar", "redirect" => "http://192.168.1.8/invalid_cards.php"]);
    } else {
        // UID sudah terdaftar, lakukan absensi
        $murid = $result->fetch_assoc();
        $nama_murid = $murid['nama'];
        $kelas = $murid['kelas'];

        date_default_timezone_set("Asia/Jakarta");
        $current_time = date("H:i");

        $status = ($current_time > "06:45") ? "Terlambat" : "Tepat Waktu";
        $insertAbsensi = "INSERT INTO data_absen (nama, kelas, keterangan) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertAbsensi);
        $stmt->bind_param("sss", $nama_murid, $kelas, $status);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Absensi berhasil", "data" => $murid, "status_absensi" => $status]);
    }
}
$conn->close();
?>
