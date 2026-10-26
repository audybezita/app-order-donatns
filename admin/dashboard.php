<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$totalProducts = (int)db()->query('SELECT COUNT(*) AS total FROM products')->fetch()['total'];
totalCustomers = (int)db()->query('SELECT COUNT(*) AS total FROM users WHERE role = "customer"')->fetch()['total'];
$orders = db()->query('SELECT COUNT(*) AS total FROM orders')->fetch()['total'];
$todaySales = (float)db()->query('SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE DATE(created_at) = CURDATE()')->fetch()['total'];
$monthSales = (float)db()->query('SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->fetch()['total'];
$processTotal = (int)db()->query('SELECT COUNT(*) AS total FROM orders WHERE order_status IN ("Pesanan Dibuat","Pesanan Dikonfirmasi Admin","Pesanan Sedang Diproses","Pesanan Sedang Disiapkan","Pesanan Dalam Pengiriman")')->fetch()['total'];
$sentTotal = (int)db()->query('SELECT COUNT(*) AS total FROM orders WHERE order_status = "Pesanan Dalam Pengiriman"')->fetch()['total'];
$doneTotal = (int)db()->query('SELECT COUNT(*) AS total FROM orders WHERE order_status = "Pesanan Selesai"')->fetch()['total'];
$cancelled = (int)db()->query('SELECT COUNT(*) AS total FROM orders WHERE order_status = "Pesanan Dibatalkan"')->fetch()['total'];

$dailyChart = db()->query('SELECT DATE(created_at) AS label, COALESCE(SUM(total_amount),0) AS total FROM orders GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC LIMIT 7')->fetchAll();
$popular = db()->query('SELECT product_name, SUM(quantity) AS sold FROM order_items GROUP BY product_name ORDER BY sold DESC LIMIT 5')->fetchAll();

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        <div class="col-md-3"><div class="stat-card"><div class="icon">🍩</div><div><span>Total Produk</span><h3><?= $totalProducts ?></h3></div></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="icon">👥</div><div><span>Total Pelanggan</span><h3><?= $totalCustomers ?></h3></div></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="icon">🧾</div><div><span>Total Pesanan</span><h3><?= $orders ?></h3></div></div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="icon">💸</div><div><span>Penjualan Hari Ini</span><h3><?= formatCurrency($todaySales) ?></h3></div></div></div></div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-6">
            <div class="chart-card">
                <h5>Penjualan Harian</h5>
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <h5>Produk Terlaris</h5>
                <canvas id="popularChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-12">
            <div class="chart-card">
                <h5>Ringkasan</h5>
                <div class="row">
                    <div class="col-md-3">Pesanan Hari Ini: <strong><?= (int)db()->query('SELECT COUNT(*) AS total FROM orders WHERE DATE(created_at)=CURDATE()')->fetch()['total'] ?></strong></div>
                    <div class="col-md-3">Penjualan Bulan Ini: <strong><?= formatCurrency($monthSales) ?></strong></div>
                    <div class="col-md-3">Pesanan Diproses: <strong><?= $processTotal ?></strong></div>
                    <div class="col-md-3">Pesanan Selesai: <strong><?= $doneTotal ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const dailyLabels = <?= json_encode(array_reverse(array_map(fn($x) => $x['label'], $dailyChart))) ?>;
const dailyValues = <?= json_encode(array_reverse(array_map(fn($x) => (float)$x['total'], $dailyChart))) ?>;
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Penjualan',
            data: dailyValues,
            borderColor: '#ff7aa2',
            backgroundColor: 'rgba(255,122,162,0.15)',
            tension: 0.4
        }]
    },
    options: { responsive: true }
});

const popularLabels = <?= json_encode(array_map(fn($x) => $x['product_name'], $popular)) ?>;
const popularValues = <?= json_encode(array_map(fn($x) => (int)$x['sold'], $popular)) ?>;
new Chart(document.getElementById('popularChart'), {
    type: 'bar',
    data: {
        labels: popularLabels,
        datasets: [{
            label: 'Jumlah Terjual',
            data: popularValues,
            backgroundColor: ['#ffd8e5','#dfeeff','#ffc5cf','#b1d4ff','#f7e9ff']
        }]
    },
    options: { responsive: true }
});
</script>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
