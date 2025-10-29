<?php

header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';

    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $page  = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $perPage = 20;

    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT u.user_id, u.first_name, u.last_name, u.phone, u.email, u.updated, u.last_login
        FROM users u
        WHERE u.first_name LIKE :query OR
              u.last_name LIKE :query OR
              u.email LIKE :query OR
              u.phone LIKE :query
        ORDER BY u.user_id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bindValue(':query', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $users
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
