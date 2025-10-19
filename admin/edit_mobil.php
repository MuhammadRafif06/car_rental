<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

$id = $_GET['id'] ?? null;
if (!$id) die("ID mobil tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama_mobil'];
  $merek = $_POST['merek'];
  $tahun = $_POST['tahun'];
  $harga = $_POST['harga_sewa_per_hari'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE mobil SET nama_mobil=?, merek=?, tahun=?, harga_sewa_per_hari=?, status=? WHERE id_mobil=?");
  $stmt->bind_param("sssisi", $nama, $merek, $tahun, $harga, $status, $id);

  if ($stmt->execute()) {
    echo "<script>alert('✅ Data mobil berhasil diperbarui!'); window.location.href='data_mobil.php';</script>";
  } else {
    echo "Gagal update: " . $stmt->error;
  }
  exit;
}

$result = $conn->query("SELECT * FROM mobil WHERE id_mobil=$id");
$mobil = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Mobil</title>
  <link href="../assets/startbootstrap-sb-admin-2-master/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow p-4 col-md-6 mx-auto">
    <h4 class="mb-3 text-center">Edit Mobil</h4>
    <form method="POST">
      <div class="mb-3">
        <label>Nama Mobil</label>
        <input type="text" name="nama_mobil" class="form-control" value="<?= $mobil['nama_mobil'] ?>" required>
      </div>
      <div class="mb-3">
        <label>Merek</label>
        <input type="text" name="merek" class="form-control" value="<?= $mobil['merek'] ?>" required>
      </div>
      <div class="mb-3">
        <label>Tahun</label>
        <input type="number" name="tahun" class="form-control" value="<?= $mobil['tahun'] ?>" required>
      </div>
      <div class="mb-3">
        <label>Harga / Hari</label>
        <input type="number" name="harga_sewa_per_hari" class="form-control" value="<?= $mobil['harga_sewa_per_hari'] ?>" required>
      </div>
      <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-select">
          <option value="tersedia" <?= $mobil['status']=='tersedia'?'selected':''; ?>>Tersedia</option>
          <option value="disewa" <?= $mobil['status']=='disewa'?'selected':''; ?>>Disewa</option>
          <option value="perawatan" <?= $mobil['status']=='perawatan'?'selected':''; ?>>Perawatan</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
    </form>
  </div>
</div>
</body>
</html>
