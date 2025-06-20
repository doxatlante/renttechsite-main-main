<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'];
    $phone = $_POST['phone'];
    $address = $_POST['shipping_address'];

    if ($productId && $phone && $address) {
        $stmt = $pdo->prepare("INSERT INTO orders (product_id, user_id, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productId, $userId, $phone, $address]);
    }
}

header("Location: user-profile.php");
exit;
