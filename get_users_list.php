<?php
// get_users_list.php — returnează lista de id + username pentru <select>
header('Content-Type: application/json');
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT id, username FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'users' => $users]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>