<?php
require_once __DIR__ . '/config/db.php';

$conn = getConnection();

if ($conn) {
    echo "✅ koneksi ke database 'car_rental' berhasil manteppppp!";
} else {
    echo "❌ koneksi gagal!";
}
?>
