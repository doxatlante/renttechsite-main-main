<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = 'user'; // по умолчанию

  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);

  if ($stmt->rowCount() > 0) {
    echo "Пользователь с таким email уже существует.";
  } else {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $password, $role])) {
      header("Location: login.html");
      exit;
    } else {
      echo "Ошибка при регистрации.";
    }
  }
}
?>
