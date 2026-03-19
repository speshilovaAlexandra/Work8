<?php
session_start();
include("../settings/connect_datebase.php");

// Получаем данные
$login = trim($_POST['login']);
$password = $_POST['password'];

// Валидация пароля (Шаг 2) - мин 8 символов
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    die("weak_password");
}

// Проверка существования (Шаг 3) - Используем подготовленные выражения
$stmt = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "-1";
} else {
    // Хеширование (Шаг 4)
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $role = 0;

    // Вставка (Шаг 5 подготовка - нужно будет добавить email)
    // Пока вставляем только логин и пароль
    $stmt_insert = $mysqli->prepare("INSERT INTO `users`(`login`, `password`, `roll`) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("ssi", $login, $hash, $role);
    
    if ($stmt_insert->execute()) {
        $new_id = $mysqli->insert_id;
        $_SESSION['user'] = $new_id;
        echo $new_id;
    } else {
        echo "db_error";
    }
}
$stmt->close();
?>