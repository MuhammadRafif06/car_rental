<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

// Pastikan penyewa login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'penyewa') {
  header("Location: ../login.php");
  exit;
}

$id_penyewa = $_SESSION['id'];

// Ambil semua pembayaran milik penyewa ini
$query = "
  SELECT py.*, b.tanggal_mulai, b.tanggal_selesai, m.nama_mobil
  FROM pembayaran py
  JOIN booking b ON py.id_booking = b.id_booking
  JOIN mobil m ON b.id_mobil = m.id_mobil
  WHERE b.id_penyewa = ?
  ORDER BY py.tanggal_bayar DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_penyewa);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Pembayaran Saya</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>

<body class='bg-dark text-light'>
  <div class='container py-5'>
    <h2 class='text-center mb-4'>Riwayat Pembayaran Saya</h2>

    <table class='table table-dark table-bordered table-striped align-middle'>
      <thead>
        <tr class='text-center'>
          <th>No</th>
          <th>Mobil</th>
          <th>Periode Sewa</th>
          <th>Metode</th>
          <th>Total Bayar</th>
          <th>Tanggal Bayar</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_mobil']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_mulai']); ?> s/d <?= htmlspecialchars($row['tanggal_selesai']); ?></td>
            <td class="text-center">
              <span class="badge 
                <?= $row['metode_pembayaran'] == 'cash' ? 'bg-success' : 
                    ($row['metode_pembayaran'] == 'transfer' ? 'bg-primary' : 'bg-warning'); ?>">
                <?= strtoupper($row['metode_pembayaran']); ?>
              </span>
            </td>
            <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars($row['tanggal_bayar']); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
