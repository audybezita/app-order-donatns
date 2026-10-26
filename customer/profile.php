<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
ensureCustomerLogin();
$customer = getCurrentCustomer();
require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="summary-card">
                <h3 class="mb-3">Akun Saya</h3>
                <p><strong>Nama:</strong> <?= e($customer['name']) ?></p>
                <p><strong>Email:</strong> <?= e($customer['email']) ?></p>
                <p><strong>WhatsApp:</strong> <?= e($customer['phone']) ?></p>
                <a href="/donat_ns/customer/order_history.php" class="btn btn-pink mt-3">Lihat Pesanan Saya</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
