<?php

$host     = 'mysql-5.7';
$dbname   = 'fullstack';
$user     = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Возвращаем экземпляр PDO
    return $pdo;
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
