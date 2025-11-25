<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit;
}

require_once '../config/db.php';
require_once './includes/header.php';
require_once './includes/sidebar_admin.php';
?>

<!-- Content Wrapper -->                
<div class="d-flex flex-column" id="content-wrapper">
    <div id="content" class="p-4">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <?php
                            $conn = getConnection();
                            $res = $conn->query("SELECT COUNT(*) AS total FROM mobil");
                            $row = $res->fetch_assoc();
                            ?>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Mobiel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $row['total'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
