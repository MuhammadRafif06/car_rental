<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

// Asumsi: Cek sesi admin sudah dilakukan di includes/topbar.php atau sejenisnya
include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Data Penyewa</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Daftar Akun Penyewa</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th>Tgl. Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->prepare("SELECT id_penyewa, nama_penyewa, email, no_hp, alamat, tgl_daftar FROM penyewa ORDER BY tgl_daftar DESC");
                        $query->execute();
                        $result = $query->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_penyewa = $row['id_penyewa'];
                                echo "<tr>";
                                echo "<td>{$id_penyewa}</td>";
                                echo "<td>" . htmlspecialchars($row['nama_penyewa']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['no_hp']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                                echo "<td>" . date('d M Y', strtotime($row['tgl_daftar'])) . "</td>";
                                echo "<td>
                                    <a href='penyewa_hapus.php?id={$id_penyewa}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus penyewa ini?\")'>
                                        <i class='fas fa-trash'></i> Hapus
                                    </a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted'>Belum ada data penyewa</td></tr>";
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