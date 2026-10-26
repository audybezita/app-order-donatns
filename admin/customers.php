<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$search = trim($_GET['search'] ?? '');
$sql = 'SELECT u.*, COUNT(o.id) AS total_orders, COALESCE(SUM(o.total_amount),0) AS total_transaction FROM users u LEFT JOIN orders o ON o.user_id = u.id WHERE u.role = "customer"';
$params = [];
if ($search !== '') {
    $sql .= ' AND (u.name LIKE :search OR u.phone LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}
$sql .= ' GROUP BY u.id ORDER BY u.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <h3 class="mb-4">Kelola Pelanggan</h3>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-8"><input type="text" name="search" class="form-control" value="<?= e($search) ?>" placeholder="Cari nama atau WhatsApp pelanggan"></div>
        <div class="col-md-4"><button type="submit" class="btn btn-pink w-100">Cari</button></div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>WhatsApp</th><th>Tanggal Registrasi</th><th>Jumlah Pesanan</th><th>Total Transaksi</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= (int)$customer['id'] ?></td>
                        <td><?= e($customer['name']) ?></td>
                        <td><?= e($customer['email']) ?></td>
                        <td><?= e($customer['phone']) ?></td>
                        <td><?= e($customer['created_at']) ?></td>
                        <td><?= (int)$customer['total_orders'] ?></td>
                        <td><?= formatCurrency($customer['total_transaction']) ?></td>
                        <td><a href="/donat_ns/admin/customer_detail.php?id=<?= (int)$customer['id'] ?>" class="btn btn-sm btn-outline-pink">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
