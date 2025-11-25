<?php
require_once(__DIR__ . '/../config/db.php');
// Pastikan fungsi getConnection() mengembalikan objek koneksi (misalnya mysqli)
$conn = getConnection(); 

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Data Mobil</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Daftar Mobil</h6>
            <a href="tambah_mobil.php" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Tambah Mobil
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nama Mobil</th>
                            <th>Merek</th>
                            <th>Tahun</th>
                            <th>Harga / Hari</th>
                            <th>Status</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM mobil ORDER BY id_mobil DESC");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                
                                // Variabel untuk Gambar
                                $gambar_path = !empty($row['foto_mobil']) ? htmlspecialchars($row['foto_mobil']) : '';
                                
                                // Variabel untuk Link Edit/Hapus
                                $id_mobil = $row['id_mobil'];
                                $link_edit = "edit_mobil.php?id=$id_mobil";
                                $link_hapus = "hapus_mobil.php?id=$id_mobil";
                                $konfirmasi_hapus = "return confirm('Yakin mau hapus?')";

                                echo "<tr>
                                    <td>{$id_mobil}</td>
                                    <td>{$row['nama_mobil']}</td>
                                    <td>{$row['merek']}</td>
                                    <td>{$row['tahun']}</td>
                                    <td>Rp " . number_format($row['harga_sewa_per_hari'], 0, ',', '.') . "</td>
                                    <td>{$row['status']}</td>
                                    
                                    <td>";
                                    
                                    if (!empty($gambar_path)) {
                                        // Jika foto_mobil terisi, tampilkan gambar.
                                        // Catatan: Jika foto_mobil berupa URL (seperti di data DB kamu), '../' mungkin tidak perlu.
                                        // Gunakan $gambar_path langsung.
                                        echo "<a href='{$gambar_path}' target='_blank'>";
                                        echo "<img src='{$gambar_path}' width='80' class='rounded'>";
                                        echo "</a>";
                                    } else {
                                        // Jika foto_mobil kosong, tampilkan placeholder.
                                        echo "N/A";
                                    }
                                    
                                    echo "</td>
                                    
                                    <td>
                                        <a href='{$link_edit}' class='btn btn-sm btn-warning'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <a href='{$link_hapus}' class='btn btn-sm btn-danger' onclick='{$konfirmasi_hapus}'>
                                            <i class='fas fa-trash'></i>
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada data mobil</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>