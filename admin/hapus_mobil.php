<?php
require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

$id = $_GET['id'] ?? 0;

if ($id) {
    $conn->query("DELETE FROM mobil WHERE id_mobil = $id");
}

header("Location: data_mobil.php");
exit;
?>
