<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection(); // koneksi database
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1️⃣ cek di tabel admin
    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_admin'];
        $_SESSION['nama'] = $data['nama_admin'];
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/dashboard.php");
        exit;
    }

    // 2️⃣ cek di tabel staff
    $sql = "SELECT * FROM staff_penyewaan WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_staff'];
        $_SESSION['nama'] = $data['nama_staff'];
        $_SESSION['role'] = 'staff';
        header("Location: ../staff/dashboard.php");
        exit;
    }

    // 3️⃣ cek di tabel penyewa
    $sql = "SELECT * FROM penyewa WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_penyewa'];
        $_SESSION['nama'] = $data['nama_penyewa'];
        $_SESSION['role'] = 'penyewa';
        header("Location: ../user/index.php");
        exit;
    }

    // kalau gak cocok
    echo "<script>alert('Email atau password salah'); window.location.href='login.php';</script>";
} else {
    echo "Akses tidak diizinkan langsung ke halaman ini!";
}
?>
