<?php
header('Content-Type: application/json');

include __DIR__ . '/../db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("
        INSERT INTO plots (status, billing, number, size, price)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['status'],
        $data['billing'],
        $data['number'],
        $data['size'],
        $data['price'],
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
