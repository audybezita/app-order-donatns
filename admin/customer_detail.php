<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM users WHERE id = :id AND role = "customer" LIMIT 1');
$stmt->execute([':id' => $id]);
$customer = $stmt->fetch();
if (!$customer) redirect('/donat_ns/admin/customers.php');

$ordersStmt = db()->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
$ordersStmt->execute([':user_id' => $id]);
$orders = $ordersStmt->fetchAll();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <h3>Detail Pelanggan</h3>
    <div class="summary-card mt-4">
        <p><strong>Nama:</strong> <?= e($customer['name']) ?></p>
        <p><strong>Email:</strong> <?= e($customer['email']) ?></p>
        <p><strong>WhatsApp:</strong> <?= e($customer['phone']) ?></p>
        <p><strong>Tanggal Registrasi:</strong> <?= e($customer['created_at']) ?></p>
    </div>

    <h4 class="mt-4">Riwayat Pesanan</h4>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Belum ada pesanan.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Nomor Pesanan</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr><td><?= e($order['order_number']) ?></td><td><?= e($order['created_at']) ?></td><td><?= formatCurrency($order['total_amount']) ?></td><td><?= e($order['order_status']) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
