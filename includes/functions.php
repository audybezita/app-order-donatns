<?php
require_once __DIR__ . '/../config/database.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount) {
    return 'Rp' . number_format((float)$amount, 0, ',', '.');
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf() {
    if (empty($_POST['csrf_token']) || ($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        die('CSRF token tidak valid.');
    }
}

function getSetting($key, $default = null) {
    $stmt = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt->execute([':key' => $key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : $default;
}

function getSettings() {
    $stmt = db()->query('SELECT setting_key, setting_value FROM settings');
    $data = [];
    while ($row = $stmt->fetch()) {
        $data[$row['setting_key']] = $row['setting_value'];
    }
    return $data;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function isCustomerLoggedIn() {
    return isset($_SESSION['customer_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function ensureCustomerLogin() {
    if (!isCustomerLoggedIn()) {
        redirect('/donat_ns/customer/login.php');
    }
}

function ensureAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect('/donat_ns/admin/login.php');
    }
}

function getCurrentCustomer() {
    if (!isCustomerLoggedIn()) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id AND role = "customer" LIMIT 1');
    $stmt->execute([':id' => $_SESSION['customer_id']]);
    return $stmt->fetch();
}

function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id AND role = "admin" LIMIT 1');
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    return $stmt->fetch();
}

function getCart() {
    return $_SESSION['cart'] ?? [];
}

function addToCart($productId, $qty = 1) {
    $productId = (int)$productId;
    $qty = (int)$qty;
    if ($productId <= 0 || $qty <= 0) {
        return false;
    }

    $stmt = db()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();
    if (!$product || $product['status'] !== 'Tersedia') {
        return false;
    }

    $cart = getCart();
    $cart[$productId] = ($cart[$productId] ?? 0) + $qty;
    $_SESSION['cart'] = $cart;
    return true;
}

function updateCartItem($productId, $qty) {
    $cart = getCart();
    $productId = (int)$productId;
    $qty = (int)$qty;

    if ($qty <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $qty;
    }

    $_SESSION['cart'] = $cart;
}

function removeCartItem($productId) {
    $cart = getCart();
    unset($cart[(int)$productId]);
    $_SESSION['cart'] = $cart;
}

function getCartSummary() {
    $cart = getCart();
    $subtotal = 0;
    $totalItems = 0;
    $items = [];

    if ($cart) {
        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT * FROM products WHERE id IN (' . $placeholders . ')';
        $stmt = db()->prepare($sql);
        $stmt->execute($ids);
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $qty = (int)($cart[$product['id']] ?? 0);
            if ($qty <= 0) continue;
            $lineSubtotal = (float)$product['price'] * $qty;
            $subtotal += $lineSubtotal;
            $totalItems += $qty;
            $items[] = [
                'product_id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'quantity' => $qty,
                'subtotal' => $lineSubtotal,
                'image' => $product['image'],
                'status' => $product['status'],
            ];
        }
    }

    $minimumOrder = (int)(getSetting('minimum_order', 6));
    $shippingCost = ($totalItems > $minimumOrder) ? (float)(getSetting('shipping_cost', 3000)) : 0;
    $totalAmount = $subtotal + $shippingCost;

    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping_cost' => $shippingCost,
        'total_amount' => $totalAmount,
        'total_items' => $totalItems,
        'minimum_order' => $minimumOrder,
        'can_checkout' => $totalItems >= $minimumOrder,
    ];
}

function generateOrderNumber() {
    $date = date('Ymd');
    $stmt = db()->query('SELECT MAX(id) AS max_id FROM orders');
    $row = $stmt->fetch();
    $next = (int)($row['max_id'] ?? 0) + 1;
    return 'DNS-' . $date . '-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
}
