<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITE311 Project - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- Simple Navbar -->
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

    <!-- Hero Section -->
    <section class="text-center py-5 bg-light">
        <div class="container">
            <h1 class="display-4 fw-bold">Welcome to ITE311 Project</h1>
            <p class="lead">This is a simple homepage with Home, About, and Contact navigation.</p>
        </di
