<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

// Pastikan staff sudah login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'staff') {
  header("Location: ../login.php");
  exit;
}

if (isset($_POST['update_status'])) 
    {
  $id_booking = intval($_POST['id_booking']);
  $status = $_POST['status'];
  $id_staff = intval($_SESSION['id']);

  $stmt = $conn->prepare("UPDATE booking SET status = ?, id_staff = ? WHERE id_booking = ?");
  $stmt->bind_param("sii", $status, $id_staff, $id_booking);

  if ($stmt->execute()) {
    echo "<script>
      alert('✅ Status booking berhasil diperbarui ke: $status');
      window.location.href='kelola_penyewaan.php';
    </script>";
  } else {
    echo '<pre>Gagal update: ' . $stmt->error . '</pre>';
  }
  exit;
}


// 🔹 Ambil semua data booking
$query = "
  SELECT b.*, p.nama_penyewa, m.nama_mobil 
  FROM booking b
  JOIN penyewa p ON b.id_penyewa = p.id_penyewa
  JOIN mobil m ON b.id_mobil = m.id_mobil
  ORDER BY b.tanggal_dibuat DESC
";

$result = $conn->query($query);
if (!$result) {
  die("<h3 style='color:red;'>Query error: " . htmlspecialchars($conn->error) . "</h3>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Penyewaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
  <div class="container py-5">
    <h2 class="text-center mb-4">Kelola Penyewaan Mobil</h2>

    <table class="table table-dark table-bordered table-hover align-middle">
      <thead>
        <tr class="text-center">
          <th>No</th>
          <th>Nama Penyewa</th>
          <th>Mobil</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>Total Harga</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1;
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
      <td>
  <span class="badge 
    <?= $row['status'] == 'dipesan' ? 'bg-warning' : 
        ($row['status'] == 'berjalan' ? 'bg-primary' : 
        ($row['status'] == 'selesai' ? 'bg-success' : 
        ($row['status'] == 'dibatalkan' ? 'bg-danger' : 'bg-secondary'))); ?>">
    <?= strtoupper($row['status'] ?: 'DIPESAN'); ?>
  </span>
</td>
<td class="text-center">
  <form method="POST" class="d-inline">
    <input type="hidden" name="id_booking" value="<?= $row['id_booking']; ?>">
    <select name="status" class="form-select form-select-sm d-inline-block w-auto">
      <option value="dipesan" <?= $row['status']=='dipesan'?'selected':''; ?>>Dipesan</option>
      <option value="berjalan" <?= $row['status']=='berjalan'?'selected':''; ?>>Berjalan</option>
      <option value="selesai" <?= $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
      <option value="dibatalkan" <?= $row['status']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
    </select>
    <button type="submit" name="update_status" class="btn btn-sm btn-light">Update</button>
  </form>
</td>

          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
