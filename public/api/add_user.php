<?php
header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';

     $data = json_decode(file_get_contents('php://input'), true);

 

    // Проверка длины кода
    if (strlen($data['phone_code']) !== 4) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Phone code must be 4 digits']);
        exit;
    }

  

    // Вставка нового пользователя
    $stmt = $pdo->prepare("
        INSERT INTO users (
            first_name,
            last_name,
            phone,
            email,
            phone_code
        ) VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['phone'],
        $data['email'],
        $data['phone_code'],
    ]);

    $userId = $pdo->lastInsertId();

    // Связывание с участками
    if (!empty($data['plot_id']) && is_array($data['plot_id'])) {
        $stmt = $pdo->prepare("INSERT INTO plots (user_id, plot_id) VALUES (?, ?)");
        foreach ($data['plot_id'] as $plotId) {
            if (is_numeric($plotId)) {
                $stmt->execute([$userId, (int)$plotId]);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'User added', 'user_id' => $userId]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}