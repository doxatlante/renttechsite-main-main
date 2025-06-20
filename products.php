<?php
session_start();
require 'db.php';

$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Каталог товаров - RentTech</title>
  <style>
    body { font-family: Arial, sans-serif; background: url('backgr.jpg')  no-repeat center center fixed; background-size: cover; }
    header {
      background-color: #333;
      color: white;
      padding: 20px;
      text-align: center;
    }
    nav a {
      color: white;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }
    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .product {
      border-bottom: 1px solid #ccc;
      padding: 15px 0;
      display: flex;
      align-items: center;
    }
    .product img {
      max-width: 150px;
      height: auto;
      margin-right: 20px;
    }
    .product-details {
      flex: 1;
    }
    .product h3 { margin: 0; }
    .product p { margin: 5px 0; }
    .buy-btn {
      background-color: #636363;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .buy-btn:hover {
      background-color: #a0a0a0;
    }

    /* Модальное окно */
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
    .close {
      float: right;
      font-size: 20px;
      font-weight: bold;
      cursor: pointer;
      color: #4B0082;
    }
    .close:hover {
      color: #000;
    }
    form input, form button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
    }
  </style>
</head>
<body>

<header>
  <h1>RentTech</h1>
  <nav>
  <a href="index.php">Главная</a>
  <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
    <a href="admin-panel.php">Личный кабинет</a>
  <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
    <a href="user-profile.php">Личный кабинет</a>
  <?php else: ?>
    <a href="login.php">Войти</a>
  <?php endif; ?>
</nav>

</header>

<div class="container">
  <h2>Каталог товаров</h2>
  <?php foreach ($products as $product): ?>
    <div class="product">
      <?php if (!empty($product['image'])): ?>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
      <?php endif; ?>
      <div class="product-details">
        <h3><?= htmlspecialchars($product['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong><?= htmlspecialchars($product['price']) ?>₽</strong></p>
        <button class="buy-btn" onclick="openModal(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($product['title'])) ?>', <?= $product['price'] ?>)">Купить</button>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Модальное окно покупки -->
<div id="buyModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Оформление покупки</h3>
    <form action="buy.php" method="post">
      <input type="hidden" name="product_id" id="product_id">
      <label>Товар:</label>
      <input type="text" id="product_title" disabled>
      <label>Цена:</label>
      <input type="text" id="product_price" disabled>
      <label>Номер телефона:</label>
      <input type="text" name="phone" placeholder="+7..." required>
      <label>Ваш адрес:</label>
      <input type="text" name="shipping_address" placeholder="Введите адрес доставки" required>
      <button type="submit">Подтвердить покупку</button>
    </form>

  </div>
</div>

<script>
  function openModal(id, title, price) {
    document.getElementById('product_id').value = id;
    document.getElementById('product_title').value = title;
    document.getElementById('product_price').value = price + ' ₽';
    document.getElementById('buyModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('buyModal').style.display = 'none';
  }

  window.onclick = function(event) {
    if (event.target === document.getElementById('buyModal')) {
      closeModal();
    }
  }
</script>

</body>
</html>
