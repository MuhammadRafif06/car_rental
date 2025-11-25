<?php
session_start();
// Pastikan path ke db.php sudah benar
require_once __DIR__ . '/../config/db.php'; 
$conn = getConnection();

// Perintah ini penting agar browser tidak menyimpan cache halaman.
header("Cache-Control: no-cache, must-revalidate"); 

// Pastikan ID Sesi ada
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Gunakan ID Sesi
$id_penyewa = $_SESSION['id_penyewa'] ?? $_SESSION['id'];

// Ambil data riwayat booking
$query = $conn->prepare("
    SELECT 
        b.id_booking,
        m.nama_mobil,
        m.merek,
        b.tanggal_mulai,
        b.tanggal_selesai,
        b.total_harga,
        b.metode_pembayaran,
        b.tanggal_bayar,
        b.status
    FROM booking b
    JOIN mobil m ON b.id_mobil = m.id_mobil
    WHERE b.id_penyewa = ?
    ORDER BY b.tanggal_dibuat DESC
");
$query->bind_param("i", $id_penyewa);
$query->execute();
$result = $query->get_result();

// Tutup koneksi setelah query selesai
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Penyewaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="fw-bold mb-4">Riwayat Penyewaan Mobil</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Booking</th>
                            <th>Mobil</th>
                            <th>Tanggal Sewa</th>
                            <th>Total Harga</th>
                            <th>Pembayaran</th>
                            <th>Tanggal Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_booking']); ?></td>
                                    <td><?= htmlspecialchars($row['merek'] . ' ' . $row['nama_mobil']); ?></td> 
                                    <td><?= htmlspecialchars($row['tanggal_mulai'] . ' s/d ' . $row['tanggal_selesai']); ?></td>
                                    <td>**Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>**</td>
                                    <td><?= htmlspecialchars($row['metode_pembayaran'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal_bayar']))) ?? '-'; ?></td>
                                    <td>
                                        <?php
                                            $statusClass = match($row['status']) {
                                                'dipesan' => 'warning',
                                                'berjalan' => 'primary',
                                                'selesai' => 'success',
                                                'dibatalkan' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?= $statusClass; ?>"><?= ucfirst($row['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted">Belum ada riwayat penyewaan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>