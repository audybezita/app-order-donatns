<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
ensureCustomerLogin();

$orderId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :user_id LIMIT 1');
$stmt->execute([':id' => $orderId, ':user_id' => $_SESSION['customer_id']]);
$order = $stmt->fetch();
if (!$order) {
    redirect('/donat_ns/customer/order_history.php');
}

$itemsStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
$itemsStmt->execute([':order_id' => $orderId]);
$items = $itemsStmt->fetchAll();

$statusStmt = db()->prepare('SELECT * FROM order_status_history WHERE order_id = :order_id ORDER BY created_at ASC');
$statusStmt->execute([':order_id' => $orderId]);
$history = $statusStmt->fetchAll();

require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Detail Pesanan</h2>
        <span class="badge bg-pink"><?= e($order['order_number']) ?></span>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="summary-card">
                <h5>Informasi Pemesanan</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Penerima:</strong> <?= e($order['recipient_name']) ?></p>
                        <p><strong>WhatsApp:</strong> <?= e($order['phone']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal:</strong> <?= e($order['created_at']) ?></p>
                        <p><strong>Status:</strong> <?= e($order['order_status']) ?></p>
                    </div>
                </div>
                <p><strong>Alamat:</strong> <?= e($order['address']) ?></p>
                <p><strong>Catatan:</strong> <?= e($order['notes'] ?: '-') ?></p>
            </div>
            <div class="summary-card mt-4">
                <h5>Produk Dipesan</h5>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span><?= e($item['product_name']) ?> x<?= (int)$item['quantity'] ?></span>
                        <strong><?= formatCurrency($item['subtotal']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><strong><?= formatCurrency($order['subtotal']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Ongkir</span><strong><?= formatCurrency($order['shipping_cost']) ?></strong></div>
                <div class="d-flex justify-content-between total-price"><span>Total</span><strong><?= formatCurrency($order['total_amount']) ?></strong></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="summary-card">
                <h5>Status Pengiriman</h5>
                <?php foreach ($history as $row): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div>
                            <strong><?= e($row['status']) ?></strong>
                            <div class="text-muted small"><?= e($row['created_at']) ?></div>
                            <p><?= e($row['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
