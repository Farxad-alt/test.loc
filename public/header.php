<?php

include_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/favicon-16x16.png">
    <link rel="stylesheet" type="text/css" media="all" href="/css/fonts.css?1" />
    <link rel="stylesheet" type="text/css" media="all" href="/css/style.css?1" />


</head>

<body>
    <?php if (isset($_SESSION['phone'])): ?>
    <div id="modal">
        <div id="modal_container">
            <div id="modal_overlay">
                <div class="dn" id="modal_close" onclick="common.modal_hide();"></div>
                <div id="modal_content"></div>
            </div>
        </div>
    </div>

    <header>
        <div class="wrap">
            <div class="main_menu">
                <div>
                    <a href="/index.php" class="<?= is_active('index.php') ?>">Home</a>
                    <a href="/delivery.php" class="<?= is_active('delivery.php') ?>">Delivery</a>
                    <a href="/services.php" class="<?= is_active('services.php') ?>">Services</a>
                    <a href="/payments.php" class="<?= is_active('payments.php') ?>">Payments</a>
                    <a href="/templates/plots.php" class="<?= is_active('plots.php') ?>">Plots</a>
                    <a href="/templates/users.php" class="<?= is_active('users.php') ?>">Users</a>
                    <a href="#">Messages</a>
                </div>

                <div>
                    <a href="#" id="logoutBtn">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <?php else: ?>
    <header>
        <div class="wrap guest-header">
            <h1>Добро пожаловать</h1>
            <p>Пожалуйста, введите номер телефона, чтобы получить доступ к полной версии сайта</p>
        </div>
    </header>
    <?php endif; ?>