<?php
session_start();

// Путь к index.php
$login_url = '/templates/plots.php';

if (! isset($_SESSION['phone'])) {
    // Если не авторизован — перенаправляем на главную
    header("Location: $login_url");
    exit;
}
