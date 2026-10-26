<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    validateCsrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 1);
    addToCart($productId, $qty);
    redirect('/donat_ns/customer/dashboard.php?success=1');
}

$stmt = db()->query('SELECT * FROM products ORDER BY id ASC LIMIT 3');
$products = $stmt->fetchAll();
$settings = getSettings();
require_once __DIR__ . '/../includes/header_customer.php';
?>

<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge badge-soft-pink mb-3">UMKM Donat NS</span>
                <h1 class="fw-bold mb-3">Selamat Datang di Donat NS</h1>
                <p class="lead">Donat lembut, enak, dan cocok untuk menemani hari kamu.</p>
                <p>Pesan donat favoritmu sekarang dengan mudah dan praktis.</p>
                <div class="d-flex gap-2 mt-4">
                    <a href="#produk" class="btn btn-pink">Lihat Produk</a>
                    <a href="/donat_ns/customer/cart.php" class="btn btn-outline-pink">Lihat Keranjang</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="banner-card">
                    <img src="/donat_ns/uploads/products/donat-coklat.svg" alt="Banner Donat NS" class="img-fluid rounded-4 w-100">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="info-box">
                <h5>Rasa yang Nikmat</h5>
                <p>Donat kami dibuat dengan tekstur lembut dan rasa yang memanjakan.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <h5>Harga Ramah</h5>
                <p>Terjangkau untuk semua kalangan tanpa mengurangi kualitas produk.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <h5>Pemesanan Mudah</h5>
                <p>Pesan dengan cepat, aman, praktis, dan tanpa ribet.</p>
            </div>
        </div>
    </div>
</section>

<section id="produk" class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Produk Donat NS</h2>
        <span class="text-muted">Minimum pembelian: <?= e(getSetting('minimum_order', 6)) ?> pcs</span>
    </div>

    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card">
                    <img src="/donat_ns/uploads/products/<?= e($product['image'] ?: 'donat-coklat.svg') ?>" alt="<?= e($product['name']) ?>" class="product-image">
                    <div class="product-body">
                        <h4><?= e($product['name']) ?></h4>
                        <p><?= e($product['description']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price"><?= formatCurrency($product['price']) ?></span>
                            <span class="status-badge <?= $product['status'] === 'Tersedia' ? 'available' : 'sold-out' ?>"><?= e($product['status']) ?></span>
                        </div>

                        <?php if ($product['status'] === 'Tersedia'): ?>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" data-action="minus" data-product="<?= (int)$product['id'] ?>">-</button>
                                        <input type="number" name="qty" value="1" min="1" class="qty-input" data-product="<?= (int)$product['id'] ?>">
                                        <button type="button" class="qty-btn" data-action="plus" data-product="<?= (int)$product['id'] ?>">+</button>
                                    </div>
                                    <button type="submit" name="add_to_cart" class="btn btn-pink">Tambah</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary mt-3 w-100" disabled>Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="checkout-info">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div><strong>Minimum Pembelian</strong><br><span><?= e(getSetting('minimum_order', 6)) ?> pcs</span></div>
                    <div><strong>Pembayaran</strong><br><span>Cash / Tunai</span></div>
                    <div><strong>Keranjang</strong><br><span><?= (int)(getCartSummary()['total_items'] ?? 0) ?> pcs</span></div>
                    <a href="/donat_ns/customer/cart.php" class="btn btn-outline-pink">Lihat Keranjang</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
