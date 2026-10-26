<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

$sql = 'SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id';
$where = [];
$params = [];
if ($search !== '') {
    $where[] = '(o.order_number LIKE :search OR u.name LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}
if ($status !== '') {
    $where[] = 'o.order_status = :status';
    $params[':status'] = $status;
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY o.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <h3 class="mb-4">Kelola Pesanan</h3>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Cari nomor pesanan atau nama customer" value="<?= e($search) ?>"></div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="Pesanan Dibuat" <?= $status === 'Pesanan Dibuat' ? 'selected' : '' ?>>Pesanan Dibuat</option>
                <option value="Pesanan Dikonfirmasi Admin" <?= $status === 'Pesanan Dikonfirmasi Admin' ? 'selected' : '' ?>>Pesanan Dikonfirmasi Admin</option>
                <option value="Pesanan Sedang Diproses" <?= $status === 'Pesanan Sedang Diproses' ? 'selected' : '' ?>>Pesanan Sedang Diproses</option>
                <option value="Pesanan Sedang Disiapkan" <?= $status === 'Pesanan Sedang Disiapkan' ? 'selected' : '' ?>>Pesanan Sedang Disiapkan</option>
                <option value="Pesanan Dalam Pengiriman" <?= $status === 'Pesanan Dalam Pengiriman' ? 'selected' : '' ?>>Pesanan Dalam Pengiriman</option>
                <option value="Pesanan Selesai" <?= $status === 'Pesanan Selesai' ? 'selected' : '' ?>>Pesanan Selesai</option>
                <option value="Pesanan Dibatalkan" <?= $status === 'Pesanan Dibatalkan' ? 'selected' : '' ?>>Pesanan Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-pink w-100" type="submit">Filter</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>Nomor Pesanan</th><th>Customer</th><th>WhatsApp</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['order_number']) ?></td>
                        <td><?= e($order['customer_name']) ?></td>
                        <td><?= e($order['phone']) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                        <td><?= formatCurrency($order['total_amount']) ?></td>
                        <td><?= e($order['order_status']) ?></td>
                        <td><a href="/donat_ns/admin/order_detail.php?id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-pink">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
