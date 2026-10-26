<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_admin.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) redirect('/donat_ns/admin/products.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'Tersedia';
    $imageName = $product['image'];

    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','svg','webp'], true)) {
            $error = 'Format gambar tidak valid.';
        } elseif ($file['size'] > 2000000) {
            $error = 'Ukuran gambar maksimal 2MB.';
        } else {
            $imageName = 'product_' . $id . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/products/' . $imageName);
        }
    }

    if (empty($error)) {
        $update = db()->prepare('UPDATE products SET name = :name, description = :description, price = :price, image = :image, status = :status, updated_at = NOW() WHERE id = :id');
        $update->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':image' => $imageName,
            ':status' => $status,
            ':id' => $id,
        ]);
        redirect('/donat_ns/admin/products.php');
    }
}

require_once __DIR__ . '/../includes/header_admin.php';
?>
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="summary-card">
                <h3 class="mb-4">Edit Produk</h3>
                <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="mb-3"><label class="form-label">Nama Produk</label><input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="4" required><?= e($product['description']) ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Harga</label><input type="number" name="price" class="form-control" value="<?= (float)$product['price'] ?>" min="1" step="100" required></div>
                    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Tersedia" <?= $product['status']==='Tersedia' ? 'selected' : '' ?>>Tersedia</option><option value="Habis" <?= $product['status']==='Habis' ? 'selected' : '' ?>>Habis</option></select></div>
                    <div class="mb-3"><label class="form-label">Foto Produk</label><input type="file" name="image" class="form-control"></div>
                    <button type="submit" class="btn btn-pink">Simpan</button>
                    <a href="/donat_ns/admin/products.php" class="btn btn-outline-pink">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
