<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.html');
  exit;
}
echo "Вы вошли как: " . htmlspecialchars($_SESSION['username']) . " (роль: " . $_SESSION['role'] . ")";
?>
