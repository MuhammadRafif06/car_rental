<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

$id = $_GET['id'] ?? null;
if (!$id) die("ID mobil tidak ditemukan.");

$stmt = $conn->prepare("DELETE FROM mobil WHERE id_mobil = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo "<script>alert('🚗 Data mobil berhasil dihapus!'); window.location.href='data_mobil.php';</script>";
} else {
  echo "Gagal hapus: " . $stmt->error;
}
