<?php
// config/db.php
// koneksi reusable ke database

function getConnection() {
    static $conn = null;

    if ($conn === null) {
        $host = 'localhost';
        $user = 'root';
        $pass = ''; // isi kalau MySQL lo pakai password
        $db   = 'car_rental';

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("❌ koneksi gagal: " . $conn->connect_error);
        }

        $conn->set_charset('utf8mb4');
    }

    return $conn;
}
?>
