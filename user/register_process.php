<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();

    $nama = $_POST['nama_penyewa'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];

    // cek apakah email sudah terdaftar
    $check = $conn->query("SELECT * FROM penyewa WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.location.href='register.php';</script>";
        exit;
    }

    // simpan data baru ke tabel penyewa
    $sql = "INSERT INTO penyewa (nama_penyewa, email, no_hp, alamat, password)
            VALUES ('$nama', '$email', '$no_hp', '$alamat', '$password')";

    if ($conn->query($sql)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Gagal registrasi: " . $conn->error . "'); window.location.href='register.php';</script>";
    }
} else {
    echo "akses langsung tidak diperbolehkan";
}
?>