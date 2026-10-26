<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = :email AND role = "customer" LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['customer_id'] = (int)$user['id'];
            $_SESSION['customer_name'] = $user['name'];
            $redirect = $_SESSION['redirect_after_login'] ?? '/donat_ns/customer/dashboard.php';
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

require_once __DIR__ . '/../includes/header_customer.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="auth-card">
                <h3 class="text-center mb-4">Login Customer</h3>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-pink w-100">Login</button>
                    <a href="/donat_ns/customer/register.php" class="btn btn-outline-pink w-100 mt-2">Daftar Akun</a>
                    <a href="/donat_ns/customer/dashboard.php" class="btn btn-link w-100 mt-2">Kembali ke dashboard</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer_customer.php'; ?>
