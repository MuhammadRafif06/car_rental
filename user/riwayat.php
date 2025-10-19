<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$conn = getConnection();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'penyewa') {
  header("Location: ../login.php");
  exit;
}

$id_penyewa = $_SESSION['id'];

$query = "
  SELECT b.*, m.nama_mobil, m.merek
  FROM booking b
  JOIN mobil m ON b.id_mobil = m.id_mobil
  WHERE b.id_penyewa = ?
  ORDER BY b.tanggal_dibuat DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_penyewa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">CarRental</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
          <li class="nav-item"><a href="#cars" class="nav-link">Cars</a></li>
          <li class="nav-item"><a href="riwayat.php" class="nav-link">History</a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-danger">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <h2 class="text-center mb-4">Riwayat Booking Anda</h2>

    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Mobil</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>Total Harga</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_mobil']); ?> (<?= htmlspecialchars($row['merek']); ?>)</td>
            <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
            <td>
              <span class="badge 
                <?= $row['status'] == 'pending' ? 'bg-warning' : 
                    ($row['status'] == 'approved' ? 'bg-success' : 
                    ($row['status'] == 'rejected' ? 'bg-danger' : 'bg-secondary')); ?>">
                <?= strtoupper($row['status']); ?>
              </span>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
