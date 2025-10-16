<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

include 'includes/header.php';
include 'includes/sidebar_admin.php';
include 'includes/topbar.php';

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM mobil WHERE id_mobil = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_mobil'];
    $merek = $_POST['merek'];
    $tahun = $_POST['tahun'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE mobil SET nama_mobil=?, merek=?, tahun=?, harga_sewa_per_hari=?, status=? WHERE id_mobil=?");
    $stmt->bind_param("ssissi", $nama, $merek, $tahun, $harga, $status, $id);

    if ($stmt->execute()) {
        header("Location: data_mobil.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data.</div>";
    }
}
?>

<h1 class="h3 mb-4 text-gray-800">Edit Mobil</h1>

<form method="POST">
    <div class="mb-3">
        <label>Nama Mobil</label>
        <input type="text" name="nama_mobil" class="form-control" value="<?= $row['nama_mobil'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Merek</label>
        <input type="text" name="merek" class="form-control" value="<?= $row['merek'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Tahun</label>
        <input type="number" name="tahun" class="form-control" value="<?= $row['tahun'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Harga Sewa / Hari</label>
        <input type="number" name="harga" class="form-control" value="<?= $row['harga_sewa_per_hari'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="tersedia" <?= $row['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
            <option value="disewa" <?= $row['status'] == 'disewa' ? 'selected' : '' ?>>Disewa</option>
            <option value="maintenance" <?= $row['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="data_mobil.php" class="btn btn-secondary">Kembali</a>
</form>

<?php include 'includes/footer.php'; ?>
