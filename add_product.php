<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Пример простого запроса
    $sql = "INSERT INTO products (title, description, price) VALUES ('$title', '$description', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo "Товар успешно добавлен!";
    } else {
        echo "Ошибка: " . $conn->error;
    }
}
?>
