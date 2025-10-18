<?php
session_start();
require_once _DIR_ . '/../config/db.php';

$conn = getConnection();

// pastikan user udah login
if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

// ambil id penyewa dan id mobil dari parameter
$id_penyewa = $_SESSION['id'];
$id_mobil = $_GET['id'] ?? null;

if (!$id_mobil) {
  die("Mobil tidak ditemukan!");
}

// ambil data mobil dari database
$query = $conn->prepare("SELECT * FROM mobil WHERE id_mobil = ?");
$query->bind_param("i", $id_mobil);
$query->execute();
$mobil = $query->get_result()->fetch_assoc();

if (!$mobil) {
  die("Mobil tidak ditemukan di database!");
}

// kalau user tekan tombol booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal_mulai = $_POST['tanggal_mulai'];
  $tanggal_selesai = $_POST['tanggal_selesai'];

  // hitung total harga (selisih hari x harga per hari)
  $selisih = (strtotime($tanggal_selesai) - strtotime($tanggal_mulai)) / (60 * 60 * 24);
  $total_harga = $selisih * $mobil['harga_sewa_per_hari'];
  if ($total_harga <= 0) {
    $total_harga = $mobil['harga_sewa_per_hari']; // minimal 1 hari
  }

  $status = 'pending';
  $tanggal_dibuat = date('Y-m-d H:i:s');
  $id_staff = null; // nanti diisi staff pas approve

  // masukkan ke tabel booking
  $stmt = $conn->prepare("
    INSERT INTO booking (id_penyewa, id_mobil, id_staff, tanggal_mulai, tanggal_selesai, total_harga, status, tanggal_dibuat)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("iiissdss", $id_penyewa, $id_mobil, $id_staff, $tanggal_mulai, $tanggal_selesai, $total_harga, $status, $tanggal_dibuat);

  if ($stmt->execute()) {
    echo "<script>alert('✅ Booking berhasil! Tunggu konfirmasi staff.'); window.location.href='riwayat.php';</script>";
  } else {
    echo "❌ Gagal booking: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Booking Mobil</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-dark text-white'>

<div class='container py-5'>
  <div class='row justify-content-center'>
    <div class='col-md-6'>
      <div class='card bg-secondary bg-opacity-25 p-4'>
        <h3 class='text-center mb-4'>Booking Mobil</h3>
        <h5><?= htmlspecialchars($mobil['nama_mobil']); ?> (<?= htmlspecialchars($mobil['merek']); ?>)</h5>
        <p>Harga: Rp <?= number_format($mobil['harga_sewa_per_hari'], 0, ',', '.'); ?> / hari</p>

        <form method='POST'>
          <div class='mb-3'>
            <label for='tanggal_mulai' class='form-label'>Tanggal Mulai</label>
            <input type='date' class='form-control' name='tanggal_mulai' required>
          </div>

          <div class='mb-3'>
            <label for='tanggal_selesai' class='form-label'>Tanggal Selesai</label>
            <input type='date' class='form-control' name='tanggal_selesai' required>
          </div>

          <button type='submit' class='btn btn-danger w-100'>Konfirmasi Booking</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>