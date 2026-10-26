<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $settings = [
        'shop_name' => $_POST['shop_name'] ?? 'Donat NS',
        'shop_description' => $_POST['shop_description'] ?? '',
        'whatsapp' => $_POST['whatsapp'] ?? '',
        'minimum_order' => (int)($_POST['minimum_order'] ?? 6),
        'shipping_cost' => (float)($_POST['shipping_cost'] ?? 3000),
        'payment_method' => 'Cash'
    ];

    foreach ($settings as $key => $value) {
        $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()');
        $stmt->execute([':key' => $key, ':value' => $value]);
    }
    redirect('/donat_ns/admin/settings.php?success=1');
}

$settings = getSettings();
require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="summary-card">
                <h3 class="mb-4">Pengaturan Website</h3>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Pengaturan berhasil disimpan.</div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="mb-3"><label class="form-label">Nama UMKM</label><input type="text" name="shop_name" class="form-control" value="<?= e($settings['shop_name'] ?? 'Donat NS') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi Website</label><textarea name="shop_description" class="form-control" rows="3"><?= e($settings['shop_description'] ?? '') ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Nomor WhatsApp</label><input type="text" name="whatsapp" class="form-control" value="<?= e($settings['whatsapp'] ?? '') ?>"></div>
                    <div class="mb-3"><label class="form-label">Minimum Pembelian</label><input type="number" name="minimum_order" class="form-control" value="<?= (int)($settings['minimum_order'] ?? 6) ?>" min="1"></div>
                    <div class="mb-3"><label class="form-label">Ongkir</label><input type="number" name="shipping_cost" class="form-control" value="<?= (float)($settings['shipping_cost'] ?? 3000) ?>" min="0"></div>
                    <button type="submit" class="btn btn-pink w-100">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
