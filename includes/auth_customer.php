<?php
require_once __DIR__ . '/functions.php';
if (!isCustomerLoggedIn()) {
    redirect('/donat_ns/customer/login.php');
}
