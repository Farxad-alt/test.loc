<?php

header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';

    $userId = $_GET['user_id'] ?? null;

    if (! $userId || ! is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");

    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user['phone'] = (string) $user['phone'];
        echo json_encode(['success' => true, 'user' => $user]);

    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
