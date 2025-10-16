<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

// include layout dashboard
include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<h1 class="h3 mb-4 text-gray-800">Data Mobil</h1>

<a href="tambah_mobil.php" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Tambah Mobil
</a>


<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nama Mobil</th>
                        <th>Merek</th>
                        <th>Tahun</th>
                        <th>Harga Sewa / Hari</th>
                        <th>Status</th>
                        <th>Tanggal Ditambahkan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM mobil");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id_mobil']}</td>
                                <td>{$row['nama_mobil']}</td>
                                <td>{$row['merek']}</td>
                                <td>{$row['tahun']}</td>
                                <td>Rp " . number_format($row['harga_sewa_per_hari'], 0, ',', '.') . "</td>
                                <td>{$row['status']}</td>
                                <td>{$row['tgl_ditambahkan']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>Belum ada data mobil</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
