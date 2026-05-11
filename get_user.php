<?php
// get_user.php — returnează datele unui singur utilizator (JSON)
header('Content-Type: application/json');
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID invalid']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.username,
            COALESCE(p.phone,  '') AS phone,
            COALESCE(p.age,    '') AS age,
            COALESCE(p.gender, '') AS gender,
            COALESCE(p.goal,   '') AS goal
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = :id
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Utilizatorul nu există']);
        exit;
    }

    echo json_encode(['success' => true, 'user' => $user]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>