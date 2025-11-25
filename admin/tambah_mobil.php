<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_mobil'];
    $merek = $_POST['merek'];
    $tahun = $_POST['tahun'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO mobil (nama_mobil, merek, tahun, harga_sewa_per_hari, status, tgl_ditambahkan)
                            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssiss", $nama, $merek, $tahun, $harga, $status);

    if ($stmt->execute()) {
        header("Location: data_mobil.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan data.</div>";
    }
}
?>

<h1 class="h3 mb-4 text-gray-800">Tambah Mobil</h1>

<form method="POST">
    <div class="mb-3">
        <label>Nama Mobil</label>
        <input type="text" name="nama_mobil" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Merek</label>
        <input type="text" name="merek" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tahun</label>
        <input type="number" name="tahun" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Harga Sewa / Hari</label>
        <input type="number" name="harga" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="tersedia">Tersedia</option>
            <option value="disewa">Disewa</option>
            <option value="maintenance">Maintenance</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="data_mobil.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include 'includes/footer.php'; ?>
