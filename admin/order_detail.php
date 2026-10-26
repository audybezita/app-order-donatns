<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$orderId = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT o.*, u.name AS customer_name, u.email FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = :id LIMIT 1');
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();
if (!$order) redirect('/donat_ns/admin/orders.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    validateCsrf();
    $newStatus = trim($_POST['status'] ?? 'Pesanan Dibuat');
    $desc = trim($_POST['description'] ?? 'Status pesanan diperbarui oleh admin.');
    db()->prepare('UPDATE orders SET order_status = :status, updated_at = NOW() WHERE id = :id')->execute([':status' => $newStatus, ':id' => $orderId]);
    db()->prepare('INSERT INTO order_status_history (order_id, status, description) VALUES (:order_id, :status, :description)')->execute([':order_id' => $orderId, ':status' => $newStatus, ':description' => $desc]);
    redirect('/donat_ns/admin/order_detail.php?id=' . $orderId);
}

$itemsStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
$itemsStmt->execute([':order_id' => $orderId]);
$items = $itemsStmt->fetchAll();

$statusStmt = db()->prepare('SELECT * FROM order_status_history WHERE order_id = :order_id ORDER BY created_at ASC');
$statusStmt->execute([':order_id' => $orderId]);
$history = $statusStmt->fetchAll();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="summary-card">
                <h4>Detail Pesanan <?= e($order['order_number']) ?></h4>
                <div class="row">
                    <div class="col-md-6"><p><strong>Customer:</strong> <?= e($order['customer_name']) ?></p><p><strong>Email:</strong> <?= e($order['email']) ?></p><p><strong>WhatsApp:</strong> <?= e($order['phone']) ?></p></div>
                    <div class="col-md-6"><p><strong>Tanggal:</strong> <?= e($order['created_at']) ?></p><p><strong>Status:</strong> <?= e($order['order_status']) ?></p><p><strong>Metode Pembayaran:</strong> <?= e($order['payment_method']) ?></p></div>
                </div>
                <p><strong>Alamat:</strong> <?= e($order['address']) ?></p>
                <p><strong>Catatan:</strong> <?= e($order['notes'] ?: '-') ?></p>
                <h5 class="mt-4">Produk</h5>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-2"><span><?= e($item['product_name']) ?> x<?= (int)$item['quantity'] ?></span><strong><?= formatCurrency($item['subtotal']) ?></strong></div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><strong><?= formatCurrency($order['subtotal']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Ongkir</span><strong><?= formatCurrency($order['shipping_cost']) ?></strong></div>
                <div class="d-flex justify-content-between total-price"><span>Total</span><strong><?= formatCurrency($order['total_amount']) ?></strong></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h5>Ubah Status Pesanan</h5>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="update_status" value="1">
                    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select">
                        <option value="Pesanan Dibuat" <?= $order['order_status']==='Pesanan Dibuat'?'selected':'' ?>>Pesanan Dibuat</option>
                        <option value="Pesanan Dikonfirmasi Admin" <?= $order['order_status']==='Pesanan Dikonfirmasi Admin'?'selected':'' ?>>Pesanan Dikonfirmasi Admin</option>
                        <option value="Pesanan Sedang Diproses" <?= $order['order_status']==='Pesanan Sedang Diproses'?'selected':'' ?>>Pesanan Sedang Diproses</option>
                        <option value="Pesanan Sedang Disiapkan" <?= $order['order_status']==='Pesanan Sedang Disiapkan'?'selected':'' ?>>Pesanan Sedang Disiapkan</option>
                        <option value="Pesanan Dalam Pengiriman" <?= $order['order_status']==='Pesanan Dalam Pengiriman'?'selected':'' ?>>Pesanan Dalam Pengiriman</option>
                        <option value="Pesanan Selesai" <?= $order['order_status']==='Pesanan Selesai'?'selected':'' ?>>Pesanan Selesai</option>
                        <option value="Pesanan Dibatalkan" <?= $order['order_status']==='Pesanan Dibatalkan'?'selected':'' ?>>Pesanan Dibatalkan</option>
                    </select></div>
                    <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="description" rows="3" class="form-control"></textarea></div>
                    <button type="submit" class="btn btn-pink w-100">Simpan</button>
                </form>
            </div>

            <div class="summary-card mt-4">
                <h5>Riwayat Status</h5>
                <?php foreach ($history as $row): ?>
                    <div class="timeline-item"><div class="timeline-dot"></div><div><strong><?= e($row['status']) ?></strong><div class="text-muted small"><?= e($row['created_at']) ?></div><p><?= e($row['description']) ?></p></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
