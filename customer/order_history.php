<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
ensureCustomerLogin();

$userId = $_SESSION['customer_id'];
$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
$stmt->execute([':user_id' => $userId]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <h2 class="fw-bold mb-4">Riwayat Pesanan</h2>
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <h4>Belum ada pesanan</h4>
            <p>Silakan mulai pesan donat favoritmu sekarang.</p>
            <a href="/donat_ns/customer/dashboard.php" class="btn btn-pink">Belanja Sekarang</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nomor Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= e($order['order_number']) ?></td>
                            <td><?= e($order['created_at']) ?></td>
                            <td><?= formatCurrency($order['total_amount']) ?></td>
                            <td><?= e($order['order_status']) ?></td>
                            <td><?= e($order['payment_method']) ?></td>
                            <td><a href="/donat_ns/customer/order_detail.php?id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-pink">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
