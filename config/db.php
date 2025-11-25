<?php
// config/db.php
// koneksi reusable ke database

function getConnection() {
    static $conn = null;

    if ($conn === null) {
        $DB_HOST = getenv('DB_HOST') ?: 'mysql';
        $DB_USER = getenv('DB_USER') ?: 'root';
        $DB_PASS = getenv('DB_PASSWORD') ?: 'password';
        $DB_NAME = getenv('DB_NAME') ?: 'car_rental';
        $DB_PORT = getenv('DB_PORT') ?: 3306;

        $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
        if ($conn->connect_error) {
            die("❌ koneksi gagal: " . $conn->connect_error);
        }

        $conn->set_charset('utf8mb4');
    }

    return $conn;
}
?>
