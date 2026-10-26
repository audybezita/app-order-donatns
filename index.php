<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    redirect('/donat_ns/admin/dashboard.php');
}

header('Location: /donat_ns/customer/dashboard.php');
exit;
