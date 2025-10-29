<?php

error_reporting(-1);
ini_set('display_errors', 1);

session_start(); // ⚠️ ВАЖНО: Запуск сессии должен быть первым!

// Подключение файлов
include __DIR__ . '/db.php';
include __DIR__ . '/functions.php'; // Убедись, что functions.php существует!
include __DIR__ . '/header.php';

// Переменная для ошибок
$errors = [];

// Путь к главной странице
$login_url = '/templates/plots.php';

// Если пользователь уже авторизован — перенаправляем
if (isset($_SESSION['phone'])) {
    header("Location: $login_url");
    exit;
}
?>

<?php



// Обработка формы телефона
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['step'])) {
    $phone = trim($_POST['phone'] ?? '');

    if (!empty($phone)) {
        if (user_exists($phone)) {
  
            $code = get_code_from_db($phone);

            $_SESSION['pending_phone'] = $phone;
            header("Location: ?step=code");
            exit;
        } else {
            $errors[] = "Пользователь с этим телефоном не найден.";
        }
    } else {
        $errors[] = "Введите телефон";
    }
}
// Обработка формы кода
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['step']) && $_GET['step'] === 'code') {
    $code  = trim($_POST['code'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $storedCode = get_code_from_db($phone);

    if ($storedCode && $code === $storedCode) {
        $_SESSION['phone'] = $phone;
        unset($_SESSION['pending_phone']);
        header("Location: /templates/plots.php");
        exit;
    } else {
        $errors[] = "Неверный код";
        header("Location: ?step=code&error=1");
        exit;
    }
}
?>

<main>
    <div class="wrap">
        <?php if (!empty($errors)): ?>
        <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>

        <?php if (!isset($_GET['step']) || $_GET['step'] !== 'code'): ?>
        <div class="login_wrap">
            <div id="login_note" class="fade"></div>
            <div class="login_controls">
                <form id="phoneForm" method="POST">
                    <input class="input_basic" type="text" name="phone" id="phoneInput" placeholder="Phone number"
                        autocorrect="off" oninput="validatePhoneInput(this.value)">
                    <small id="phoneError" class="error-message" style="display: none; color: red;">Введите только
                        цифры</small>
                    <button type="submit" class="btn_basic">Login</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="login_wrap">
            <div id="login_note" class="fade"><?php echo @$_GET['error'] ? 'Неверный код' : ''; ?></div>
            <div class="login_controls">
                <form id="codeForm" method="POST">
                    <input class="input_basic" type="text" name="code" placeholder="Code" autocomplete="off"
                        autocorrect="off" spellcheck="false">
                    <input type="hidden" name="phone"
                        value="<?php echo htmlspecialchars($_SESSION['pending_phone'] ?? ''); ?>">
                    <button type="submit" class="btn_basic">Confirm</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>

</main>
<?php include __DIR__ . '/footer.php'; ?>