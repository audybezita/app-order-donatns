<?php require_once __DIR__ . '/functions.php'; $settings = getSettings(); $cart = getCartSummary(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['shop_name'] ?? 'Donat NS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/donat_ns/assets/css/customer.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-pink" href="/donat_ns/customer/dashboard.php">
            <img src="/donat_ns/uploads/products/donat-coklat.svg" alt="Logo Donat NS" class="brand-logo">
            <?= e($settings['shop_name'] ?? 'Donat NS') ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCustomer">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navCustomer">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="/donat_ns/customer/dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/donat_ns/customer/dashboard.php#produk">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="/donat_ns/customer/cart.php">Keranjang <span class="badge bg-pink"><?= (int)($cart['total_items'] ?? 0) ?></span></a></li>
                <li class="nav-item"><a class="nav-link" href="/donat_ns/customer/order_history.php">Pesanan</a></li>
                <?php if (isCustomerLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="/donat_ns/customer/profile.php">Akun</a></li>
                    <li class="nav-item"><a class="btn btn-outline-pink ms-2" href="/donat_ns/customer/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-outline-pink ms-2" href="/donat_ns/customer/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
