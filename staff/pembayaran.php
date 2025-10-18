<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

// Pastikan staff login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'staff') {
  header("Location: ../login.php");
  exit;
}

// Ambil semua data pembayaran
$query = "
  SELECT py.*, b.id_booking, p.nama_penyewa, m.nama_mobil
  FROM pembayaran py
  JOIN booking b ON py.id_booking = b.id_booking
  JOIN penyewa p ON b.id_penyewa = p.id_penyewa
  JOIN mobil m ON b.id_mobil = m.id_mobil
  ORDER BY py.tanggal_bayar DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Daftar Pembayaran</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-dark text-light'>
  <div class='container py-5'>
    <h2 class='text-center mb-4'>Daftar Pembayaran</h2>

    <table class='table table-dark table-bordered table-hover align-middle'>
      <thead>
        <tr class='text-center'>
          <th>No</th>
          <th>Nama Penyewa</th>
          <th>Mobil</th>
          <th>Metode Pembayaran</th>
          <th>Total Bayar</th>
          <th>Tanggal Bayar</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_penyewa']); ?></td>
            <td><?= htmlspecialchars($row['nama_mobil']); ?></td>
            <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
            <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars($row['tanggal_bayar']); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
