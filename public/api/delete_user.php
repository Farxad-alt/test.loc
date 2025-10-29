<?php
header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';

    $userId = $_GET['id'] ?? null;

    if (! $userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing user ID']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'User deleted']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
