<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// ✅ pastikan koneksi aktif
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_admin'];
        $_SESSION['nama'] = $data['nama_admin'];
        $_SESSION['role'] = 'admin';
        $_SESSION['user_id'] = $data['id_admin'];
        header("Location: ../admin/dashboard.php");
        exit;
    }

    // 2️⃣ cek di tabel staff
    $sql = "SELECT * FROM staff_penyewaan WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_staff'];
        $_SESSION['nama'] = $data['nama_staff'];
        $_SESSION['role'] = 'staff';
        $_SESSION['user_id'] = $data['id_staff'];
        header("Location: ../staff/dashboard.php");
        exit;
    }

    $sql = "SELECT * FROM penyewa WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION['id'] = $data['id_penyewa'];
        $_SESSION['nama'] = $data['nama_penyewa'];
        $_SESSION['role'] = 'penyewa';
        $_SESSION['user_id'] = $data['id_penyewa'];
        header("Location: ../user/index.php");
        exit;
    }

    echo "<script>alert('Email atau password salah'); window.location.href='login.php';</script>";
} else {
    echo "Akses tidak diizinkan langsung ke halaman ini!";
}
?>

