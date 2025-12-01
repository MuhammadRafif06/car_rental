<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    /* ===== BODY & VIDEO BACKGROUND ===== */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: 'Poppins', sans-serif;
    }

    /* Video Fullscreen */
    video#bg-video {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover; /* biar video penuh dan gak ketarik */
      z-index: -1; /* biar di belakang form */
      filter: brightness(55%); /* agak gelap biar form kebaca */
    }

    /* ===== CARD LOGIN ===== */
    .card {
      background: rgba(20, 20, 20, 0.7); /* lembut abu kehitaman */
      backdrop-filter: blur(8px);
      color: white;
      border-radius: 15px;
      border: 1px solid rgba(12, 4, 4, 0.15);
      box-shadow: 0px 0px 25px rgba(0,0,0,0.4);
      padding: 25px;
    }

    /* Input Field */
.form-control {
  background-color: rgba(30, 13, 13, 0.15); /* putih transparan lembut */
  border: 1px solid rgba(5, 3, 3, 0.3);
  color: #000000ff; /* biar teks-nya putih */
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-control::placeholder {
  color: rgba(6, 0, 0, 0.6); /* placeholder-nya abu lembut */
}

.form-control:focus {
  background-color: rgba(255, 255, 255, 0.25);
  border: 1px solid #020101ff;
  box-shadow: 0 0 10px rgba(38, 20, 20, 0.5);
  color: #0e0606ff;
}

    /* Tombol Login */
    .btn-primary {
      background-color: #180505ff;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      width: 100%;
      padding: 10px;
    }

    .btn-primary:hover {
      background-color: #100a0aff;
      color: #000;
      transition: 0.3s;
    }

    /* Link Register */
    .nav-link {
      color: #0c0101ff;
      font-size: 0.9rem;
    }

    .nav-link:hover {
      color: #b91b1bff;
    }

    /* ===== TENGAHIN FORM LOGIN ===== */
    .login-container {
      height: 100vh; /* full layar */
      display: flex;
      justify-content: center;
      align-items: center;
    }
  </style>
</head>

<body>
  <!-- 🎥 Background Video -->
  <video autoplay muted loop id="bg-video">
    <source src="../assets/startbootstrap-sb-admin-2-master/img/vid_car.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>

  <!-- LOGIN FORM -->
  <section class="login-container">
    <div class="col-10 col-sm-8 col-md-5 col-lg-4">
      <div class="card border-0 shadow-lg">
        <div class="card-body">
          <h3 class="text-center mb-4 fw-bold">Login to Car Rental</h3>

          <form action="login_process.php" method="post">
            <input type="text" name="email" class="form-control my-3 py-2" placeholder="Email" required>
            <input type="password" name="password" class="form-control my-3 py-2" placeholder="Password" required>

            <div class="text-center mt-3">
              <button class="btn btn-primary">Login</button>
              <a href="register.php" class="nav-link mt-2">Don't have an account? Register now</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>
</html>