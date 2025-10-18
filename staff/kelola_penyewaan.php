<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

// Pastikan staff sudah login
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'staff') {
  header("Location: ../login.php");
  exit;
}

if (isset($_POST['update_status'])) {
  $id_booking = intval($_POST['id_booking']);
  $status = $_POST['status'];
  $id_staff = intval($_SESSION['id']);

  // Update status di tabel booking
  $stmt = $conn->prepare("UPDATE booking SET status = ?, id_staff = ? WHERE id_booking = ?");
  $stmt->bind_param("sii", $status, $id_staff, $id_booking);
  $stmt->execute();

  // Kalau status = 'selesai', buat otomatis pembayaran
  if ($status === 'selesai') {
    // Ambil total harga booking
    $q = $conn->prepare("SELECT total_harga FROM booking WHERE id_booking = ?");
    $q->bind_param("i", $id_booking);
    $q->execute();
    $hasil = $q->get_result();
    $data = $hasil->fetch_assoc();

    if ($data) {
      $total_bayar = floatval($data['total_harga']);
      $metode = 'Transfer Bank'; // default
      
      $tanggal_bayar = date('Y-m-d H:i:s');

      // Cek apakah sudah ada pembayaran
      $cek = $conn->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_booking = ?");
      $cek->bind_param("i", $id_booking);
      $cek->execute();
      $hasil_cek = $cek->get_result();

      if ($hasil_cek->num_rows == 0) {
        $insert = $conn->prepare("
          INSERT INTO pembayaran (id_booking, metode_pembayaran, total_bayar, tanggal_bayar)
          VALUES (?, ?, ?, ?)
        ");
        $insert->bind_param("isds", $id_booking, $metode, $total_bayar, $tanggal_bayar);
        if ($insert->execute()) {
          echo "<script>alert('✅ Status selesai dan pembayaran berhasil dicatat!');</script>";
        } else {
          echo "<script>alert('⚠️ Status selesai tapi gagal mencatat pembayaran: {$insert->error}');</script>";
        }
      }
    }
  }

  echo "<script>window.location.href='kelola_penyewaan.php';</script>";
  exit;
}

// Ambil semua booking
$query = "
  SELECT b.*, p.nama_penyewa, m.nama_mobil 
  FROM booking b
  JOIN penyewa p ON b.id_penyewa = p.id_penyewa
  JOIN mobil m ON b.id_mobil = m.id_mobil
  ORDER BY b.tanggal_dibuat DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Kelola Penyewaan</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-dark text-light'>
  <div class='container py-5'>
    <h2 class='text-center mb-4'>Kelola Penyewaan Mobil</h2>
    
    <table class='table table-dark table-bordered table-hover align-middle'>
      <thead>
        <tr class='text-center'>
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
        <?php $no=1; while($row=$result->fetch_assoc()){ ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_penyewa']); ?></td>
          <td><?= htmlspecialchars($row['nama_mobil']); ?></td>
          <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
          <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
          <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
          <td>
            <span class='badge
              <?= $row['status']=='dipesan'?'bg-warning':
                  ($row['status']=='berjalan'?'bg-primary':
                  ($row['status']=='selesai'?'bg-success':
                  ($row['status']=='dibatalkan'?'bg-danger':'bg-secondary'))); ?>'>
              <?= strtoupper($row['status']); ?>
            </span>
          </td>
          <td class='text-center'>
            <form method='POST'>
              <input type='hidden' name='id_booking' value='<?= $row['id_booking']; ?>'>
              <select name='status' class='form-select form-select-sm d-inline-block w-auto'>
                <option value='dipesan' <?= $row['status']=='dipesan'?'selected':''; ?>>Dipesan</option>
                <option value='berjalan' <?= $row['status']=='berjalan'?'selected':''; ?>>Berjalan</option>
                <option value='selesai' <?= $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
                <option value='dibatalkan' <?= $row['status']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
              </select>
              <button type='submit' name='update_status' class='btn btn-sm btn-light'>Update</button>
            </form>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
