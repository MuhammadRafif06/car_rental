<?php
// admin/hapus_mobil.php

require_once(__DIR__ . '/../config/db.php');
$conn = getConnection();

$id_mobil = $_GET['id'] ?? null; // Mengganti $id menjadi $id_mobil untuk kejelasan
if (!$id_mobil) {
    // Memberikan pesan error yang lebih informatif jika ID hilang
    die("ID mobil tidak ditemukan. Proses penghapusan dibatalkan.");
}

// 1. Mulai Transaksi: Memastikan semua query berhasil atau gagal bersamaan
$conn->begin_transaction();

try {
    // 2. HAPUS BOOKING TERKAIT (Mengatasi Foreign Key Constraint)
    // Hapus semua baris di tabel 'booking' yang terkait dengan id_mobil ini
    $stmt_booking = $conn->prepare("DELETE FROM booking WHERE id_mobil = ?");
    $stmt_booking->bind_param("i", $id_mobil);
    $stmt_booking->execute();
    $stmt_booking->close();

    // 3. HAPUS MOBIL ITU SENDIRI
    $stmt_mobil = $conn->prepare("DELETE FROM mobil WHERE id_mobil = ?");
    $stmt_mobil->bind_param("i", $id_mobil);

    if ($stmt_mobil->execute()) {
        // 4. COMMIT: Jika kedua operasi DELETE berhasil
        $conn->commit();
        $stmt_mobil->close();
        
        echo "<script>alert('🚗 Data mobil dan semua booking terkait berhasil dihapus!'); window.location.href='data_mobil.php';</script>";
        exit();
    } else {
        // 5. ROLLBACK: Jika ada masalah pada penghapusan mobil
        $conn->rollback();
        $stmt_mobil->close();
        
        echo "<script>alert('Gagal menghapus mobil: " . $conn->error . "'); window.location.href='data_mobil.php';</script>";
    }
    
} catch (mysqli_sql_exception $e) {
    // Tangani error jika terjadi exception database (misalnya koneksi putus)
    $conn->rollback();
    echo "<script>alert('Terjadi error database: " . $e->getMessage() . "'); window.location.href='data_mobil.php';</script>";
}

$conn->close();
?>