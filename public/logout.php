<?php
session_start();

// Уничтожаем сессию
$_SESSION = [];
session_destroy();

// Отправляем ответ
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
