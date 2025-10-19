<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$conn = getConnection();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'staff') {
  header("Location: ../login.php");
  exit;
}

// include layout sb-admin-2
include 'includes/header.php';
include 'includes/sidebar_staff.php';
include 'includes/topbar.php';

// update status booking
if (isset($_POST['update_status'])) {
  $id_booking = intval($_POST['id_booking']);
  $status = $_POST['status'];
  $id_staff = intval($_SESSION['id']);

  $stmt = $conn->prepare("UPDATE booking SET status = ?, id_staff = ? WHERE id_booking = ?");
  $stmt->bind_param("sii", $status, $id_staff, $id_booking);
  $stmt->execute();

  echo "<script>alert('✅ Status booking diperbarui!'); window.location.href='kelola_penyewaan.php';</script>";
  exit;
}

// ambil semua data booking
$query = "
  SELECT b.*, p.nama_penyewa, m.nama_mobil 
  FROM booking b
  JOIN penyewa p ON b.id_penyewa = p.id_penyewa
  JOIN mobil m ON b.id_mobil = m.id_mobil
  ORDER BY b.tanggal_dibuat DESC
";
$result = $conn->query($query);
?>

<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Kelola Penyewaan Mobil</h1>

  <div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
      <h6 class="m-0 font-weight-bold">Daftar Penyewaan</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle" width="100%">
          <thead class="table-primary text-center">
            <tr>
              <th>No</th>
              <th>Penyewa</th>
              <th>Mobil</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Selesai</th>
              <th>Total Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result->num_rows > 0) {
              $no = 1;
              while($row = $result->fetch_assoc()) { ?>
                <tr class="text-center">
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['nama_penyewa']); ?></td>
                  <td><?= htmlspecialchars($row['nama_mobil']); ?></td>
                  <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
                  <td><?= htmlspecialchars($row['tanggal_selesai']); ?></td>
                  <td>Rp <?= number_format($row['total_harga'],0,',','.'); ?></td>
                  <td>
                    <span class="badge 
                      <?= $row['status']=='dipesan'?'bg-warning':
                          ($row['status']=='berjalan'?'bg-info':
                          ($row['status']=='selesai'?'bg-success':
                          ($row['status']=='dibatalkan'?'bg-danger':'bg-secondary'))); ?>">
                      <?= strtoupper($row['status']); ?>
                    </span>
                  </td>
                  <td>
                    <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                      <input type="hidden" name="id_booking" value="<?= $row['id_booking']; ?>">
                      <select name="status" class="form-select form-select-sm w-auto">
                        <option value="dipesan" <?= $row['status']=='dipesan'?'selected':''; ?>>Dipesan</option>
                        <option value="berjalan" <?= $row['status']=='berjalan'?'selected':''; ?>>Berjalan</option>
                        <option value="selesai" <?= $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
                        <option value="dibatalkan" <?= $row['status']=='dibatalkan'?'selected':''; ?>>Dibatalkan</option>
                      </select>
                      <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                    </form>
                  </td>
                </tr>
            <?php } 
            } else { ?>
              <tr><td colspan="8" class="text-center text-muted">Belum ada data penyewaan</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
