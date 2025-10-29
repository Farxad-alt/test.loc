<?php
include __DIR__ . '/Plot.php';
include __DIR__ . '/User.php';
class PlotManager
{
    private $pdo;

    public function __construct($pdo)
    {
        if (! is_object($pdo) || ! ($pdo instanceof PDO)) {
            throw new Exception("Invalid PDO connection");
        }
        $this->pdo = $pdo;
    }

    // Получение общего количества участков с Plots
    public function getPlotsWithPagination($page = 1, $perPage = 20)
    {

        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare("
            SELECT
                p.plot_id,
                p.status,
                p.billing,
                p.number,
                p.size,
                p.price,
                u.user_id,
                u.first_name,
                u.last_name,
                u.phone
            FROM plots p
            LEFT JOIN users u ON p.plot_id = u.plot_id
            ORDER BY p.plot_id
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        // Группировка пользователей по участкам
        $plots = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $plotId = $row['plot_id'];
            if (! isset($plots[$plotId])) {
                $plots[$plotId] = [
                    'plot_id' => $plotId,
                    'status'  => $row['status'],
                    'billing' => $row['billing'],
                    'number'  => $row['number'],
                    'size'    => $row['size'],
                    'price'   => $row['price'],
                    'users'   => [],
                ];
            }

            if (! empty($row['user_id'])) {
                $plots[$plotId]['users'][] = [
                    'first_name' => $row['first_name'],
                    'last_name'  => $row['last_name'],
                    'phone'      => $row['phone'],
                ];
            }
        }

        return array_values($plots);
    }

    public function getTotalPlots()
    {
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT plot_id) FROM plots");
        return (int) $stmt->fetchColumn();
    }
    // Получение общего количества участков Users

    public function getUsersWithPagination($page = 1, $perPage = 20, $searchQuery = '')
    {
        $offset     = ($page - 1) * $perPage;
        $conditions = [];
        $params     = [];

        if (! empty($searchQuery)) {
            $conditions[]     = "u.first_name LIKE :query";
            $conditions[]     = "u.phone LIKE :query";
            $conditions[]     = "u.email LIKE :query";
            $params[':query'] = "%$searchQuery%";
        }

        $stmt = $this->pdo->prepare("
        SELECT
            u.user_id,
            u.first_name,
            u.last_name,
            u.phone,
            u.email,
            u.last_login,
            p.plot_id AS plot_id
        FROM users u
        LEFT JOIN plots p ON u.plot_id = p.plot_id
        " . (! empty($conditions) ? "WHERE " . implode(" OR ", $conditions) : "") . "
        ORDER BY u.user_id
        LIMIT :limit OFFSET :offset
    ");

        // Привязка параметров
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = [
                'user_id'    => $row['user_id'],
                'first_name' => $row['first_name'] ?? null,
                'last_name'  => $row['last_name'] ?? null,
                'phone'      => $row['phone'] ?? null,
                'email'      => $row['email'] ?? null,
                'last_login' => $row['last_login'] ?? null,
                'plot_id'    => $row['plot_id'] ?? null,
            ];
        }

        return $users;
    }

    public function getTotalUsers()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        return (int) $stmt->fetchColumn();
    }

    // Поиск участков по номеру
    public function searchPlots($query, $page = 1, $perPage = 20)
    {
        $offset  = max(0, ($page - 1) * $perPage);
        $perPage = max(1, min(100, (int) $perPage));

        $stmt = $this->pdo->prepare("
    SELECT
        p.plot_id,
        p.status,
        p.billing,
        p.number,
        p.size,
        p.price,
        u.user_id,
        u.first_name,
        u.last_name,
        u.phone
    FROM plots p
    LEFT JOIN users u ON p.plot_id = u.plot_id
    WHERE
        (CONCAT(p.number) LIKE :query_str OR p.number = :query_int)
        OR p.status = :query_int
        OR p.billing = :query_int
    ORDER BY p.plot_id
    LIMIT :limit OFFSET :offset
");

        $searchStr = "%$query%";
        $searchInt = (int) $query;

        $stmt->bindParam(':query_str', $searchStr, PDO::PARAM_STR);
        $stmt->bindParam(':query_int', $searchInt, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $plots = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $plotId = $row['plot_id'];
            if (! isset($plots[$plotId])) {
                $plots[$plotId] = [
                    'plot_id'  => $plotId,
                    'status'   => $row['status'],
                    'billing'  => $row['billing'],
                    'number'   => $row['number'],
                    'size'     => $row['size'],
                    'price'    => $row['price'],
                    'users'    => [],
                    'user_ids' => [], // Для проверки уникальности
                ];
            }

            if (! empty($row['user_id']) && ! in_array($row['user_id'], $plots[$plotId]['user_ids'])) {
                $plots[$plotId]['users'][] = [
                    'first_name' => $row['first_name'],
                    'last_name'  => $row['last_name'],
                    'phone'      => $row['phone'],
                ];
                $plots[$plotId]['user_ids'][] = $row['user_id'];
            }
        }

        if (empty($plots)) {
            error_log("Ничего не найдено для запроса: $query");
        }
        if (! $stmt->execute()) {
            error_log("Ошибка выполнения запроса: " . print_r($stmt->errorInfo(), true));
        }

        return array_values($plots);
    }

}
