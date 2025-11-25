<?php
// admin/booking_detail.php

require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

// 1. Ambil ID Booking dari URL
$id_booking = $_GET['id'] ?? null;

if (!$id_booking) {
    die("ID Booking tidak ditemukan.");
}

// 2. Query untuk mendapatkan semua detail (JOIN 3-4 Tabel)
// Menggunakan AS (alias) untuk membedakan kolom nama/email/hp yang sama
$query = $conn->prepare("
    SELECT 
        b.*, 
        p.nama_penyewa, p.email AS email_penyewa, p.no_hp AS hp_penyewa, p.alamat AS alamat_penyewa,
        m.nama_mobil, m.merek, m.tahun, m.harga_sewa_per_hari, m.transmisi, m.foto_mobil,
        s.nama_staff
    FROM booking b
    JOIN penyewa p ON b.id_penyewa = p.id_penyewa
    JOIN mobil m ON b.id_mobil = m.id_mobil
    LEFT JOIN staff_penyewaan s ON b.id_staff = s.id_staff -- LEFT JOIN karena id_staff bisa NULL
    WHERE b.id_booking = ?
");
$query->bind_param("i", $id_booking);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data booking dengan ID: " . htmlspecialchars($id_booking) . " tidak ditemukan.");
}

// 3. Menghitung Durasi Sewa
$tgl_mulai = new DateTime($data['tanggal_mulai']);
$tgl_selesai = new DateTime($data['tanggal_selesai']);
$durasi = $tgl_mulai->diff($tgl_selesai)->days + 1;

// Fungsi bantuan untuk format status
function getStatusBadge($status) {
    $class = match($status) {
        'dipesan' => 'warning',
        'berjalan' => 'primary',
        'selesai' => 'success',
        'dibatalkan' => 'danger',
        default => 'secondary'
    };
    return "<span class='badge bg-{$class}'>" . ucfirst($status) . "</span>";
}

// 4. Inisialisasi tampilan Admin/Staff (sesuaikan include files)
include 'includes/header.php';
include 'includes/sidebar_admin.php'; // Asumsi ini sidebar admin
include 'includes/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Booking #<?= htmlspecialchars($id_booking); ?></h1>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Transaksi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th>Status</th>
                            <td><?= getStatusBadge($data['status']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td><?= date('d F Y H:i:s', strtotime($data['tanggal_dibuat'])); ?></td>
                        </tr>
                        <tr>
                            <th>Periode Sewa</th>
                            <td><?= date('d M Y', strtotime($data['tanggal_mulai'])); ?> s/d <?= date('d M Y', strtotime($data['tanggal_selesai'])); ?></td>
                        </tr>
                        <tr>
                            <th>Durasi Sewa</th>
                            <td><?= $durasi; ?> Hari</td>
                        </tr>
                        <tr>
                            <th>Staff yang Menangani</th>
                            <td><?= htmlspecialchars($data['nama_staff'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <td><?= htmlspecialchars(ucfirst($data['metode_pembayaran'] ?? 'Belum Bayar')); ?></td>
                        </tr>
                         <tr>
                            <th>Tanggal Bayar</th>
                            <td><?= $data['tanggal_bayar'] ? date('d F Y H:i:s', strtotime($data['tanggal_bayar'])) : 'Belum Bayar'; ?></td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>**Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?>**</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mb-4">
                <a href="data_penyewaan.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Detail Penyewa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr><th>Nama</th><td><?= htmlspecialchars($data['nama_penyewa']); ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($data['email_penyewa']); ?></td></tr>
                        <tr><th>No. HP</th><td><?= htmlspecialchars($data['hp_penyewa']); ?></td></tr>
                        <tr><th>Alamat</th><td><?= nl2br(htmlspecialchars($data['alamat_penyewa'])); ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold">Detail Mobil</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if (!empty($data['foto_mobil'])): ?>
                            <img src="<?= htmlspecialchars($data['foto_mobil']); ?>" alt="<?= htmlspecialchars($data['nama_mobil']); ?>" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <span class="text-muted">Tidak Ada Foto Mobil</span>
                        <?php endif; ?>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr><th>Mobil</th><td><?= htmlspecialchars($data['merek'] . ' ' . $data['nama_mobil']); ?></td></tr>
                        <tr><th>Tahun</th><td><?= htmlspecialchars($data['tahun']); ?></td></tr>
                        <tr><th>Transmisi</th><td><?= htmlspecialchars($data['transmisi'] ?? 'N/A'); ?></td></tr>
                        <tr><th>Harga Harian</th><td>Rp <?= number_format($data['harga_sewa_per_hari'], 0, ',', '.'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>