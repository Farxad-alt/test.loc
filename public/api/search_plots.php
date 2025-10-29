<?php

header('Content-Type: application/json');

try {
    include __DIR__ . '/../db.php';
    include __DIR__ . '/../classes/PlotManager.php';

    $plotManager = new PlotManager($pdo);
    $query       = isset($_GET['query']) ? $_GET['query'] : '';
    $page        = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

    $plots = $plotManager->searchPlots($query, $page);
    echo json_encode(['success' => true, 'data' => $plots]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// header('Content-Type: application/json');

// try {
//     include __DIR__ . '/../db.php';
//     include __DIR__ . '/../classes/PlotManager.php';

//     $plotManager = new PlotManager($pdo);
//     $type        = strtolower($_GET['type'] ?? '');
//     $query       = $_GET['query'] ?? '';
//     $page        = max(1, (int) ($_GET['page'] ?? 1));

//     switch ($type) {
//         case 'plots':
//             $data = $plotManager->searchPlots($query, $page);
//             break;
//         case 'users':
//             $data = $plotManager->getUsersWithPagination($page, 20, $query);
//             break;
//         default:
//             throw new Exception("Invalid type: $type");
//     }

//     echo json_encode(['success' => true, 'data' => $data]);
// } catch (Exception $e) {
//     http_response_code(500);
//     echo json_encode(['success' => false, 'error' => $e->getMessage()]);
// }