document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const error = document.getElementById("errorMessage");

  const user = JSON.parse(localStorage.getItem("user"));

  if (!user) {
    error.textContent = "Пользователь не найден. Зарегистрируйтесь.";
    return;
  }

  if (user.email === email && user.password === password) {
    localStorage.setItem("loggedIn", "true");
    alert("Добро пожаловать, " + user.name + "!");
    window.location.href = "profile.html"; // Переход в профиль
  } else {
    error.textContent = "Неверный email или пароль.";
  }
});