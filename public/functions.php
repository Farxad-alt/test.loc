<?php

function debug($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function is_active($page)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return ($currentPage === $page) ? 'active' : '';
}

function is_authorized()
{
    return isset($_SESSION['phone']);
}

function get_user_phone()
{
    return $_SESSION['phone'] ?? null;
}

function logout()
{
    session_unset();
    session_destroy();
}

function user_exists($phone)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    return (bool)$stmt->fetchColumn();
}

function register_user($phone)
{
    global $pdo;

    $code = rand(1000, 9999);

    $stmt = $pdo->prepare("INSERT INTO users (phone, phone_code) VALUES (?, ?)");
    $stmt->execute([$phone, $code]);

    return $code;
}

function get_code_from_db($phone)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT phone_code FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['phone_code'] : null;
}

function generate_code($phone)
{
    global $pdo;

    $code = rand(1000, 9999);

    $stmt = $pdo->prepare("UPDATE users SET phone_code = ? WHERE phone = ?");
    $stmt->execute([$code, $phone]);

    return $code;
}

function verify_code($phone, $code)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT phone_code FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['phone_code'] == $code;
}

function activate_user($phone)
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE phone = ?");
    $stmt->execute([$phone]);
    return $stmt->rowCount() > 0;
}

function get_db()
{
    static $pdo = null;

    if ($pdo === null) {
        require __DIR__ . '/db.php';
        $pdo = $pdo;
    }

    return $pdo;
}

function clear_old_codes()
{
    global $pdo;

    $stmt = $pdo->prepare("UPDATE users SET phone_code = NULL WHERE phone_code IS NOT NULL AND created_at < NOW() - INTERVAL 10 MINUTE");
    $stmt->execute();
}