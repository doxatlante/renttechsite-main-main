<?php
$host = 'localhost';
$db   = 'renttechdb'; // название базы данных
$user = 'root';
$pass = ''; // без пароля по умолчанию в XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
  $pdo = new PDO($dsn, $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Ошибка подключения: " . $e->getMessage());
}
?>
