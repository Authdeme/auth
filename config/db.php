<?php
$host = 'sql106.infinityfree.com';
$dbname = 'if0_38945159_authsystem';
$username = 'if0_38945159';
$password = 'AuthDemo1';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>