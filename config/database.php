<?php
session_start();

function db() {
    static $pdo = null;

    if ($pdo === null) {
        $host = 'localhost';
        $db = 'donat_ns_app';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Koneksi database gagal. Pastikan MySQL XAMPP aktif dan database donat_ns_app sudah dibuat.');
        }
    }

    return $pdo;
}
