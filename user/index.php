<?php
session_start();
require_once '../config/db.php';

$conn = getConnection();
$query = "SELECT * FROM mobil";
$result = mysqli_query($conn, $query);

if (!$result) {
  die("Query gagal: " . mysqli_error($conn));
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental | Explore Cars</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: #fff;
    }

    /* Navbar */
    .navbar {
      background-color: rgba(0, 0, 0, 0.9);
    }

    /* Hero Section */
    .hero {
      position: relative;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      overflow: hidden;
    }

    .hero video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(50%);
      z-index: -1;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 700;
      color: #fff;
    }

    .hero p {
      font-size: 1.2rem;
      color: #ddd;
    }

    /* Mobil Cards */
    .car-section {
      background-color: #000;
      color: white;
      padding: 80px 0;
    }

    .car-card {
      background-color: #111;
      border-radius: 20px;
      overflow: hidden;
      transition: 0.3s;
    }

    .car-card:hover {
      transform: translateY(-5px);
      box-shadow: 0px 4px 15px rgba(255, 255, 255, 0.1);
    }

    .car-card img {
      width: 100%;
      height: 230px;
      object-fit: cover;
    }

    .car-info {
      padding: 20px;
    }

    .car-info h5 {
      color: #fff;
      font-weight: 600;
    }

    .car-info p {
      color: #bbb;
      font-size: 0.9rem;
    }

    .btn-rent {
      background-color: #fff;
      color: #000;
      border: none;
      font-weight: bold;
      width: 100%;
      transition: 0.3s;
    }

    .btn-rent:hover {
      background-color: #ff0000;
      color: #fff;
    }

    footer {
      background-color: #111;
      color: #999;
      text-align: center;
      padding: 20px 0;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">CarRental</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a href="#home" class="nav-link">Home</a></li>
          <li class="nav-item"><a href="#cars" class="nav-link">Cars</a></li>
          <li class="nav-item"><a href="riwayat.php" class="nav-link">History</a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-danger">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <video autoplay muted loop>
      <source src="../assets/startbootstrap-sb-admin-2-master/img/bag_prof.mp4" type="video/mp4">
    </video>
    <div class="text-center">
      <h1>Explore Most Popular Cars</h1>
      <p>Find and rent your dream car easily, anytime, anywhere.</p>
    </div>
  </section>

   <!-- Cars Section -->
<section class="car-section" id="cars">
  <div class="container">
    <h2 class="fw-bold text-center mb-5">Available Cars</h2>
    <div class="row g-4">
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="col-md-4">
          
      
<div class="car-card">
  <?php
    $foto = $row['foto_mobil'];

    // cek apakah foto dari URL atau dari folder lokal
    if (!preg_match('/^http/', $foto)) {
      $foto = "../assets/startbootstrap-sb-admin-2-master/img/" . $foto;
    }
  ?>
  <img src="<?php echo htmlspecialchars($foto); ?>" 
       alt="<?php echo htmlspecialchars($row['nama_mobil']); ?>" 
       class="img-fluid rounded-top">

  <div class="car-info">
    <h5><?php echo htmlspecialchars($row['nama_mobil']); ?></h5>
    <p>
      <?php echo htmlspecialchars($row['merek']); ?> • 
      <?php echo htmlspecialchars($row['transmisi']); ?> • 
      <?php echo htmlspecialchars($row['tahun']); ?>
    </p>
    <p><strong>Rp <?php echo number_format($row['harga_sewa_per_hari'], 0, ',', '.'); ?> / day</strong></p>
    <a href="sewa.php?id=<?php echo $row['id_mobil']; ?>" class="btn btn-rent">Rent Now</a>
  </div>
</div>

        </div>
      <?php } ?>
    </div>
  </div>
</section>



  <footer>
    <p>© 2025 CarRental | Designed with ❤ APIP DAN😘</p>
  </footer> APIP CAYANG AYA dan aya JUGAAA MANTEPPP

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
