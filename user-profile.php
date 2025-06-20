<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Получение заказов
$orders = $pdo->prepare("
  SELECT o.*, p.title FROM orders o
  JOIN products p ON o.product_id = p.id
  WHERE o.user_id = ?
  ORDER BY o.created_at DESC
");
$orders->execute([$userId]);
$orderList = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Личный кабинет - RentTech</title>
  <style>
     body { font-family: Arial, sans-serif; margin: 0;  background: url('backgr.jpg')  no-repeat center center fixed; background-size: cover; }
    header { background-color: #333; color: white; padding: 20px; text-align: center; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 800px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; }
    .order { padding: 10px; border-bottom: 1px solid #ccc; margin-bottom: 15px; }
    .status-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      color: white;
      font-weight: bold;
      display: inline-block;
      margin-top: 8px;
    }
    .status-awaiting { background-color: #888; }       /* серый */
    .status-processing { background-color: #FFA500; }  /* оранжевый */
    .status-done { background-color: #28a745; }         /* зелёный */
  </style>
</head>
<body>

<header>
  <h1>RentTech</h1>
  <nav>
    <a href="index.php">Главная</a>
    <a href="products.php">Каталог</a>
    <a href="logout.php">Выйти</a>
  </nav>
</header>

<div class="container">
  <h2>Здравствуйте, <?= htmlspecialchars($user['username']) ?>!</h2>

  <h3>История заказов</h3>
  <?php if (count($orderList) === 0): ?>
    <p>У вас пока нет заказов.</p>
  <?php else: ?>
    <?php foreach ($orderList as $order): ?>
      <div class="order">
        <strong>Товар:</strong> <?= htmlspecialchars($order['title']) ?><br>
        <strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?><br>
        <strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?><br>
        <strong>Дата:</strong> <?= $order['created_at'] ?><br>

        <?php
          $statusClass = match ($order['status']) {
              'ожидает' => 'status-awaiting',
              'в процессе' => 'status-processing',
              'завершено' => 'status-done',
              default => 'status-awaiting'
          };
        ?>
        <span class="status-btn <?= $statusClass ?>">
          <?= htmlspecialchars($order['status']) ?>
        </span>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
