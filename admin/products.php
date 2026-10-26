<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$stmt = db()->query('SELECT * FROM products ORDER BY id ASC');
$products = $stmt->fetchAll();
require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <h3 class="mb-4">Kelola Produk</h3>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-lg-4">
                <div class="product-admin-card">
                    <img src="/donat_ns/uploads/products/<?= e($product['image'] ?: 'donat-coklat.svg') ?>" alt="<?= e($product['name']) ?>">
                    <div class="mt-3">
                        <h5><?= e($product['name']) ?></h5>
                        <p><?= e($product['description']) ?></p>
                        <div class="d-flex justify-content-between"><span>Harga</span><strong><?= formatCurrency($product['price']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Status</span><strong><?= e($product['status']) ?></strong></div>
                    </div>
                    <a href="/donat_ns/admin/product_edit.php?id=<?= (int)$product['id'] ?>" class="btn btn-pink mt-3 w-100">Edit Produk</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
