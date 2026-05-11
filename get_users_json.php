<?php
// get_users.php — endpoint AJAX pentru paginare
// Pune acest fișier în folderul fittrack/ (lângă db.php)

header('Content-Type: application/json');

require_once 'db.php'; // conexiunea ta PDO existentă

$k      = isset($_GET['k'])      ? (int)$_GET['k']      : 5;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if ($k < 1)      $k = 5;
if ($offset < 0) $offset = 0;

try {
    // Total înregistrări din users
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM users");
    $total = (int)$stmtTotal->fetchColumn();

    // JOIN users + user_profiles pentru date complete
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.username,
            u.created_at,
            COALESCE(p.phone,  '-') AS phone,
            COALESCE(p.age,    '-') AS age,
            COALESCE(p.gender, '-') AS gender,
            COALESCE(p.goal,   '-') AS goal
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        ORDER BY u.id
        LIMIT :k OFFSET :offset
    ");
    $stmt->bindValue(':k',      $k,      PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total'   => $total,
        'offset'  => $offset,
        'k'       => $k,
        'records' => $records
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>