<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // например: user или admin

        if ($user['role'] === 'admin') {
            header("Location: admin_panel.php");
        } else {
            header("Location: user_profile.php");
        }
        exit;
    } else {
        echo "Неверный логин или пароль.";
    }
}
?>