<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    if (isset($_POST['remove_item'])) {
        removeCartItem((int)($_POST['product_id'] ?? 0));
        redirect('/donat_ns/customer/cart.php');
    }
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $productId => $qty) {
            updateCartItem((int)$productId, (int)$qty);
        }
        redirect('/donat_ns/customer/cart.php');
    }
}

$summary = getCartSummary();
require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4">Keranjang Belanja</h2>
            <?php if (empty($summary['items'])): ?>
                <div class="empty-state">
                    <h4>Keranjang masih kosong</h4>
                    <p>Silakan pilih produk donat favoritmu.</p>
                    <a href="/donat_ns/customer/dashboard.php" class="btn btn-pink">Belanja Sekarang</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="update_cart" value="1">
                    <?php foreach ($summary['items'] as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center g-3">
                                <div class="col-md-3">
                                    <img src="/donat_ns/uploads/products/<?= e($item['image'] ?: 'donat-coklat.svg') ?>" class="cart-image" alt="<?= e($item['name']) ?>">
                                </div>
                                <div class="col-md-3">
                                    <h5><?= e($item['name']) ?></h5>
                                    <p class="mb-0"><?= formatCurrency($item['price']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" data-action="minus" data-product="<?= (int)$item['product_id'] ?>">-</button>
                                        <input type="number" name="qty[<?= (int)$item['product_id'] ?>]" value="<?= (int)$item['quantity'] ?>" min="1" class="qty-input" data-product="<?= (int)$item['product_id'] ?>">
                                        <button type="button" class="qty-btn" data-action="plus" data-product="<?= (int)$item['product_id'] ?>">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><?= formatCurrency($item['subtotal']) ?></strong>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="submit" name="remove_item" value="1" class="btn btn-link text-danger">Hapus</button>
                                    <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="/donat_ns/customer/dashboard.php" class="btn btn-outline-pink">Kembali Belanja</a>
                        <button type="submit" class="btn btn-pink">Update Keranjang</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h4>Ringkasan Belanja</h4>
                <div class="d-flex justify-content-between"><span>Total Produk</span><strong><?= (int)$summary['total_items'] ?> pcs</strong></div>
                <div class="d-flex justify-content-between"><span>Subtotal</span><strong><?= formatCurrency($summary['subtotal']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Ongkir</span><strong><?= formatCurrency($summary['shipping_cost']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Minimum Pembelian</span><strong><?= (int)$summary['minimum_order'] ?> pcs</strong></div>
                <hr>
                <div class="d-flex justify-content-between total-price"><span>Total</span><strong><?= formatCurrency($summary['total_amount']) ?></strong></div>

                <?php if ($summary['total_items'] < (int)$summary['minimum_order']): ?>
                    <div class="alert alert-warning mt-3">Minimal pembelian <?= (int)$summary['minimum_order'] ?> pcs. Saat ini Anda membeli <?= (int)$summary['total_items'] ?> pcs.</div>
                <?php endif; ?>

                <?php if (isCustomerLoggedIn()): ?>
                    <?php if ($summary['can_checkout']): ?>
                        <a href="/donat_ns/customer/checkout.php" class="btn btn-pink w-100 mt-3">Checkout</a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 mt-3" disabled>Checkout</button>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/donat_ns/customer/login.php" class="btn btn-pink w-100 mt-3">Login untuk Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
