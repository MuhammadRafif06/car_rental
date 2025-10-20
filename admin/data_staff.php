<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Data Staff Penyewaan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Daftar Staff</h6>
            <a href="staff_tambah.php" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Tambah Staff
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nama Staff</th>
                            <th>Email</th>
                            <th>Tgl. Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->prepare("SELECT id_staff, nama_staff, email, tgl_dibuat FROM staff_penyewaan ORDER BY tgl_dibuat DESC");
                        $query->execute();
                        $result = $query->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_staff = $row['id_staff'];
                                echo "<tr>";
                                echo "<td>{$id_staff}</td>";
                                echo "<td>" . htmlspecialchars($row['nama_staff']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . date('d M Y', strtotime($row['tgl_dibuat'])) . "</td>";
                                echo "<td>
                                    <a href='staff_edit.php?id={$id_staff}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Edit</a>
                                    <a href='staff_hapus.php?id={$id_staff}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus staff ini?\")'><i class='fas fa-trash'></i> Hapus</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data staff</td></tr>";
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