<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Data Penyewaan (Booking)</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Daftar Semua Transaksi Penyewaan</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID Booking</th>
                            <th>Penyewa</th>
                            <th>Mobil</th>
                            <th>Periode Sewa</th>
                            <th>Total Harga</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->prepare("
                            SELECT 
                                b.*, p.nama_penyewa, m.nama_mobil, m.merek 
                            FROM booking b
                            JOIN penyewa p ON b.id_penyewa = p.id_penyewa
                            JOIN mobil m ON b.id_mobil = m.id_mobil
                            ORDER BY b.tanggal_dibuat DESC
                        ");
                        $query->execute();
                        $result = $query->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_booking = $row['id_booking'];
                                
                                // Logic untuk badge status
                                $statusClass = match($row['status']) {
                                    'dipesan' => 'warning',
                                    'berjalan' => 'primary',
                                    'selesai' => 'success',
                                    'dibatalkan' => 'danger',
                                    default => 'secondary'
                                };
                                
                                echo "<tr>";
                                echo "<td>{$id_booking}</td>";
                                echo "<td>" . htmlspecialchars($row['nama_penyewa']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['merek'] . ' ' . $row['nama_mobil']) . "</td>";
                                echo "<td>" . date('d/m/y', strtotime($row['tanggal_mulai'])) . " s/d " . date('d/m/y', strtotime($row['tanggal_selesai'])) . "</td>";
                                echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                                echo "<td>" . htmlspecialchars($row['metode_pembayaran'] ?? '-') . "</td>";
                                echo "<td><span class='badge bg-{$statusClass}'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td>
                                    <a href='booking_detail.php?id={$id_booking}' class='btn btn-sm btn-info'><i class='fas fa-eye'></i> Detail</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data penyewaan</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>