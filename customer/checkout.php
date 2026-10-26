<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
ensureCustomerLogin();

$customer = getCurrentCustomer();
$summary = getCartSummary();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();

    $recipientName = trim($_POST['recipient_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($recipientName === '' || $phone === '' || $address === '') {
        $error = 'Nama penerima, WhatsApp, dan alamat wajib diisi.';
    } elseif ($summary['total_items'] < (int)getSetting('minimum_order', 6)) {
        $error = 'Total pembelian belum mencapai minimum 6 pcs.';
    } else {
        db()->beginTransaction();
        try {
            $orderNumber = generateOrderNumber();
            $stmt = db()->prepare('INSERT INTO orders (order_number, user_id, recipient_name, phone, address, total_items, subtotal, shipping_cost, total_amount, payment_method, payment_status, order_status, notes) VALUES (:order_number, :user_id, :recipient_name, :phone, :address, :total_items, :subtotal, :shipping_cost, :total_amount, "Cash", "Belum Dibayar", "Pesanan Dibuat", :notes)');
            $stmt->execute([
                ':order_number' => $orderNumber,
                ':user_id' => $_SESSION['customer_id'],
                ':recipient_name' => $recipientName,
                ':phone' => $phone,
                ':address' => $address,
                ':total_items' => (int)$summary['total_items'],
                ':subtotal' => (float)$summary['subtotal'],
                ':shipping_cost' => (float)$summary['shipping_cost'],
                ':total_amount' => (float)$summary['total_amount'],
                ':notes' => $notes,
            ]);
            $orderId = (int)db()->lastInsertId();

            foreach ($summary['items'] as $item) {
                $insertItem = db()->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)');
                $insertItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => (int)$item['product_id'],
                    ':product_name' => $item['name'],
                    ':price' => (float)$item['price'],
                    ':quantity' => (int)$item['quantity'],
                    ':subtotal' => (float)$item['subtotal'],
                ]);
            }

            $history = db()->prepare('INSERT INTO order_status_history (order_id, status, description) VALUES (:order_id, "Pesanan Dibuat", "Pesanan berhasil dibuat oleh customer.")');
            $history->execute([':order_id' => $orderId]);
            db()->commit();

            $_SESSION['cart'] = [];
            redirect('/donat_ns/customer/order_detail.php?id=' . $orderId);
        } catch (Exception $e) {
            db()->rollBack();
            $error = 'Terjadi kesalahan saat checkout.';
        }
    }
}

require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4">Checkout</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <div class="mb-3">
                    <label class="form-label">Nama Penerima</label>
                    <input type="text" name="recipient_name" class="form-control" value="<?= e($customer['name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="text" name="phone" class="form-control" value="<?= e($customer['phone'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Pesanan</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-pink w-100">Konfirmasi Pesanan</button>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="summary-card">
                <h4>Ringkasan Pesanan</h4>
                <?php foreach ($summary['items'] as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span><?= e($item['name']) ?> x<?= (int)$item['quantity'] ?></span>
                        <strong><?= formatCurrency($item['subtotal']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between mt-3"><span>Subtotal</span><strong><?= formatCurrency($summary['subtotal']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Ongkir</span><strong><?= formatCurrency($summary['shipping_cost']) ?></strong></div>
                <div class="d-flex justify-content-between total-price"><span>Total Pembayaran</span><strong><?= formatCurrency($summary['total_amount']) ?></strong></div>
                <div class="alert alert-info mt-3">Metode pembayaran: Cash / Tunai</div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
