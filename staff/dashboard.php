<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

include 'includes/header.php';
include 'includes/sidebar_staff.php';
include 'includes/topbar.php';

$total_booking = $conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()['total'];
$berjalan = $conn->query("SELECT COUNT(*) AS total FROM booking WHERE status='Berjalan'")->fetch_assoc()['total'];
$selesai = $conn->query("SELECT COUNT(*) AS total FROM booking WHERE status='Selesai'")->fetch_assoc()['total'];
$total_penyewa = $conn->query("SELECT COUNT(*) AS total FROM penyewa")->fetch_assoc()['total'];

$booking = $conn->query("
  SELECT b.id_booking, m.nama_mobil, p.nama_penyewa AS penyewa, 
         b.tanggal_mulai, b.tanggal_selesai, b.status
  FROM booking b
  JOIN mobil m ON b.id_mobil = m.id_mobil
  JOIN penyewa p ON b.id_penyewa = p.id_penyewa
  ORDER BY b.tanggal_dibuat DESC
");
?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Dashboard Staff</h1>

  <div class="row mb-4">
    <div class="col-md-3"><div class="card bg-primary text-white p-3">Total Booking: <?= $total_booking ?></div></div>
    <div class="col-md-3"><div class="card bg-warning text-white p-3">Berjalan: <?= $berjalan ?></div></div>
    <div class="col-md-3"><div class="card bg-success text-white p-3">Selesai: <?= $selesai ?></div></div>
    <div class="col-md-3"><div class="card bg-info text-white p-3">Total Penyewa: <?= $total_penyewa ?></div></div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">Penyewaan Terbaru</div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th><th>Mobil</th><th>Penyewa</th><th>Tgl Mulai</th><th>Tgl Selesai</th><th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $booking->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id_booking'] ?></td>
            <td><?= $row['nama_mobil'] ?></td>
            <td><?= $row['penyewa'] ?></td>
            <td><?= $row['tanggal_mulai'] ?></td>
            <td><?= $row['tanggal_selesai'] ?></td>
            <td><?= $row['status'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
