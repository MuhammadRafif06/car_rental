<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Penyewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <section>
        <div class="container mt-5 pt-5">
            <div class="row">
                <div class="col-12 col-sm-8 col-md-6 m-auto">
                    <div class="card shadow">
                        <div class="card-body">
                            <h4 class="text-center mb-4">register penyewa</h4>
                            <form action="register_process.php" method="post">
                                <input type="text" name="nama_penyewa" class="form-control my-2" placeholder="nama lengkap" required>
                                <input type="email" name="email" class="form-control my-2" placeholder="email" required>
                                <input type="text" name="no_hp" class="form-control my-2" placeholder="nomor hp" required>
                                <textarea name="alamat" class="form-control my-2" placeholder="alamat lengkap" required></textarea>
                                <input type="password" name="password" class="form-control my-2" placeholder="password" required>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success">register</button>
                                    <p class="mt-3">
                                        already have account? <a href="login.php" class="nav-link d-inline p-0">login</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
