<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);

        header("Location: admin-panel.php?success=1");
    } catch (PDOException $e) {
        header("Location: admin-panel.php?error=" . urlencode($e->getMessage()));
    }
}
?>
