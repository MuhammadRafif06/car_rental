<?php
// admin/kelola_penyewaan.php

session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

if (!isset($_SESSION['id']) || ($_SESSION['role'] ?? '') !== 'staff') {
    header("Location: ../login.php");
    exit;
}

// include layout sb-admin-2
include 'includes/header.php';
include 'includes/sidebar_staff.php';
include 'includes/topbar.php';

// Fungsi bantuan untuk badge status
function getStatusBadge($status) {
    $status = strtolower($status);
    $class = match($status) {
        'dipesan' => 'bg-warning',
        'berjalan' => 'bg-info',
        'selesai' => 'bg-success',
        'dibatalkan' => 'bg-danger',
        default => 'bg-secondary'
    };
    return "<span class='badge {$class}'>" . strtoupper($status) . "</span>";
}

// Logika update status booking
if (isset($_POST['update_status'])) {
    $id_booking = intval($_POST['id_booking']);
    $status = $_POST['status'];
    $id_staff = intval($_SESSION['id']);
    
    // Periksa status lama untuk menentukan apakah tanggal_bayar perlu di-update
    $stmt_check = $conn->prepare("SELECT tanggal_bayar FROM booking WHERE id_booking = ?");
    $stmt_check->bind_param("i", $id_booking);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $data_lama = $result_check->fetch_assoc();
    $stmt_check->close();

    $update_bayar = false;
    $tanggal_bayar = null;

    // Jika status baru adalah 'berjalan' atau 'selesai' dan tanggal_bayar lama masih NULL
    if (($status === 'berjalan' || $status === 'selesai') && is_null($data_lama['tanggal_bayar'])) {
        $update_bayar = true;
        $tanggal_bayar = date('Y-m-d H:i:s');
    }

    if ($update_bayar) {
        $stmt = $conn->prepare("UPDATE booking SET status = ?, id_staff = ?, tanggal_bayar = ? WHERE id_booking = ?");
        $stmt->bind_param("sisi", $status, $id_staff, $tanggal_bayar, $id_booking);
    } else {
        $stmt = $conn->prepare("UPDATE booking SET status = ?, id_staff = ? WHERE id_booking = ?");
        $stmt->bind_param("sii", $status, $id_staff, $id_booking);
    }
    
    if ($stmt->execute()) {
        echo "<script>alert('✅ Status booking diperbarui!'); window.location.href='kelola_penyewaan.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal memperbarui status: " . $conn->error . "'); window.location.href='kelola_penyewaan.php';</script>";
    }
    $stmt->close();
    exit;
}

// ambil semua data booking
$query = "
    SELECT 
        b.id_booking, b.tanggal_mulai, b.tanggal_selesai, b.total_harga, b.status,
        p.nama_penyewa, 
        m.nama_mobil, m.merek 
    FROM booking b
    JOIN penyewa p ON b.id_penyewa = p.id_penyewa
    JOIN mobil m ON b.id_mobil = m.id_mobil
    ORDER BY b.tanggal_dibuat DESC
";
$result = $conn->query($query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Kelola Penyewaan Mobil</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Daftar Penyewaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" width="100%">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Penyewa</th>
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
                        if ($result && $result->num_rows > 0) {
                            $no = 1;
                            while($row = $result->fetch_assoc()) { ?>
                                <tr class="text-center">
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_penyewa']); ?></td>
                                    <td><?= htmlspecialchars($row['merek'] . ' ' . $row['nama_mobil']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
                                    <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                                    <td>
                                        <?= getStatusBadge($row['status']); ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="booking_detail.php?id=<?= $row['id_booking']; ?>" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <input type="hidden" name="id_booking" value="<?= $row['id_booking']; ?>">
                                            <select name="status" class="form-select form-select-sm w-auto">
                                                <option value="dipesan" <?= $row['status']=='dipesan'?'selected':''; ?>>Dipesan</option>
                                                <option value="berjalan" <?= $row['status']=='berjalan'?'selected':''; ?>>Berjalan</option>
                                                <option value="selesai" <?= $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
                                                <option value="dibatalkan" <?= $row['status']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr><td colspan="8" class="text-center text-muted">Belum ada data penyewaan</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>