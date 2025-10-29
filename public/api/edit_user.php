<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $userId    = $data['user_id'] ?? null;
    $firstName = $data['first_name'] ?? '';
    $lastName  = $data['last_name'] ?? '';
    $phone     = $data['phone'] ?? '';
    $email     = $data['email'] ?? '';
    $plotId    = $data['plot_id'] ?? null;
    $plots     = $data['plots'] ?? [];

    if (! $userId || ! is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    // Обновление данных пользователя
    $stmt = $pdo->prepare("
        UPDATE users
        SET
            first_name = ?,
            last_name = ?,
            phone = ?,
            email = ?,
            plot_id = ?
        WHERE user_id = ?
    ");

    $stmt->execute([
        $firstName,
        $lastName,
        $phone,
        $email,
        $plotId,
        $userId,
    ]);
 if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'User updated']);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found or no changes made']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
