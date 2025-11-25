<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$conn = getConnection();

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

$id_penyewa = $_SESSION['id_penyewa'];
$id_mobil = $_GET['id'] ?? null;

if (!$id_mobil) {
  die("Mobil tidak ditemukan!");
}


$query = $conn->prepare("SELECT * FROM mobil WHERE id_mobil = ?");
$query->bind_param("i", $id_mobil);
$query->execute();
$mobil = $query->get_result()->fetch_assoc();

if (!$mobil) {
  die("Mobil tidak ditemukan di database!");
}


$foto = $mobil['gambar'] ?? 'default.jpg';
if (!preg_match('/^https?:\/\//', $foto)) {
  $foto = "../assets/img/" . $foto;  // 
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggal_mulai = $_POST['tanggal_mulai'];
  $tanggal_selesai = $_POST['tanggal_selesai'];
  $metode_pembayaran = $_POST['metode_pembayaran'];

  $selisih = (strtotime($tanggal_selesai) - strtotime($tanggal_mulai)) / (60 * 60 * 24);
  if ($selisih <= 0) $selisih = 1;
  $total_harga = $selisih * $mobil['harga_sewa_per_hari'];

  $stmt = $conn->prepare("
    INSERT INTO booking (id_penyewa, id_mobil, id_staff, tanggal_mulai, tanggal_selesai, total_harga, status, tanggal_dibuat)
    VALUES (?, ?, NULL, ?, ?, ?, 'dipesan', NOW())
  ");
  $stmt->bind_param("iissd", $id_penyewa, $id_mobil, $tanggal_mulai, $tanggal_selesai, $total_harga);

  if ($stmt->execute()) {
    $id_booking = $conn->insert_id;

   
    $update = $conn->prepare("
    UPDATE booking
    SET metode_pembayaran = ?, tanggal_bayar = NOW()
    WHERE id_booking = ?
    ");
    $update->bind_param("si", $metode_pembayaran, $id_booking);
    $update->execute();


    echo "<script>alert('✅ Booking berhasil!'); window.location.href='riwayat.php';</script>";
    exit;
  } else {
    echo "❌ Gagal booking: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sewa Mobil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>

    .navbar {
        background-color: rgba(0, 0, 0, 0.9)
    }
    .card-custom { border-radius: 14px; }
    .form-label { font-weight: 600; }
    .harga-box { background: #f8f9fa; padding: 10px 15px; border-radius: 10px; }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">CarRental</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a href="#home" class="nav-link">Home</a></li>
          <li class="nav-item"><a href="#cars" class="nav-link">Cars</a></li>
          <li class="nav-item"><a href="riwayat.php" class="nav-link">History</a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-danger">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container py-5">
    <div class="row g-4 align-items-center justify-content-between">
    <!-- kiri: form sewa -->
    <div class="col-lg-7">
      <h3 class="mb-4 fw-bold">Form Penyewaan</h3>

      <form method="POST" class="card card-custom p-4 shadow-sm mb-4">
        <div class="mb-3">
          <label class="form-label">Tanggal Mulai</label>
          <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal Selesai</label>
          <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Metode Pembayaran</label>
          <select name="metode_pembayaran" class="form-select" required>
            <option value="" disabled selected>-- Pilih Metode --</option>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer Bank</option>
            <option value="ewallet">E-Wallet</option>
          </select>
        </div>

        <div class="mb-4 harga-box">
          <p class="mb-1 text-muted">Total Harga:</p>
          <h4 class="text-danger fw-bold" id="totalHarga">Rp 0</h4>
        </div>

        <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold">
          Konfirmasi Sewa
        </button>
      </form>
    </div>

    
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 card-custom">
      <?php
        $foto = $mobil['foto_mobil'];
       
        if (!preg_match('/^https?:\/\//', $foto)) {
        $foto = "../assets/img/" . $foto;
        }
        ?>
        <img src="<?= htmlspecialchars($foto); ?>" 
            alt="<?= htmlspecialchars($mobil['nama_mobil']); ?>" 
            class="card-img-top" 
            style="height: 220px; object-fit: cover;">

        <div class="card-body">
          <h5 class="fw-bold mb-1"><?= htmlspecialchars($mobil['nama_mobil']); ?></h5>
          <p class="text-muted mb-2"><?= htmlspecialchars($mobil['merek']); ?> • <?= $mobil['tahun']; ?></p>
          <p class="mb-0">Harga per hari:</p>
          <h4 class="text-danger">Rp <?= number_format($mobil['harga_sewa_per_hari'], 0, ',', '.'); ?></h4>
          <hr>
          <p class="small text-muted mb-1">Total harga akan muncul otomatis setelah pilih tanggal</p>
        </div>
      </div>
    </div>
  </div>
</div>

    
<script>
  const hargaPerHari = <?= $mobil['harga_sewa_per_hari']; ?>;
  const tMulai = document.getElementById('tanggal_mulai');
  const tSelesai = document.getElementById('tanggal_selesai');
  const totalHargaEl = document.getElementById('totalHarga');

  function updateTotal() {
    const mulai = new Date(tMulai.value);
    const selesai = new Date(tSelesai.value);
    if (mulai && selesai && selesai >= mulai) {
      const selisihHari = Math.ceil((selesai - mulai) / (1000 * 60 * 60 * 24)) || 1;
      const total = selisihHari * hargaPerHari;
      totalHargaEl.textContent = "Rp " + total.toLocaleString('id-ID');
    } else {
      totalHargaEl.textContent = "Rp 0";
    }
  }

  tMulai.addEventListener('change', updateTotal);
  tSelesai.addEventListener('change', updateTotal);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
