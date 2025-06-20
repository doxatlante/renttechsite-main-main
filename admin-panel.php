<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.html');
    exit;
}

// === Удаление товара ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $productId = $_POST['delete_product_id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
}

// === Добавление товара ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $image = $_POST['image_url'] ?? '';

    if ($title && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (title, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $price, $description, $image]);
    }
}

// === Обработка заказа ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_order_id'])) {
    $orderId = $_POST['process_order_id'];
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $currentStatus = $stmt->fetchColumn();

    $nextStatus = match ($currentStatus) {
        'ожидает' => 'в процессе',
        'в процессе' => 'завершено',
        default => 'завершено'
    };

    $update = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update->execute([$nextStatus, $orderId]);
}

// === Получение админа ===
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// === Товары ===
$products = $pdo->query("SELECT * FROM products")->fetchAll();

// === Заказы ===
$orders = $pdo->query("
  SELECT orders.id, orders.phone, orders.address, orders.created_at, orders.status,
         users.username, products.title
  FROM orders
  JOIN users ON orders.user_id = users.id
  JOIN products ON orders.product_id = products.id
  ORDER BY orders.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Админ-панель - RentTech</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0;  background: url('backgr.jpg')  no-repeat center center fixed; background-size: cover; }
    header { background-color: #333; color: #fff; padding: 20px; text-align: center; }
    nav a { color: #fff; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1100px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2, h3 { color: #333; }
    form { margin-top: 20px; }
    input, textarea { padding: 8px; margin-bottom: 10px; width: 100%; }
    button { background: #636363; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
    button:hover { background: #a0a0a0; }
    .product-list, .order-list { margin-top: 30px; }
    .product-item, .order-item {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .order-item div {
      flex: 1;
    }
  </style>
</head>
<body>

<header>
  <h1>RentTech</h1>
  <nav>
    <a href="index.php">Главная</a>
    <a href="logout.php">Выйти</a>
  </nav>
</header>

<div class="container">
  <h2>Админ: <?= htmlspecialchars($user['username']) ?></h2>

  <h3>Добавить товар</h3>
  <form method="post">
    <input type="hidden" name="add_product" value="1">
    <input type="text" name="name" placeholder="Название товара" required>
    <input type="number" name="price" placeholder="Цена (₽)" step="1" required>
    <textarea name="description" placeholder="Описание товара"></textarea>
    <input type="text" name="image_url" placeholder="Ссылка на изображение">
    <button type="submit">Добавить</button>
  </form>

  <h3>Список товаров</h3>
  <div class="product-list">
    <?php foreach ($products as $product): ?>
      <div class="product-item">
        <div>
          <strong><?= htmlspecialchars($product['title']) ?></strong><br>
          <?= htmlspecialchars($product['price']) ?>₽
        </div>
        <form method="post" onsubmit="return confirm('Удалить этот товар?');">
          <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
          <button type="submit">Удалить</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <h3>Заказы</h3>
  <div class="order-list">
    <?php foreach ($orders as $order): ?>
      <div class="order-item">
        <div><strong>Товар:</strong> <?= htmlspecialchars($order['title']) ?></div>
        <div><strong>Пользователь:</strong> <?= htmlspecialchars($order['username']) ?></div>
        <div><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></div>
        <div><strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?></div>
        <div><strong>Статус:</strong> <?= htmlspecialchars($order['status']) ?></div>
        <div><strong>Дата:</strong> <?= $order['created_at'] ?></div>
        <?php if ($order['status'] !== 'завершено'): ?>
          <form method="post">
            <input type="hidden" name="process_order_id" value="<?= $order['id'] ?>">
            <button type="submit">Обработать заказ</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
