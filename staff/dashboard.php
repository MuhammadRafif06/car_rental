<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 🧠 Debug sementara (lihat session login aktif apa belum)
// HAPUS nanti kalau udah gak perlu
// echo "<pre>"; var_dump($_SESSION); echo "</pre>";

// 🔒 Pastikan hanya staff yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'staff') {
  header("Location: ../login.php");
  exit;
}

// 🔌 Koneksi ke database
$conn = getConnection();

// 🧑‍💻 Ambil data staff yang login
$id_staff = $_SESSION['id'];

$sql = "SELECT * FROM staff_penyewaan WHERE id_staff = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_staff);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Kalau data staff gak ditemukan (misalnya udah dihapus)
if (!$staff) {
  echo "<script>alert('Data staff tidak ditemukan!'); window.location.href='../logout.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Staff</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar {
      background-color: #000;
    }

    .card {
      background-color: #1e1e1e;
      border: 1px solid #333;
      border-radius: 15px;
      color: #fff;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
    }

    .btn-custom {
      background-color: #ff0000;
      color: #fff;
      border-radius: 10px;
      border: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-custom:hover {
      background-color: #fff;
      color: #000;
    }

    footer {
      margin-top: auto;
      text-align: center;
      color: #888;
      padding: 15px 0;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">Staff Dashboard</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger fw-bold">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <div class="container mt-5">
    <h1 class="text-center mb-4">Halo, <?= htmlspecialchars($staff['nama_staff']); ?> 👋</h1>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <h4 class="fw-bold mb-3">Menu Staff</h4>
          <p>Email: <?= htmlspecialchars($staff['email']); ?></p>
          <p>Tanggal dibuat: <?= htmlspecialchars($staff['tgl_dibuat']); ?></p>
          <a href="kelola_penyewaan.php" class="btn btn-custom mt-3 w-100">
            🚗 Kelola Penyewaan Mobil
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <p>© 2025 CarRental | Staff Portal</p>
  </footer>
</body>
</html>
