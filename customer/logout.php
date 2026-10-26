<?php
require_once __DIR__ . '/../config/database.php';
session_unset();
session_destroy();
header('Location: /donat_ns/customer/login.php');
exit;
