

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITE311 Project - About</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311 ALBERCA</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('contact') ?>">Contact</a>
                    </li>
                </ul>
                <a class="btn btn-primary me-3" href="<?= base_url('login')?>" role="button">Login</a>
                <a class="btn btn-primary" href="<?= base_url('register')?>" role="button">Register</a>
            </div>
        </div>
    </nav>

    <!-- About Section -->
    <section class="text-center py-5">
        <div class="container">
            <h2>About This Project</h2>
            <p>This project is created for ITE311 to demonstrate CodeIgniter and Bootstrap integration.</p>
        </div>
    </section>

</body>
</html>
