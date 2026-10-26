<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="brand-box">
        <h5>Donat NS</h5>
    </div>
    <ul class="nav flex-column">
        <li><a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="/donat_ns/admin/dashboard.php">Dashboard</a></li>
        <li><a class="<?= $currentPage === 'products.php' ? 'active' : '' ?>" href="/donat_ns/admin/products.php">Produk</a></li>
        <li><a class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>" href="/donat_ns/admin/orders.php">Pesanan</a></li>
        <li><a class="<?= $currentPage === 'customers.php' ? 'active' : '' ?>" href="/donat_ns/admin/customers.php">Pelanggan</a></li>
        <li><a class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>" href="/donat_ns/admin/reports.php">Laporan</a></li>
        <li><a class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>" href="/donat_ns/admin/settings.php">Pengaturan</a></li>
    </ul>
</aside>
