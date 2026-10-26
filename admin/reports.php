<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$range = $_GET['range'] ?? 'bulan_ini';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = '1=1';
$params = [];

if ($from && $to) {
    $where = 'DATE(o.created_at) BETWEEN :from AND :to';
    $params = [':from' => $from, ':to' => $to];
} else {
    switch ($range) {
        case 'hari_ini': $where = 'DATE(o.created_at) = CURDATE()'; break;
        case 'kemarin': $where = 'DATE(o.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)'; break;
        case 'minggu_ini': $where = 'YEARWEEK(o.created_at,1) = YEARWEEK(CURDATE(),1)'; break;
        case 'bulan_ini': $where = 'MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())'; break;
        case 'tahun_ini': $where = 'YEAR(o.created_at) = YEAR(CURDATE())'; break;
        default: $where = '1=1';
    }
}

$stmt = db()->prepare('SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE ' . $where . ' ORDER BY o.created_at DESC');
$stmt->execute($params);
$orders = $stmt->fetchAll();

$totalSales = 0; $totalItems = 0; $totalShipping = 0; $completed = 0; $cancelled = 0;
foreach ($orders as $order) {
    $totalSales += (float)$order['total_amount'];
    $totalItems += (int)$order['total_items'];
    $totalShipping += (float)$order['shipping_cost'];
    if ($order['order_status'] === 'Pesanan Selesai') $completed++;
    if ($order['order_status'] === 'Pesanan Dibatalkan') $cancelled++;
}

$top = db()->query('SELECT product_name, SUM(quantity) AS sold FROM order_items GROUP BY product_name ORDER BY sold DESC LIMIT 1')->fetch();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <h3 class="mb-4">Laporan Penjualan</h3>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-2"><select name="range" class="form-select"><option value="hari_ini" <?= $range === 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option><option value="kemarin" <?= $range === 'kemarin' ? 'selected' : '' ?>>Kemarin</option><option value="minggu_ini" <?= $range === 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option><option value="bulan_ini" <?= $range === 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option><option value="tahun_ini" <?= $range === 'tahun_ini' ? 'selected' : '' ?>>Tahun Ini</option><option value="custom" <?= $range === 'custom' ? 'selected' : '' ?>>Rentang Tanggal</option></select></div>
        <div class="col-md-3"><input type="date" name="from" class="form-control" value="<?= e($from) ?>"></div>
        <div class="col-md-3"><input type="date" name="to" class="form-control" value="<?= e($to) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-pink w-100">Filter</button></div>
        <div class="col-md-2"><a href="/donat_ns/admin/reports.php?export=1" class="btn btn-outline-pink w-100">Export CSV</a></div>
    </form>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="stat-card"><div><span>Total Penjualan</span><h4><?= formatCurrency($totalSales) ?></h4></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div><span>Total Pesanan</span><h4><?= count($orders) ?></h4></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div><span>Total Produk Terjual</span><h4><?= (int)$totalItems ?></h4></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div><span>Produk Terlaris</span><h4><?= e($top['product_name'] ?? '-') ?></h4></div></div></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>Tanggal</th><th>Nomor Pesanan</th><th>Nama Customer</th><th>Jumlah Produk</th><th>Subtotal</th><th>Ongkir</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['created_at']) ?></td>
                        <td><?= e($order['order_number']) ?></td>
                        <td><?= e($order['customer_name']) ?></td>
                        <td><?= (int)$order['total_items'] ?></td>
                        <td><?= formatCurrency($order['subtotal']) ?></td>
                        <td><?= formatCurrency($order['shipping_cost']) ?></td>
                        <td><?= formatCurrency($order['total_amount']) ?></td>
                        <td><?= e($order['order_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
