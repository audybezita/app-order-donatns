<?php
require_once __DIR__ . '/functions.php';
if (!isAdminLoggedIn()) {
    redirect('/donat_ns/admin/login.php');
}
