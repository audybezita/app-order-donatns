<?php require_once __DIR__ . '/functions.php'; $admin = getCurrentAdmin(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Donat NS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/donat_ns/assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include __DIR__ . '/sidebar_admin.php'; ?>
    <main class="main-content">
        <nav class="topbar">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">Dashboard Admin</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted"><?= e($admin['name'] ?? 'Admin') ?></span>
                        <a href="/donat_ns/admin/logout.php" class="btn btn-sm btn-outline-pink">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
